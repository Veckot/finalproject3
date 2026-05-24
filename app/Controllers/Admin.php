<?php

namespace App\Controllers;

use App\Models\Genres;
use App\Models\Movie;
use App\Models\People;

class Admin extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $movieCount  = (new Movie())->countAllResults();
        $genreCount  = (new Genres())->countAllResults();
        $personCount = (new People())->countAllResults();

        return view('admin/index', [
            'title'        => 'Admin Panel',
            'movieCount'   => $movieCount,
            'genreCount'   => $genreCount,
            'personCount'  => $personCount,
        ]);
    }

    public function add()
    {
        $entity = $this->request->getGet('entity') ?? 'movie';
        $entity = in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';

        return view('admin/add', [
            'title'  => 'Add Entry',
            'entity' => $entity,
        ]);
    }

    public function store()
    {
        $entity = $this->request->getPost('entity');

        switch ($entity) {
            case 'movie':
                $rules = [
                    'name'              => 'required|min_length[1]|max_length[255]',
                    'original_title'    => 'required|max_length[255]',
                    'original_language' => 'required|max_length[10]',
                    'description'       => 'permit_empty',
                    'release_date'      => 'permit_empty|valid_date[Y-m-d]',
                    'runtime'           => 'permit_empty|integer',
                    'rating'            => 'permit_empty|decimal',
                    'pic'               => 'permit_empty|max_length[255]',
                ];

                if (! $this->validate($rules)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors());
                }

                (new Movie())->insert([
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
                ]);
                break;

            case 'genre':
                $rules = [
                    'id'   => 'required|integer|is_unique[genres.id]',
                    'name' => 'required|max_length[255]',
                ];
                if (! $this->validate($rules)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors());
                }
                (new Genres())->insert([
                    'id'   => (int) $this->request->getPost('id'),
                    'name' => $this->request->getPost('name'),
                ]);
                break;

            case 'person':
                $rules = [
                    'name'                 => 'required|max_length[255]',
                    'gender'               => 'permit_empty|integer',
                    'birthday'             => 'permit_empty|valid_date[Y-m-d]',
                    'deathday'             => 'permit_empty|valid_date[Y-m-d]',
                    'place_of_birth'       => 'permit_empty|max_length[255]',
                    'known_for_department' => 'permit_empty|max_length[100]',
                    'profile_path'         => 'permit_empty|max_length[255]',
                ];
                if (! $this->validate($rules)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $this->validator->getErrors());
                }
                (new People())->insert([
                    'name'                 => $this->request->getPost('name'),
                    'gender'               => $this->request->getPost('gender') ?: null,
                    'birthday'             => $this->request->getPost('birthday') ?: null,
                    'deathday'             => $this->request->getPost('deathday') ?: null,
                    'place_of_birth'       => $this->request->getPost('place_of_birth') ?: null,
                    'known_for_department' => $this->request->getPost('known_for_department') ?: null,
                    'profile_path'         => $this->request->getPost('profile_path') ?: null,
                ]);
                break;

            default:
                return redirect()->to(site_url('admin/add'))
                    ->with('error', 'Unknown entity type.');
        }

        return redirect()->to(site_url('admin'))
            ->with('message', ucfirst($entity) . ' added successfully.');
    }

    public function listEntries()
    {
        $entity = $this->request->getGet('entity') ?? 'movie';
        $entity = in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';

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

    public function edit(string $entity, int $id)
    {
        $entity = in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';
        $model  = $this->modelFor($entity);
        $item   = $model->find($id);

        if (! $item) {
            return redirect()->to(site_url('admin/list?entity=' . $entity))
                ->with('error', 'Entry not found.');
        }

        return view('admin/edit', [
            'title'  => 'Edit ' . ucfirst($entity),
            'entity' => $entity,
            'item'   => $item,
        ]);
    }

    public function update(string $entity, int $id)
    {
        $entity = in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';
        $model  = $this->modelFor($entity);

        $data = $this->request->getPost();
        unset($data['id'], $data[csrf_token()]);

        foreach ($data as $k => $v) {
            if ($v === '') {
                $data[$k] = null;
            }
        }

        if ($entity === 'movie') {
            $data['adult'] = !empty($data['adult']) ? 1 : 0;
        }

        $model->update($id, $data);

        return redirect()->to(site_url('admin/list?entity=' . $entity))
            ->with('message', ucfirst($entity) . ' updated.');
    }

    public function delete(string $entity, int $id)
    {
        $entity = in_array($entity, ['movie', 'genre', 'person'], true) ? $entity : 'movie';
        $model  = $this->modelFor($entity);
        $model->delete($id);

        return redirect()->to(site_url('admin/list?entity=' . $entity))
            ->with('message', ucfirst($entity) . ' deleted.');
    }

    private function modelFor(string $entity)
    {
        return match ($entity) {
            'movie'  => new Movie(),
            'genre'  => new Genres(),
            'person' => new People(),
        };
    }
}
