<?php

namespace App\Models;

use CodeIgniter\Model;

class People extends Model
{
    protected $table            = 'people';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tmdb_id',
        'name',
        'gender',
        'birthday',
        'deathday',
        'place_of_birth',
        'popularity',
        'known_for_department',
        'profile_path',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    /**
     * All movies a person is linked to, with their role on each.
     *
     * JOINs movie_people with movie and orders by newest release first.
     *
     * @param int $person_id Person id to look up.
     * @return array<int,object> Rows of {id, name, pic, release_date, rating, role}.
     */
    public function get_movies(int $person_id): array
    {
        return $this->db->table('movie_people mp')
            ->select('m.id, m.name, m.pic, m.release_date, m.rating, mp.role')
            ->join('movie m', 'm.id = mp.movie_id')
            ->where('mp.people_id', $person_id)
            ->orderBy('m.release_date', 'DESC')
            ->get()
            ->getResult();
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
