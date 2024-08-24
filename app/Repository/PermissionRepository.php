<?php

namespace App\Repository;

use App\Models\Permission;
use App\Models\Roles;

class PermissionRepository
{
    /**
     * Fetch the roles
     * @return Permission list
     */
    public function getPermissions()
    {
        $permissions = Permission::get();
        return $permissions;
    }
}
