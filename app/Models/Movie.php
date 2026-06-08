<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Movie model.
 *
 * Maps the `movie` table and adds query helpers for the many-to-many links
 * (genres, people) plus the statistics aggregations. Returns objects.
 *
 * Note: the framework property names below (e.g. $returnType, $allowedFields)
 * are defined by CodeIgniter\Model and therefore stay camelCase. Our own
 * methods use snake_case.
 */
class Movie extends Model
{
    protected $table            = 'movie';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tmdb_id',
        'name',
        'description',
        'release_date',
        'runtime',
        'rating',
        'vote_count',
        'revenue',
        'budget',
        'status',
        'adult',
        'original_language',
        'original_title',
        'popularity',
        'pic',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates (framework)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation (framework)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks (framework)
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // =========================================================================
    // Related data (JOINs)
    // =========================================================================

    /**
     * Genres linked to a movie.
     *
     * @param int $movie_id Movie id.
     * @return array<int,object> Rows of {id, name}.
     */
    public function get_genres(int $movie_id): array
    {
        return $this->db->table('movie_genres mg')
            ->select('g.id, g.name')
            ->join('genres g', 'g.id = mg.genres_id')
            ->where('mg.movie_id', $movie_id)
            ->get()
            ->getResult();
    }

    /**
     * People linked to a movie, with their role.
     *
     * @param int $movie_id Movie id.
     * @return array<int,object> Rows of {id, name, profile_path, known_for_department, role}.
     */
    public function get_people(int $movie_id): array
    {
        return $this->db->table('movie_people mp')
            ->select('p.id, p.name, p.profile_path, p.known_for_department, mp.role')
            ->join('people p', 'p.id = mp.people_id')
            ->where('mp.movie_id', $movie_id)
            ->orderBy('mp.role', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Restrict the query to movies of a given genre.
     *
     * Returns the model itself (with the JOIN applied) so the caller can chain
     * ->paginate() / ->findAll() and still read $model->pager.
     *
     * @param int $genre_id Genre id to filter by.
     * @return self
     */
    public function for_genre(int $genre_id): self
    {
        return $this->select('movie.*')
            ->join('movie_genres mg', 'mg.movie_id = movie.id')
            ->where('mg.genres_id', $genre_id);
    }

    // =========================================================================
    // Cast & crew links (movie_people)
    // =========================================================================

    /**
     * Attach a person to a movie in a role.
     *
     * The primary key of movie_people is (people_id, movie_id), so a person can
     * hold only one role per movie. We delete any existing link first, which
     * doubles as a "change role" operation.
     *
     * @param int    $movie_id  Movie id.
     * @param int    $person_id Person id.
     * @param string $role      Role label (actor / director / writer / producer).
     * @return void
     */
    public function attach_person(int $movie_id, int $person_id, string $role): void
    {
        $this->db->table('movie_people')
            ->where(['movie_id' => $movie_id, 'people_id' => $person_id])
            ->delete();

        $this->db->table('movie_people')->insert([
            'movie_id'  => $movie_id,
            'people_id' => $person_id,
            'role'      => $role,
        ]);
    }

    /**
     * Remove a person from a movie.
     *
     * @param int $movie_id  Movie id.
     * @param int $person_id Person id.
     * @return void
     */
    public function detach_person(int $movie_id, int $person_id): void
    {
        $this->db->table('movie_people')
            ->where(['movie_id' => $movie_id, 'people_id' => $person_id])
            ->delete();
    }

    // =========================================================================
    // Statistics (aggregation)
    // =========================================================================

    /**
     * Per-genre statistics.
     *
     * JOINs genres / movie_genres / movie and aggregates with COUNT / AVG / SUM,
     * grouped by genre.
     *
     * @return array<int,object> Rows of {genre, movie_count, avg_rating, total_revenue}.
     */
    public function genre_stats(): array
    {
        return $this->db->table('genres g')
            ->select('g.name AS genre')
            ->select('COUNT(mg.movie_id) AS movie_count')
            ->select('AVG(m.rating) AS avg_rating')
            ->select('SUM(m.revenue) AS total_revenue')
            ->join('movie_genres mg', 'mg.genres_id = g.id')
            ->join('movie m', 'm.id = mg.movie_id')
            ->groupBy('g.id, g.name')
            ->having('movie_count >', 0)
            ->orderBy('movie_count', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Catalogue-wide totals (a single aggregate row).
     *
     * @return object {total_movies, avg_rating, total_revenue, newest_year}.
     */
    public function catalogue_totals(): object
    {
        return $this->db->table('movie')
            ->select('COUNT(*) AS total_movies')
            ->select('AVG(rating) AS avg_rating')
            ->select('SUM(revenue) AS total_revenue')
            ->select('MAX(YEAR(release_date)) AS newest_year')
            ->get()
            ->getRow();
    }
}
