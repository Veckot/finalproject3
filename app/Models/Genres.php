<?php

namespace App\Models;

use CodeIgniter\Model;

class Genres extends Model
{
    protected $table            = 'genres';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'name'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    /**
     * All genres together with how many movies each contains.
     *
     * LEFT JOIN so genres with zero movies still appear; aggregates with COUNT
     * and GROUP BY, ordered by movie count descending.
     *
     * @return array<int,object> Rows of {id, name, movie_count}.
     */
    public function with_movie_counts(): array
    {
        return $this->select('genres.id, genres.name, COUNT(mg.movie_id) AS movie_count')
            ->join('movie_genres mg', 'mg.genres_id = genres.id', 'left')
            ->groupBy('genres.id, genres.name')
            ->orderBy('movie_count', 'DESC')
            ->orderBy('genres.name', 'ASC')
            ->findAll();
    }

    // Dates
    protected $useTimestamps = true;
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
