<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * TMDBSeeder
 * ==========
 * Fills the catalogue from the public TMDB API: the first 20 pages of popular
 * movies plus, for each movie, its genres, top 5 cast members and director.
 *
 * Requires a TMDB_API_KEY environment variable (.env). `run()` is the method
 * Ion Auth / CodeIgniter's seeder runner calls and keeps that name.
 */
class TMDBSeeder extends Seeder
{
    /** TMDB API key, read from the environment. */
    private $api_key;

    /** Database connection. */
    protected $db;

    public function __construct()
    {
        $this->api_key = getenv('TMDB_API_KEY');
    }

    /**
     * Import popular movies (and their genres, cast and director) from TMDB.
     *
     * @return void
     */
    public function run()
    {
        $this->db = \Config\Database::connect();
        $this->load_genres();

        for ($page = 1; $page <= 20; $page++) {

            $data = $this->fetch("https://api.themoviedb.org/3/movie/popular?api_key={$this->api_key}&page={$page}");

            foreach ($data->results ?? [] as $movie) {

                // Skip movies we already imported (unique by tmdb_id).
                $existing = $this->db->table('movie')
                    ->where('tmdb_id', $movie->id)
                    ->get()->getRow();

                if ($existing) {
                    $movie_id = $existing->id;
                } else {
                    $details = $this->fetch("https://api.themoviedb.org/3/movie/{$movie->id}?api_key={$this->api_key}");

                    $this->db->table('movie')->insert([
                        'tmdb_id'           => $movie->id,
                        'name'              => $movie->title,
                        'description'       => $movie->overview,
                        'release_date'      => $movie->release_date ?: null,
                        'runtime'           => $details->runtime ?? null,
                        'rating'            => $movie->vote_average,
                        'vote_count'        => $movie->vote_count,
                        'revenue'           => $details->revenue ?? null,
                        'budget'            => $details->budget ?? null,
                        'status'            => $details->status ?? null,
                        'adult'             => $movie->adult,
                        'original_language' => $movie->original_language,
                        'original_title'    => $movie->original_title,
                        'popularity'        => $movie->popularity,
                        'pic'               => $movie->poster_path ? "https://image.tmdb.org/t/p/w500{$movie->poster_path}" : null,
                    ]);

                    $movie_id = $this->db->insertID();
                }

                // Link genres.
                foreach ($movie->genre_ids ?? [] as $genre_id) {
                    $this->db->table('movie_genres')->ignore(true)->insert([
                        'genres_id' => $genre_id,
                        'movie_id'  => $movie_id,
                    ]);
                }

                // Link top 5 cast members and the director.
                $credits = $this->fetch("https://api.themoviedb.org/3/movie/{$movie->id}/credits?api_key={$this->api_key}");

                foreach (array_slice($credits->cast ?? [], 0, 5) as $actor) {
                    $person_id = $this->upsert_person($actor->id);
                    $this->db->table('movie_people')->ignore(true)->insert([
                        'people_id' => $person_id,
                        'movie_id'  => $movie_id,
                        'role'      => 'actor',
                    ]);
                }

                foreach ($credits->crew ?? [] as $crew) {
                    if ($crew->job === 'Director') {
                        $person_id = $this->upsert_person($crew->id);
                        $this->db->table('movie_people')->ignore(true)->insert([
                            'people_id' => $person_id,
                            'movie_id'  => $movie_id,
                            'role'      => 'director',
                        ]);
                        break;
                    }
                }

                usleep(250000); // be polite to the TMDB API
            }
        }
    }

    /**
     * Insert a person by their TMDB id if not already present.
     *
     * @param int $tmdb_id TMDB person id.
     * @return int          Local people.id for the person.
     */
    private function upsert_person($tmdb_id)
    {
        $existing = $this->db->table('people')->where('tmdb_id', $tmdb_id)->get()->getRow();
        if ($existing) {
            return $existing->id;
        }

        $person = $this->fetch("https://api.themoviedb.org/3/person/{$tmdb_id}?api_key={$this->api_key}");

        $this->db->table('people')->insert([
            'tmdb_id'              => $tmdb_id,
            'name'                 => $person->name ?? '',
            'gender'               => $person->gender ?? null,
            'birthday'             => $person->birthday ?: null,
            'deathday'             => $person->deathday ?: null,
            'place_of_birth'       => $person->place_of_birth ?? null,
            'popularity'           => $person->popularity ?? null,
            'known_for_department' => $person->known_for_department ?? null,
            'profile_path'         => $person->profile_path ?? null,
        ]);

        return $this->db->insertID();
    }

    /**
     * Import the full TMDB genre list.
     *
     * @return void
     */
    private function load_genres()
    {
        $data = $this->fetch("https://api.themoviedb.org/3/genre/movie/list?api_key={$this->api_key}");

        foreach ($data->genres ?? [] as $genre) {
            $this->db->table('genres')->ignore(true)->insert([
                'id'   => $genre->id,
                'name' => $genre->name,
            ]);
        }
    }

    /**
     * Fetch a URL and decode its JSON body.
     *
     * @param string $url Absolute URL to fetch.
     * @return object|null Decoded JSON, or null on failure.
     */
    private function fetch($url)
    {
        $json = @file_get_contents($url);
        return $json ? json_decode($json) : null;
    }
}
