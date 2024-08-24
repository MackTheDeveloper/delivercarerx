<?php

namespace App\Service;

use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\RoleRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;

class RoleService
{

    protected $hospiceRepo, $userRepo, $roleRepo;

    /**
     * @param RoleRepository $roleRepo reference to roleRepo
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     * @param UserRepository $userRepo reference to userRepo
     * 
     */
    public function __construct(RoleRepository $roleRepo, HospiceRepository $hospiceRepo, UserRepository $userRepo)
    {
        $this->roleRepo = $roleRepo;
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
    }

    /** 
     * Fetch roles
     * @return Response
     */
    public function getRoleList()
    {
        return $this->roleRepo->fetchRoles();
    }

    /** 
     * Get role
     * @param integer $roleId
     * @return Response
     */
    public function getRole($roleId)
    {
        return $this->roleRepo->getRole($roleId);
    }

    /** 
     * Dropdown role
     * @param object $request
     */
    public function dropdown()
    {
        return $this->roleRepo->fetchRoleUsers();
    }
}
