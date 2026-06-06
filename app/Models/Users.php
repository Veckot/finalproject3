<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // Read-only helper model over the Ion Auth `users` table. Mutations
    // (create/update/delete, group membership, password) go through the
    // Ion Auth library so its hashing and group logic stay authoritative.
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    /**
     * Whether a user belongs to the administrators group.
     *
     * @param int    $user_id    User id to check.
     * @param string $admin_name Admin group name (from Ion Auth config).
     * @return bool               True if the user is in the admin group.
     */
    public function is_admin(int $user_id, string $admin_name = 'admin'): bool
    {
        $count = $this->db->table('users_groups ug')
            ->join('groups g', 'g.id = ug.group_id')
            ->where('ug.user_id', $user_id)
            ->where('g.name', $admin_name)
            ->countAllResults();

        return $count > 0;
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
