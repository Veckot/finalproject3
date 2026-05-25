<?php

namespace App\Controllers;

use App\Models\Genres;
use App\Models\Movie;
use App\Models\People;
use Config\Messages;

class Admin extends BaseController
{
    protected $helpers = ['form', 'url'];

    /** @var Messages */
    protected $msg;

    public function __construct()
    {
        $this->msg = config('Messages');
    }

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function index(): string
    {
        return view('admin/index', [
            'title'       => 'Admin Panel',
            'movieCount'  => (new Movie())->countAllResults(),
            'genreCount'  => (new Genres())->countAllResults(),
            'personCount' => (new People())->countAllResults(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Add
    // -------------------------------------------------------------------------

    public function add(): string
    {
        $entity = $this->resolveEntity($this->request->getGet('entity'));

        return view('admin/add', [
            'title'  => 'Add Entry',
            'entity' => $entity,
        ]);
    }

    public function store()
    {
        $entity = $this->resolveEntity($this->request->getPost('entity'));

        switch ($entity) {
            case 'movie':
                if (! $this->validate($this->movieRules())) {
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors())
                        ->with('warning', $this->msg->validationFailed);
                }
                $name = $this->request->getPost('name');
                (new Movie())->insert([
                    'name'              => $name,
                    'original_title'    => $this->request->getPost('original_title'),
                    'original_language' => $this->request->getPost('original_language'),
                    'description'       => $this->request->getPost('description'),
                    'release_date'      => $this->request->getPost('release_date') ?: null,
                    'runtime'           => $this->request->getPost('runtime') ?: null,
                    'rating'            => $this->request->getPost('rating') ?: null,
                    'pic'               => $this->request->getPost('pic') ?: null,
                    'status'            => $this->request->getPost('status') ?: null,
                    'adult'             => $this->request->getPost('adult') ? 1 : 0,
                ]);
                return redirect()->to(site_url('admin'))
                    ->with('success', sprintf($this->msg->movieAdded, $name));

            case 'genre':
                if (! $this->validate($this->genreRules())) {
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors())
                        ->with('warning', $this->msg->validationFailed);
                }
                $name = $this->request->getPost('name');
                (new Genres())->insert([
                    'id'   => (int) $this->request->getPost('id'),
                    'name' => $name,
                ]);
                return redirect()->to(site_url('admin'))
                    ->with('success', sprintf($this->msg->genreAdded, $name));

            case 'person':
                if (! $this->validate($this->personRules())) {
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors())
                        ->with('warning', $this->msg->validationFailed);
                }
                $name = $this->request->getPost('name');
                (new People())->insert([
                    'name'                 => $name,
                    'gender'               => $this->request->getPost('gender') ?: null,
                    'birthday'             => $this->request->getPost('birthday') ?: null,
                    'deathday'             => $this->request->getPost('deathday') ?: null,
                    'place_of_birth'       => $this->request->getPost('place_of_birth') ?: null,
                    'known_for_department' => $this->request->getPost('known_for_department') ?: null,
                    'profile_path'         => $this->request->getPost('profile_path') ?: null,
                ]);
                return redirect()->to(site_url('admin'))
                    ->with('success', sprintf($this->msg->personAdded, $name));

            default:
                return redirect()->to(site_url('admin/add'))
                    ->with('error', $this->msg->unknownEntity);
        }
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function listEntries(): string
    {
        $entity  = $this->resolveEntity($this->request->getGet('entity'));
        $perPage = 15;
        $model   = $this->modelFor($entity);

        $items = $entity === 'genre'
            ? $model->orderBy('id', 'ASC')->paginate($perPage)
            : $model->orderBy('id', 'DESC')->paginate($perPage);

        return view('admin/list', [
            'title'  => 'Manage ' . ucfirst($entity) . 's',
            'entity' => $entity,
            'items'  => $items,
            'pager'  => $model->pager,
        ]);
    }

    // -------------------------------------------------------------------------
    // Edit
    // -------------------------------------------------------------------------

    public function edit(string $entity, int $id)
    {
        $entity = $this->resolveEntity($entity);
        $model  = $this->modelFor($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', $this->notFoundMsg($entity));
        }

        return view('admin/edit', [
            'title'  => 'Edit ' . ucfirst($entity),
            'entity' => $entity,
            'item'   => $item,
        ]);
    }

    public function update(string $entity, int $id)
    {
        $entity = $this->resolveEntity($entity);
        $model  = $this->modelFor($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', $this->notFoundMsg($entity));
        }

        $data = $this->request->getPost();
        unset($data['id'], $data[csrf_token()]);

        foreach ($data as $k => $v) {
            if ($v === '') {
                $data[$k] = null;
            }
        }

        if ($entity === 'movie') {
            $data['adult'] = ! empty($data['adult']) ? 1 : 0;
        }

        $model->update($id, $data);

        return redirect()->to(site_url('admin/list?entity=' . $entity))
            ->with('success', sprintf($this->updatedMsg($entity), $item->name));
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function delete(string $entity, int $id)
    {
        $entity = $this->resolveEntity($entity);
        $model  = $this->modelFor($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', $this->notFoundMsg($entity));
        }

        $model->delete($id);

        return redirect()->to(site_url('admin/list?entity=' . $entity))
            ->with('success', sprintf($this->deletedMsg($entity), $item->name));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function resolveEntity(?string $entity): string
    {
        return in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';
    }

    private function modelFor(string $entity)
    {
        return match ($entity) {
            'movie'  => new Movie(),
            'genre'  => new Genres(),
            'person' => new People(),
        };
    }

    private function notFoundMsg(string $entity): string
    {
        return match ($entity) {
            'movie'  => $this->msg->movieNotFound,
            'genre'  => $this->msg->genreNotFound,
            'person' => $this->msg->personNotFound,
        };
    }

    private function updatedMsg(string $entity): string
    {
        return match ($entity) {
            'movie'  => $this->msg->movieUpdated,
            'genre'  => $this->msg->genreUpdated,
            'person' => $this->msg->personUpdated,
        };
    }

    private function deletedMsg(string $entity): string
    {
        return match ($entity) {
            'movie'  => $this->msg->movieDeleted,
            'genre'  => $this->msg->genreDeleted,
            'person' => $this->msg->personDeleted,
        };
    }

    // -------------------------------------------------------------------------
    // Validation rule sets
    // -------------------------------------------------------------------------

    private function movieRules(): array
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

    private function genreRules(): array
    {
        return [
            'id'   => 'required|integer|is_unique[genres.id]',
            'name' => 'required|max_length[255]',
        ];
    }

    private function personRules(): array
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
}
