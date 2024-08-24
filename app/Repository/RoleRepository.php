<?php

namespace App\Repository;

use App\Models\Roles;

class RoleRepository
{
    /**
     * Fetch the roles
     * @return Roles list
     */
    public function fetchRoles()
    {
        $roles = Roles::get();
        return $roles;
    }

    /**
     * Get the role
     * @param integer $roleId
     * @return Roles role
     */
    public function getRole($roleId)
    {
        $role = Roles::find($roleId);
        return $role;
    }

    /**
     * Fetch role user
     * @return array
     */
    public function fetchRoleUsers()
    {
        $hospiceUsers = Roles::select('id', 'role_title')->get()->toArray();
        return $hospiceUsers;
    }
}
