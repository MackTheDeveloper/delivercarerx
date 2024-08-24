<?php

namespace App\Repository;

use App\Models\PasswordReset;
use App\Models\User;
use Mail;
use App\Models\Partners;
use Illuminate\Support\Facades\Hash;

class PartnerRepository
{
    /**
     * Send email
     * @return bool
     */
    public function create($input)
    {
        $model = new Partners();
        $model->name = $input['name'];
        $model->address = $input['address'];
        $model->city = $input['city'];
        $model->state = $input['state'];
        $model->zipcode = $input['zipcode'];
        $model->username = $input['username'];
        $model->password = Hash::make($input['password']);
        $model->status = $input['status'];
        $model->save();
        return $model;
    }

    public function fetch($id)
    {
        return Partners::where('id', $id)->first();
    }

    public function update($data, $id = null)
    {
        return Partners::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Partners::where('id', $id)->delete();
    }
}
