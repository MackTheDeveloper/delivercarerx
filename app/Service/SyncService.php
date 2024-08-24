<?php

namespace App\Service;

use App\Models\PatientAddresses;
use App\Models\Patients;
use App\Models\Pharmacy;
use App\Repository\AdminRepository;
use App\Repository\CommonRepository;
use App\Repository\PatientAddressesRepository;
use App\Repository\PatientRepository;

use App\Models\PrescriberAddresses;
use App\Models\Prescriber;
use App\Models\Rxs;
use App\Repository\PrescriberAddressesRepository;
use App\Repository\PrescriberRepository;
use App\Repository\RxsRepository;

use App\Models\Drugs;
use App\Repository\DrugsRepository;

use App\Models\Refill;
use App\Models\RefillAdjudcation;
use App\Models\RefillShipment;

use App\Repository\RefillsRepository;
use App\Repository\RefillAdjudicationsRepository;
use App\Repository\RefillShipmentRepository;

use Carbon\Carbon;

class SyncService
{

    protected $patientCommonRepo, $patientAddressCommonRepo, $patientRepo, $patientAddressRepo, $prescriberCommonRepo, $prescriberAddressCommonRepo, $prescriberRepo, $prescriberAddressRepo, $drugsRepo, $drugsCommonRepo,
        $refillCommonRepo, $refillAdjudicationsCommonRepo, $refillShipmentCommonRepo, $refillRepo, $refillAdjudicationsRepo, $refillShipmentRepo;

    /**
     * @param Patients $patient
     * @param Rxs $rxs;
     * @param PatientAddresses $patientAddress
     * @param PatientRepository $patientRepo
     * @param PatientAddressesRepository $patientRepo
     * @param RxsRepository $rxsRepo
     *
     * 
     * * @param Prescriber $patient
     * @param PrescriberAddresses $patientAddress
     * @param PrescriberRepository $patientRepo
     * @param PrescriberAddressesRepository $patientRepo
    
     */
    public function __construct(
        Patients $patient,
        Rxs $rxs,
        PatientAddresses $patientAddress,
        PatientRepository $patientRepo,
        PatientAddressesRepository $patientAddressRepo,
        Prescriber $prescriber,
        PrescriberAddresses $prescriberAddress,
        PrescriberRepository $prescriberRepo,
        PrescriberAddressesRepository $prescriberAddressRepo,
        RxsRepository $rxsRepo,
        Drugs $drugs,
        DrugsRepository $drugsRepo,
        Refill $refill,
        RefillAdjudcation $refillAdjudication,
        RefillShipment $refillShipment,
        RefillsRepository $refillRepo,
        RefillAdjudicationsRepository $refillAdjudicationsRepo,
        RefillShipmentRepository $refillShipmentRepo
    ) {
        $this->patientCommonRepo = new CommonRepository($patient);
        $this->patientAddressCommonRepo = new CommonRepository($patientAddress);
        $this->patientRepo = $patientRepo;
        $this->patientAddressRepo = $patientAddressRepo;

        $this->prescriberCommonRepo = new CommonRepository($prescriber);
        $this->prescriberAddressCommonRepo = new CommonRepository($prescriberAddress);
        $this->prescriberRepo = $prescriberRepo;
        $this->prescriberAddressRepo = $prescriberAddressRepo;

        $this->rxsCommonRepo = new CommonRepository($rxs);
        $this->rxsRepo = $rxsRepo;
        $this->drugsCommonRepo = new CommonRepository($drugs);
        $this->drugsRepo = $drugsRepo;

        $this->refillCommonRepo = new CommonRepository($refill);
        $this->refillAdjudicationCommonRepo = new CommonRepository($refillAdjudication);
        $this->refillShipmentCommonRepo = new CommonRepository($refillShipment);
        $this->refillRepo = $refillRepo;
        $this->refillAdjudicationsRepo = $refillAdjudicationsRepo;
        $this->refillShipmentRepo = $refillShipmentRepo;
    }

    public function syncPatient($pharmacy_id, $page = 1, $records = 1000, $nextUrl = null, $Token = null)
    {

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->where('id', $pharmacy_id)->first();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for Patients data sync process.";
            exit;
        }

        $newLeafEndpoint = $pharmacyData->newleaf_endpoint;
        $newLeafPort = $pharmacyData->newleaf_port;
        $newLeafUsername = $pharmacyData->newleaf_username;
        $newLeafPwd = $pharmacyData->newleaf_password;

