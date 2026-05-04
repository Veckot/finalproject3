<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TMDBSeeder extends Seeder
{
    private $apiKey;
    protected $db;
    public function __construct()
    {
        $this->apiKey = getenv('TMDB_API_KEY');
    }

    public function run()
    {
        $this->db = \Config\Database::connect();
        $this->loadGenres();


        for ($page = 1; $page <= 20; $page++) {

            $data = $this->fetch("https://api.themoviedb.org/3/movie/popular?api_key={$this->apiKey}&page={$page}");

            foreach ($data->results ?? [] as $movie) {

                // skip duplicates
                $existing = $this->db->table('movie')
                    ->where('tmdb_id', $movie->id)
                    ->get()->getRow();

                if ($existing) {
                    $movieId = $existing->id;
                } else {

                    $details = $this->fetch("https://api.themoviedb.org/3/movie/{$movie->id}?api_key={$this->apiKey}");

                    $this->db->table('movie')->insert([
                        'tmdb_id' => $movie->id,
                        'name' => $movie->title,
                        'description' => $movie->overview,
                        'release_date' => $movie->release_date ?: null,
                        'runtime' => $details->runtime ?? null,
                        'rating' => $movie->vote_average,
                        'vote_count' => $movie->vote_count,
                        'revenue' => $details->revenue ?? null,
                        'budget' => $details->budget ?? null,
                        'status' => $details->status ?? null,
                        'adult' => $movie->adult,
                        'original_language' => $movie->original_language,
                        'original_title' => $movie->original_title,
                        'popularity' => $movie->popularity,
                        'pic' => $movie->poster_path ? "https://image.tmdb.org/t/p/w500{$movie->poster_path}" : null,
                    ]);

                    $movieId = $this->db->insertID();
                }

                // genres
                foreach ($movie->genre_ids ?? [] as $gid) {
                    $this->db->table('movie_genres')->ignore(true)->insert([
                        'genres_id' => $gid,
                        'movie_id' => $movieId
                    ]);
                }

                // credits
                $credits = $this->fetch("https://api.themoviedb.org/3/movie/{$movie->id}/credits?api_key={$this->apiKey}");

                foreach (array_slice($credits->cast ?? [], 0, 5) as $actor) {
                    $personId = $this->upsertPerson($actor->id);
                    $this->db->table('movie_people')->ignore(true)->insert([
                        'people_id' => $personId,
                        'movie_id' => $movieId,
                        'role' => 'actor'
                    ]);
                }

                foreach ($credits->crew ?? [] as $crew) {
                    if ($crew->job === 'Director') {
                        $personId = $this->upsertPerson($crew->id);
                        $this->db->table('movie_people')->ignore(true)->insert([
                            'people_id' => $personId,
                            'movie_id' => $movieId,
                            'role' => 'director'
                        ]);
                        break;
                    }
                }

                usleep(250000);
            }
        }
    }

    private function upsertPerson($id)
    {
        $existing = $this->db->table('people')->where('tmdb_id', $id)->get()->getRow();
        if ($existing) return $existing->id;

        $p = $this->fetch("https://api.themoviedb.org/3/person/{$id}?api_key={$this->apiKey}");

        $this->db->table('people')->insert([
            'tmdb_id' => $id,
            'name' => $p->name ?? '',
            'gender' => $p->gender ?? null,
            'birthday' => $p->birthday ?: null,
            'deathday' => $p->deathday ?: null,
            'place_of_birth' => $p->place_of_birth ?? null,
            'popularity' => $p->popularity ?? null,
            'known_for_department' => $p->known_for_department ?? null,
            'profile_path' => $p->profile_path ?? null
        ]);

        return $this->db->insertID();
    }

    private function loadGenres()
    {
        $data = $this->fetch("https://api.themoviedb.org/3/genre/movie/list?api_key={$this->apiKey}");

        foreach ($data->genres ?? [] as $g) {
            $this->db->table('genres')->ignore(true)->insert([
                'id' => $g->id,
                'name' => $g->name
            ]);
        }
    }

    private function fetch($url)
    {
        $json = @file_get_contents($url);
        return $json ? json_decode($json) : null;
    }
}
