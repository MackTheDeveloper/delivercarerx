<?php

namespace App\Http\Controllers;

use App\Models\PermissionRole;
use App\Models\User;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\HospiceService;
use App\Service\PermissionService;
use App\Service\RoleService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Session;

class RoleController extends Controller
{

    protected $roleService, $permissionServie;

    /**
     * constructor for initialize Role service
     *
     * @param RoleService $roleService reference to roleService
     * @param PermissionService $permissionServie reference to permissionServie
     * 
     */
    public function __construct(RoleService $roleService, PermissionService $permissionServie)
    {
        $this->roleService = $roleService;
        $this->permissionServie = $permissionServie;
    }

    /**
     * Roles listing
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->getRoleList();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Get permissions
     *
     * @param  Request $request
     * @param  $roleId
     * @return Response
     */
    public function getPermissions(Request $request, $roleId)
    {
        $role = $this->roleService->getRole($roleId);
        $arPermissions = $this->permissionServie->getPermissions();
        return view('admin.roles.permission', compact('roleId', 'arPermissions', 'role'));
    }

    /**
     * Update permissions
     *
     * @param  Request $request
     * @return Response
     */
    public function updatePermissions(Request $request)
    {
        $result = $this->permissionServie->updatePermissions($request);
        $return['status'] = $result;
        $return['msg'] = config('message.permissionMgt.updated');
        return $return;
    }
}
