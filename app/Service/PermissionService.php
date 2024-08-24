<?php

namespace App\Service;

use App\Repository\AdminRepository;
use App\Repository\PermissionRepository;
use App\Repository\PermissionRoleRepository;
use App\Repository\RoleRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;

class PermissionService
{

    protected $permissionRepo, $permissionRoleRepo;

    /**
     * @param PermissionRepository $permissionRepo reference to permissionRepo
     * @param PermissionRoleRepository $permissionRoleRepo reference to permissionRoleRepo
     * 
     */
    public function __construct(PermissionRepository $permissionRepo, PermissionRoleRepository $permissionRoleRepo)
    {
        $this->permissionRepo = $permissionRepo;
        $this->permissionRoleRepo = $permissionRoleRepo;
    }

    /** 
     * Fetch all permissions
     * @return Response
     */
    public function getPermissions()
    {
        $permissions = $this->permissionRepo->getPermissions();
        foreach ($permissions as $key => $permission) {
            $arrPermissions[$permission->permission_group][$key] = $permission;
        }
        return $arrPermissions;
    }

    /** 
     * Update all permissions
     * @param Request $request
     * @return Response
     */
    public function updatePermissions($request)
    {
        $exist = $this->permissionRoleRepo->checkRoleHasSpecificPermission($request->permissionId, $request->roleId);
        if ($exist) {
            $this->permissionRoleRepo->deletePermission($request->permissionId, $request->roleId);
        } else {
            $permissionData['permission_id'] = $request->permissionId;
            $permissionData['role_id'] = $request->roleId;
            $this->permissionRoleRepo->savePermission($permissionData);
        }
        return 'success';
    }
}
