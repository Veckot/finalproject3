<?php

namespace App\Controllers;

use App\Models\Genres;
use App\Models\Movie;
use App\Models\People;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Home
 * ====
 * Public (no login required) part of the site.
 *
 * Methods are grouped by area:
 *   1) Movies   – index (list) + show (detail)
 *   2) Genres   – genres (list) + genre (films in one genre)
 *   3) People   – people (list) + person (detail)
 */
class Home extends BaseController
{
    // =========================================================================
    // 1) MOVIES
    // =========================================================================

    /**
     * Home page: paginated, searchable grid of movies (by popularity).
     *
     * @return string Rendered movie list.
     */
    public function index(): string
    {
        $per_page = 20;
        $search   = trim((string) $this->request->getGet('q'));

        $model = new Movie();
        $model->orderBy('popularity', 'DESC');

        if ($search !== '') {
            $model->like('name', $search);
        }

        $movies = $model->paginate($per_page);
        $model->pager->only(['q']);

        return view('home/index', [
            'title'  => 'CineDB · Movies',
            'movies' => $movies,
            'pager'  => $model->pager,
            'total'  => $model->countAllResults(false),
            'q'      => $search,
        ]);
    }

    /**
     * Movie detail: core info, genres and people (cast split from directors).
     *
     * @param int $id Movie id.
     * @return string Rendered detail page.
     * @throws PageNotFoundException When the movie does not exist.
     */
    public function show(int $id): string
    {
        $model = new Movie();
        $movie = $model->find($id);

        if (! $movie) {
            throw PageNotFoundException::forPageNotFound('Movie #' . $id . ' not found.');
        }

        $genres = $model->get_genres($id);
        $people = $model->get_people($id);

        // Split credited people into the two roles the detail page shows.
        $actors    = array_values(array_filter($people, static fn ($p) => $p->role === 'actor'));
        $directors = array_values(array_filter($people, static fn ($p) => $p->role === 'director'));

        return view('home/show', [
            'title'     => $movie->name,
            'movie'     => $movie,
            'genres'    => $genres,
            'actors'    => $actors,
            'directors' => $directors,
            'crumbs'    => [
                ['label' => 'Home',   'url' => site_url('/')],
                ['label' => 'Movies', 'url' => site_url('/')],
                ['label' => $movie->name, 'url' => null],
            ],
        ]);
    }

    // =========================================================================
    // 2) GENRES
    // =========================================================================

    /**
     * Browse all genres with their movie counts.
     *
     * @return string Rendered genre list.
     */
    public function genres(): string
    {
        return view('home/genres', [
            'title'  => 'Genres',
            'genres' => (new Genres())->with_movie_counts(),
            'crumbs' => [
                ['label' => 'Home',   'url' => site_url('/')],
                ['label' => 'Genres', 'url' => null],
            ],
        ]);
    }

    /**
     * Films belonging to one genre (paginated grid, like the main films view).
     *
     * @param int $id Genre id.
     * @return string Rendered grid.
     * @throws PageNotFoundException When the genre does not exist.
     */
    public function genre(int $id): string
    {
        $genre = (new Genres())->find($id);

        if (! $genre) {
            throw PageNotFoundException::forPageNotFound('Genre #' . $id . ' not found.');
        }

        $model  = new Movie();
        $movies = $model->for_genre($id)->orderBy('movie.popularity', 'DESC')->paginate(20);

        return view('home/genre', [
            'title'  => $genre->name . ' movies',
            'genre'  => $genre,
            'movies' => $movies,
            'pager'  => $model->pager,
            'crumbs' => [
                ['label' => 'Home',   'url' => site_url('/')],
                ['label' => 'Genres', 'url' => site_url('genres')],
                ['label' => $genre->name, 'url' => null],
            ],
        ]);
    }

    // =========================================================================
    // 3) PEOPLE
    // =========================================================================

    /**
     * Browse all people as a paginated, searchable grid.
     *
     * @return string Rendered people list.
     */
    public function people(): string
    {
        $per_page = 24;
        $search   = trim((string) $this->request->getGet('q'));

        $model = new People();
        if ($search !== '') {
            $model->like('name', $search);
        }

        $people = $model->orderBy('popularity', 'DESC')->paginate($per_page);
        $model->pager->only(['q']);

        return view('home/people', [
            'title'  => 'People',
            'people' => $people,
            'pager'  => $model->pager,
            'q'      => $search,
            'total'  => $model->countAllResults(false),
            'crumbs' => [
                ['label' => 'Home',   'url' => site_url('/')],
                ['label' => 'People', 'url' => null],
            ],
        ]);
    }

    /**
     * Person detail: profile + every movie they're credited on.
     *
     * @param int $id Person id.
     * @return string Rendered detail page.
     * @throws PageNotFoundException When the person does not exist.
     */
    public function person(int $id): string
    {
        $model  = new People();
        $person = $model->find($id);

        if (! $person) {
            throw PageNotFoundException::forPageNotFound('Person #' . $id . ' not found.');
        }

        return view('home/person', [
            'title'  => $person->name,
            'person' => $person,
            'movies' => $model->get_movies($id),
            'crumbs' => [
                ['label' => 'Home',   'url' => site_url('/')],
                ['label' => 'People', 'url' => site_url('people')],
                ['label' => $person->name, 'url' => null],
            ],
        ]);
    }
}
