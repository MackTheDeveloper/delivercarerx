<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplates;
use App\Models\Patients;
use App\Models\Refill;
use App\Models\RefillOrder;
use App\Models\RefillOrderItems;
use App\Models\RefillShipment;
use App\Service\SyncService;
use DB;
use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Carbon\Carbon;
use App\Models\Pharmacy;
use App\Repository\EmailTemplatesRepository;
use File;
use Exception;


class SyncController extends Controller
{

    protected $syncService;

    protected $orderController;

    /**
     * constructor for initialize Sync service
     *
     * @param SyncService $syncService reference to SyncService
     *
     */
    public function __construct(SyncService $syncService,OrdersController $orderController)
    {
        $this->syncService = $syncService;
        $this->orderController = $orderController;
    }

    public function sync($slug = '', $pharmacy_id, $page = 1, $records = 1000)
    {
        if (env('DB_HOST') != '192.168.51.10' && env('DB_DATABASE') != 'beta_delivercare') {
            // echo "Wrong database connection";
            // exit;
        }

        if ($slug == 'patient') {
            $this->syncService->syncPatient($pharmacy_id, $page, $records);
        }

        if ($slug == 'prescriber') {
            $this->syncService->syncPrescriber($pharmacy_id, $page, $records);
        }

        if ($slug == 'rxs') {
            $this->syncService->syncRxs($pharmacy_id, $page, $records);
        }

        if ($slug == 'drugs') {
            $this->syncService->syncDrugs($pharmacy_id, $page, $records);
        }

        if ($slug == 'refills') {
            $this->syncService->syncRefills($pharmacy_id, $page, $records);
        }
    }

