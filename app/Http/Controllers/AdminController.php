<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\OptumSSO;
use App\Models\User;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\BranchService;
use App\Service\PatientService;
use App\Service\RefillsInQueueService;
use App\Service\AdminServie;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
use File;
use Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Service\CityService;
use App\Service\CountryService;
use App\Service\StateService;
use App\Service\UserService;

// Used for optum sso
use App\Http\Controllers\OpenIDConnectClient;

class AdminController extends Controller
{
    
    protected $adminService, $countryService, $stateService, $cityService, $activityService, $userService, $branchService, $patientService, $refillsInQueueService, $OpenIDConnectClient;

    /**
     * constructor for initialize Admin service
     *
     * @param AdminService $adminService reference to AdminService
     * @param ActivityService $activityService reference to activityService
     *
     */
    public function __construct(AdminService $adminService, CountryService $countryService, StateService $stateService, CityService $cityService, ActivityService $activityService, UserService $userService, BranchService $branchService, PatientService $patientService, RefillsInQueueService $refillsInQueueService)
    {
        $this->adminService = $adminService;
        $this->activityService = $activityService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->userService = $userService;
        $this->branchService = $branchService;
        $this->patientService = $patientService;
        $this->refillsInQueueService = $refillsInQueueService;
    }

    public static function index($slug)
    {
        return view('pages.' . $slug);
    }

