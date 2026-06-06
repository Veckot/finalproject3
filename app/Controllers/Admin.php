<?php

namespace App\Controllers;

use App\Models\Genres;
use App\Models\Movie;
use App\Models\People;
use App\Models\Users;
use Config\Messages;

/**
 * Admin
 * =====
 * Back-office controller. Every route is protected by the `admin` filter, so
 * only logged-in administrators reach these methods.
 *
 * Methods are grouped by area:
 *   1) Dashboard
 *   2) Generic entity CRUD (movie / genre / person)
 *   3) Movie cast & crew (movie <-> people links)
 *   4) User management (Ion Auth accounts)
 *   5) Private helpers
 *   6) Validation rule sets
 *
 * The controller stays thin: shared decisions (which model, which message,
 * which rules) live in the small private helpers at the bottom.
 */
class Admin extends BaseController
{
    /** Helpers available in this controller and its views. */
    protected $helpers = ['form', 'url', 'form_ext'];

    /** User-facing text (flash messages), loaded from Config\Messages. */
    protected Messages $msg;

    public function __construct()
    {
        $this->msg = config('Messages');
    }

    // =========================================================================
    // 1) DASHBOARD
    // =========================================================================

    /**
     * Admin landing page: action tiles + record counts (COUNT aggregation).
     *
     * @return string Rendered admin dashboard.
     */
    public function index(): string
    {
        return view('admin/index', [
            'title'        => 'Admin Panel',
            'movie_count'  => (new Movie())->countAllResults(),
            'genre_count'  => (new Genres())->countAllResults(),
            'person_count' => (new People())->countAllResults(),
        ]);
    }

    // =========================================================================
    // 2) GENERIC ENTITY CRUD  (movie / genre / person)
    // =========================================================================

    /**
     * Show the "add entry" form with a type switch (movie / genre / person).
     *
     * Named create() to follow the standard CRUD convention (GET = show the
     * creation form); store() handles the POST.
     *
     * @return string Rendered add form.
     */
    public function create(): string
    {
        $entity = $this->resolve_entity($this->request->getGet('entity'));

        return view('admin/add', [
            'title'  => 'Add Entry',
            'entity' => $entity,
        ]);
    }

    /**
     * Validate and store a new entry of the posted type.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Back to the dashboard on
     *                                            success, or back to the form
     *                                            with errors on failure.
     */
    public function store()
    {
        $entity = $this->resolve_entity($this->request->getPost('entity'));

        // Pick the right validation rules for the chosen type.
        $rules = match ($entity) {
            'movie'  => $this->movie_rules(),
            'genre'  => $this->genre_rules(),
            'person' => $this->person_rules(),
        };

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('warning', $this->msg->validationFailed);
        }

        $name = (string) $this->request->getPost('name');

        // Build the row and the success message per type.
        switch ($entity) {
            case 'movie':
                (new Movie())->insert($this->movie_payload());
                $message = sprintf($this->msg->movieAdded, $name);
                break;

            case 'genre':
                (new Genres())->insert([
                    'id'   => (int) $this->request->getPost('id'),
                    'name' => $name,
                ]);
                $message = sprintf($this->msg->genreAdded, $name);
                break;

            case 'person':
                (new People())->insert($this->person_payload());
                $message = sprintf($this->msg->personAdded, $name);
                break;

            default:
                return redirect()->to(site_url('admin/add'))
                    ->with('error', $this->msg->unknownEntity);
        }

