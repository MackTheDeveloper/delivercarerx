<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),
    'APP_LOGO' => 'https://portal.delivercarerx.com/assets/img/logo.png',
    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://127.0.0.1:8000/'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'America/New_York',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        Maatwebsite\Excel\ExcelServiceProvider::class,

    ],

    /**
     * @OA\Info(title="Search API", version="1.0.0")
     */



    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Js' => Illuminate\Support\Js::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,

    ],
    'newleaf_api_domain' => 'http://10.160.31.83:8084',
    'newleaf_data_sync_per_thread' => '10000',
    'newleaf_api_username' => 'DeliverCareAPI',
    'newleaf_api_password' => 'D3l1v3rc@re',
    'adminPrefix' => 'securedlccontrol/',
    'arrWhoCanCheck' => ['site-admin', 'pharmacy-admin', 'user'],
    'newleaf_api_domain' => 'http://10.160.31.83:8084',

    'activityModules' => [
        "Login" => "Login",
        "Logout" => "Logout",
        "Hospice" => "Hospice",
        "Shipping" => "Shipping",
        "HospiceUser" => "HospiceUser",
        "Nurse" => "Nurse",
        "User" => "User",
        "Pharmacy" => "Pharmacy",
        "Profile" => "Profile",
        "Email-Template" => "Email-Template",
        "Facilities" => "Facilities",
        "Branch" => "Branch",
        "Assign-Nurse" => "Assign-Nurse",
        "Patients" => "Patients",
        "Import-Hospice" => "Import-Hospice",
        "Import-Patients" => "Import-Patients",
        "Import-DeliverCareX-Users" => "Import-DeliverCareX-Users",
        "Import-Facility" => "Import-Facility",
        "Import-Branches" => "Import-Branches",
        "Telephonic-Orders" => "Telephonic-Orders",
        "Orders" => "Orders",
        "Import-Newleaf-Orders" => "Import-Newleaf-Orders",
    ],

    'activityDescriptions' => [
        'User_Login' => 'User Login',
        "User_Logout" => 'User Logout',
        "General" => [
            'Added' => "{PARAM} Added",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted",
        ],
        "Hospice" => [
            'Added' => "{PARAM} Added With the Code {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted With the Code {PARAM1}",
        ],
        "User" => [
            'Added' => "{PARAM} Added With the Email-ID - {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "User - {PARAM} is Deleted.",
            "Exported" =>  "{PARAM}( {PARAM1} ) Exported All Orders Into CSV File!!",
        ],
        "Email-Template" => [
            'Added' => "Email Template Added With the Title {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "Email Template Deleted With the Title {PARAM}",
        ],
        "Facilities" => [
            'Added' => "{PARAM} Added With the New Leaf Id {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted With the New Leaf Id {PARAM1}",
        ],
        "Branch" => [
            'Added' => "{PARAM} Added With the Code {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted With the Code {PARAM1}",
        ],
        "Profile" => [
            'Added' => "{PARAM} Added With the Name {PARAM1}",
            'Updated' => "{PARAM} Updated",
            'Changed' => "{PARAM} Changed",
            "Deleted" => "{PARAM} Deleted With the Name {PARAM1}",
        ],

        "Pharmacy" => [
            'Added' => "{PARAM} Added With the Name",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted With the Name",
        ],
        "Shipping" => [
            'Added' => "{PARAM} Added With the Name",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted With the Name",
        ],

        "Nurse" => [
            'Added' => "{PARAM} Added With the Email-ID - {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "Nurse - {PARAM} is Deleted",
            "Imported" => "Import Nurse - {PARAM} Rows Inserted and {PARAM1} Rows has Error",
        ],
        "HospiceUser" => [
            'Added' => "{PARAM} Added With the Email-ID - {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "Hospice User - {PARAM} is Deleted.",
        ],
        "Assign-Nurse" => [
            'Added' => "{PARAM} Added With the Branch Name - {PARAM1}",
            'Updated' => "{PARAM} Updated",
        ],
        "Patients" => [
            'Added' => "{PARAM} Added With the Code {PARAM1}",
            'Updated' => "{PARAM} Updated",
            "Deleted" => "{PARAM} Deleted With the Code {PARAM1}",
        ],
        "Import-Hospice" => [
            'Imported' => "Imported with {PARAM} success and {PARAM1} with failure",
        ],
        "Import-Patients" => [
            'Imported' => "Imported with {PARAM} success and {PARAM1} with failure",
        ],
        "Import-DeliverCareX Users" => [
            'Imported' => "Imported with {PARAM} success and {PARAM1} with failure",
        ],
        "Import-Facility" => [
            'Imported' => "Imported with {PARAM} success and {PARAM1} with failure",
        ],
        "Import-Branches" => [
            'Imported' => "Imported with {PARAM} success and {PARAM1} with failure",
        ],
        "Telephonic-Orders" => [
            'Added' => "Id with {PARAM} of Telephonic-Orders is placed",
        ],
        "Orders" => [
            'Added' => "{PARAM} status updated with  {PARAM1}",
        ],
        "Import-Newleaf-Orders" => [
            'Added' => "Imported with {PARAM} success and {PARAM1} with failure",
        ],

    ],

    'renamedAttributes' => [
        'country_id' => 'Country',
        'state_id' => 'State',
        'city_id' => 'City',
        'address_1' => 'Address 1',
        'address_2' => 'Address 2',
        'is_active' => 'Status',
        'firstname' => 'First Name',
        'lastname' => 'Last Name',
        'newleaf_id' => 'New Leaf Id',
        'address1' => 'Address 1',
        'address2' => 'Address 2',
        'pharmacy_id' => 'Pharmacy',
        'google_link' => 'Google Link',
        'tracking_url' => 'Tracking URL',
        'tracking_prefix' => 'Tracking Prefix',
        'tracking_length' => 'Tracking Length',
        'tracking_suffix' => 'Tracking Suffix',
        'gender' => 'Gender',
        'branch_id' => 'Branch',
        'facility_id' => 'Facility',
        'hospice_id' => 'Hospice',
        'hospice_user_role' => 'Hospice User Role',
        'role_id' => 'Role',
        'facility_code' => 'Facility Branch',
        'patient_id' => 'Patient Name',
    ],

    'attrStoredWithId' => [
        'country_id' => 'countries:name',
        'state_id' => 'states:name',
        'city_id' => 'cities:name',
        'hospice_id' => 'hospice:name',
        'user_id' => 'users.name',
    ],

    'timezones' => [
        "-12:00" => "(GMT -12:00) Eniwetok, Kwajalein",
        "-11:00" => "(GMT -11:00) Midway Island, Samoa",
        "-10:00" => "(GMT -10:00) Hawaii",
        "-09:30" => "(GMT -9:30) Taiohae",
        "-09:00" => "(GMT -9:00) Alaska",
        "-08:00" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
        "-07:00" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
        "-06:00" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
        "-05:00" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
        "-04:30" => "(GMT -4:30) Caracas",
        "-04:00" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
        "-03:30" => "(GMT -3:30) Newfoundland",
        "-03:00" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
        "-02:00" => "(GMT -2:00) Mid-Atlantic",
        "-01:00" => "(GMT -1:00) Azores, Cape Verde Islands",
        "+00:00" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
        "+01:00" => "(GMT +1:00) Brussels, Copenhagen, Madrid, Paris",
        "+02:00" => "(GMT +2:00) Kaliningrad, South Africa",
        "+03:00" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
        "+03:30" => "(GMT +3:30) Tehran",
        "+04:00" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
        "+04:30" => "(GMT +4:30) Kabul",
        "+05:00" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
        "+05:30" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
        "+05:75" => "(GMT +5:45) Kathmandu, Pokhara",
        "+06:00" => "(GMT +6:00) Almaty, Dhaka, Colombo",
        "+06:30" => "(GMT +6:30) Yangon, Mandalay",
        "+07:00" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
        "+08:00" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
        "+08:75" => "(GMT +8:45) Eucla",
        "+09:00" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
        "+09:30" => "(GMT +9:30) Adelaide, Darwin",
        "+10:00" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
        "+10:30" => "(GMT +10:30) Lord Howe Island",
        "+11:00" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
        "+11:30" => "(GMT +11:30) Norfolk Island",
        "+12:00" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka",
        "+12:75" => "(GMT +12:45) Chatham Islands",
        "+13:00" => "(GMT +13:00) Apia, Nukualofa",
        "+14:00" => "(GMT +14:00) Line Islands, Tokelau"
    ],
    'hospice_user_role' => [
        "1" => "Admin",
        "2" => "Branch Admin",
    ],
    'patients-status' => [
        "0" => "Inactive",
        "1" => "Active",
        "2" => "InFacility",
        "3" => "Transfer",
    ],

    'patient_shipping_method' => [
    "0" => "Not Specified",
    "1" => "Pickup",
    "2" => "Mail",
    "3" => "Delivery",
    "4" => "FedEx",
    "5" => "UPS",
    "6" => "Courier",
    "7" => "Delivery Courier",
    "8" => "Next Day",
    "9" => "Second Day",
    "10" => "Ground",
    "11" => "Other",
    "12" => "Employee Delivery",
    "13" => "Waiting",
    "14" => "FedEx Home Delivery",
    "15" => "FedEx 2 Day",
    "16" => "FedEx Express Saver",
    "17" => "FedEx First Overnight",
    "18" => "FedEx Priority Overnight",
    "19" => "FedEx Standard Overnight",
    "20" => "FedEx International Economy",
    "21" => "FedEx International Priority",
    "22" => "Mail Overnight",
    "23" => "UPS Ground",
    "24" => "UPS Mail Innovations",
    "25" => "UPS Next Day Early AM",
    "26" => "UPS Next Day Regular",
    "27" => "UPS Second Day Regular",
    "28" => "USPS Mail",
    "29" => "UPS International",
    "30" => "UPS Ground International",
    "31" => "UPS Mail Innovations International",
    "32" => "UPS Next Day Early AM International",
    "33" => "UPS Next Day Regular International",
    "34" => "UPS Second Day Regular International",
    "35" => "USPS Mail International",
    "36" => "Norco",
    "37" => "Norco $9",
    "38" => "Norco $20",
    "39" => "Pay at Pickup",
    "40" => "Prepay",
    "41" => "COD",
    "42" => "Delivery POS Prepay",
    "43" => "EBNHC",
    "44" => "Element Care",
    "45" => "Normal",
    "46" => "Arriving Soon",
    "47" => "Delivery Today",
    "48" => "Delivery AM",
    "49" => "Delivery PM",
    "50" => "Mail Priority",
    "51" => "Hospice Delivery",
    "52" => "Hospice Pick-Up",
    "53" => "Neuro Today",
    "54" => "Neuro Cycle 1",
    "55" => "Neuro Cycle 2",
    "56" => "Neuro Cycle 3",
    "57" => "Neuro Cycle 4",
    "58" => "LABX",
    "59" => "Indianapolis",
    "60" => "Muncie",
    "61" => "Louisville",
    "62" => "Denver",
    "63" => "Colorado Springs",
    "64" => "Seattle",
    "65" => "Innovative",
    "66" => "Local",
    "67" => "Standard",
    "68" => "Special-BR",
    "69" => "Delivery Next Day",
    "70" => "Delivery Today (Time Sp)",
    "71" => "Delivery Next Day (Time Sp)",
    "72" => "Delivery Today WPD",
    "73" => "Delivery Next Day WPD",
    "74" => "Delivery Today Time Sp WPD",
    "75" => "Delivery Next Day Time Sp WPD",
    "76" => "Local - Regular",
    "77" => "Local - Stat",
    "78" => "Local - Will Call",
    "79" => "VirtueScript",
    "80" => "VirtueScript USPS",
    "81" => "VirtueScript FedEx",
    "82" => "VirtueScript UPS",
    "83" => "VirtueScript Courier",
    "84" => "FedEx Smart Post",
    "85" => "Courier - USPack",
    "86" => "ATL - Next Day",
    "87" => "AUS - Next Day",
    "88" => "CHS - Next Day",
    "89" => "CLT - Next Day",
    "90" => "COL - Next Day",
    "91" => "FAY - Next Day",
    "92" => "FAY - Same Day",
    "93" => "FCT - Next Day",
    "94" => "FCT - Same Day",
    "95" => "FLO - Next Day",
    "96" => "FTW - Next Day",
    "97" => "GBO - Next Day",
    "98" => "GBO - Same Day",
    "99" => "GRN - Next Day",
    "100" => "GRN - Same Day",
    "101" => "GSP - Next Day",
    "102" => "HOU - Next Day",
    "103" => "LUM - Next Day",
    "104" => "LUM - Same Day",
    "105" => "MEM - Next Day",
    "106" => "RMW - Next Day",
    "107" => "RMW - Same Day",
    "108" => "RTP - Next Day",
    "109" => "RTP - Same Day",
    "110" => "SAN - Next Day",
    "111" => "Next Day Air",
    "112" => "Next Day Air Saver",
    "113" => "Courier - Place Holder",
    "114" => "Site 1 - Next Day",
    "115" => "Site 1 - Same Day",
    "116" => "Site 2 - Next Day",
    "117" => "Site 2 - Same Day",
    "118" => "Site 3 - Next Day",
    "119" => "Site 3 - Same Day",
    "120" => "Site 4 - Next Day",
    "121" => "Site 4 - Same Day",
    ],


    'doses_form' => [
        "0" => "",
        "1" => "Aerosol Powder Breath Activated",
        "2" => "AEROSOL BREATH ACTIVATED",
        "3" => "AEROSOL",
        "4" => "Aerosol Powder",
        "5" => "Aerosol Solution",
        "6" => "BAR",
        "7" => "BEADS",
        "8" => "Capsule",
        "9" => "Tablet Chewable",
        "10" => "Concentrate",
        "11" => "Capsule Extended Release 12 Hour",
        "12" => "Capsule Extended Release 24 Hour",
        "13" => "Capsule Extended Release",
        "14" => "CAPSULE DELAYED RELEASE",
        "16" => "Capsule Delayed Release Particles",
        "17" => "CAPSULE SPRINKLE",
        "18" => "CREAM",
        "19" => "CRYSTALS",
        "23" => "DEVICE",
        "24" => "DISK",
        "25" => "DIAPHRAGM",
        "26" => "ELIXIR",
        "27" => "EMULSION",
        "28" => "ENEMA",
        "29" => "Fluid Extract",
        "30" => "FILM",
        "31" => "FLAKES",
        "32" => "FOAM",
        "33" => "GAS",
        "34" => "GEL",
        "35" => "GRANULES",
        "36" => "GRANULES EFFERVESCENT",
        "37" => "GUM",
        "38" => "IMPLANT",
        "39" => "INHALER",
        "40" => "INJECTABLE",
        "41" => "INSERT",
        "42" => "Intrauterine Device",
        "43" => "KIT",
        "44" => "LEAVES",
        "45" => "LIQUID",
        "46" => "LOTION",
        "47" => "LOZENGE",
        "48" => "Lozenge on a Handle",
        "49" => "Liquid Extended Release",
        "50" => "Miscellaneous",
        "51" => "Nebulization Solution",
        "53" => "OIL",
        "54" => "OINTMENT",
        "55" => "PACKET",
        "56" => "PAD",
        "57" => "Powder Effervescent",
        "58" => "PELLET",
        "59" => "POWDER",
        "60" => "PASTE",
        "61" => "Patch 24 Hour",
        "62" => "Patch 72 Hour",
        "63" => "Patch Twice Weekly",
        "64" => "PATCH WEEKLY",
        "65" => "PUDDING",
        "66" => "RING",
        "67" => "SHAMPOO",
        "68" => "SHEET",
        "69" => "Gel Forming Solution",
        "70" => "SOLUTION",
        "71" => "Solution Reconstituted",
        "72" => "SPIRIT",
        "73" => "STICK",
        "74" => "STRIP",
        "75" => "Tablet Sublingual",
        "76" => "SUPPOSITORY",
        "77" => "SUSPENSION",
        "78" => "Suspension Reconstituted",
        "79" => "SWAB",
        "80" => "SYRUP",
        "81" => "Tablet",
        "82" => "TAMPON",
        "83" => "TAPE",
        "84" => "TAR",
        "85" => "Tablet Extended Release 12 Hour",
        "86" => "Tablet Extended Release 24 Hour",
        "87" => "Tablet Extended Release",
        "88" => "Tablet Disintegrating",
        "89" => "Tablet Delayed Release (Obsolete)",
        "90" => "TABLET DELAYED RELEASE",
        "91" => "Tablet Effervescent",
        "92" => "TABLET SOLUBLE",
        "94" => "DIAGNOSTIC TEST",
        "95" => "TINCTURE",
        "96" => "TROCHE",
        "97" => "WAFER",
        "98" => "WAX",
        "100" => "PATCH",
        "106" => "CAPSULE ER 12 HOUR ABUSE-DETERRENT",
        "108" => "TABLET ABUSE-DETERRENT",
        "109" => "TABLET EXTENDED RELEASE ABUSE-DETERRENT",
        "110" => "TABLET ER 12 HOUR ABUSE-DETERRENT",
        "111" => "TABLET ER 24 HOUR ABUSE-DETERRENT",
        "112" => "THERAPY PACK",
        "113" => "CAPSULE THERAPY PACK",
        "117" => "CAPSULE ER 24 HOUR THERAPY PACK",
        "118" => "TABLET THERAPY PACK",
        "124" => "LIQUID THERAPY PACK",
        "125" => "SOLUTION THERAPY PACK",
        "127" => "PEN-INJECTOR",
        "128" => "SOLUTION PEN-INJECTOR",
        "129" => "SUSPENSION PEN-INJECTOR",
        "130" => "AUTO-INJECTOR",
        "131" => "SOLUTION AUTO-INJECTOR",
        "133" => "JET-INJECTOR",
        "134" => "SOLUTION JET-INJECTOR",
        "136" => "CARTRIDGE",
        "137" => "SOLUTION CARTRIDGE",
        "138" => "SUSPENSION CARTRIDGE",
        "139" => "PREFILLED SYRINGE",
        "140" => "SOLUTION PREFILLED SYRINGE",
        "141" => "SUSPENSION PREFILLED SYRINGE",
        "144" => "CAPSULE ER 24 HOUR SPRINKLE",
        "145" => "AUTO-INJECTOR KIT",
        "148" => "PEN-INJECTOR KIT",
        "149" => "PREFILLED SYRINGE KIT",
        "151" => "SUSPENSION EXTENDED RELEASE",
        "152" => "SUSPENSION RECONSTITUTED ER",
        "154" => "EXHALER POWDER",
        "157" => "EXHALER SUSPENSION",
        "158" => "TABLET DISINTEGRATING SOLUBLE",
        "160" => "Tablet Chewable Extended Release",
        "161" => "Capsule Delayed Release Sprinkle",
        "162" => "Tablet Delayed Release Disintegrating",
        "163" => "Tablet Extended Release Disintegrating",
        "164" => "Capsule Sprinkle Therapy Pack"
    ],

    'syncKeyValues' => [
        'patient' => [
            'CustomerId' => 'newleaf_customer_id',
            'FirstName' => 'first_name',
            'MiddleName' => 'middle_name',
            'LastName' => 'last_name',
            'Gender' => 'gender',
            'EmailAddress' => 'email',
            'FacilityId' => 'newleaf_facility_id',
            'DefaultDestinationType' => 'shipping_method',
            'IsActive' => 'patient_status',
            'is_active' => 'is_active',
            'DateOfBirth' => 'dob',
            'CustomerAddresses' => [
                'CustomerAddressId' => 'newleaf_customer_address_id',
                'CustomerId' => 'newleaf_customer_id',
                'AddressType' => 'address_type',
                'IsActive' => 'is_active',
                'Address1' => 'address_1',
                'Address2' => 'address_2',
                'City' => 'city',
                'State' => 'state',
                'ZipCode' => 'zipcode',
                'Country' => 'country',
                'IsPrimary' => 'is_primary',
                'Comment' => 'comment',
                'IsDeleted' => 'deleted_at',
                'CreatedOn' => 'created_at',
                'CreatedBy' => 'created_by',
                'UpdatedOn' => 'updated_at',
                'UpdatedBy' => 'updated_by',
            ]
        ],
        'prescriber' => [
            'PrescriberId' => 'prescriber_id',
            'FirstName' => 'first_name',
            'LastName' => 'last_name',
            'Email' => 'email',
            'PersonalPhoneNbr' => 'phone_number',
            'PrescriberType' => 'prescriber_type',
            'SpecialtyType' => 'speciality_type',
            'Identifier' => 'identifier',
            'ExternalIdentifier' => 'external_identifier',
            'DeaNbr' => 'dea_number',
            'PrescriberAddresses' => [
                'PrescriberAddressId' => 'prescriber_address_id',
                'PrescriberID' => 'prescriber_id',
                'PrescriberAddressType' => 'address_type',
                'IsActive' => 'is_active',
                'Address1' => 'address_1',
                'Address2' => 'address_2',
                'City' => 'city',
                'State' => 'state',
                'Zip' => 'zipcode',
                'Country' => 'country',
                'IsPrimary' => 'is_primary',
                'Comment' => 'comment',
                'IsDeleted' => 'deleted_at',
                'CreatedOn' => 'created_at',
                'CreatedBy' => 'created_by',
                'UpdatedOn' => 'updated_at',
                'UpdatedBy' => 'updated_by',
            ]
        ],
        'drugs' => [
            'DrugId' => 'newleaf_drug_id',
            'CreatedBy' => 'created_by',
            'CreatedOn' => 'created_on',
            'UpdatedBy' => 'updated_by',
            'UpdatedOn' => 'updated_on',
            'Identifier' => 'identifier',
            'Description' => 'description',
            'Strength' => 'strength',
            'NewNDC' => 'new_ndc',
            'ManufacturerName' => 'manufacturer_name',
            'IsGeneric' => 'is_generic',
            'IsRx' => 'is_rx',
            'StatusCode' => 'status_code',
            'DosageFormCode' => 'dosage_form_code',
            'DosageForm' => 'dosage_form',
            'DirectSource' => 'direct_source',
            'DirectSourceDesc' => 'direct_source_description',
            'MasterDescription' => 'master_description',
        ],
        'rxs' => [
            'RxId' => 'rx_id',
            'RxNumber' => 'rx_number',
            'CreatedOn' => 'created_on',
            'CreatedBy' => 'created_by',
            'UpdatedOn' => 'updated_on',
            'UpdatedBy' => 'updated_by',
            'Status' => 'status',
            'Origin' => 'origin',
            'DAWCode' => 'daw_code',
            'CustomerId' => 'customer_id',
            'PrescriberId' => 'prescriber_id',
            'PrescribedDrugId' => 'prescribed_drug_id',
            'OriginalQuantity' => 'original_quantity',
            'OwedQuantity' => 'owed_quantity',
            'RefillsAuthorized' => 'refills_authorized',
            'RefillsRemaining' => 'refills_remaining',
            'DateWritten' => 'date_written',
            'DateExpires' => 'date_expires',
            'DateInactivated' => 'date_inactivated',
            'OriginalSIG' => 'original_sig',
            'OriginalSIGExpanded' => 'original_sig_expanded',
            'OriginalDaysSupply' => 'original_days_supply',
            'IsVerified' => 'is_verified',
            'VerifiedQuantityDispensed' => 'verified_quantity_dispensed',
            'VerifiedMinDaysSupply' => 'verified_min_days_supply',
            'IsCancelled' => 'is_cancelled'
        ],

        'carekit' => [
            'HospiceCareKitId' => 'hospice_care_kit_id',
            'FacilityId' => 'facility_id',
            'Name' => 'name',
            'IsActive' => 'is_active',
            'CreatedOn' => 'createdOn',
            'CreatedBy' => 'createdBy',
            'UpdatedOn' => 'updatedOn',
            'UpdatedBy' => 'UpdatedBy'
        ],
        'care_kit_items' => [
            'HospiceCareKitItemId' => 'hospice_care_kit_item_id',
            'HospiceCareKitId' => 'hospice_care_kit_id',
            'DrugId' => 'drug_id',
            'Quantity' => 'quantity',
            'DaysSupply' => 'days_supply',
            'SIG' => 'sig',
            'CreatedOn' => 'CreatedOn',
            'CreatedBy' => 'CreatedBy',
            'UpdatedOn' => 'UpdatedOn',
            'UpdatedBy' => 'UpdatedBy',
        ],

        'refills' => [
            'RefillId' => 'refill_id',
            'CreatedBy' => 'created_by',
            'CreatedOn' => 'created_on',
            'UpdatedBy' => 'updated_by',
            'UpdatedOn' => 'updated_on',
            'RefillNumber' => 'refill_number',
            'RxId' => 'rx_id',
            'DrugId' => 'drug_id',
            'DestinationTypeId' => 'destination_type_id',
            'DestinationDate' => 'destination_date',
            'CustomerAddressId' => 'customer_address_id',
            'Status' => 'status',
            'FacilityAddressId' => 'facility_address_id',
            'PackageChoice' => 'package_choice',
            'DateFilled' => 'date_filled',
            'SIG' => 'sig',
            'SIGExpanded' => 'sig_expanded',
            'DestinationNotes' => 'destination_notes',
            'DispensedQuantity' => 'dispensed_quantity',
            'DaysSupply' => 'days_supply',
            'MinDaysSupply' => 'min_days_supply',
            'MaxDaysSupply' => 'max_days_supply',
            'NumberOfPieces' => 'number_of_pieces',
            'RPHUserName' => 'rph_user_name',
            'RPHUserId' => 'rph_user_id',
            'IsOrdered' => 'is_ordered',
            'IsDispensed' => 'is_dispensed',
            'IsPrefill' => 'is_prefill',
            'DiscardAfterDate' => 'discard_after_date',
            'WorkflowStatus' => 'workflow_status',
            'NumberOfLabels' => 'number_of_labels   ',
            'DosesPerDay' => 'doses_per_day',
            'UnitsPerDose' => 'units_per_dose',
            'DestinationAddress1' => 'destination_address1',
            'DestinationAddress2' => 'destination_address2',
            'DestinationCity' => 'destination_city',
            'DestinationState' => 'destination_state',
            'DestinationZip' => 'destination_zip',
            'EffectiveDate' => 'effective_date',
            'PrescriberAddressId' => 'prescriber_address_id',

            /*'RefillAdjudications' => [
                'RefillAdjudicationId' => 'refill_adjudication_id',
                'UpdatedBy' => 'updated_by',
                'UpdatedOn' => 'updated_on',
                'RefillPlanOrder' => 'refill_plan_order',
                'ThirdPartyId' => 'third_party_id',
                'AdjudicationType' => 'adjudication_type    ',
                'PrintCopies' => 'print_copies',
                'PrintMonograph' => 'print_monograph',
                'WorkstationName' => 'workstation_name',
                'RefillAdjudicationStatus' => 'refill_adjudication_status',
                'RefillId' => 'refill_id',
                'CustomerId' => 'customer_id',
                'ClaimData' => 'claim_data',
                'CustomerARAccountId' => 'customer_ar_account_id',
                'ResetAging' => 'reset_aging',
            ],*/

            'RefillShipments' => [
                'RefillShipmentId' => 'refill_shipment_id',
                'CreatedBy' => 'created_by',
                'CreatedOn' => 'created_on',
                'UpdatedBy' => 'updated_by',
                'UpdatedOn' => 'updated_on',
                'RefillNumber' => 'refill_number',
                'RxId' => 'rx_id',
                'DrugId' => 'drug_id',
                'DestinationTypeId' => 'destination_type_id',
                'DestinationDate' => 'destination_date',
                'CustomerAddressId' => 'customer_address_id',
                'Status' => 'status',
                'FacilityAddressId' => 'facility_address_id',
                'PackageChoice' => 'package_choice',
                'DateFilled' => 'date_filled',
                'SIG' => 'sig',
                'SIGExpanded' => 'sig_expanded',
                'DestinationNotes' => 'destination_notes',
                'DispensedQuantity' => 'dispensed_quantity',
                'DaysSupply' => 'days_supply',
                'MinDaysSupply' => 'min_days_supply',
                'MaxDaysSupply' => 'max_days_supply',
                'NumberOfPieces' => 'number_of_pieces',
                'RPHUserName' => 'rph_user_name',
                'RPHUserId' => 'rph_user_id',
                'IsOrdered' => 'is_ordered',
                'IsDispensed' => 'is_dispensed',
                'IsPrefill' => 'is_prefill',
                'DiscardAfterDate' => 'discard_after_date',
                'WorkflowStatus' => 'workflow_status',
                'NumberOfLabels' => 'number_of_labels',
                'DosesPerDay' => 'doses_per_day',
                'UnitsPerDose' => 'units_per_dose',
                'DestinationAddress1' => 'destination_address1',
                'DestinationAddress2' => 'destination_address2',
                'DestinationCity' => 'destination_city',
                'DestinationState' => 'destination_state',
                'DestinationZip' => 'destination_zip',
                'EffectiveDate' => 'effective_date',
                'PrescriberAddressId' => 'prescriber_address_id',

                'RefillAdjudications' => [
                    'RefillAdjudicationId' => 'refill_adjudication_id',
                    'UpdatedBy' => 'updated_by',
                    'UpdatedOn' => 'updated_on',
                    'RefillPlanOrder' => 'refill_plan_order',
                    'ThirdPartyId' => 'third_party_id',
                    'AdjudicationType' => 'adjudication_type    ',
                    'PrintCopies' => 'print_copies',
                    'PrintMonograph' => 'print_monograph',
                    'WorkstationName' => 'workstation_name',
                    'RefillAdjudicationStatus' => 'refill_adjudication_status',
                    'RefillId' => 'refill_id',
                    'CustomerId' => 'customer_id',
                    'ClaimData' => 'claim_data',
                    'CustomerARAccountId' => 'customer_ar_account_id',
                    'ResetAging' => 'reset_aging',
                ],

                'RefillShipments' => [
                    'RefillShipmentId' => 'refill_shipment_id',
                    'CreatedBy' => 'created_by',
                    'CreatedOn' => 'created_on',
                    'UpdatedBy' => 'updated_by',
                    'UpdatedOn' => 'updated_on',
                    'Type' => 'type',
                    'SaturdayDelivery' => 'saturday_delivery',
                    'RequireSignature' => 'require_signature    ',
                    'Insurance' => 'insurance',
                    'SignatureType' => 'signature_type',
                    'RefillId' => 'refill_id',
                    'EnterpriseOrderId' => 'enterprise_order_id',
                    'Courier' => 'courier',
                    'TrackingNumber' => 'tracking_number',
                    'RecipientNumber' => 'recipient_number',
                    'NoOfItems' => 'no_of_items',
                    'ShipmentStatus' => 'shipment_status',
                    'SuccessfullySubmitted' => 'successfully_submitted',
                    'ErrorMessage' => 'error_message',
                    'IsTrackable' => 'is_trackable',
                    'Weight' => 'weight',
                    'InsuranceAmount' => 'insurance_amount',
                    'CountryOfManufacture' => 'country_of_manufacture',
                    'CustomsDescription' => 'customs_description',
                    'LabelLocation' => 'label_location',
                    'IsThermalLabel' => 'is_thermal_label',
                    'TrackingUpdateBatchId' => 'tracking_update_batch_id',
                    'FedexScanEventCode' => 'fedex_scan_event_code',
                    'ShippedOn' => 'shipped_on',
                    'IsDeliveredByApi' => 'is_delivered_by_api',
                    'RemoteFillOrderId' => 'remote_fill_order_id',
                    'InternalOrderNum' => 'internal_order_num',
                    'Height' => 'height',
                    'Length' => 'length',
                    'Width' => 'width',
                    'RequirePhotold' => 'require_photo_id',
                    'PackagingType' => 'packaging_type',
                    'WeightUnits' => 'weight_units',
                    'ShippingFee' => 'shipping_fee',

                ],

            ],

        ],

    ],
    'shipping_methods' => [
        "FD2" => "Second Day",
        "FD1" => "Next Day",
        "RSD" => "SameDay",
        "FDS" => "Saturday",
        "ES2" => "STAT 2",
        "RST" => "STAT 4",
        "RPH" => 'After hour RPH',
        "AHS" => 'After hours Stat',  
        "rover" => "Rover",
        "uds_saturday" => "UDS Saturday",
        "uds_monday_friday" => "UDS Monday to Friday",
        "ups_ground" => "UPS Ground",
        "ups_next_day" => "UPS Next Day"
    ],
    'ups_config' => [
        'endPointUrl' => 'https://onlinetools.ups.com/ups.app/xml/TimeInTransit',
        'AccessLicenseNumber' => '4D63055C8FB864AA',
        'UserId' => 'T.Wilhelm1',
        'Password' => 'Welcome1'
    ]

];
