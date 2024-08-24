<?php

namespace App\Repository;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Roles;

class PermissionRoleRepository
{
    /**
     * Check if role has specified permissions
     * @return PermissionRole list
     */
    public function checkRoleHasSpecificPermission($permissionId, $roleId)
    {
        $return = false;
        $exist = PermissionRole::where('permission_id', $permissionId)->where('role_id', $roleId)->first();
        if ($exist) {
            $return = true;
        }
        return $return;
    }

    /**
     * Delete permisison if exists
     * @return PermissionRole list
     */
    public function deletePermission($permissionId, $roleId)
    {
        PermissionRole::where('permission_id', $permissionId)->where('role_id', $roleId)->delete();
    }

    /**
     * Save permisison
     * @param array $permissionData
     * @return PermissionRole list
     */
    public function savePermission($permissionData)
    {
        return PermissionRole::create($permissionData);
    }
}
