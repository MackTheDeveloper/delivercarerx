<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Service\AdminService;
use App\Service\ActivityService;
use App\Service\ShippingService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

class ShippingController extends Controller
{
    protected $shippingService, $activityServie;

    /**
     * constructor for initialize Admin service
     *
     * @param ShippingService $shippingService reference to shippingService
     * 
     */
    public function __construct(ShippingService $shippingService, ActivityService $activityServie)
    {

    $this->shippingService = $shippingService;
    $this->activityServie = $activityServie;
    /**
     * Show the form for login
     *
     * @param  Request $request
     * @return Response
     */
    }

    public function add(Request $request)
    {
        return view('admin.shipping.shipping-add');
    }

    /**
     * Listing of the shipping
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.shipping.shipping-list');
    }

    public function list(Request $request)
    {
        $result = $this->shippingService->fetchListing($request);
        return Response::json($result);
    }

    /**
     * store the shipping information
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $result = $this->shippingService->addInformation($request);

         // Save activity for new hospice added
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$request->name];
        $this->activityServie->logs('added', config('app.activityModules')["Shipping"], '', config('app.activityModules')["Shipping"], $keyForAddOperation, $valueForAddOperation);

        if ($result == 'success') {
            $notification = array(
                'message' => config('message.shippingMgt.created'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * Edit form for shipping information
     *
     * @param  Request $request
     * @param  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {

        try {
            $model = $this->shippingService->fetchInformation($id);
            if ($model) {
                return view('admin.shipping.shipping-edit',compact('model'));
            } else {
                abort('404');
            }
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * update the shipping information
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
         // Save activities for updated information
        $model = $this->shippingService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Shipping"], $model, '', '', '');

        $result = $this->shippingService->updateInformation($request, $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.shippingMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('shipping-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * delete the shipping
     *
     * @param  Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
          // Save activity for deleted
        $shippingData = $this->shippingService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$shippingData->name];
        $this->activityServie->logs('deleted', config('app.activityModules')["Shipping"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->shippingService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.shippingMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