    /**
     * Show the form for login
     *
     * @param array $data
     * @return Response
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function profile(Request $request)
    {
        if (Auth::check()) {
            $profile = User::find(Auth::user()->id);
            $countries = $this->countryService->getCountryList();
            $states = $this->stateService->getStateList($profile->country_id);
            $cities = $this->cityService->getCityList($profile->state_id);
            return view('pages.profile', compact('profile', 'countries', 'states', 'cities'));
        }

        return view('admin.login');
    }

    public function updateProfile(Request $request)
    {

        try {
            $id = Auth::user()->id;
            $user = User::find($id);  // Find the user using model and hold its reference
            // $this->validate($request, [
            //     'file' => 'bail|image|mimes:jpeg,png,jpg,gif,svg|max:800',
            //     'first_name'=>'bail|required',
            //     'email'=>'bail|required',
            //     'phone' => 'bail|required|min:10|max:10',
            //     'address1' => 'bail|required',
            //     'country_id' => 'bail|required',
            //     'state_id' => 'bail|required',
            //     'city_id' => 'bail|required',
            //     'zipcode' => 'bail|required'
            // ]);

            // Save activities for updated information
            $model = $this->userService->fetchUserInformation($id);
            $model->fill($request->all());
            $this->activityService->logs('updated', config('app.activityModules')["Profile"], $model, '', '', '');

            // $activityData['module_name'] = config('app.activityModules')["Profile"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}','User '. $user->id. ' Profile', config('app.activityDescriptions')['Profile']['Updated']);
            // $this->activityService->addInformation($activityData);

            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/assets/upload/profile-pic');
                $image->move($destinationPath, $name);
                $user->profile_picture = $name;
            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address1 = $request->address1;
            $user->address2 = $request->address2;
            $user->country_id = $request->country_id;
            $user->state_id = $request->state_id;
            $user->city_id = $request->city_id;
            $user->zipcode = $request->zipcode;

            if ($user->save()) {
                $notification = array(
                    'message' => config('message.AuthMessages.profileSuccess'),
                    'alert-type' => 'success'
                );


                return redirect()->route('admin.profile')->with($notification);
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public function changePassword(Request $request)
    {
        //return $request;
        try {
            $this->validate($request, [

                'password' => 'bail|required',
                'new_password' => 'bail|required',
                'password_confirmation' => 'bail|required|same:new_password'
            ]);
            $id = Auth::user()->id;
            $user = User::find($id);  // Find the user using model and hold its reference


            if (password_verify($request->password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            }
            if ($user->save()) {
                $notification = array(
                    'message' => config('message.AuthMessages.passwordResetSuccess'),
                    'alert-type' => 'success'
                );
                // Save activity for user login
                $activityData['module_name'] = config('app.activityModules')["Profile"];
                $activityData['performed_by'] = Auth::user()->id;
                $activityData['description'] = str_replace('{PARAM}', 'User ' . $user->id . ' Password', config('app.activityDescriptions')['Profile']['Changed']);
                $this->activityService->addInformation($activityData);
                return redirect()->route('admin.profile')->with($notification);
            }
        } catch (Exception $e) {
            return $e;
        }


    }

    /**
     * Log the user In the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember_me = $request->has('remember') ? true : false;

        if (auth()->attempt($credentials, $remember_me)) {
            $user = User::where(["email" => $credentials['email']])->first();
            //Auth::login($user, $remember_me);

            // Save activity for user login
            $activityData['module_name'] = config('app.activityModules')["Login"]; 
            $activityData['performed_by'] = Auth::user()->id; 
            $activityData['description'] = config('app.activityDescriptions')['User_Login'];
            $this->activityService->addInformation($activityData);

            $notification = array(
                'message' => config('message.AuthMessages.loginSuccess'),
                'alert-type' => 'success'
            );

            //if($credentials['email'] != "michelletest@delivercarerx.com" && Auth::user()->hospice_user_role == 3 || $credentials['email'] != "michelletest@delivercarerx.com" && Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1 || $credentials['email'] != "michelletest@delivercarerx.com" &&  Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)

           if(Auth::user()->hospice_user_role == 3 || Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1 || Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
            {
                return redirect()->route('nursePatients-list')->with($notification);
            //} else if($user['email'] == "michelletest@delivercarerx.com"){
              //  return redirect()->route('nursePatients-list-found')->with($notification);
            }else{
                return redirect()->route('admin.dashboard')->with($notification);
            }

        } else {
            $notification = array(
                'message' => config('message.AuthMessages.InvalidCredentials'),
                'alert-type' => 'error'
            );
            return redirect(config('app.adminPrefix') . 'login')->with($notification);
        }
    }

    /**
     * Handle an authentication attempt for Optum SSO
     *
     * @return Response
     */
    public function loginWithToken()
    {
        try {
            /* Fetch User name, User Full Name, User Email, & Carrier Code */
            $Authority = 'https://ohsssouat.optum.com'; 
            $ClientId = 'DC';
            $ClientSecret = 'secret';
            $response_types = 'code';
            $response_mode = 'form_post';
            $UsePkce = true;
            $SaveTokens = true;
            $scope = 'api1, openid, profile';

            $redirectURL = 'https://dev2-portal.delivercarerx.com/securedlccontrol/openId';
            $token = '';
        
            $oidc = new OpenIDConnectClient($Authority, $ClientId, $ClientSecret);
            $oidc->setRedirectURL($redirectURL);
            $oidc->setClientID('DC');
            $oidc->setClientSecret('secret'); 
            $oidc->setCodeChallengeMethod('S256');
            $oidc->authenticate(); 

            $accessToken = $oidc->getAccessToken();
            // save id token for later logout:
            $idToken = $oidc->getIdToken();
            $claims = $oidc->getVerifiedClaims();
            $certPath = $oidc->getCertPath(); 
            $verifyHost = $oidc->getVerifyHost(); 
            $verifyPeer = $oidc->getVerifyPeer(); 
            $clientID = $oidc->getClientID(); 
            $clientSecret = $oidc->getClientSecret(); 


            $name = $oidc->requestUserInfo('given_name'); 
            $email = $oidc->requestUserInfo('Email');
            $role = $oidc->requestUserInfo('role');  //Must equal DC:Default
            $roleClient = $oidc->requestUserInfo('RoleClient');  //Must equal DC
            $roleCode = $oidc->requestUserInfo('RoleCode');  //Must equal DC:2000

            $code = $_GET['code'];
            
            // Optum: The following claims will need to be checked to verify the user has authorized access to your application:
            if(in_array('DC:Default', $role) && in_array('DC', $roleClient) && in_array('DC:2000', $roleCode)){
                // Verify that email address matches what we have in the db.    
                $authInDb = User::select('email','name','role_id','user_type','hospice_user_role','is_active','password','id', 'branch_id')
                ->where('email', $email)
                ->first();
    
                if(isset($authInDb)){
                    $emailAuth = $authInDb->email;
                    $password = $authInDb->password;
                    $name = $authInDb->name;
                    //$performedBy = $authInDb->performed_by; dd($performedBy);
                    $userId = $authInDb->id;
    
                    // The user is active, and the email isn't empty redirect the user to the openId route that executes the login Optum().
                    if( $authInDb['is_active'] == '1' && $authInDb['email'] != '' && $authInDb['branch_id'] != null) {
                        // Add idToken from Optum to Optum_sso table.
                        DB::table('Optum_sso')->insert([
                            'email' => $emailAuth,
                            'user' => $name,
                            'token' => $idToken,
                            'sessionid' => $userId
                        ]);
    
                        $_SESSION['email'] = $authInDb->email; 
                        $_SESSION['password'] = $authInDb->password;
                        $_SESSION['branch_id'] = $authInDb->branch_id;   
                        
                        return redirect()->route('openIdNew', ['email' => $email, 'password' => $password, 'idToken' => $idToken]);
                    } else  {
                        $_SESSION['email'] = $authInDb->email;
                        $_SESSION['password'] = $authInDb->password;
                        $_SESSION['name'] = $authInDb->name;
    
                        return view('openId');
                    }  
                } else {
                    // If not found in our DB, set session email & name to optum data.
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $name;
                    return view('openId');
                }
    
                
            } else {
                return view('openId')->with($name);
            }            
        } catch (Exception $e) {
            abort('404');
        }


    }

