<?php

namespace App\Controllers;

use App\Models\Movie;

class Home extends BaseController
{
    public function index(): string
    {
        $perPage = 20;
        $q       = trim((string) $this->request->getGet('q'));

        $model   = new Movie();
        $builder = $model->orderBy('popularity', 'DESC');

        if ($q !== '') {
            $builder = $builder->like('name', $q);
        }

        $movies = $builder->paginate($perPage);
        $pager  = $model->pager;
        $total  = $model->countAllResults(false);

        return view('home/index', [
            'movies' => $movies,
            'pager'  => $pager,
            'total'  => $total,
            'q'      => $q,
            'title'  => 'CineDB · Movies',
        ]);
    }

    public function show(int $id): string
    {
        $model = new Movie();
        $movie = $model->find($id);

        if (! $movie) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Movie #' . $id . ' not found.'
            );
        }

        $genres = $model->getGenres($id);
        $people = $model->getPeople($id);

        $actors    = array_values(array_filter($people, static fn ($p) => $p->role === 'actor'));
        $directors = array_values(array_filter($people, static fn ($p) => $p->role === 'director'));

        return view('home/show', [
            'title'     => $movie->name,
            'movie'     => $movie,
            'genres'    => $genres,
            'actors'    => $actors,
            'directors' => $directors,
        ]);
    }
}
