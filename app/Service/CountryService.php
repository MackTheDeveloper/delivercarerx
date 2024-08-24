<?php

namespace App\Service;

use App\Repository\AdminRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;

class CountryService
{

    protected $countryRepo;

    /**
     * @param CountryRepository $countryRepo reference to countryRepo
     * 
     */
    public function __construct(CountryRepository $countryRepo)
    {
        $this->countryRepo = $countryRepo;
    }

      /** 
     * Get state list
    */
    public function getCountryList(){
        return $this->countryRepo->findAllCountryList();
    }
}
