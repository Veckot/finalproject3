<?php

namespace App\Controllers;

use App\Models\Movie;

/**
 * Stats
 * =====
 * Public catalogue-statistics page.
 *
 * Demonstrates JOINs (genres + movie_genres + movie) and aggregation
 * functions (COUNT / AVG / SUM, GROUP BY) — see App\Models\Movie::genre_stats().
 */
class Stats extends BaseController
{
    /**
     * Show catalogue-wide totals and a per-genre breakdown.
     *
     * @return string Rendered statistics page.
     */
    public function index(): string
    {
        $movie = new Movie();

        $genre_stats = $movie->genre_stats();
        $totals      = $movie->catalogue_totals();

        // Largest movie count, used to scale the bar widths in the view.
        $max_count = 0;
        foreach ($genre_stats as $row) {
            $max_count = max($max_count, (int) $row->movie_count);
        }

        return view('stats/index', [
            'title'       => 'Statistics',
            'genre_stats' => $genre_stats,
            'totals'      => $totals,
            'max_count'   => $max_count,
            'crumbs'      => [
                ['label' => 'Home',       'url' => site_url('/')],
                ['label' => 'Statistics', 'url' => null],
            ],
        ]);
    }
}
