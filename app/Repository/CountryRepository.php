<?php

namespace App\Repository;

use App\Models\Countries;
use App\Models\State;

class CountryRepository
{
     /**
     * Get all countries
     * @return Countries list
     */
    public function findAllCountryList() {
        $countries = Countries::all()->where('is_active',1)->toArray();
        return $countries;
    }
}
