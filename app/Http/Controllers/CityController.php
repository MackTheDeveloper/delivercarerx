<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\City;
use App\Models\State;

class CityController extends Controller
{
    public function getState($country_id)
    {
        $states = State::where('country_id',$country_id)->get();
        foreach($states as $s)

        {
            echo '<option value="'.$s->id.'">'.$s->name.'</option>';
        }
        
    }

    public function getCity($state_id)
    {
        $cities = City::where('state_id',$state_id)->get();
        foreach($cities as $c)

        {
            echo '<option value="'.$c->id.'">'.$c->name.'</option>';
        }
    }
    public function getCityId($city_name)
    {
        $cities = DB::table('cities')->where('name',$city_name)->first();

        return $cities->id;
    }
}