        return redirect()->to(site_url('admin'))->with('success', $message);
    }

    /**
     * Paginated, searchable list of entries for one type (edit/delete screen).
     *
     * @return string Rendered list view. Search term `q` and `entity` are kept
     *                in the pagination links.
     */
    public function list_entries(): string
    {
        $entity = $this->resolve_entity($this->request->getGet('entity'));
        $search = trim((string) $this->request->getGet('q'));
        $model  = $this->model_for($entity);

        if ($search !== '') {
            $model->like('name', $search);
        }

        $items = $model->orderBy('name', 'ASC')->paginate(15);
        $model->pager->only(['entity', 'q']);

        return view('admin/list', [
            'title'  => 'Manage ' . ucfirst($entity) . 's',
            'entity' => $entity,
            'items'  => $items,
            'pager'  => $model->pager,
            'q'      => $search,
        ]);
    }

    /**
     * Show the edit form for a single entry.
     *
     * For movies it also loads the attached cast/crew and the full people list
     * so the admin can manage who is involved in the film.
     *
     * @param string $entity Entity type (movie / genre / person).
     * @param int    $id     Record id.
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function edit(string $entity, int $id)
    {
        $entity = $this->resolve_entity($entity);
        $model  = $this->model_for($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', $this->not_found_msg($entity));
        }

        $data = [
            'title'  => 'Edit ' . ucfirst($entity),
            'entity' => $entity,
            'item'   => $item,
        ];

        if ($entity === 'movie') {
            $data['attached_people'] = $model->get_people($id);
            $data['all_people']      = (new People())->orderBy('name', 'ASC')->findAll();
            $data['roles']           = $this->movie_roles();
        }

        return view('admin/edit', $data);
    }

    /**
     * Save changes to an existing entry.
     *
     * @param string $entity Entity type (movie / genre / person).
     * @param int    $id     Record id.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update(string $entity, int $id)
    {
        $entity = $this->resolve_entity($entity);
        $model  = $this->model_for($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', $this->not_found_msg($entity));
        }

        // Collect posted fields, drop the id and CSRF token, normalise blanks.
        $data = $this->request->getPost();
        unset($data['id'], $data[csrf_token()]);

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        // Checkbox isn't sent when unchecked, so force a boolean column.
        if ($entity === 'movie') {
            $data['adult'] = ! empty($data['adult']) ? 1 : 0;
        }

        $model->update($id, $data);

        return redirect()->to(site_url('admin/list?entity=' . $entity))
            ->with('success', sprintf($this->updated_msg($entity), $item->name));
    }

    /**
     * Delete an entry.
     *
     * @param string $entity Entity type (movie / genre / person).
     * @param int    $id     Record id.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete(string $entity, int $id)
    {
        $entity = $this->resolve_entity($entity);
        $model  = $this->model_for($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', $this->not_found_msg($entity));
        }

        $model->delete($id);

        return redirect()->to(site_url('admin/list?entity=' . $entity))
            ->with('success', sprintf($this->deleted_msg($entity), $item->name));
    }

    // =========================================================================
    // 3) MOVIE CAST & CREW  (movie <-> people)
    // =========================================================================

    /**
     * Attach a person to a movie in a given role (also re-roles if existing).
     *
     * @param int $movie_id Movie id.
     * @return \CodeIgniter\HTTP\RedirectResponse Back to the movie edit page.
     */
    public function attach_person(int $movie_id)
    {
        $movie_model = new Movie();

        if (! $movie_model->find($movie_id)) {
            return redirect()->to(site_url('admin/list?entity=movie'))
                ->with('error', $this->msg->movieNotFound);
        }

        $person_id = (int) $this->request->getPost('people_id');
        $role      = (string) $this->request->getPost('role');
        $person    = $person_id ? (new People())->find($person_id) : null;

        // Reject unknown people or roles outside our allowed set.
        if (! $person || ! array_key_exists($role, $this->movie_roles())) {
            return redirect()->to(site_url('admin/edit/movie/' . $movie_id))
                ->with('error', $this->msg->personAttachInvalid);
        }

        $movie_model->attach_person($movie_id, $person_id, $role);

        return redirect()->to(site_url('admin/edit/movie/' . $movie_id))
            ->with('success', sprintf(
                $this->msg->personAttached,
                $person->name,
                $this->movie_roles()[$role]
            ));
    }

    /**
     * Remove a person from a movie.
     *
     * @param int $movie_id  Movie id.
     * @param int $person_id Person id.
     * @return \CodeIgniter\HTTP\RedirectResponse Back to the movie edit page.
     */
    public function detach_person(int $movie_id, int $person_id)
    {
        $movie_model = new Movie();

        if (! $movie_model->find($movie_id)) {
            return redirect()->to(site_url('admin/list?entity=movie'))
                ->with('error', $this->msg->movieNotFound);
        }

        $person = (new People())->find($person_id);
        $movie_model->detach_person($movie_id, $person_id);

        return redirect()->to(site_url('admin/edit/movie/' . $movie_id))
            ->with('success', sprintf($this->msg->personDetached, $person->name ?? 'Person'));
    }

    // =========================================================================
    // 4) USER MANAGEMENT  (Ion Auth)
    // =========================================================================

    /**
     * Paginated, searchable list of user accounts; flags administrators.
     *
     * @return string Rendered users list.
     */
    public function users(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        $model  = new Users();

        if ($search !== '') {
            $model->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $items = $model->orderBy('id', 'ASC')->paginate(15);
        $model->pager->only(['q']);

        // Tag each row with an is_admin flag for the badge in the table.
        $admin_name = config('IonAuth')->adminGroup;
        $checker    = new Users();
        foreach ($items as $user) {
            $user->is_admin = $checker->is_admin((int) $user->id, $admin_name);
        }

        return view('admin/users', [
            'title' => 'Manage Users',
            'items' => $items,
            'pager' => $model->pager,
            'q'     => $search,
        ]);
    }

    /**
     * Show the edit form for a single user account.
     *
     * @param int $id User id.
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function edit_user(int $id)
    {
        $ion_auth = new \IonAuth\Libraries\IonAuth();
        $user     = $ion_auth->user($id)->row();

        if (! $user) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', $this->msg->userNotFound);
        }

        return view('admin/user_edit', [
            'title'    => 'Edit User',
            'user'     => $user,
            'is_admin' => $ion_auth->isAdmin($id),
        ]);
    }

    /**
     * Persist user changes: profile fields, active flag, admin role and
     * (optionally) a new password.
     *
     * @param int $id User id.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update_user(int $id)
    {
        $ion_auth = new \IonAuth\Libraries\IonAuth();
        $user     = $ion_auth->user($id)->row();

        if (! $user) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', $this->msg->userNotFound);
        }

        if (! $this->validate($this->user_rules())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('warning', $this->msg->userUpdateFailed);
        }

        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'active'     => $this->request->getPost('active') ? 1 : 0,
        ];

        // Only touch the password when a new one was actually entered.
        $new_password = (string) $this->request->getPost('password');
        if ($new_password !== '') {
            $data['password'] = $new_password;
        }

        $ion_auth->update($id, $data);

        // Keep admin-group membership in sync with the checkbox.
        $admin_group_id = $this->admin_group_id();
        if ($admin_group_id !== null) {
            if ($this->request->getPost('is_admin')) {
                $ion_auth->addToGroup($admin_group_id, $id);
            } else {
                $ion_auth->removeFromGroup($admin_group_id, $id);
            }
        }

        return redirect()->to(site_url('admin/users'))
            ->with('success', sprintf($this->msg->userUpdated, $data['username']));
    }

    /**
     * Delete a user account (cannot delete the currently logged-in account).
     *
     * @param int $id User id.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete_user(int $id)
    {
        $ion_auth = new \IonAuth\Libraries\IonAuth();
        $user     = $ion_auth->user($id)->row();

        if (! $user) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', $this->msg->userNotFound);
        }

        if ((int) $ion_auth->user()->row()->id === $id) {
            return redirect()->to(site_url('admin/users'))
                ->with('error', $this->msg->userSelfDelete);
        }

        $ion_auth->deleteUser($id);

        return redirect()->to(site_url('admin/users'))
            ->with('success', sprintf($this->msg->userDeleted, $user->username ?? $user->email));
    }

    // =========================================================================
    // 5) PRIVATE HELPERS  (keep the actions above small and duplication-free)
    // =========================================================================

    /**
     * Validate an entity type, falling back to "movie" for anything unknown.
     *
     * @param string|null $entity Raw entity value from the request.
     * @return string             One of: movie, genre, person.
     */
    private function resolve_entity(?string $entity): string
    {
        return in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';
    }

    /**
     * Return the model instance for an entity type.
     *
     * @param string $entity One of: movie, genre, person.
     * @return Movie|Genres|People
     */
    private function model_for(string $entity)
    {
        return match ($entity) {
            'movie'  => new Movie(),
            'genre'  => new Genres(),
            'person' => new People(),
        };
    }

    /**
     * Roles a person can hold on a movie (value => label).
     *
     * @return array<string,string>
     */
    private function movie_roles(): array
    {
        return [
            'actor'    => 'Actor',
            'director' => 'Director',
            'writer'   => 'Writer',
            'producer' => 'Producer',
        ];
    }

    /**
     * Look up the numeric id of the administrators group.
     *
     * @return int|null Group id, or null when the group doesn't exist.
     */
    private function admin_group_id(): ?int
    {
        $config = config('IonAuth');
        $group  = $config->databaseGroupName ?: null;

        $row = \Config\Database::connect($group)
            ->table($config->tables['groups'])
            ->where('name', $config->adminGroup)
            ->get()
            ->getRow();

        return isset($row->id) ? (int) $row->id : null;
    }

    /**
     * "Not found" message for an entity type.
     *
     * @param string $entity One of: movie, genre, person.
     * @return string
     */
    private function not_found_msg(string $entity): string
    {
        return match ($entity) {
            'movie'  => $this->msg->movieNotFound,
            'genre'  => $this->msg->genreNotFound,
            'person' => $this->msg->personNotFound,
        };
    }

    /**
     * "Updated" message for an entity type.
     *
     * @param string $entity One of: movie, genre, person.
     * @return string
     */
    private function updated_msg(string $entity): string
    {
        return match ($entity) {
            'movie'  => $this->msg->movieUpdated,
            'genre'  => $this->msg->genreUpdated,
            'person' => $this->msg->personUpdated,
        };
    }

    /**
     * "Deleted" message for an entity type.
     *
     * @param string $entity One of: movie, genre, person.
     * @return string
     */
    private function deleted_msg(string $entity): string
    {
        return match ($entity) {
            'movie'  => $this->msg->movieDeleted,
            'genre'  => $this->msg->genreDeleted,
            'person' => $this->msg->personDeleted,
        };
    }

    /**
     * Build the movie insert payload from the current request.
     *
     * @return array<string,mixed>
     */
    private function movie_payload(): array
    {
        return [
            'name'              => $this->request->getPost('name'),
            'original_title'    => $this->request->getPost('original_title'),
            'original_language' => $this->request->getPost('original_language'),
            'description'       => $this->request->getPost('description'),
            'release_date'      => $this->request->getPost('release_date') ?: null,
            'runtime'           => $this->request->getPost('runtime') ?: null,
            'rating'            => $this->request->getPost('rating') ?: null,
            'pic'               => $this->request->getPost('pic') ?: null,
            'status'            => $this->request->getPost('status') ?: null,
            'adult'             => $this->request->getPost('adult') ? 1 : 0,
        ];
    }

    /**
     * Build the person insert payload from the current request.
     *
     * @return array<string,mixed>
     */
    private function person_payload(): array
    {
        return [
            'name'                 => $this->request->getPost('name'),
            'gender'               => $this->request->getPost('gender') ?: null,
            'birthday'             => $this->request->getPost('birthday') ?: null,
            'deathday'             => $this->request->getPost('deathday') ?: null,
            'place_of_birth'       => $this->request->getPost('place_of_birth') ?: null,
            'known_for_department' => $this->request->getPost('known_for_department') ?: null,
            'profile_path'         => $this->request->getPost('profile_path') ?: null,
        ];
    }

    // =========================================================================
    // 6) VALIDATION RULE SETS
    // =========================================================================

    /** @return array<string,string> Rules for creating a movie. */
    private function movie_rules(): array
    {
        return [
            'name'              => 'required|min_length[1]|max_length[255]',
            'original_title'    => 'required|max_length[255]',
            'original_language' => 'required|max_length[10]',
            'description'       => 'permit_empty',
            'release_date'      => 'permit_empty|valid_date[Y-m-d]',
            'runtime'           => 'permit_empty|integer',
            'rating'            => 'permit_empty|decimal',
            'pic'               => 'permit_empty|max_length[255]',
        ];
    }

    /** @return array<string,string> Rules for creating a genre. */
    private function genre_rules(): array
    {
        return [
            'id'   => 'required|integer|is_unique[genres.id]',
            'name' => 'required|max_length[255]',
        ];
    }

    /** @return array<string,string> Rules for creating a person. */
    private function person_rules(): array
    {
        return [
            'name'                 => 'required|max_length[255]',
            'gender'               => 'permit_empty|integer',
            'birthday'             => 'permit_empty|valid_date[Y-m-d]',
            'deathday'             => 'permit_empty|valid_date[Y-m-d]',
            'place_of_birth'       => 'permit_empty|max_length[255]',
            'known_for_department' => 'permit_empty|max_length[100]',
            'profile_path'         => 'permit_empty|max_length[255]',
        ];
    }

    /** @return array<string,string> Rules for updating a user account. */
    private function user_rules(): array
    {
        return [
            'username' => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email',
            'password' => 'permit_empty|min_length[8]',
        ];
    }
}
