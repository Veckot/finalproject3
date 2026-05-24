<?php

namespace App\Models;

use CodeIgniter\Model;

class Movie extends Model
{
    protected $table            = 'movie';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
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

    public function getGenres(int $movieId): array
    {
        return $this->db->table('movie_genres mg')
            ->select('g.id, g.name')
            ->join('genres g', 'g.id = mg.genres_id')
            ->where('mg.movie_id', $movieId)
            ->get()
            ->getResult();
    }

    public function getPeople(int $movieId): array
    {
        return $this->db->table('movie_people mp')
            ->select('p.id, p.name, p.profile_path, p.known_for_department, mp.role')
            ->join('people p', 'p.id = mp.people_id')
            ->where('mp.movie_id', $movieId)
            ->get()
            ->getResult();
    }

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