    /* Redirect Optum user to the nurse path once verified. */
    public function loginOptum(Request $request){
        $input = $request->all(); 
        $email = $input['email']; 
        $idToken = $input['idToken']; 


        $user = User::where('email', $email)->first();

        Auth::login($user);
            if(Auth::check()){
                $notification = array(
                    'message' => config('message.AuthMessages.loginSuccess'),
                    'alert-type' => 'success'
                );

                $optumUser = DB::table('Optum_sso')
                ->select('user', 'id', 'email','token','used_at','sessionid')
                ->where('email', '=', $email)
                ->latest('used_at')->first();

                return redirect()
                ->route('nursePatients-list',['email' => $email, 'idToken' => $idToken])
                ->with($notification);
            }
    }

    /**
     * Log the user Out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        $url = parse_url($_SERVER['HTTP_REFERER']); 
        $token = '';
        $email = '';
        if(isset($url['query'])){
            if (str_contains($url['query'], 'idToken')) {
                // Save activity for user logout
                $activityData['module_name'] = config('app.activityModules')["Logout"];
                $activityData['performed_by'] = Auth::user()->id;
                $activityData['description'] = config('app.activityDescriptions')['User_Logout'];
                $this->activityService->addInformation($activityData);

                $userId = $activityData['performed_by'];

                $optumUser = DB::table('Optum_sso')
                ->select('user', 'id', 'email','token','used_at','sessionid')
                ->where('sessionid', '=', $userId)
                ->whereDate('used_at', date('Y-m-d'))
                ->latest('used_at')->first();
                echo "inside of query";
    
                if(isset($token)){
                    $token = $optumUser->token;
    
                    $Authority = 'https://ohsssouat.optum.com'; 
                    $ClientId = 'DC';
                    $ClientSecret = 'secret';
                    $redirect = 'https://beta.hospicepharmacy.optum.com';
            
                    $idToken = $token;
    
                    $oidc = new OpenIDConnectClient($Authority, $ClientId, $ClientSecret);
                    $oidc->setRedirectURL($redirect);
                    $oidc->signOut($idToken, $redirect);
    
                }
                Auth::logout();
                $notification = array(
                    'message' => config('message.AuthMessages.logoutSuccess'),
                    'alert-type' => 'success'
                );
                
                // forget session
                Session::forget('cart_master_id');
            }else{
            //Has no query params

                Auth::logout();
                $notification = array(
                    'message' => config('message.AuthMessages.logoutSuccess'),
                    'alert-type' => 'success'
                );
                
                // forget session
                Session::forget('cart_master_id');

                return redirect(config('app.adminPrefix') . 'login')->with($notification);
            }
        } else {

            // Save activity for user logout
            $activityData['module_name'] = config('app.activityModules')["Logout"];
            $activityData['performed_by'] = Auth::user()->id;
            $activityData['description'] = config('app.activityDescriptions')['User_Logout'];
            $this->activityService->addInformation($activityData);

            $userId = $activityData['performed_by'];            
            $token = '';

            $optumUser = DB::table('Optum_sso')
            ->select('user', 'id', 'email','token','used_at','sessionid')
            ->where('sessionid', '=', $userId)
            ->whereDate('used_at', date('Y-m-d'))
            ->latest('used_at')->first();

            if(isset($optumUser)){
                Auth::logout();
                $notification = array(
                    'message' => config('message.AuthMessages.logoutSuccess'),
                    'alert-type' => 'success'
                );
                
                
                // forget session
                Session::forget('cart_master_id');

                $token = $optumUser->token;

                $Authority = 'https://ohsssouat.optum.com'; 
                $ClientId = 'DC';
                $ClientSecret = 'secret';
                $redirect = 'https://beta.hospicepharmacy.optum.com';
        
                $idToken = $token;

                $oidc = new OpenIDConnectClient($Authority, $ClientId, $ClientSecret);
                $oidc->setRedirectURL($redirect);
                $oidc->signOut($idToken, $redirect); 
            } else {                                            
                Auth::logout();
                $notification = array(
                    'message' => config('message.AuthMessages.logoutSuccess'),
                    'alert-type' => 'success'
                );
                
                // forget session
                Session::forget('cart_master_id');

                return redirect(config('app.adminPrefix') . 'login')->with($notification);  
            }
        }
    }

    /**
     * Admin dashboard
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {

        return view('admin/dashboard');
    }

    /**
     * Show the form for forgot password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function showForgotPassForm()
    {
        return view('admin.forgot-password');
    }


    /**
     * Handle the forgot password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $result = $this->adminService->verifyEmail($request->all());
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.AuthMessages.resetPassword'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => config('message.AuthMessages.EmailNotFound'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * Show the form for reset password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function showResetPassForm(Request $request)
    {
        $result = $this->adminService->verifyToken($request['token']);
        if ($result == 'invalid-token-error') {
            $notification = array(
                'message' => config('message.AuthMessages.InvalidToken'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        } else if ($result == 'expired-token-error') {
            $notification = array(
                'message' => config('message.AuthMessages.tokenExpired'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        } else {
            $token = $request['token'];
            return view('admin.reset-password', compact('token'));
        }
    }

    /**
     * Handle the reset password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $token = $request['token'];
        $result = $this->adminService->resetPassword($request->all());
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.AuthMessages.passwordResetSuccess'),
                'alert-type' => 'success'
            );
            return redirect()->route('login')->with($notification);
        } elseif ($result == 'error') {
            $notification = array(
                'message' => config('message.AuthMessages.newPasswordSameAsOldPassword'),
                'alert-type' => 'error'
            );
            return redirect()->route('show-reset-password', $token)->with($notification);
        } else {
            $notification = array(
                'message' => config('message.AuthMessages.InvalidToken'),
                'alert-type' => 'error'
            );
            return redirect()->route('show-reset-password', $token)->with($notification);
        }
    }

    /**
     * Show the form for reset password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function showSetPassForm($token)
    {
        $id = decrypt($token);
        $model = User::find($id);
        if (!empty($model->password)) {
            $notification = array(
                'message' => config('Token Expired!!'),
                'alert-type' => 'error'
            );
            return redirect()->route('login')->with($notification);
        }
        return view('admin.set-password', compact('id'));
    }

    /**
     * Handle the reset password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setPassword(Request $request)
    {
        $result = $this->adminService->setPassword($request->all());
        if ($result == 'success') {
            $notification = array(
                'message' => config('New Password Set Successfully'),
                'alert-type' => 'success'
            );
            return redirect()->route('login')->with($notification);
        } elseif ($result == 'user-error') {
            $notification = array(
                'message' => config('Error While Setting Up Credentials'),
                'alert-type' => 'error'
            );
            return redirect()->route('show-set-password')->with($notification);
        } else {
            $notification = array(
                'message' => config('Error While Setting Up Credentials'),
                'alert-type' => 'error'
            );
            return redirect()->route('show-set-password')->with($notification);
        }
    }
}