        if (empty($nextUrl)) {
            if ($page == 1) {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = 0;
            } else {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = ($page - 1) * $records;
            }

            // Get the token
            $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);
        } else {
            // Get the token
            $token = $Token;
        }

        // $count - To get the count of the records (Boolean datatype)
        $count = 'true';

        // $filter - To get the filtered records (Boolean datatype)
        $filterSingle = "FirstName eq 'SANDRA'"; // Single condition
        $filterWithEnd = "FirstName eq 'CYNTHIA' and LastName eq 'CIRUTI'"; // With "and" condition

        // $select - To get the selected columns (String datatype)
        $select = "FirstName,LastName";
        $selectAll = 'CustomerId,CreatedBy,CreatedOn,UpdatedBy,UpdatedOn,FirstName,MiddleName,LastName,Gender,EmailAddress,FacilityId,DefaultDestinationType,IsActive,DateOfBirth';

        // $orderby - To sorts the results, asc/desc (String datatype)
        $orderby = "FirstName asc, LastName desc";

        // $expand - To get children nodes (String datatype)
        $expandSingleChildren = 'CustomerAddresses'; // Single child node
        $expandMultipleChildren = 'CustomerAddresses,Activities'; // Multiple child nodes

        //--- END Parameters --------

        // Set API Request parameters
        if (empty($nextUrl)) {
            $api_request_parameters = array(
                '$top' => $top,
                '$count' => $count,
                '$expand' => $expandSingleChildren,
                //'$filter' => $filterSingle,
                '$select' => $selectAll,
                //'$orderby' => $orderby,
                '$skip' => $skip,
            );
        }

        // Get Patient/Customer information
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => !empty($nextUrl) ? $nextUrl : $newLeafEndpoint . "/Customers?" . http_build_query($api_request_parameters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $token,
            ),
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //pre($response);
            $finalResponse = json_decode($response, true);

            // Get the count from the odata response
            // $countRecords = $finalResponse['@odata.count'];

            // next URL
            if (!empty($finalResponse['@odata.nextLink'])) {
                $nextURL = $finalResponse['@odata.nextLink'];
            }
        }

        if (!empty($finalResponse['value'])) {
            foreach ($finalResponse['value'] as $key => $value) {
                $patientData = array(); // Array for the patient data
                $patientAddressData = array(); // Array for the patient address data
                $otherFieldsForPatient = array(); // Array for storing the extra fields of patient information
                foreach ($value as $k => $v) {
                    if (isset(config('app.syncKeyValues')['patient'][$k])) { // Check the key exists for the parent patient in the configuration(config > app.php) file
                        if (is_array($value[$k]) && $k == 'CustomerAddresses') { // if child node exists, means patient has a relationship with the child node
                            foreach ($value[$k] as $addressKey => $addressValue) {
                                $otherFieldsForPatientAddress = array(); // Array for storing the extra fields of patient address information
                                foreach ($addressValue as $fKey => $fValue) {
                                    if (isset(config('app.syncKeyValues')['patient'][$k][$fKey])) { // Check the key exists for the child node of patient in the configuration(config > app.php) file
                                        if ($fKey == 'CreatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        } else if ($fKey == 'UpdatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        }
                                        $patientAddressData[$addressKey][config('app.syncKeyValues')['patient'][$k][$fKey]] = $fValue;
                                    } else { // Check if key does not exist then store extra fields in the $otherFieldsForPatientAddress array
                                        $otherFieldsForPatientAddress[$fKey] = $fValue;
                                    }
                                }
                                if (!empty($otherFieldsForPatientAddress)) { // Assign an array of other fields of the patient address to $patientAddressData
                                    //$patientAddressData[$addressKey]['other_fields'] = json_encode($otherFieldsForPatientAddress);
                                }
                            }
                        } else { // Create an array of patient information
                            // Specific logic for some fields
                            if ($k == "IsActive") {
                                if ($v) {
                                    $patientData[config('app.syncKeyValues')['patient'][$k]] = 1;
                                    $patientData[config('app.syncKeyValues')['patient']['is_active']] = 1;
                                } else {
                                    $patientData[config('app.syncKeyValues')['patient'][$k]] = 0;
                                    $patientData[config('app.syncKeyValues')['patient']['is_active']] = 0;
                                }
                            } elseif ($k == "DateOfBirth") {
                                if (!empty($v)) {
                                    $v = Carbon::parse($v)->toDateTimeString();
                                    $patientData[config('app.syncKeyValues')['patient'][$k]] = $v;
                                }
                            } else {
                                $patientData[config('app.syncKeyValues')['patient'][$k]] = $v;
                            }
                        }
                    } else { // Check if key does not exist then store extra fields in the $otherFieldsForPatient array
                        //$otherFieldsForPatient[$k] = $v;
                    }
                }

                if (!empty($otherFieldsForPatient)) { // Assign an array of other fields of the patient to $patientData
                    //$patientData['other_fields'] = json_encode($otherFieldsForPatient);
                }

                /* pre($patientData, 1);
            pre($patientAddressData, 1); */

                try {

                    // Save/Update patient information
                    if (isset($patientData['newleaf_customer_id'])) {
                        $newLeafCustomerId = $patientData['newleaf_customer_id'];
                        $newLeafCustomer = $this->patientRepo->checkRecordExistBySpecificField('newleaf_customer_id', $newLeafCustomerId, 'id');
                        if (!empty($newLeafCustomer)) {
                            $this->patientCommonRepo->update($patientData, $newLeafCustomer['returnValue']);
                        } else {
                            $this->patientCommonRepo->create($patientData);
                        }
                    } else {
                        $this->patientCommonRepo->create($patientData);
                    }

                    // Save/Update patient address information
                    $primaryAddress = array();
                    if (!empty($patientAddressData)) {
                        foreach ($patientAddressData as $addressKey => $addressValue) {
                            $newLeafCustomerAddressId = $addressValue['newleaf_customer_address_id'];
                            $newLeafCustomerAddress = $this->patientAddressRepo->checkRecordExistBySpecificField('newleaf_customer_address_id', $newLeafCustomerAddressId, 'id');
                            if (!empty($newLeafCustomerAddress)) {
                                $this->patientAddressCommonRepo->update($addressValue, $newLeafCustomerAddress['returnValue']);
                            } else {
                                $this->patientAddressCommonRepo->create($addressValue);
                            }

                            if ($addressValue['is_primary'] == 1) {
                                $primaryAddress = $addressValue;
                            }
                        }
                    }

                    // Update addresses in patient table
                    $updateAddressInPatient = self::createAnAddressArrayForPatient($primaryAddress);
                    $newLeafCustomerId = $primaryAddress['newleaf_customer_id'];
                    $newLeafCustomer = $this->patientRepo->checkRecordExistBySpecificField('newleaf_customer_id', $newLeafCustomerId, 'id');
                    $this->patientCommonRepo->update($updateAddressInPatient, $newLeafCustomer['returnValue']);
                } catch (\Exception $e) {
                }
            }
        }
        if (!empty($nextURL)) {
            $this->syncPatient($pharmacy_id, null, null, $nextURL, $token);
        } else {
            echo "Success";
            exit;
        }
    }

    public function syncPrescriber($pharmacy_id, $page = 1, $records = 1000, $nextUrl = null, $Token = null)
    {

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->where('id', $pharmacy_id)->first();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for Prescribers data sync process.";
            exit;
        }

        $newLeafEndpoint = $pharmacyData->newleaf_endpoint;
        $newLeafPort = $pharmacyData->newleaf_port;
        $newLeafUsername = $pharmacyData->newleaf_username;
        $newLeafPwd = $pharmacyData->newleaf_password;

        if (empty($nextUrl)) {
            if ($page == 1) {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = 0;
            } else {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = ($page - 1) * $records;
            }

            // Get the token
            $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);
        } else {
            // Get the token
            $token = $Token;
        }

        // $count - To get the count of the records (Boolean datatype)
        $count = 'true';

        // $filter - To get the filtered records (Boolean datatype)
        $filterSingle = "FirstName eq 'SANDRA'"; // Single condition
        $filterWithEnd = "FirstName eq 'CYNTHIA' and LastName eq 'CIRUTI'"; // With "and" condition

        // $select - To get the selected columns (String datatype)
        $select = "FirstName,LastName";
        $selectAll = '*';

        // $orderby - To sorts the results, asc/desc (String datatype)
        $orderby = "FirstName asc, LastName desc";

        // $expand - To get children nodes (String datatype)
        $expandSingleChildren = 'PrescriberAddresses'; // Single child node
        $expandMultipleChildren = 'PrescriberAddresses,Activities'; // Multiple child nodes

        //--- END Parameters --------

        // Set API Request parameters
        if (empty($nextUrl)) {
            $api_request_parameters = array(
                '$count' => $count,
                //'$filter' => $filterSingle,
                '$expand' => $expandSingleChildren,
                '$select' => $selectAll,
                //'$orderby' => $orderby,
                '$skip' => $skip,
                '$top' => $top,
            );
        }

        // Get Prescriber information
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => !empty($nextUrl) ? $nextUrl : $newLeafEndpoint . "/Prescribers?" . http_build_query($api_request_parameters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $token,
            ),
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            // pre($response);
            $finalResponse = json_decode($response, true);

            // Get the count from the odata response
            // $countRecords = $finalResponse['@odata.count'];

            // next URL
            if (!empty($finalResponse['@odata.nextLink'])) {
                $nextURL = $finalResponse['@odata.nextLink'];
            }
        }

        if (!empty($finalResponse['value'])) {
            foreach ($finalResponse['value'] as $key => $value) {
                $prescriberData = array(); // Array for the prescriber data
                $prescriberAddressData = array(); // Array for the prescriber address data
                $otherFieldsForPrescriber = array(); // Array for storing the extra fields of precriber information
                foreach ($value as $k => $v) {
                    if (isset(config('app.syncKeyValues')['prescriber'][$k])) { // Check the key exists for the parent prescriber in the configuration(config > app.php) file
                        if (is_array($value[$k]) && $k == 'PrescriberAddresses') { // if child node exists, means prescriber has a relationship with the child node
                            foreach ($value[$k] as $addressKey => $addressValue) {
                                $otherFieldsForPrescriber = array(); // Array for storing the extra fields of patient address information
                                foreach ($addressValue as $fKey => $fValue) {
                                    if (isset(config('app.syncKeyValues')['prescriber'][$k][$fKey])) { // Check the key exists for the child node of patient in the configuration(config > app.php) file
                                        if ($fKey == 'CreatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        } else if ($fKey == 'UpdatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        }
                                        $prescriberAddressData[$addressKey][config('app.syncKeyValues')['prescriber'][$k][$fKey]] = $fValue;
                                    } else { // Check if key does not exist then store extra fields in the $otherFieldsForPatientAddress array
                                        $otherFieldsForPrescriber[$fKey] = $fValue;
                                    }
                                }
                                if (!empty($otherFieldsForPrescriberAddress)) { // Assign an array of other fields of the patient address to $patientAddressData
                                    //$prescriberAddressData[$addressKey]['other_fields'] = json_encode($otherFieldsForPrescriberAddress);
                                }
                            }
                        } else { // Create an array of prescriber information
                            $prescriberData[config('app.syncKeyValues')['prescriber'][$k]] = $v;
                        }
                    } else { // Check if key does not exist then store extra fields in the $otherFieldsForPrescriber array
                        $otherFieldsForPrescriber[$k] = $v;
                    }
                }

                if (!empty($otherFieldsForPrescriber)) { // Assign an array of other fields of the prescriber to $prescriberData
                    //$prescriberData['other_fields'] = json_encode($otherFieldsForPrescriber);
                }

                // pre($prescriberData, 1);
                //pre($prescriberAddressData, 1); 
                //dd($prescriberData[0]);

                try {

                    // Save/Update prescriber information
                    if (isset($prescriberData['prescriber_id'])) {
                        $prescriberId = $prescriberData['prescriber_id'];
                        //dd($prescriberId);
                        $new_prescriber = $this->prescriberRepo->checkRecordExistBySpecificField('prescriber_id', $prescriberId, 'id');
                        //dd($new_prescriber);
                        if (!empty($new_prescriber)) {
                            $this->prescriberCommonRepo->update($prescriberData, $new_prescriber['returnValue']);
                        } else {

                            $this->prescriberCommonRepo->create($prescriberData);
                        }
                    } else {
                        $this->prescriberCommonRepo->create($prescriberData);
                    }

                    // Save/Update prescriber address information
                    $primaryAddress = array();
                    if (!empty($prescriberAddressData)) {
                        foreach ($prescriberAddressData as $addressKey => $addressValue) {
                            $prescriberAddressId = $addressValue['prescriber_address_id'];
                            //dd($prescriberAddressId);
                            $prescriberAddress = $this->prescriberAddressRepo->checkRecordExistBySpecificField('prescriber_address_id', $prescriberAddressId, 'id');
                            //dd($prescriberAddress);
                            if (!empty($prescriberAddress)) {

                                //dd($addressValue);
                                $this->prescriberAddressRepo->update($addressValue, $prescriberAddress['returnValue']);
                            } else {
                                $this->prescriberAddressCommonRepo->create($addressValue);
                            }

                            if ($addressValue['is_primary'] == 1) {
                                $primaryAddress = $addressValue;
                            }
                        }
                    }

                    // Update addresses in prescriber table
                    $updateAddressInPrescriber = self::createAnAddressArrayForPatient($primaryAddress);
                    $prescriberId = $primaryAddress['prescriber_id'];
                    $newPrescriber = $this->prescriberRepo->checkRecordExistBySpecificField('prescriber_id', $prescriberId, 'id');
                    $this->precriberCommonRepo->update($updateAddressInPrescriber, $newPrescriber['returnValue']);
                } catch (\Exception $e) {
                }
            }
        }

        if (!empty($nextURL)) {
            $this->syncPrescriber($pharmacy_id, null, null, $nextURL, $token);
        }

        echo "Success";
    }

    public function syncRxs($pharmacy_id, $page = 1, $records = 1000, $nextUrl = null, $Token = null)
    {

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->where('id', $pharmacy_id)->first();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for Rxs data sync process.";
            exit;
        }

        $newLeafEndpoint = $pharmacyData->newleaf_endpoint;
        $newLeafPort = $pharmacyData->newleaf_port;
        $newLeafUsername = $pharmacyData->newleaf_username;
        $newLeafPwd = $pharmacyData->newleaf_password;

        // Get the token
        if (empty($nextUrl)) {
            if ($page == 1) {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = 0;
            } else {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = ($page - 1) * $records;
            }

            // Get the token
            $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);
        } else {
            // Get the token
            $token = $Token;
        }

        // $count - To get the count of the records (Boolean datatype)
        $count = 'true';

        // $filter - To get the filtered records (Boolean datatype)
        $filterSingle = "FirstName eq 'SANDRA'"; // Single condition
        $filterWithEnd = "FirstName eq 'CYNTHIA' and LastName eq 'CIRUTI'"; // With "and" condition

        // $select - To get the selected columns (String datatype)
        $select = "RxId,CreatedBy,CreatedOn,UpdatedBy,UpdatedOn,RxNumber,DAWCode,Origin,Status,CustomerId,PrescriberId,PrescribedDrugId,OriginalQuantity,OwedQuantity,RefillsAuthorized,RefillsRemaining,DateWritten,DateExpires,DateInactivated,OriginalSIG,OriginalSIGExpanded,OriginalDaysSupply,IsVerified,VerifiedQuantityDispensed,VerifiedMinDaysSupply,IsCancelled";
        $selectAll = '*';

        // $orderby - To sorts the results, asc/desc (String datatype)
        $orderby = "FirstName asc, LastName desc";

        // $expand - To get children nodes (String datatype)
        $expandSingleChildren = 'PrescriberAddresses'; // Single child node
        $expandMultipleChildren = 'PrescriberAddresses,Activities'; // Multiple child nodes

        //--- END Parameters --------

        // Set API Request parameters
        if (empty($nextUrl)) {
            $api_request_parameters = array(
                '$top' => $top,
                '$count' => $count,
                // '$expand' => $expandSingleChildren,
                //'$filter' => $filterSingle,
                '$select' => $select,
                //'$orderby' => $orderby,
                '$skip' => $skip,
            );
        }
        // Get Prescriber information
        $curl = curl_init();
        curl_setopt_array($curl, array(
            //CURLOPT_URL => "http://10.160.31.83:8084/Customers?%24top=$top&%24count=$count&%24expand=$expandSingleChildren",
            CURLOPT_URL => !empty($nextUrl) ? $nextUrl : $newLeafEndpoint . "/Rx?" . http_build_query($api_request_parameters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $token,
            ),
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //pre($response);
            $finalResponse = json_decode($response, true);

            // Get the count from the odata response
            //$countRecords = $finalResponse['@odata.count'];

            // next URL
            if (!empty($finalResponse['@odata.nextLink'])) {
                $nextURL = $finalResponse['@odata.nextLink'];
            }
        }
        foreach ($finalResponse['value'] as $key => $value) {
            $rxData = array();
            foreach ($value as $k => $v) {
                if (isset(config('app.syncKeyValues')['rxs'][$k])) {
                    $rxData[config('app.syncKeyValues')['rxs'][$k]] = $v;
                } else {
                    $otherFields[$k] = $v;
                }
            }
            if (!empty($otherFieldsForPrescriber)) { // Assign an array of other fields of the prescriber to $prescriberData
                $rxData['other_fields'] = json_encode($otherFields);
            }
            try {
                // Save/Update Rx information
                if (isset($rxData['rx_id'])) {
                    $newRxId = $rxData['rx_id'];
                    $newRx = $this->rxsRepo->checkRecordExistBySpecificField('rx_id', $newRxId, 'id');
                    if (!empty($newRx)) {
                        $this->rxsCommonRepo->update($rxData, $newRx['returnValue']);
                    } else {
                        $this->rxsCommonRepo->create($rxData);
                    }
                } else {
                    $this->rxsCommonRepo->create($rxData);
                }
            } catch (\Exception $e) {
                dd($e);
            }
        }
        if (!empty($nextURL)) {
            $this->syncRxs($pharmacy_id, null, null, $nextURL, $token);
        }
        echo "Success";
    }

    public static function getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd, $returnArray = false)
    {
        $curl = curl_init();
        $loginCredentials = json_encode([
            "Username" => $newLeafUsername,
            "Password" => $newLeafPwd
        ]);
        curl_setopt_array($curl, array(
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_URL => $newLeafEndpoint . "/api/Login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $loginCredentials,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: c22c76f8-dabd-48a4-0379-578cc9c51702"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            if($returnArray)
            {
                return ['status' => false, 'message' => $err];
            }
            else
            {
                return false;
            }
            
        } else {
            return $response;
        }
    }

    public static function createAnAddressArrayForPatient($primaryAddress)
    {
        $updateAddressInPatient['address_1'] = $primaryAddress['address_1'];
        $updateAddressInPatient['address_2'] = $primaryAddress['address_2'];
        $updateAddressInPatient['country'] = $primaryAddress['country'];
        $updateAddressInPatient['state'] = $primaryAddress['state'];
        $updateAddressInPatient['city'] = $primaryAddress['city'];
        $updateAddressInPatient['zipcode'] = $primaryAddress['zipcode'];
        return $updateAddressInPatient;
    }

    public function syncDrugs($pharmacy_id, $page = 1, $records = 1000, $nextUrl = null, $Token = null)
    {

        //--- START Parameters --------

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->where('id', $pharmacy_id)->first();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for Drugs data sync process.";
            exit;
        }

        $newLeafEndpoint = $pharmacyData->newleaf_endpoint;
        $newLeafPort = $pharmacyData->newleaf_port;
        $newLeafUsername = $pharmacyData->newleaf_username;
        $newLeafPwd = $pharmacyData->newleaf_password;

        if (empty($nextUrl)) {
            if ($page == 1) {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = 0;
            } else {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;

                // $skip - Skips the first n results (Number datatype)
                $skip = ($page - 1) * $records;
            }

            // Get the token
            $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);
        } else {
            // Get the token
            $token = $Token;
        }


        // $count - To get the count of the records (Boolean datatype)
        $count = 'true';

        // $select - To get the selected columns (String datatype)
        $select = "DrugId,CreatedBy,CreatedOn,UpdatedBy,UpdatedOn,Identifier,Description,Strength,NewNDC,ManufacturerName,IsGeneric,IsRx,StatusCode,DosageFormCode,DirectSource,MasterDescription";
        $selectAll = '*';

        //--- END Parameters --------

        // Set API Request parameters
        if (empty($nextUrl)) {
            $api_request_parameters = array(
                '$count' => $count,
                //'$filter' => $filterSingle,
                '$select' => $select,
                //'$orderby' => $orderby,
                '$skip' => $skip,
                '$top' => $top,
            );
        }

        // Get drugs information
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => !empty($nextUrl) ? $nextUrl : $newLeafEndpoint . "/drugs?" . http_build_query($api_request_parameters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $token,
            ),
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //pre($response);
            $finalResponse = json_decode($response, true);

            // Get the count from the odata response
            //$countRecords = $finalResponse['@odata.count'];

            // next URL
            if (!empty($finalResponse['@odata.nextLink'])) {
                $nextURL = $finalResponse['@odata.nextLink'];
            }
        }

        if (!empty($finalResponse['value'])) {
            foreach ($finalResponse['value'] as $key => $value) {
                $drugsData = array(); // Array for the drugs data
                foreach ($value as $k => $v) {
                    if (isset(config('app.syncKeyValues')['drugs'][$k])) { // Check the key exists for the drug in the configuration(config > app.php) file
                        $drugsData[config('app.syncKeyValues')['drugs'][$k]] = $v;

                        if ($k == "DosageFormCode") {
                            $drugsData['dosage_form'] = !empty(config('app.doses_form')[$v]) ? config('app.doses_form')[$v] : "";
                        }

                        if ($k == "DirectSource") {
                            $drugsData['direct_source_description'] = !empty(config('app.drug_direct_source')[$v]) ? config('app.drug_direct_source')[$v] : "";
                        }
                    }
                }

                try {
                    // Save/Update drug information
                    if (isset($drugsData['newleaf_drug_id'])) {
                        $newLeafDrugId = $drugsData['newleaf_drug_id'];
                        $newLeafDrug = $this->drugsRepo->checkRecordExistBySpecificField('newleaf_drug_id', $newLeafDrugId, 'id');
                        if (!empty($newLeafDrug)) {
                            $this->drugsCommonRepo->update($drugsData, $newLeafDrug['returnValue']);
                        } else {
                            $this->drugsCommonRepo->create($drugsData);
                        }
                    } else {
                        $this->drugsCommonRepo->create($drugsData);
                    }
                } catch (\Exception $e) {
                    dd($e);
                }
            }
        }

        if (!empty($nextURL)) {
            $this->syncDrugs($pharmacy_id, null, null, $nextURL, $token);
        } else {
            echo "Success";
            exit;
        }
    }


    public function syncRefills($pharmacy_id, $page = 1, $records = 1000, $nextUrl = null, $Token = null)
    {

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->where('id', $pharmacy_id)->first();
        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for Refills data sync process.";
            exit;
        }
        $newLeafEndpoint = $pharmacyData->newleaf_endpoint;
        $newLeafPort = $pharmacyData->newleaf_port;
        $newLeafUsername = $pharmacyData->newleaf_username;
        $newLeafPwd = $pharmacyData->newleaf_password;
        //--- START Parameters --------
        if (empty($nextUrl)) {
            if ($page == 1) {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;
                // $skip - Skips the first n results (Number datatype)
                $skip = 0;
            } else {
                // $top - To get the specific number of records (Number datatype)
                $top = $records;
                // $skip - Skips the first n results (Number datatype)
                $skip = ($page - 1) * $records;
            }
            // Get the token
            $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);
        } else {
            // Get the token
            $token = $Token;
        }

        // $count - To get the count of the records (Boolean datatype)
        $count = 'true';
        // $select - To get the selected columns (String datatype)
        // $select = "refill_id,CreatedBy,CreatedOn,UpdatedBy,UpdatedOn,refill_number,rx_id,drug_id,destination_type_id,destination_date,customer_address_id,facility_address_id,status,package_choice,date_filled,sig,sig_expanded,destination_notes,dispensed_quantity,days_supply,min_days_supply,max_days_supply,number_of_pieces,rph_user_name,
        // rph_user_id,is_ordered,is_dispensed,is_prefill,discard_after_date,workflow_status,number_of_labels,doses_per_day,units_per_dose,destination_address1,destination_address2,destination_city,destination_state,destination_zip,effective_date,prescriber_address_id";

        $select = "RefillId,CreatedBy,CreatedOn,UpdatedBy,UpdatedOn,RefillNumber,RxId,DrugId,DestinationTypeId,DestinationDate,CustomerAddressId,FacilityAddressId,Status,PackageChoice,DateFilled,SIG,SIGExpanded,DestinationNotes,DispensedQuantity,DaysSupply,MinDaysSupply,MaxDaysSupply,NumberOfPieces,RPHUserName,
        RPHUserId,IsOrdered,IsDispensed,IsPrefill,DiscardAfterDate,WorkflowStatus,NumberOfLabels,DosesPerDay,UnitsPerDose,DestinationAddress1,DestinationAddress2,DestinationCity,DestinationState,DestinationZip,EffectiveDate,PrescriberAddressId";
        $selectAll = '*';
        //--- END Parameters --------
        // $expand - To get children nodes (String datatype)
        $expandSingleChildren = 'RefillAdjudications'; // Single child node
        $expandMultipleChildren = 'RefillAdjudications,RefillShipments'; // Multiple child nodes
        // Set API Request parameters
        if (empty($nextUrl)) {
            $api_request_parameters = array(
                '$count' => $count,
                //'$filter' => $filterSingle,
                '$expand' => $expandMultipleChildren,
                '$select' => $select,
                //'$orderby' => $orderby,
                '$skip' => $skip,
                '$top' => $top,
            );
        }

        // Get drugs information
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => !empty($nextUrl) ? $nextUrl : $newLeafEndpoint . "/refills?" . http_build_query($api_request_parameters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $token,
            ),
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {

            $finalResponse = json_decode($response, true);
            // next URL
            if (!empty($finalResponse['@odata.nextLink'])) {
                $nextURL = $finalResponse['@odata.nextLink'];
            }
        }
        if (!empty($finalResponse['value'])) {   //dd($finalResponse['value']);
            foreach ($finalResponse['value'] as $key => $value) {
                $refillData = array(); // Array for the refill data
                $refillAdjudicationsData = array(); // Array for the refill adjudication data
                $refillShipmentsData = array(); // Array for refill shipment information
                foreach ($value as $k => $v) {
                    if (isset(config('app.syncKeyValues')['refills'][$k])) {  // Check the key exists for the parent prescriber in the configuration(config > app.php) file

                        if (is_array($value[$k])  && $k == 'RefillAdjudications') { // if child node exists, means prescriber has a relationship with the child node
                            foreach ($value[$k] as $adjucationKey => $adjucationValue) {

                                foreach ($adjucationValue as $fKey => $fValue) {
                                    if (isset(config('app.syncKeyValues')['refills'][$k][$fKey])) { // Check the key exists for the child node of patient in the configuration(config > app.php) file
                                        if ($fKey == 'CreatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        } else if ($fKey == 'UpdatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        }
                                        $refillAdjudicationsData[$adjucationKey][config('app.syncKeyValues')['refills'][$k][$fKey]] = $fValue;
                                    }
                                }
                            }
                        } elseif (is_array($value[$k]) && $k == 'RefillShipments') { // if child node exists, means prescriber has a relationship with the child node
                            foreach ($value[$k] as $shipmentKey => $shipmentValue) {
                                foreach ($shipmentValue as $fKey => $fValue) {
                                    if (isset(config('app.syncKeyValues')['refills'][$k][$fKey])) { // Check the key exists for the child node of patient in the configuration(config > app.php) file
                                        if ($fKey == 'CreatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        } else if ($fKey == 'UpdatedOn' && !empty($fValue)) { // Check the key/value and customize the data and set the value accordingly
                                            $fValue = Carbon::parse($fValue)->toDateTimeString();
                                        }
                                        $refillShipmentsData[$shipmentKey][config('app.syncKeyValues')['refills'][$k][$fKey]] = $fValue;
                                    }
                                }
                            }
                        } else {
                            // Create an array of refills information
                            $refillData[config('app.syncKeyValues')['refills'][$k]] = $v;
                        }
                    }
                }
                try {
                    // Save/Update refillData information
                    if (isset($refillData['refill_id'])) {
                        $refillId = $refillData['refill_id'];
                        $new_refill = $this->refillRepo->checkRecordExistBySpecificField('refill_id', $refillId, 'id');

                        if (!empty($new_refill)) {
                            $this->refillRepo->update($refillData, $new_refill['returnValue']);
                        } else {

                            $this->refillRepo->create($refillData);
                        }
                    } else {
                        $this->refillRepo->create($refillData);
                    }
                    //Save/Update refill adjucant information
                    $refillAdjudications = array();
                    if (!empty($refillAdjudicationsData)) {
                        foreach ($refillAdjudicationsData as $adjucationKey => $adjucationValue) {
                            $refillAdjucationId = $adjucationValue['refill_adjudication_id'];
                            //dd($refillAdjucationId);
                            $refillAdjudications = $this->refillAdjudicationsRepo->checkRecordExistBySpecificField('refill_adjudication_id', $refillAdjucationId, 'id');
                            //dd($refillAdjudications);
                            if (!empty($refillAdjudications)) {
                                //dd($addressValue);
                                $this->refillAdjudicationsRepo->update($adjucationValue, $refillAdjudications['returnValue']);
                            } else {
                                $this->refillAdjudicationsRepo->create($adjucationValue);
                            }
                        }
                    }
                    // Save/Update refill shipment information
                    $refillShipments = array();
                    if (!empty($refillShipmentsData)) {
                        foreach ($refillShipmentsData as $shipmentKey => $shipmentValue) {
                            $refillShipmentId = $shipmentValue['refill_shipment_id'];
                            //dd($refillShipmentId);
                            $refillShipments = $this->refillShipmentRepo->checkRecordExistBySpecificField('refill_shipment_id', $refillShipmentId, 'id');
                            //dd($refillShipmentId);
                            if (!empty($refillShipments)) {
                                //dd($addressValue);
                                $this->refillShipmentRepo->update($shipmentValue, $refillShipments['returnValue']);
                            } else {
                                $this->refillShipmentRepo->create($shipmentValue);
                            }
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }
        if (!empty($nextURL)) {
            $this->syncRefills($pharmacy_id, null, null, $nextURL, $token);
        } else {
            echo "Success";
            exit;
        }
    }


    public function syncMaster($slug)
    {
        // $count - To get the count of the records (Boolean datatype)
        $count = 'true';

        $selectAll = '*';

        $api_request_parameters = array(
            '$top' => 1,
            '$count' => $count,
            '$select' => $selectAll
        );

        // Set URL based on slug
        if ($slug == "rxs") {
            $newLeafURL = "/Rx?";
            $dataURL = "rxs";
        } elseif ($slug == "drugs") {
            $newLeafURL = "/Drugs?";
            $dataURL = "drugs";
        } elseif ($slug == "prescriber") {
            $newLeafURL = "/Prescribers?";
            $dataURL = "prescriber";
        } elseif ($slug == "patient") {
            $newLeafURL = "/Customers?";
            $dataURL = "patient";
        } elseif ($slug == "refills") {
            $newLeafURL = "/refills?";
            $dataURL = "refills";
        }

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for " . $slug . " data sync process.";
            exit;
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $newLeafEndpoint = $pharmacy['newleaf_endpoint'];
            $newLeafPort = $pharmacy['newleaf_port'];
            $newLeafUsername = $pharmacy['newleaf_username'];
            $newLeafPwd = $pharmacy['newleaf_password'];
            $pharmacy_id = $pharmacy['id'];

            // Get the token
            $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);

            // Get Prescriber information
            $curl = curl_init();
            curl_setopt_array($curl, array(
                //CURLOPT_URL => "http://10.160.31.83:8084/Customers?%24top=$top&%24count=$count&%24expand=$expandSingleChildren",
                CURLOPT_URL => $newLeafEndpoint . $newLeafURL . http_build_query($api_request_parameters),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    "authorization: Bearer " . $token,
                ),
                CURLOPT_PORT => $newLeafPort,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $finalResponse = json_decode($response, true);

                // Get the count from the odata response
                $countRecords = $finalResponse['@odata.count'];

                if (!empty($countRecords) &&  $countRecords > 0) {
                    // number of threads
                    $threads = ceil($countRecords / config('app.newleaf_data_sync_per_thread'));

                    // executing each thread with pagination
                    for ($i = 1; $i <= $threads; $i++) {

                        // base url
                        $URL = url("/sync-data") . "/" . $dataURL . "/" . $pharmacy_id . "/" . $i . "/" . config('app.newleaf_data_sync_per_thread');


                        //$URL = "http://beta-delivercare.magnetoinfotech.com/sync-data/" .$dataURL . "/" . $i . "/" . config('app.newleaf_data_sync_per_thread');

                        echo $URL . "<br /><br />";

                        shell_exec("curl $URL");
                    }
                }
            }
        }
    }

    public function fetchNewLeafData($pharmacy_id, $endpoint, $api_request_parameters = array())
    {

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->where('id', $pharmacy_id)->first();

        if (empty($pharmacyData)) {
            echo "NewLeaf endpoint not defined for this pharmacy.";
            exit;
        }

        $newLeafEndpoint = $pharmacyData->newleaf_endpoint;
        $newLeafPort = $pharmacyData->newleaf_port;
        $newLeafUsername = $pharmacyData->newleaf_username;
        $newLeafPwd = $pharmacyData->newleaf_password;

        // Get the token
        $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);

        // If newleaf down
        if (!$token) {
            return false;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $newLeafEndpoint . $endpoint . http_build_query($api_request_parameters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $token,
            ),
            CURLOPT_PORT => $newLeafPort,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        } else {
            $finalResponse = json_decode($response, true);

            return $finalResponse;
        }
    }
}
