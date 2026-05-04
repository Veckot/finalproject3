<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TMDBSeeder extends Seeder
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('TMDB_API_KEY');
    }


    public function run()
    {
        // Cache genres once
        $genreMap = $this->loadGenres();

        for ($page = 1; $page <= 20; $page++) {
            $url = "https://api.themoviedb.org/3/movie/popular?api_key={$this->apiKey}&page={$page}";
            $data = $this->fetch($url);

            if (!$data || empty($data->results)) continue;

            foreach ($data->results as $movie) {

                // Avoid duplicates
                $existingMovie = $this->db->table('movie')
                    ->where('name', $movie->title)
                    ->where('year', substr($movie->release_date ?? '', 0, 4))
                    ->get()->getRow();

                if ($existingMovie) {
                    $movieId = $existingMovie->id;
                } else {
                    // Insert movie
                    $this->db->table('movie')->insert([
                        'name' => $movie->title,
                        'tmdb_id' => $movie->id,
                        'description' => $movie->overview ?? '',
                        'year' => !empty($movie->release_date) ? substr($movie->release_date, 0, 4) : null,
                        'pic' => $movie->poster_path
                            ? 'https://image.tmdb.org/t/p/w500' . $movie->poster_path
                            : '',
                        'rating' => round($movie->vote_average),
                        'length' => rand(80, 180),
                        'language' => $movie->original_language ?? 'unknown'
                    ]);

                    $movieId = $this->db->insertID();
                }

                // Genres mapping
                foreach ($movie->genre_ids as $gid) {
                    if (!isset($genreMap[$gid])) continue;

                    $this->db->table('movie_genres')->ignore(true)->insert([
                        'genres_id' => $gid,
                        'movie_id' => $movieId
                    ]);
                }

                // Credits
                $credits = $this->fetch(
                    "https://api.themoviedb.org/3/movie/{$movie->id}/credits?api_key={$this->apiKey}"
                );

                if (!$credits) continue;

                // Actors (top 5)
                foreach (array_slice($credits->cast ?? [], 0, 5) as $actor) {

                    $personId = $this->upsertPerson($actor);

                    $this->db->table('movie_people')->ignore(true)->insert([
                        'people_id' => $personId,
                        'movie_id' => $movieId,
                        'role' => 'actor'
                    ]);
                }

                // Director
                foreach ($credits->crew ?? [] as $crew) {
                    if ($crew->job === 'Director') {

                        $personId = $this->upsertPerson($crew);

                        $this->db->table('movie_people')->ignore(true)->insert([
                            'people_id' => $personId,
                            'movie_id' => $movieId,
                            'role' => 'director'
                        ]);

                        break;
                    }
                }

                // Rate limit protection
                usleep(200000);
            }
        }
    }

    private function fetch($url)
    {
        $json = @file_get_contents($url);
        return $json ? json_decode($json) : null;
    }

    private function loadGenres()
    {
        $url = "https://api.themoviedb.org/3/genre/movie/list?api_key={$this->apiKey}";
        $data = $this->fetch($url);

        $map = [];

        if (!$data || empty($data->genres)) return $map;

        foreach ($data->genres as $g) {

            // insert if not exists
            $exists = $this->db->table('genres')
                ->where('id', $g->id)
                ->get()->getRow();

            if (!$exists) {
                $this->db->table('genres')->insert([
                    'id' => $g->id,
                    'name' => $g->name,
                    'description' => ''
                ]);
            }

            $map[$g->id] = $g->name;
        }

        return $map;
    }

    private function upsertPerson($person)
    {
        // check by tmdb_id
        $existing = $this->db->table('people')
            ->where('tmdb_id', $person->id)
            ->get()->getRow();

        if ($existing) {
            return $existing->id;
        }

        $this->db->table('people')->insert([
            'tmdb_id' => $person->id,
            'name' => $person->name ?? '',
            'surname' => '',
            'pic' => !empty($person->profile_path)
                ? 'https://image.tmdb.org/t/p/w200' . $person->profile_path
                : '',
            'bio' => '',
            'sex' => 'unknown'
        ]);

        return $this->db->insertID();
    }
}
