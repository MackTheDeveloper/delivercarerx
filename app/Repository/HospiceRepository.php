<?php

namespace App\Repository;

use App\Models\EmailTemplates;
use App\Models\Hospice;
use App\Models\Facilities;
use App\Models\User;
use Auth;

class HospiceRepository
{
    /**
     * Store hospice information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        return Hospice::create($data);
    }

    /**
     * Update hospice information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return Hospice::where('id', $id)->update($data);
    }

    public function createUpdate($data)
    {   //dd($data['email']);
        return Hospice::updateOrCreate(['code' => $data['code']],
            [
                'name' => $data['name'],
                'address_1' => $data['address_1'],
                'address_2' => $data['address_2'],
                'state_id' => $data['state_id'],
                'city_id' => $data['city_id'],
                'zipcode' => $data['zipcode'],
                'email' => $data['email'],
                'created_by' => Auth::user()->id
            ]);
    }

    /**
     * Fetch hospice information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return Hospice::where('id', $id)->first();
    }

    public function findAllHospiceList()
    {
        if (Auth::user()->user_type == 2) {
            $hospice = Hospice::where('id', Auth::user()->hospice_id)->where('is_active', 1)->get()->toArray();
        } else {
            $hospice = Hospice::where('is_active', 1)->get()->toArray();
        }


        return $hospice;
    }

    /**
     * Get hospice facilities from hospiceId
     * @param $hospiceId
     * @return array
     */
    public function getHospiceFacilities($hospiceId)
    {
        return Facilities::where('hospice_id', $hospiceId)->get()->toArray();
    }

    /**
     * delete hospice
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return Hospice::where('id', $id)->delete();
    }

    public function getListNameAndCode()
    {
        return Hospice::select('name', 'code', 'id')->get();
    }

    public function getBranchName($id)
    {
        $data = Hospice::select('name')->where('id', $id)->first();
        return $data->name ?? '';
    }

    /**
     * Convert an object to an array
     */
    public function getHospiceByIdZeroRepo()
    {
        // fetch all hospice records having user_id = 0 and deleted_at IS NULL
        $model = Hospice::where('user_id', 0)->whereNull('deleted_at')->get();
        $userEmails = User::all()->pluck('email')->toArray();

        foreach ($model as $key => $value) {

            //  check if user exists with hospice.email
            $emailExist = in_array($value["email"], $userEmails);
            if (!$emailExist) {
                $user = new User();
                $user->user_type = 2;
                $user->hospice_user_role = 1;
                $user->hospice_id = $value["id"];
                $user->first_name = $value["name"] ?? "";
                $user->last_name = $value["name"] ?? "";
                $user->name = $value["name"] ?? "";
                $user->email = $value["email"] ?? "";
                $user->is_active = 1;
                $user->save();

                // update hospice.user_id = user.id (which is new created after user insert)
                Hospice::where('id', $value["id"])->update(['user_id' => $user->id]);

                // send email to hospice email for set password
                $encryptId = encrypt($user->id);
                $link = route('show-set-password', $encryptId);
                $data = ['NAME' => $value["name"], 'EMAIL' => $value["email"], 'LINK' => $link];
                EmailTemplatesRepository::sendMail('set-password', $data);
            }
        }
    }
}
