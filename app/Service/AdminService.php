<?php

namespace App\Service;

use App\Models\EmailTemplates;
use App\Repository\AdminRepository;
use App\Repository\EmailTemplatesRepository;
use App\Repository\UserRepository;

class AdminService
{

    protected $userRepo, $adminRepo, $emailRepo;

    /**
     * @param UserRepository $userRepo reference to userRepo
     *
     */
    public function __construct(UserRepository $userRepo, AdminRepository $adminRepo, EmailTemplatesRepository $emailRepo)
    {
        $this->userRepo = $userRepo;
        $this->adminRepo = $adminRepo;
        $this->emailRepo = $emailRepo;
    }

    /**
     * Verify the email
     * @param array @attributes
     */
    public function verifyEmail($attributes)
    {
        $result = $this->userRepo->verifyAdminEmail($attributes);
        if ($result == NULL) {
            return 'error';
        } else {
            $rememberToken = microtime();
            $encryptRememberToken =  encrypt($rememberToken);
            $data = [];
            $data['EMAIL'] = $attributes['email'];
            $data['email'] = $attributes['email'];
            $data['NAME'] = $result['name'];
            $link = url(config('app.adminPrefix') . 'reset-password') . '/' . $encryptRememberToken;

            $data['LINK'] = $link;
            $data['token'] = $encryptRememberToken;
            $data['imgPath'] = env('APP_URL') . 'public/backend/dist/img/user.png';
            $data['logoPath'] = env('APP_URL') . 'public/frontend/images/logo-color.png';
            try {
                $condition = [['email', $attributes['email']]];
                $result1 = $this->adminRepo->fetchPasswordDetails($condition);
                if ($result1) {
                    $updateData['token'] = $encryptRememberToken;
                    $condition = [['email', $attributes['email']]];
                    $this->adminRepo->updateToken($updateData, $condition);
                } else {
                    $this->adminRepo->createToken($data);
                }
                //pre($link);
                $this->emailRepo->sendMail('admin-forgot-password', $data);
                return 'success';
            } catch (Exception $e) {
                return 'error';
            }
        }
    }

    /**
     * Verify the token
     * @param string $attributes
     */
    public function verifyToken($attributes)
    {
        $condition = [['token', $attributes]];
        $result = $this->adminRepo->fetchPasswordDetails($condition);

        if ($result == NULL) {
            return 'invalid-token-error';
        } else {
            $addedTime = strtotime($result['updated_at']);
            $expTime = 1800; // 30 min
            $currentTime = time();

            if (($currentTime - $addedTime) > $expTime) {
                return 'expired-token-error';
            } else {
                return 'success';
            }
        }
    }

    /**
     * Reset the password
     * @param string $attributes
     */
    public function resetPassword($attributes)
    {
        $conditionForGettingAnEmail = [['token', $attributes['token']]];
        $fetchEmail = $this->adminRepo->fetchPasswordDetails($conditionForGettingAnEmail);

        $condition = [['token', $attributes['token']], ['email', $fetchEmail->email]];
        $result = $this->adminRepo->fetchPasswordDetails($condition);
        if ($result == NULL) {
            return 'user-error';
        } else {
            $attributes['email'] = $fetchEmail->email;
            $result = $this->adminRepo->passwordResetAction($attributes);
            //dd($result);
            return $result;
        }
    }

    /**
     * Set the password
     * @param string $attributes
     */
    public function setPassword($attributes)
    {
        $result = $this->userRepo->fetch($attributes['id']);
        if ($result == NULL) {
            return 'user-error';
        } else {
            $result = $this->adminRepo->passwordSetAction($attributes['id'],$attributes);
            return $result;
        }
    }
}
