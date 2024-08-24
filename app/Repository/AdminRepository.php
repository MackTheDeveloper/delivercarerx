<?php

namespace App\Repository;

use App\Models\PasswordReset;
use App\Models\User;
use Hash;

class AdminRepository
{
    /**
     * Fetch password-reset details
     * @return PasswordReset
     */
    public function fetchPasswordDetails($condition)
    {
        return PasswordReset::where($condition)->first();
    }

    /**
     * Create password-reset token
     * @param array $data
     * @return PasswordReset
     */
    public function createToken(array $data)
    {
        $user = PasswordReset::create($data);
        return $user;
    }

    /**
     * Update password-reset token
     * @param array $data
     * @param array $condition
     * @return PasswordReset
     */
    public function updateToken(array $data, array $condition)
    {
        PasswordReset::where($condition)->update($data);
    }

    /**
     * Reset password-reset token
     * @param array $attributes
     * @return string
     */
    public function passwordResetAction($attributes)
    {
        $email = $attributes['email'];
        $password = $attributes['password'];
        $adminData = User::where('email', $email)->first();
        if (!empty($adminData['password'])) {
            $current_password = $adminData['password'];
            if (!Hash::check($password, $current_password)) {
                User::where('id', $adminData['id'])->update(['password' => Hash::make($password)]);
                PasswordReset::where('email', $email)->delete();
                return 'success';
            } else {
                return 'error';
            }
        } else {
            User::where('id', $adminData['id'])->update(['password' => Hash::make($password)]);
            return 'success';
        }
    }

    /**
     * Reset password-reset token
     * @param array $attributes
     * @return string
     */
    public function passwordSetAction($id, $attributes)
    {
        $password = $attributes['password'];
        $adminData = User::find($id);
        if (!Hash::check($attributes['password'], $attributes['confirm_password'])) {
            $adminData->update(['password' => Hash::make($password)]);
            return 'success';
        } else {
            return 'error';
        }
    }
}