    public function syncMaster($slug = '')
    {
        if (env('DB_HOST') != '192.168.51.10' && env('DB_DATABASE') != 'beta_delivercare') {
            // echo "Wrong database connection";
            // exit;
        }

        $this->syncService->syncMaster($slug);
    }
    public function syncRxsImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_rx_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName;
            $fileDir .= '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('rxs')->truncate();
        }
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_rx_$p_id";
            // $fileName = "export_rx";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName;
            $fileDir .= '.csv';
            if (file_exists($fileDir)) {
                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('rxs')
                    ->columns(['rx_id', 'rx_number', DB::raw("@var1"), 'created_by', 'updated_on', 'updated_by', 'status', 'origin', 'daw_code', 'customer_id', 'prescriber_id', 'prescribed_drug_id', 'original_quantity', 'owed_quantity', 'refills_authorized', 'refills_remaining', 'date_written', 'date_expires', 'date_inactivated', 'original_sig', 'original_sig_expanded', 'original_days_supply', 'is_verified', 'verified_quantity_dispensed', 'verified_min_days_supply', 'Is_cancelled'])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }
        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }
    public function syncRxsImportChild()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_rx_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName;
            $fileDir .= '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_rx_$p_id";
            // $fileName = "export_rx";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName;
            $fileDir .= '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from rxs_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('rxs_child')
                    ->columns(['rx_id', 'rx_number', DB::raw("@var1"), 'created_by', 'updated_on', 'updated_by', 'status', 'origin', 'daw_code', 'customer_id', 'prescriber_id', 'prescribed_drug_id', 'original_quantity', 'owed_quantity', 'refills_authorized', 'refills_remaining', 'date_written', 'date_expires', 'date_inactivated', 'original_sig', 'original_sig_expanded', 'original_days_supply', 'is_verified', 'verified_quantity_dispensed', 'verified_min_days_supply', 'Is_cancelled'])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();

                // Update existing pharmacy data
                DB::statement("UPDATE rxs r, rxs_child rc SET r.customer_id = rc.customer_id,r.created_on = rc.created_on,r.created_by = rc.created_by,r.updated_on = rc.updated_on,r.updated_by = rc.updated_by,r.status = rc.status,r.origin = rc.origin,r.daw_code = rc.daw_code,r.prescriber_id = rc.prescriber_id,r.prescribed_drug_id = rc.prescribed_drug_id,r.original_quantity = rc.original_quantity,r.owed_quantity = rc.owed_quantity,r.refills_authorized = rc.refills_authorized,r.refills_remaining = rc.refills_remaining,r.date_written = rc.date_written,r.date_expires = rc.date_expires,r.date_inactivated = rc.date_inactivated,r.original_sig = rc.original_sig,r.original_sig_expanded = rc.original_sig_expanded,r.original_days_supply = rc.original_days_supply,r.is_verified = rc.is_verified,r.verified_quantity_dispensed = rc.verified_quantity_dispensed,r.verified_min_days_supply = rc.verified_min_days_supply,r.is_cancelled = rc.is_cancelled,r.rx_id = rc.rx_id,r.rx_number = rc.rx_number,r.created_at = rc.created_at,r.pharmacy_id = rc.pharmacy_id WHERE r.rx_id = rc.rx_id AND rc.pharmacy_id = '" . $p_id . "'");

                // Update flag update = 1 for existing processing data
                DB::statement("UPDATE rxs r, rxs_child rc SET rc.flag_update = '1' WHERE r.rx_id = rc.rx_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                // Insert new data for this pharmacy
                DB::statement("INSERT INTO rxs (rx_id, rx_number, customer_id, created_on, created_by, updated_on, updated_by, status, origin, daw_code,prescriber_id, prescribed_drug_id, original_quantity, owed_quantity, refills_authorized,refills_remaining, date_written, date_expires, date_inactivated, original_sig, original_sig_expanded,original_days_supply, is_verified, verified_quantity_dispensed, verified_min_days_supply, is_cancelled,created_at,pharmacy_id)
            SELECT rx_id, rx_number, customer_id, created_on, created_by, updated_on, updated_by, status, origin, daw_code,prescriber_id, prescribed_drug_id, original_quantity, owed_quantity, refills_authorized,refills_remaining, date_written, date_expires, date_inactivated, original_sig, original_sig_expanded,original_days_supply, is_verified, verified_quantity_dispensed, verified_min_days_supply, is_cancelled, created_at,pharmacy_id FROM rxs_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                // Update flag update = 2 for existing processing data
                DB::statement("UPDATE rxs r, rxs_child rc SET rc.flag_update = '2' WHERE r.rx_id = rc.rx_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");


                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    $flagSuccess = 1;

                } else {
                    $flagSuccess = 0;
                }
            }
        }

        if ($flagSuccess) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }
    public function syncRefillsImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_refill_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('refills')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_refill_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('refills')
                    ->columns(['refill_id', 'created_by', 'created_on', 'updated_by', 'updated_on', 'refill_number', 'rx_id', 'drug_id', DB::raw("@var1"), 'destination_type_id', 'destination_date', 'customer_address_id', 'status', 'facility_address_id', 'package_choice', 'date_filled', 'sig', 'sig_expanded', 'destination_notes', 'dispensed_quantity', 'days_supply', 'min_days_supply', 'max_days_supply', 'number_of_pieces', 'rph_user_name', 'rph_user_id', 'is_ordered', 'is_dispensed', 'is_prefill', 'discard_after_date', 'workflow_status', 'number_of_labels', 'doses_per_day', 'units_per_dose', 'destination_address1', 'destination_address2', 'destination_city', 'destination_state', 'destination_zip', 'effective_date', 'prescriber_address_id'])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }
        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }
    public function syncRefillsImportChild()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_refill_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_refill_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from refills_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('refills_child')
                    ->columns(['refill_id', 'created_by', 'created_on', 'updated_by', 'updated_on', 'refill_number', 'rx_id', 'drug_id', DB::raw("@var1"), 'destination_type_id', 'destination_date', 'customer_address_id', 'status', 'facility_address_id', 'package_choice', 'date_filled', 'sig', 'sig_expanded', 'destination_notes', 'dispensed_quantity', 'days_supply', 'min_days_supply', 'max_days_supply', 'number_of_pieces', 'rph_user_name', 'rph_user_id', 'is_ordered', 'is_dispensed', 'is_prefill', 'discard_after_date', 'workflow_status', 'number_of_labels', 'doses_per_day', 'units_per_dose', 'destination_address1', 'destination_address2', 'destination_city', 'destination_state', 'destination_zip', 'effective_date', 'prescriber_address_id'])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();

                // Update existing pharmacy data
                DB::statement("UPDATE refills r, refills_child rc SET r.refill_id = rc.refill_id,r.refill_number = rc.refill_number,r.rx_id = rc.rx_id,r.drug_id = rc.drug_id,r.destination_type_id = rc.destination_type_id,r.destination_date = rc.destination_date,r.customer_address_id = rc.customer_address_id,r.facility_address_id = rc.facility_address_id,r.status = rc.status,r.package_choice = rc.package_choice,r.date_filled = rc.date_filled,r.sig = rc.sig,r.sig_expanded = rc.sig_expanded,r.destination_notes = rc.destination_notes,r.dispensed_quantity = rc.dispensed_quantity,r.days_supply = rc.days_supply,r.min_days_supply = rc.min_days_supply,r.max_days_supply = rc.max_days_supply,r.number_of_pieces = rc.number_of_pieces,r.rph_user_name = rc.rph_user_name,r.rph_user_id = rc.rph_user_id,r.is_ordered = rc.is_ordered,r.is_dispensed = rc.is_dispensed,r.is_prefill = rc.is_prefill,r.discard_after_date = rc.discard_after_date,r.workflow_status = rc.workflow_status,r.number_of_labels = rc.number_of_labels,r.doses_per_day = rc.doses_per_day,r.units_per_dose = rc.units_per_dose,r.destination_address1 = rc.destination_address1,r.destination_address2 = rc.destination_address2,r.destination_city = rc.destination_city,r.destination_state = rc.destination_state,r.destination_zip = rc.destination_zip,r.effective_date = rc.effective_date,r.prescriber_address_id = rc.prescriber_address_id,r.updated_on = rc.updated_on,r.updated_by = rc.updated_by,r.created_on = rc.created_on,r.created_by = rc.created_by,r.created_at = rc.created_at,r.deleted_at = rc.deleted_at,r.pharmacy_id = rc.pharmacy_id WHERE r.refill_id = rc.refill_id AND rc.pharmacy_id = '" . $p_id . "'");

                // Update flag update = 1 for existing processing data
                DB::statement("UPDATE refills r, refills_child rc SET rc.flag_update = '1' WHERE r.refill_id = rc.refill_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                // Insert new data for this pharmacy
                DB::statement("INSERT INTO refills (refill_id,refill_number,created_on,created_by,updated_on,updated_by,rx_id,drug_id,destination_type_id,destination_date,customer_address_id,status,facility_address_id,package_choice,date_filled,sig,sig_expanded,destination_notes,dispensed_quantity,days_supply,min_days_supply,max_days_supply,number_of_pieces,rph_user_name,rph_user_id,is_ordered,is_dispensed,is_prefill,discard_after_date,workflow_status,number_of_labels,doses_per_day,units_per_dose,destination_address1,destination_address2,destination_city,destination_state,destination_zip,effective_date,prescriber_address_id,created_at,pharmacy_id)
                SELECT refill_id,refill_number,created_on,created_by,updated_on,updated_by,rx_id,drug_id,destination_type_id,destination_date,customer_address_id,status,facility_address_id,package_choice,date_filled,sig,sig_expanded,destination_notes,dispensed_quantity,days_supply,min_days_supply,max_days_supply,number_of_pieces,rph_user_name,rph_user_id,is_ordered,is_dispensed,is_prefill,discard_after_date,workflow_status,number_of_labels,doses_per_day,units_per_dose,destination_address1,destination_address2,destination_city,destination_state,destination_zip,effective_date,prescriber_address_id,created_at,pharmacy_id FROM refills_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                // Update flag update = 2 for existing processing data
                DB::statement("UPDATE refills r, refills_child rc SET rc.flag_update = '2' WHERE r.refill_id = rc.refill_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    $flagSuccess = 1;

                } else {
                    $flagSuccess = 0;
                }

            }
        }

        if ($flagSuccess) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function syncRefillShipmentImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_refillshipment_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('refill_shipments')->truncate();
        }
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_refillshipment_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('refill_shipments')
                    ->columns(['refill_shipment_id', 'created_by', 'created_on', 'updated_by', 'updated_on', 'type', 'saturday_delivery', 'require_signature', 'insurance', 'signature_type', 'newleaf_order_number', 'refill_id', 'enterprise_order_id', 'courier', 'tracking_number', 'recipient_number', 'no_of_items', 'shipment_status', 'successfully_submitted', 'error_message', 'is_trackable', 'weight', 'country_of_manufacture', 'customs_description', 'label_location', 'is_thermal_label', 'tracking_update_batch_id', 'fedex_scan_event_code', 'shipped_on', 'is_delivered_by_api', 'remote_fill_order_id', 'height', 'length', 'width', 'require_photo_id', 'packaging_type', 'weight_units', 'shipping_fee', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
                // load csv data here ..

            }
        }

        DB::statement("UPDATE refill_shipments rs, refill_orders ro, shipping s SET ro.status = 3, ro.tracking_number = rs.tracking_number, ro.shipped_by = s.id WHERE ro.newleaf_order_number = rs.newleaf_order_number AND rs.courier = s.name AND rs.tracking_number IS NOT NULL AND rs.courier IS NOT NULL AND rs.courier != ''");

        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }
    public function syncRefillShipmentImportChild()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_refillshipment_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_refillshipment_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from refill_shipments_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('refill_shipments_child')
                    ->columns(['refill_shipment_id', 'created_by', 'created_on', 'updated_by', 'updated_on', 'type', 'saturday_delivery', 'require_signature', 'insurance', 'signature_type', 'newleaf_order_number', 'refill_id', 'enterprise_order_id', 'courier', 'tracking_number', 'recipient_number', 'no_of_items', 'shipment_status', 'successfully_submitted', 'error_message', 'is_trackable', 'weight', 'country_of_manufacture', 'customs_description', 'label_location', 'is_thermal_label', 'tracking_update_batch_id', 'fedex_scan_event_code', 'shipped_on', 'is_delivered_by_api', 'remote_fill_order_id', 'height', 'length', 'width', 'require_photo_id', 'packaging_type', 'weight_units', 'shipping_fee', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
                // load csv data here ..

                // Update existing pharmacy data
                DB::statement("UPDATE refill_shipments r, refill_shipments_child rc SET r.refill_shipment_id = rc.refill_shipment_id,r.type = rc.type,r.saturday_delivery = rc.saturday_delivery,r.require_signature = rc.require_signature,r.insurance = rc.insurance,r.signature_type = rc.signature_type,r.refill_id = rc.refill_id,r.enterprise_order_id = rc.enterprise_order_id,r.courier = rc.courier,r.tracking_number = rc.tracking_number,r.recipient_number = rc.recipient_number,r.no_of_items = rc.no_of_items,r.shipment_status = rc.shipment_status,r.successfully_submitted = rc.successfully_submitted,r.error_message = rc.error_message,r.is_trackable = rc.is_trackable,r.weight = rc.weight,r.insurance_amount = rc.insurance_amount,r.country_of_manufacture = rc.country_of_manufacture,r.customs_description = rc.customs_description,r.label_location = rc.label_location,r.is_thermal_label = rc.is_thermal_label,r.tracking_update_batch_id = rc.tracking_update_batch_id,r.fedex_scan_event_code = rc.fedex_scan_event_code,r.shipped_on = rc.shipped_on,r.is_delivered_by_api = rc.is_delivered_by_api,r.remote_fill_order_id = rc.remote_fill_order_id,r.internal_order_num = rc.internal_order_num,r.height = rc.height,r.length = rc.length,r.width = rc.width,r.require_photo_id = rc.require_photo_id,r.packaging_type = rc.packaging_type,r.weight_units = rc.weight_units,r.shipping_fee = rc.shipping_fee,r.created_on = rc.created_on,r.created_by = rc.created_by,r.updated_on = rc.updated_on,r.shipped_on = rc.shipped_on,r.updated_by = rc.updated_by,r.created_at = rc.created_at,r.updated_at = rc.updated_at,r.deleted_at = rc.deleted_at,r.pharmacy_id = rc.pharmacy_id WHERE r.refill_shipment_id = rc.refill_shipment_id AND rc.pharmacy_id = '" . $p_id . "'");

                // Update flag update = 1 for existing processing data
                DB::statement("UPDATE refill_shipments r, refill_shipments_child rc SET rc.flag_update = '1' WHERE r.refill_shipment_id = rc.refill_shipment_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                // Insert new data for this pharmacy
                DB::statement("INSERT INTO refill_shipments (refill_shipment_id,type,saturday_delivery,require_signature,insurance,signature_type,  refill_id,enterprise_order_id,courier,tracking_number,recipient_number,no_of_items,shipment_status,successfully_submitted,error_message,is_trackable,weight,insurance_amount,country_of_manufacture,customs_description,label_location,is_thermal_label,tracking_update_batch_id,fedex_scan_event_code,shipped_on,is_delivered_by_api,remote_fill_order_id,internal_order_num,height,length,width,require_photo_id,packaging_type,weight_units,shipping_fee,created_on,created_by,updated_on,updated_by,created_at,updated_at,deleted_at,pharmacy_id)
                SELECT refill_shipment_id,type,saturday_delivery,require_signature,insurance,signature_type,  refill_id,enterprise_order_id,courier,tracking_number,recipient_number,no_of_items,shipment_status,successfully_submitted,error_message,is_trackable,weight,insurance_amount,country_of_manufacture,customs_description,label_location,is_thermal_label,tracking_update_batch_id,fedex_scan_event_code,shipped_on,is_delivered_by_api,remote_fill_order_id,internal_order_num,height,length,width,require_photo_id,packaging_type,weight_units,shipping_fee,created_on,created_by,updated_on,updated_by,created_at,updated_at,deleted_at,pharmacy_id FROM refill_shipments_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                // Update flag update = 2 for existing processing data
                DB::statement("UPDATE refill_shipments r, refill_shipments_child rc SET rc.flag_update = '2' WHERE r.refill_shipment_id = rc.refill_shipment_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                // Update tracking information
                DB::statement("UPDATE refill_shipments rs, refill_orders ro, shipping s SET ro.status = 3, ro.tracking_number = rs.tracking_number, ro.shipped_by = s.id WHERE ro.newleaf_order_number = rs.newleaf_order_number AND rs.courier = s.name AND rs.tracking_number IS NOT NULL AND rs.courier IS NOT NULL AND rs.courier != ''");

                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    $flagSuccess = 1;

                } else {
                    $flagSuccess = 0;
                }

            }
        }

        if ($flagSuccess) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function syncCareKitImport()
    {
        $uploads = false;

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKit_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        if ($count) {
            // truncate rxs table ..
            DB::table('carekit')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKit_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('carekit')
                    ->columns(['hospice_care_kit_id', 'facility_id', 'name', 'is_active', 'createdOn', 'createdBy', 'updatedOn', 'UpdatedBy'])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
                // load csv data here ..

                if (!empty($uploads)) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    //return view('admin.import-success');
                } else {
                    //return view('admin.import-error');
                }
            }
        }

        if (!empty($uploads)) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }
    public function syncCareKitImportChild()
    {
        $uploads = false;

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKit_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKit_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from carekit_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('carekit_child')
                    ->columns(['hospice_care_kit_id', 'facility_id', 'name', 'is_active', 'createdOn', 'createdBy', 'updatedOn', 'UpdatedBy'])
                    ->set([
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
                // load csv data here ..

                // Update existing pharmacy data
                DB::statement("UPDATE carekit r, carekit_child rc SET r.hospice_care_kit_id = rc.hospice_care_kit_id,r.facility_id = rc.facility_id,r.name = rc.name,r.is_active = rc.is_active,r.createdOn = rc.createdOn,r.createdBy = rc.createdBy,r.updatedOn = rc.updatedOn,r.UpdatedBy = rc.UpdatedBy WHERE r.hospice_care_kit_id = rc.hospice_care_kit_id AND rc.pharmacy_id = '" . $p_id . "'");

                // Update flag update = 1 for existing processing data
                DB::statement("UPDATE carekit r, carekit_child rc SET rc.flag_update = '1' WHERE r.hospice_care_kit_id = rc.hospice_care_kit_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                // Insert new data for this pharmacy
                DB::statement("INSERT INTO carekit (hospice_care_kit_id,facility_id,name, is_active,createdOn,createdBy,updatedOn,UpdatedBy) SELECT hospice_care_kit_id,facility_id,name, is_active,createdOn,createdBy,updatedOn,UpdatedBy FROM carekit_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                // Update flag update = 2 for existing processing data
                 DB::statement("UPDATE carekit r, carekit_child rc SET rc.flag_update = '2' WHERE r.hospice_care_kit_id = rc.hospice_care_kit_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");


                 if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    $flagSuccess = 1;

                } else {
                    $flagSuccess = 0;
                }
            }
        }


        if (!empty($flagSuccess)) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function syncCareKitItemImport()
    {

        $uploads = false;

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKitItems_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        if ($count) {
            // truncate rxs table ..
            DB::table('care_kit_items')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKitItems_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('care_kit_items')
                    ->columns(['hospice_care_kit_item_id', 'hospice_care_kit_id', 'drug_id', 'quantity', 'days_supply', 'sig', 'CreatedOn', 'CreatedBy', 'updatedOn', 'UpdatedBy'])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
                // load csv data here ..

                if (!empty($uploads)) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    //return view('admin.import-success');
                } else {
                    //return view('admin.import-error');
                }
            }
        }

        if (!empty($uploads)) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }
    public function syncCareKitItemImportChild()
    {

        $uploads = false;

        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKitItems_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_CareKitItems_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from care_kit_items_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('care_kit_items_child')
                    ->columns(['hospice_care_kit_item_id', 'hospice_care_kit_id', 'drug_id', 'quantity', 'days_supply', 'sig', 'CreatedOn', 'CreatedBy', 'updatedOn', 'UpdatedBy'])
                     ->set([
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
                // load csv data here ..

                DB::statement("UPDATE care_kit_items r, care_kit_items_child rc SET r.hospice_care_kit_id = rc.hospice_care_kit_id,r.hospice_care_kit_item_id = rc.hospice_care_kit_item_id,r.drug_id = rc.drug_id,r.quantity = rc.quantity,r.days_supply = rc.days_supply,r.sig = rc.sig,r.createdOn = rc.createdOn,r.updatedBy = rc.updatedBy,r.updatedOn = rc.updatedOn,r.CreatedBy = rc.CreatedBy WHERE r.hospice_care_kit_item_id = rc.hospice_care_kit_item_id AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE care_kit_items r, care_kit_items_child rc SET rc.flag_update = '1' WHERE r.hospice_care_kit_item_id = rc.hospice_care_kit_item_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("INSERT INTO care_kit_items (hospice_care_kit_id,hospice_care_kit_item_id,drug_id,quantity, days_supply,sig,createdOn,updatedBy,updatedOn,CreatedBy) SELECT hospice_care_kit_id,hospice_care_kit_item_id,drug_id,quantity, days_supply,sig,createdOn,updatedBy,updatedOn,CreatedBy FROM care_kit_items_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE care_kit_items r, care_kit_items_child rc SET rc.flag_update = '2' WHERE r.hospice_care_kit_item_id = rc.hospice_care_kit_item_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    $flagSuccess = 1;

                } else {
                    $flagSuccess = 0;
                }
            }
        }

        if (!empty($flagSuccess)) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function phpInfo()
    {
        return phpinfo();
    }

    public function syncPrescribersImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_prescriber_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('prescriber')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_prescriber_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('prescriber')
                    ->columns(['prescriber_id', 'dea_number', 'first_name', 'last_name', 'email', 'phone_number', 'prescriber_type', 'speciality_type', 'identifier', 'external_identifier', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }
        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            //return view('admin.import-success');
        } else {
            //return view('admin.import-error');
        }

        $count_add = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "export_prescriber_Address_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {
                $count_add += 1;
            }
        }
        if ($count_add) {
            // truncate rxs table ..
            DB::table('prescriber_address')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "export_prescriber_Address_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploadsAdd = LoadFile::file($fileDir, $local = true)
                    ->into('prescriber_address')
                    ->columns(['prescriber_id', 'prescriber_address_id', 'address_type', 'address_1', 'address_2', 'state', 'city', 'country', 'zipcode', 'is_primary', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }

        DB::statement("UPDATE prescriber_address pa SET pa.is_primary = 1 WHERE pa.is_primary = 'True' OR pa.is_primary = 'TRUE'");
        DB::statement("UPDATE prescriber_address pa SET pa.is_primary = 0 WHERE pa.is_primary = 'False' OR pa.is_primary = 'FALSE'");

        if ($uploadsAdd) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileNameAdd . '.csv');
            $delete = File::delete($fileDir);
            //return view('admin.import-success');
        } else {
            //return view('admin.import-error');
        }
    }

    public function syncPrescribersImportChild()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_prescriber_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_prescriber_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from prescriber_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('prescriber_child')
                    ->columns(['prescriber_id', 'dea_number', 'first_name', 'last_name', 'email', 'phone_number', 'prescriber_type', 'speciality_type', 'identifier', 'external_identifier', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();

                DB::statement("UPDATE prescriber r, prescriber_child rc SET r.prescriber_id = rc.prescriber_id,r.dea_number = rc.dea_number,r.first_name = rc.first_name,r.middle_name = rc.middle_name,r.last_name = rc.last_name,r.email = rc.email,r.phone_number = rc.phone_number,r.prescriber_type = rc.prescriber_type,r.speciality_type = rc.speciality_type,r.identifier = rc.identifier,r.external_identifier = rc.external_identifier,r.is_active = rc.is_active,r.other_fields = rc.other_fields,r.created_at = rc.created_at,r.updated_at = rc.updated_at,r.deleted_at = rc.deleted_at,r.pharmacy_id = rc.pharmacy_id WHERE r.prescriber_id = rc.prescriber_id AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE prescriber r, prescriber_child rc SET rc.flag_update = '1' WHERE r.prescriber_id = rc.prescriber_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("INSERT INTO prescriber (prescriber_id,dea_number,first_name,middle_name,last_name,email,phone_number,prescriber_type,
                    speciality_type,identifier,external_identifier,is_active,other_fields,created_at,updated_at,deleted_at,pharmacy_id)
                    SELECT prescriber_id,dea_number,first_name,middle_name,last_name,email,phone_number,prescriber_type,speciality_type,identifier,external_identifier,is_active,other_fields,created_at,updated_at,deleted_at,pharmacy_id FROM prescriber_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE prescriber r, prescriber_child rc SET rc.flag_update = '2' WHERE r.prescriber_id = rc.prescriber_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    //return view('admin.import-success');
                } else {
                    //return view('admin.import-error');
                }
            }
        }


        $count_add = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "dev_export_prescriber_Address_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {
                $count_add += 1;
            }
        }

        // flag error
        $flagSuccessP = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "dev_export_prescriber_Address_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from prescriber_address_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploadsAdd = LoadFile::file($fileDir, $local = true)
                    ->into('prescriber_address_child')
                    ->columns(['prescriber_id', 'prescriber_address_id', 'address_type', 'address_1', 'address_2', 'state', 'city', 'country', 'zipcode', 'is_primary', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();


                DB::statement("UPDATE prescriber_address r, prescriber_address_child rc SET r.prescriber_id = rc.prescriber_id,r.prescriber_address_id = rc.prescriber_address_id,r.address_type = rc.address_type,r.address_1 = rc.address_1,r.address_2 = rc.address_2,r.state = rc.state,r.city = rc.city,r.country = rc.country,r.zipcode = rc.zipcode,r.is_primary = rc.is_primary,r.created_at = rc.created_at,r.pharmacy_id = rc.pharmacy_id WHERE r.prescriber_address_id = rc.prescriber_address_id AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE prescriber_address r, prescriber_address_child rc SET rc.flag_update = '1' WHERE r.prescriber_address_id = rc.prescriber_address_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("INSERT INTO prescriber_address (prescriber_id,prescriber_address_id,address_type,address_1,address_2,state,city,country,zipcode,is_primary,is_active,created_at,pharmacy_id) SELECT prescriber_id,prescriber_address_id,address_type,address_1,address_2,state,city,country,zipcode,is_primary,is_active,created_at,pharmacy_id FROM prescriber_address_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE prescriber_address r, prescriber_address_child rc SET rc.flag_update = '2' WHERE r.prescriber_address_id = rc.prescriber_address_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE prescriber_address pa SET pa.is_primary = 1 WHERE pa.is_primary = 'True' OR pa.is_primary = 'TRUE'");
                DB::statement("UPDATE prescriber_address pa SET pa.is_primary = 0 WHERE pa.is_primary = 'False' OR pa.is_primary = 'FALSE'");

                if ($uploadsAdd) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileNameAdd . '.csv');
                    $delete = File::delete($fileDir);
                    //return view('admin.import-success');
                } else {
                    //return view('admin.import-error');
                }
            }
        }

    }

    public function syncNewLeafOrdersImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_Orders_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('newleaf_orders')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_Orders_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('newleaf_orders')
                    ->columns(['patient_id', 'order_date', 'order_number', 'tracking_number', 'courier_name', DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }

        DB::statement("UPDATE newleaf_orders no, shipping s SET no.shipped_by = s.id WHERE no.courier_name = s.name AND no.tracking_number IS NOT NULL AND no.courier_name IS NOT NULL AND no.courier_name != ''");

        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function syncDrugsImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_drugs_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('drugs')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_drugs_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('drugs')
                    ->columns(['newleaf_drug_id', 'identifier', 'description', 'strength', 'new_ndc', 'manufacturer_name', 'is_generic', 'is_rx', 'status_code', 'dosage_form_code', 'dosage_form', 'direct_source', 'master_description', 'created_by', 'created_on', 'updated_by', 'updated_on',  DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }

        DB::statement("UPDATE drugs d, drug_dosage_forms df SET d.dosage_form = df.dosage_form WHERE d.dosage_form_code = df.dosage_code");

        DB::statement("UPDATE drugs d SET d.description = UPPER(d.description)");

        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function syncDrugsImportChild()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_drugs_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_drugs_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from drugs_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('drugs_child')
                    ->columns(['newleaf_drug_id', 'identifier', 'description', 'strength', 'new_ndc', 'manufacturer_name', 'is_generic', 'is_rx', 'status_code', 'dosage_form_code', 'dosage_form', 'direct_source', 'master_description', 'created_by', 'created_on', 'updated_by', 'updated_on',  DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();

                DB::statement("UPDATE drugs r, drugs_child rc SET r.created_by = rc.created_by,r.created_on = rc.created_on,r.updated_by = rc.updated_by,r.updated_on = rc.updated_on,r.identifier = rc.identifier,r.description = rc.description,r.strength = rc.strength,r.new_ndc = rc.new_ndc,r.manufacturer_name = rc.manufacturer_name,r.is_generic = rc.is_generic,r.is_rx = rc.is_rx,r.status_code = rc.status_code,r.dosage_form_code = rc.dosage_form_code,r.direct_source = rc.direct_source,r.master_description = rc.master_description,r.newleaf_drug_id = rc.newleaf_drug_id,r.pharmacy_id = rc.pharmacy_id WHERE r.newleaf_drug_id = rc.newleaf_drug_id AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE drugs r, drugs_child rc SET rc.flag_update = '1' WHERE r.newleaf_drug_id = rc.newleaf_drug_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("INSERT INTO drugs (created_by,created_on,updated_by,updated_on,identifier,description,strength,new_ndc,manufacturer_name,is_generic,is_rx,status_code,dosage_form_code,dosage_form,direct_source,master_description,newleaf_drug_id,pharmacy_id) SELECT created_by,created_on,updated_by,updated_on,identifier,description,strength,new_ndc,manufacturer_name,is_generic,is_rx,status_code,dosage_form_code,dosage_form,direct_source,master_description,newleaf_drug_id,pharmacy_id FROM drugs_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE drugs r, drugs_child rc SET rc.flag_update = '2' WHERE r.newleaf_drug_id = rc.newleaf_drug_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE drugs d, drug_dosage_forms df SET d.dosage_form = df.dosage_form WHERE d.dosage_form_code = df.dosage_code");
                DB::statement("UPDATE drugs d SET d.description = UPPER(d.description)");

                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    $flagSuccess = 1;

                } else {
                    $flagSuccess = 0;
                }
            }
        }

        if ($flagSuccess) {
            return view('admin.import-success');
        } else {
            return view('admin.import-error');
        }
    }

    public function syncPatientsImport()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_patients_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }
        if ($count) {
            // truncate rxs table ..
            DB::table('patients')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "export_patients_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('patients')
                    ->columns(['newleaf_customer_id', 'first_name', 'middle_name', 'last_name', 'gender', 'email', 'newleaf_facility_id', 'dob', 'shipping_method', 'isActive',  DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s')
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }

        DB::statement("UPDATE patients pa SET pa.patient_status = 1, pa.is_active = 1 WHERE pa.isActive = 'True' OR pa.isActive = 'TRUE'");
        DB::statement("UPDATE patients pa SET pa.patient_status = 0, pa.is_active = 0 WHERE pa.isActive = 'False' OR pa.isActive = 'FALSE'");
        DB::statement("UPDATE patients p, branch b SET p.facility_code = b.id WHERE p.newleaf_facility_id IS NOT NULL AND p.newleaf_facility_id = b.newleaf_id");

        if ($uploads) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
            $delete = File::delete($fileDir);
            //return view('admin.import-success');
        } else {
            //return view('admin.import-error');
        }

        /// Patient Address Import

        $count_add = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "export_patient_address_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {
                $count_add += 1;
            }
        }
        if ($count_add) {
            // truncate rxs table ..
            DB::table('patient_addresses')->truncate();
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "export_patient_address_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {

                // load csv data here ..
                $uploadsAdd = LoadFile::file($fileDir, $local = true)
                    ->into('patient_addresses')
                    ->columns(['newleaf_customer_id', 'newleaf_customer_address_id', 'address_type', 'isActive', 'address_1', 'address_2', 'city', 'state', 'zipcode', 'country', 'isPrimary', 'comment', 'created_at', 'created_by', 'updated_at', 'updated_by'])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();
            }
        }

        DB::statement("UPDATE patient_addresses pa SET pa.is_active = 1 WHERE pa.isActive = 'True' OR pa.isActive = 'TRUE'");
        DB::statement("UPDATE patient_addresses pa SET pa.is_active = 0 WHERE pa.isActive = 'False' OR pa.isActive = 'FALSE'");

        DB::statement("UPDATE patient_addresses pa SET pa.is_primary = 1 WHERE pa.isPrimary = 'True' OR pa.isPrimary = 'TRUE'");
        DB::statement("UPDATE patient_addresses pa SET pa.is_primary = 0 WHERE pa.isPrimary = 'False' OR pa.isPrimary = 'FALSE'");

        // update patient table address with this primary table
        DB::statement("UPDATE patients p, patient_addresses pa SET p.address_1 = pa.address_1, p.address_2 = pa.address_2, p.state = pa.state, p.city = pa.city, p.country = pa.country, p.zipcode = pa.zipcode WHERE pa.is_primary = '1' AND pa.newleaf_customer_id = p.newleaf_customer_id");

        if ($uploadsAdd) {
            $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileNameAdd . '.csv');
            $delete = File::delete($fileDir);
            //return view('admin.import-success');
        } else {
            //return view('admin.import-error');
        }
    }
    public function syncPatientsImportChild()
    {
        $uploads = false;
        // Get all pharmacies
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }

        $count = 0;
        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_patients_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {
                $count += 1;
            }
        }

        // flag error
        $flagSuccess = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileName = "dev_export_patients_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileName . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from patients_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploads = LoadFile::file($fileDir, $local = true)
                    ->into('patients_child')
                    ->columns(['newleaf_customer_id', 'first_name', 'middle_name', 'last_name', 'gender', 'email', 'newleaf_facility_id', 'dob', 'shipping_method', 'isActive',  DB::raw("@var1")])
                    ->set([
                        'created_at' => date('Y-m-d H:i:s'),
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();

                DB::statement("UPDATE patients r, patients_child rc SET r.first_name = rc.first_name,r.middle_name = rc.middle_name,r.last_name = rc.last_name,r.address_1 = rc.address_1,r.address_2 = rc.address_2,r.country = rc.country,r.state = rc.state,r.city = rc.city,r.zipcode = rc.zipcode,r.phone_number = rc.phone_number,r.facility_code = rc.facility_code,r.gender = rc.gender,r.dob = rc.dob,r.patient_status = rc.patient_status,r.newleaf_facility_id = rc.newleaf_facility_id,r.ipu = rc.ipu,r.shipping_method = rc.shipping_method,r.other_fields = rc.other_fields,r.is_active = rc.is_active,r.isActive = rc.isActive,r.newleaf_customer_id = rc.newleaf_customer_id,r.patient_id = rc.patient_id,r.created_at = rc.created_at,r.pharmacy_id = rc.pharmacy_id WHERE r.newleaf_customer_id = rc.newleaf_customer_id AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE patients r, patients_child rc SET rc.flag_update = '1' WHERE r.newleaf_customer_id = rc.newleaf_customer_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("INSERT INTO patients (first_name,middle_name,last_name,address_1,address_2,country,state,city,zipcode,phone_number,facility_code,gender,dob,patient_id,patient_status,newleaf_facility_id,ipu,shipping_method,other_fields,is_active,isActive,newleaf_customer_id,created_at,pharmacy_id) SELECT first_name,middle_name,last_name,address_1,address_2,country,state,city,zipcode,phone_number,facility_code,gender,dob,patient_id,patient_status,newleaf_facility_id,ipu,shipping_method,other_fields,is_active,isActive,newleaf_customer_id,created_at,pharmacy_id FROM patients_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE patients r, patients_child rc SET rc.flag_update = '2' WHERE r.newleaf_customer_id = rc.newleaf_customer_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE patients pa SET pa.patient_status = 1, pa.is_active = 1 WHERE pa.isActive = 'True' OR pa.isActive = 'TRUE'");
                DB::statement("UPDATE patients pa SET pa.patient_status = 0, pa.is_active = 0 WHERE pa.isActive = 'False' OR pa.isActive = 'FALSE'");
                DB::statement("UPDATE patients p, branch b SET p.facility_code = b.id WHERE p.newleaf_facility_id IS NOT NULL AND p.newleaf_facility_id = b.newleaf_id");

                if ($uploads) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileName . '.csv');
                    $delete = File::delete($fileDir);
                    //return view('admin.import-success');
                } else {
                    //return view('admin.import-error');
                }
            }
        }

        /// Patient Address Import

        $count_add = 0;

        // flag error
        $flagSuccessP = 1;

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "dev_export_patient_address_$p_id";
            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {
                $count_add += 1;
            }
        }

        foreach ($pharmacyData as $k => $pharmacy) {
            $p_id = $pharmacy['id'];
            $fileNameAdd = "dev_export_patient_address_$p_id";

            $fileDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/' . $fileNameAdd . '.csv';
            if (file_exists($fileDir)) {

                // Delete pharmacy related data
                DB::statement("DELETE from patient_addresses_child WHERE pharmacy_id = '". $p_id . "'");

                // load csv data here ..
                $uploadsAdd = LoadFile::file($fileDir, $local = true)
                    ->into('patient_addresses_child')
                    ->columns(['newleaf_customer_id', 'newleaf_customer_address_id', 'address_type', 'isActive', 'address_1', 'address_2', 'city', 'state', 'zipcode', 'country', 'isPrimary', 'comment', 'created_at', 'created_by', 'updated_at', 'updated_by'])
                    ->set([
                        'pharmacy_id' => $p_id,
                    ])
                    ->linesTerminatedBy("\r\n")
                    ->fieldsTerminatedBy(',')
                    // ->fieldsEscapedBy('\\\\')
                    ->fieldsEnclosedBy('"')
                    ->ignoreLines(1)
                    ->load();


                DB::statement("UPDATE patient_addresses r, patient_addresses_child rc SET r.newleaf_customer_id = rc.newleaf_customer_id,r.newleaf_customer_address_id = rc.newleaf_customer_address_id,r.address_type = rc.address_type,r.address_1 = rc.address_1,r.address_2 = rc.address_2,r.state = rc.state,r.city = rc.city,r.country = rc.country,r.zipcode = rc.zipcode,r.isPrimary = rc.isPrimary,r.comment = rc.comment,r.isActive = rc.isActive,r.created_at = rc.created_at,r.created_by = rc.created_by,r.updated_at = rc.updated_at,r.updated_by = rc.updated_by,r.pharmacy_id = rc.pharmacy_id WHERE r.newleaf_customer_address_id = rc.newleaf_customer_address_id AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE patient_addresses r, patient_addresses_child rc SET rc.flag_update = '1' WHERE r.newleaf_customer_address_id = rc.newleaf_customer_address_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");

                DB::statement("INSERT INTO patient_addresses (newleaf_customer_id,newleaf_customer_address_id,address_type,address_1,address_2,state,city,country,zipcode,isPrimary,comment,isActive,created_at,created_by,updated_at,updated_by,pharmacy_id) SELECT newleaf_customer_id,newleaf_customer_address_id,address_type,address_1,address_2,state,city,country,zipcode,isPrimary,comment,isActive,created_at,created_by,updated_at,updated_by,pharmacy_id FROM patient_addresses_child WHERE flag_update = '0' AND pharmacy_id = '" . $p_id . "'");

                DB::statement("UPDATE patient_addresses r, patient_addresses_child rc SET rc.flag_update = '2' WHERE r.newleaf_customer_address_id = rc.newleaf_customer_address_id AND rc.flag_update = '0' AND rc.pharmacy_id = '" . $p_id . "'");


                // Setting address flags and update to patient table
                DB::statement("UPDATE patient_addresses pa SET pa.is_active = 1 WHERE pa.isActive = 'True' OR pa.isActive = 'TRUE'");
                DB::statement("UPDATE patient_addresses pa SET pa.is_active = 0 WHERE pa.isActive = 'False' OR pa.isActive = 'FALSE'");

                DB::statement("UPDATE patient_addresses pa SET pa.is_primary = 1 WHERE pa.isPrimary = 'True' OR pa.isPrimary = 'TRUE'");
                DB::statement("UPDATE patient_addresses pa SET pa.is_primary = 0 WHERE pa.isPrimary = 'False' OR pa.isPrimary = 'FALSE'");

                // update patient table address with this primary table
                DB::statement("UPDATE patients p, patient_addresses pa SET p.address_1 = pa.address_1, p.address_2 = pa.address_2, p.state = pa.state, p.city = pa.city, p.country = pa.country, p.zipcode = pa.zipcode WHERE pa.is_primary = '1' AND pa.newleaf_customer_id = p.newleaf_customer_id");

                if ($uploadsAdd) {
                    $move = File::move($fileDir, dirname(dirname(dirname(dirname(__FILE__)))) . '/public/newleafdata/archived/' . $fileNameAdd . '.csv');
                    $delete = File::delete($fileDir);
                    //return view('admin.import-success');
                } else {
                    //return view('admin.import-error');
                }
            }
        }

    }

    public function checkPharmacy()
    {
        $pharmacyData = Pharmacy::select('*')->where('is_active', 1)->where('newleaf_endpoint', '!=', '')->where('newleaf_port', '!=', '')->where('newleaf_username', '!=', '')->where('newleaf_password', '!=', '')->get()->toArray();

        if (empty($pharmacyData)) {
            echo "No Pharmacy connected with newLeaf for data sync process.";
            exit;
        }
        $isOk = true;
        foreach ($pharmacyData as $k => $pharmacy) {
            if ($pharmacy) {
                $isOk = $this->syncService->getToken($pharmacy['newleaf_endpoint'], $pharmacy['newleaf_port'], $pharmacy['newleaf_username'], $pharmacy['newleaf_password'], true);
                if (!$isOk["status"]) {
                    try {
                        $data = ['NAME' => $pharmacy['name'], 'MESSAGE' => $isOk["message"] ?? "", 'EMAIL' => 'mmartinez@delivercarerx.com'];
                        EmailTemplatesRepository::sendMail('pharmacy-check', $data);
                        $data = ['NAME' => $pharmacy['name'], 'MESSAGE' => $isOk["message"] ?? "", 'EMAIL' => 'tech@delivercarerx.com'];
                        EmailTemplatesRepository::sendMail('pharmacy-check', $data);
                    } catch (\Exception $e) {
                        dd($e);
                    }
                }
            }
        }
    }
    
    public function createTiffAndPdf()
    {
        try 
        {
            /***************************************** VARIABLES **************************************************************/
            // We need to store error message like "Rx not available" etc..
            $errorMessage = ''; $errMessage = '';
            // We have to set errorFound incase personal information is missing from table
            $errorFound = false;
            // Create empty variables to use
            $Identifier = ''; $successRxOrder = ''; $apiId ='';
            $id = ''; $patient_id = ''; $order_number = ''; $pharmacy_id = ''; $address_1 = ''; $state = '';
            // We have to set a false if there's no response from initial POST API call to the RxCreateRefillAndAdjudicate
            $apiCallSuccessful = false;
            // Used to trigger PDF creation and create comment date
            $createPDFDueToError = false; $createTiffDueToError = false; $commentDate = date("Y-m-d");

            // We need a time/date stamp
            $datetime = date("Y-m-d h:i:s");
            /***************************************************************************************************************/
                
            // We need to find the rows with a 0 value in the if_tiff_generated column and a 0 in the is_error column
            //$model = RefillOrder::where('if_tiff_generated', 0)->whereNull('deleted_at')->get();
            $matchThese = ['if_tiff_generated' => 0, 'is_error' => null];
            $model = RefillOrder::where($matchThese)->whereNull('deleted_at')->get();

            if($model){
                foreach($model as $key => $value){
                    //echo "<pre>RxCreateRefillAndAdjudicate Post: "; dd($value); echo "</pre>";
                    $id = $value['id'];
                    $patient_id = $value['patient_id'];
                    $newLeafCustId = $value['newleaf_customer_id'];
                    $order_number = $value['order_number'];
                    $pharmacy_id = $value['pharmacy_id'];
                    // Personal Info
                    $address_1 = $value['address_1']; $state = $value['state'];
    
                    $patientsRefillOrder = RefillOrder::select('refill_orders.id','refill_orders.patient_id', 'refill_orders.patient_name','refill_orders.order_number',
                    'refill_orders.nurse_name','refill_orders.pharmacy_id', 'refill_orders.shipping_method','refill_orders.newleaf_customer_id', 'refill_orders.address_1',
                    'refill_orders.state','refill_order_items.rx_number','refill_order_items.drug_name','refill_order_items.refill_order_id','patients.newleaf_customer_id')
                    ->join('refill_order_items','refill_order_items.refill_order_id','refill_orders.id')
                    ->join('patients','patients.newleaf_customer_id','refill_orders.newleaf_customer_id')
                    ->where('order_number',$order_number)
                    ->get()
                    ->toArray();
    
                    $data = [];
                    foreach($patientsRefillOrder as $k => $items){
                        $drugname = $items['drug_name'];
                        $id = $items['id'];
                        $address_1 = $items['address_1'];
                        $state = $items['state'];
                        $newLeafCustId = $items['newleaf_customer_id'];
                        $nurseName = $items['nurse_name'];
                        $orderNumber = $items['order_number'];
                        $patientId = $items['patient_id'];
                        $patientName = $items['patient_name'];
                        $pharmacyId = $items['pharmacy_id'];
                        $refillOrderId = $items['refill_order_id'];
                        $rxNumbers = $items['rx_number'];
                        //We need the shipping method, we attach it to the api call, but we need the whole description which we get by prescription.
                        $shippingMethod = $items['shipping_method'];
                        //$shippingMethodCode = $items['shipping_method_code'];
    
                        // If personal Info is missing, we set the is_error column to 1 and break out of API call.
                        if($address_1 == '' || $state == '' || $address_1 == null || $state == null){
                            $errorFound = true; $apiCallSuccessful = false;
                            //RefillOrder::where('id', $value['id'])->update(['is_error' => '1']); 
                            //throw new \Exception('There was an error with the order.');    
                        }
                    } // END nested foreach
    
                    if(!$errorFound){
                        /*********** Insert API POST & GET for RX ************/
                        // Fetch newLeaf Info
                        $pharmacyData = Patients::select('*')
                        ->join('branch', 'branch.id', 'patients.facility_code')
                        ->join('facilities', 'facilities.id', 'branch.facility_id')
                        ->join('pharmacy', 'pharmacy.id', 'facilities.pharmacy_id')
                        ->where('patients.id',$patientId)
                        ->first();
                        
                        // Fetch information to use for the token
                        $newLeafEndpoint = $pharmacyData->newleaf_endpoint; //dd($newLeafEndpoint);
                        //$newLeafEndpoint = "http://10.160.31.83:8084"; 
                        $newLeafPort = $pharmacyData->newleaf_port;
                        $newLeafUsername = $pharmacyData->newleaf_username;
                        $newLeafPwd = $pharmacyData->newleaf_password;
                        $token = self::getToken($newLeafEndpoint, $newLeafPort, $newLeafUsername, $newLeafPwd);        
                        
                        /* Start Loop to check for index & assign index to rxNumber */
                        //Count array length
                        $rxCount = count($patientsRefillOrder);
                        // Count # of Med's on order
                        $rxNumOfMeds = $rxCount;
                        // for loop prints out the indexes of $rxNumOfMeds
                        $count = 0;
    
                        for($i=0; $i < $rxCount; $i++){ 
                            // Initiate curl
                            $curl = curl_init();
                            /* POST API call for RX */
                            $fields = json_encode([
                                "rxnumber" => $patientsRefillOrder[$i]['rx_number'], "matchorder" => true
                            ]);
    
                            curl_setopt_array($curl, array(                   
                                CURLOPT_URL => $newLeafEndpoint . "/RxCreateRefillAndAdjudicate",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => $fields,
                                CURLOPT_HTTPHEADER => array(
                                    "content-type: application/json",
                                    "Accept: application/json",
                                    "authorization: Bearer " . $token
                                ),        
                            ));
                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            // If there is No response, break out of request
                            if (!$response) {
                                $apiCallSuccessful = false; break;
                                echo "<pre>API RxCreateRefillAndAdjudicate was unsuccessful</pre>";
                            }
                            curl_close($curl);
                            if ($err) {
                                echo $err;
                            } else {
                                $result = json_decode($response, true);
                                echo "<pre>RxCreateRefillAndAdjudicate Post: "; var_dump($result); echo "</pre>";
                                // Set a Successful POST request for RxCreateRefillAndAdjudicate
                                $apiCallSuccessful = true;
    
                                // message to be logged
                                $adjudicateMessage = "New Leaf Endpoint: $newLeafEndpoint. Refill order_number # $order_number. RxCreateRefillAndAdjudicate Post: $response.";
                                
                                // path of the log file where errors need to be logged
                                $log_file = '/home/beta-delivercare/public_html/storage/logs/apiLogs.log';
    
                                // setting error logging to be active
                                ini_set("log_errors", TRUE);
                                
                                // setting the logging file in php.ini
                                ini_set('error_log', $log_file);
                                
                                // logging the error
                                error_log($adjudicateMessage);
                                $out = ob_get_clean(); echo $out;
    
                                if ($result) {
                                    //  Fetch Id from $result and If Id isn't blank run this code
                                    if(isset($result['Id']) && $result['Id'] != "00000000-0000-0000-0000-000000000000"){
                                        $apiId = $result['Id'];
                                        // GET API call for Orders w/ ID from POST results 
                                        $curlGet = curl_init();
                                        curl_setopt_array($curlGet, array(    
                                            CURLOPT_URL => $newLeafEndpoint . "/Orders($apiId)",               
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => "",
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => "GET",
                                            CURLOPT_HTTPHEADER => array(
                                                "content-type: application/json",
                                                "authorization: Bearer " . $token
                                            ),
                                        ));
                                        $getResponse = curl_exec($curlGet);
                                        $errGet = curl_error($curlGet);
                                        curl_close($curlGet);
                                        if ($errGet) {
                                            echo $errGet;
                                        } else {
                                            $responseGet = json_decode($getResponse, true);
                                            echo "<pre>Orders(Id#) Get: "; var_dump($responseGet); echo "</pre>";
    
                                            // message to be logged
                                            $ordersMessage = "New Leaf Endpoint: $newLeafEndpoint. Refill order_number # $order_number. Orders(Id) Get: $getResponse.";
    
                                            // path of the log file where errors need to be logged
                                            $log_file = '/home/beta-delivercare/public_html/storage/logs/apiLogs.log';
    
                                            // setting error logging to be active
                                            ini_set("log_errors", TRUE);
    
                                            // setting the logging file in php.ini
                                            ini_set('error_log', $log_file);
    
                                            // logging the error
                                            error_log($ordersMessage);
                                            $out = ob_get_clean(); echo $out;
                                                        
                                            // We need the Identifier # from the $responseGet results
                                            if(!empty($responseGet['Identifier'])){
                                                $Identifier = 'Identifier#: '. $responseGet['Identifier'] . '. ';
                                            }
                                        }
                                    }                                    
                                } // END $result
    
                                // Loop through results and check if we have an error. If we have an error, create a new TIFF and send to "AttenTIFF" folder.
                                if (!empty($result)) {
                                    foreach($result as $value){
                                        if (!empty($value["message"])) { 
                                            $errorMessage .= implode('', $value); 
                                            if (str_contains($errorMessage, $patientsRefillOrder[$i]['rx_number'])) {
                                                if (str_contains($errorMessage, 'is not found.')) {
                                                    $errMessage = 'Rx #' . $patientsRefillOrder[$i]['rx_number'] . ' is not found.';
                                                }
                                            }
                                            RefillOrderItems::join('refill_orders','refill_orders.id','refill_order_items.refill_order_id')
                                            ->where('refill_orders.patient_id', $patientId)
                                            ->where('refill_order_items.refill_order_id', $refillOrderId)
                                            ->where('refill_order_items.rx_number', $patientsRefillOrder[$i]['rx_number'])
                                            ->update(['refill_order_items.notes' => $errMessage]);       
                                            $createPDFDueToError = true;
                                        } else {
                                            RefillOrderItems::join('refill_orders','refill_orders.id','refill_order_items.refill_order_id')
                                            ->where('refill_orders.patient_id', $patientId)
                                            ->where('refill_order_items.refill_order_id', $refillOrderId)
                                            ->where('refill_order_items.rx_number', $patientsRefillOrder[$i]['rx_number'])
                                            ->update(['refill_order_items.notes' => $patientsRefillOrder[$i]['rx_number'] . ' Refill Item Successful.']);
                                            $createPDFDueToError = true;
                                            $successRxOrder = 'Successful';
                                        }
                                    } 
                                }
                                // END Loop through results and check if we have an error.
    
                                if (!empty($result["Reasons"])) {
                                    foreach($result["Reasons"] as $reasons){
                                        //var_dump('here2' . $reasons);
                                        if (!empty($result["Reasons"])) {                      
                                            RefillOrderItems::join('refill_orders','refill_orders.id','refill_order_items.refill_order_id')
                                            ->where('refill_orders.patient_id', $patientId)
                                            ->where('refill_order_items.refill_order_id', $refillOrderId)
                                            ->where('refill_order_items.rx_number', $result["RxNumber"])
                                            ->update(['refill_order_items.notes' => $result["RxNumber"]. ': '.$result["Reasons"][0].'. ']);
                                            $createPDFDueToError = true;
                                        } 
                                    }    
                                }
                            }
                        } // END for loop
                    } // END !errorFound                
                } // END 1st foreach
    
                var_dump('Is API Successful? ' . $apiCallSuccessful);
                if ($apiCallSuccessful) {
                    // Add the Identifier # from the API to the Refill_Orders table
                    if(!empty($responseGet['Identifier'])){
                        $addIdentifier = RefillOrder::select('patient_id','patient_name')
                        ->where('newleaf_customer_id', $newLeafCustId)
                        ->where('order_number', $order_number)
                        ->update(['refill_orders.newleaf_order_number' => $responseGet['Identifier']]);
                    }
    
                    // If we get a $responseGet, send out a PUT request. We only want to send it 1 time.
                    if (!empty($responseGet)) {
                        $enterpriseOrderId = $responseGet["EnterpriseOrderId"];
                        $identifier = $responseGet["Identifier"];
                        $storeNum = $responseGet["StoreNum"];    
                        $customerId = $responseGet["CustomerId"];
                        $facilityId = $responseGet["FacilityId"];
                        $prescriberId = $responseGet["PrescriberId"];
                        $ownerType = $responseGet["OwnerType"];
                        $needBy = $responseGet["NeedBy"];
                        $priority = $responseGet["Priority"];
                        $deliveryMethod = $responseGet["DeliveryMethod"];
                        $description = $responseGet["Description"];
                        $customerAddressId = $responseGet["CustomerAddressId"];
                        $facilityAddressId = $responseGet["FacilityAddressId"];
                        $prescriberAddressId = $responseGet["prescriberAddressId"];
                        $destinationAddress1 = $responseGet["DestinationAddress1"];
                        $destinationAddress2 = $responseGet["DestinationAddress2"];
                        $destinationCity = $responseGet["DestinationCity"];
                        $destinationState = $responseGet["DestinationState"];
                        $destinationZip = $responseGet["DestinationZip"];
                        $isSealed = $responseGet["IsSealed"];
                        $isComplete = $responseGet["IsComplete"];
                        $isCancelled = $responseGet["IsCancelled"];
                        $isSystemOrder = $responseGet["IsSystemOrder"];
                        $isSplit = $responseGet["IsSplit"];
                        $shipToPrescriberOverride = $responseGet["ShipToPrescriberOverride"];
                        $centralFillDispensingStatus = $responseGet["CentralFillDispensingStatus"];
                        $completedOn = $responseGet["CompletedOn"];
                        $centralFillPharmacyId = $responseGet["CentralFillPharmacyId"];
                        $shipBy = $responseGet["ShipBy"];
                        $shipByOverride = $responseGet["ShipByOverride"];
                        $remoteFillOrderStatus = $responseGet["RemoteFillOrderStatus"];
                        $partnerId = $responseGet["PartnerId"];
                        $storeOrderManagerFlagId = "0B179261-B72F-EE11-B832-000C29EFA6BB";
                        $data = array(
                            "CreatedBy" => "test",
                            "EnterpriseOrderId" => $enterpriseOrderId,
                            "Identifier" => $identifier, 
                            "StoreNum"=> $storeNum,
                            "CustomerId"=> $customerId,
                            "FacilityId"=> $facilityId,
                            "PrescriberId"=> $prescriberId,
                            "OwnerType"=> $ownerType,
                            "NeedBy"=> $needBy,
                            "Priority"=> $priority,
                            "DeliveryMethod"=> $deliveryMethod,
                            "Description"=> $description,
                            "CustomerAddressId"=> $customerAddressId,
                            "FacilityAddressId"=> $facilityAddressId,
                            "prescriberAddressId"=> $prescriberAddressId,
                            "DestinationAddress1"=> $destinationAddress1,
                            "DestinationAddress2"=> $destinationAddress2,
                            "DestinationCity"=> $destinationCity,
                            "DestinationState"=> $destinationState,
                            "DestinationZip"=> $destinationZip,
                            "IsSealed"=> $isSealed,
                            "IsComplete"=> $isComplete,
                            "IsCancelled"=> $isCancelled,
                            "IsSystemOrder"=> $isSystemOrder,
                            "IsSplit"=> $isSplit,
                            "ShipToPrescriberOverride"=> $shipToPrescriberOverride,
                            "CentralFillDispensingStatus"=> $centralFillDispensingStatus,
                            "CompletedOn"=> $completedOn,
                            "CentralFillPharmacyId"=> $centralFillPharmacyId,
                            "ShipBy"=> $shipBy,
                            "ShipByOverride"=> $shipByOverride,
                            "RemoteFillOrderStatus"=> $remoteFillOrderStatus,
                            "PartnerId"=> $partnerId,
                            "EnterpriseOrderComments"=> [array(
                                "EnterpriseOrderId"=> $enterpriseOrderId,
                                "IsDeleted"=> false,
                                "PriorityCode"=> 1,
                                "Comment"=> "Filled by: " . $nurseName . ". Portal Order#: " . $orderNumber,
                                "CommentDate" => $commentDate                         
                            )],
                            "EnterpriseOrderFlags" => [array(
                                "EnterpriseOrderId" => $enterpriseOrderId,
                                "StoreOrderManagerFlagId" => $storeOrderManagerFlagId
                            )]
                        );
    
                        $postData = json_encode($data);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(                   
                            CURLOPT_URL => $newLeafEndpoint . "/Orders($apiId)",               
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'PUT',
                            CURLOPT_POSTFIELDS => $postData,
                            CURLOPT_HTTPHEADER => array(
                                "content-type: application/json",
                                "authorization: Bearer " . $token
                            ),                   
                        ));
                        $responsePut = curl_exec($curl);
                        $putErr = curl_error($curl);
                        curl_close($curl);
                        if ($putErr) {
                            echo $putErr;
                        } else {
                            $putResponse = json_decode($responsePut, true);
                            echo "<pre>Orders(Id#) Put: "; var_dump($putResponse); echo "</pre>";
    
                            // message to be logged
                            $ordersPutMessage = "New Leaf Endpoint: $newLeafEndpoint. Refill order_number # $order_number. Orders(Id) Put: $responsePut.";
    
                            // path of the log file where errors need to be logged
                            $log_file = '/home/beta-delivercare/public_html/storage/logs/apiLogs.log';
    
                            // setting error logging to be active
                            ini_set("log_errors", TRUE);
                            
                            // setting the logging file in php.ini
                            ini_set('error_log', $log_file);
                            
                            // logging the error
                            error_log($ordersPutMessage);
                            $out = ob_get_clean(); echo $out;
                        }
                    } // END !empty($responseGet)
    
                    // Create PDF/TIFF and send it over to ordersController file.
                    if ($createPDFDueToError) { 
                        $createTiffDueToError = true;
                        $this->orderController->generateOrdersTIFF($id,true);        
                    }
    
                } // END $apiCallSuccessful = 1(true)
    
                if (!$apiCallSuccessful) { 
                    foreach ($model as $key => $value) {
                        var_dump('Create TIFF and add a value of 1');
        
                        if(!$errorFound){
                            if (!empty($value)) {
                                $this->orderController->generateOrdersTIFF($value['id'], true);
                                RefillOrder::where('id', $value['id'])->update(['if_tiff_generated' => '1']);
                            }    
                        }
                    }                  
                } //END !$apiCallSuccessful

            } // END if $model

        } catch (\Exception $e) {
            var_dump('Exception error thrown. Expect email.');

            // If we have an issue creating tiff, we must add a 1 to the is_error column & send email.
            //if ($errorFound) {
                $matchThese = ['if_tiff_generated' => 0, 'id' => $id];
                RefillOrder::where($matchThese)->update(['is_error' => '1']);
    
                //RefillOrder::where('id', $id)->update(['is_error' => '1']);
            //}

            // error message to be logged
            $error_message = "There was an error with the order_number #: $order_number. $e";
            
            // path of the log file where errors need to be logged
            $log_file = '/home/beta-delivercare/public_html/storage/logs/laravel.log';
            //$log_file = '/home/beta-delivercare/logs/error_log';

            // setting error logging to be active
            ini_set("log_errors", TRUE); 
            
            // setting the logging file in php.ini
            ini_set('error_log', $log_file);
            
            //dd($error_message);
            // logging the error
            error_log($error_message);

            $data = ['MESSAGE' => $e ?? "", 'EMAIL' => 'mmartinez@delivercarerx.com'];
            EmailTemplatesRepository::sendMail('tiff-pdf-issue', $data);
            $data = ['MESSAGE' => $e ?? "", 'EMAIL' => 'tech@delivercarerx.com'];
            EmailTemplatesRepository::sendMail('tiff-pdf-issue', $data);
        }
    }


    /* Get Token */
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
            //CURLOPT_URL => "http://10.160.31.83:8084/api/Login",
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
}
