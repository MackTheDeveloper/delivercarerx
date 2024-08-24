<?php

namespace App\Service;

use App\Models\Hospice;
use App\Models\EmailTemplates;
use App\Models\EmailTemplatesCc;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use App\Repository\EmailTemplatesRepository;
use App\Repository\ActivityRepository;
use Hash;
use Auth;


class EmailTemplateService
{

    protected $hospiceRepo, $userRepo, $emailTempRepo,$activityRepo;

    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     * @param UserRepository $userRepo reference to userRepo
     * @param ActivityRepository $activityRepo reference to activityRepo
     * 
     */
    public function __construct(HospiceRepository $hospiceRepo, UserRepository $userRepo, EmailTemplatesRepository $emailTempRepo,ActivityRepository $activityRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->emailTempRepo = $emailTempRepo;
        $this->activityRepo = $activityRepo;
    }


    /** 
     * Add email temp information
     * @param object $request
     */

    public function addInformation($request)
    {
        $data = $request->all();

        
        try {
            $response = $this->emailTempRepo->create($data);

            $cc = $request->cc;
            
            if(!empty($cc))
            {
                for($count = 0; $count < count($cc); $count++)
                {
                    $cc_email = array(
                        'template_id'    => $response->id,
                        'email_cc'   => $cc[$count]
                    );
                    $cc_email_data[] = $cc_email;
                }

                EmailTemplatesCc::insert($cc_email_data);  
            }
            
                    // Save activity for new email-template added
                // $activityData['module_name'] = config('app.activityModules')["Email-Template"];
                // $activityData['performed_by'] = Auth::user()->id;
                // $activityData['description'] = str_replace('{PARAM}',$data['title'], config('app.activityDescriptions')['Added']);
                // $this->activityRepo->create($activityData);


            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }


    
    /** 
     * Fetch email temp information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->emailTempRepo->fetch($id);
    }


    /** 
     * Add hospice information
     * @param object $request
     */
    public function fetchListing($request)
    {
        
        $req = $request->all();
       $start = $req['start'];
       $length = $req['length'];
       $search = $req['search']['value'];
       $order = $req['order'][0]['dir'];
       $column = $req['order'][0]['column'];
      
        $orderby = ['title', 'slug', 'subject', 'is_active', 'created_at'];


        $total = EmailTemplates::selectRaw('count(*) as total')->where('is_active',1)->first();
        $query = EmailTemplates::selectRaw('email_templates.*')->where('is_active',1);
        $filteredq = EmailTemplates::selectRaw('email_templates.*')->where('is_active',1);
        
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->Where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    
                    ->orWhere('subject', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->Where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    
                    ->orWhere('subject', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();

        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'email_template_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'email_template_delete');
        foreach ($query as $key => $value) {

            $action = '';
            $editUrl = route('admin.email-template-edit', encrypt($value->id));

        //     $action = '<div class="dropdown">
        //   <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
        //   <div class="dropdown-menu dropdown-menu-right">
        //     <a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>
        //     <a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>
        //   </div>
        // </div>';
        $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';

            $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>' . $actionInner . '</div>';
            $status = $value->is_active == 1 ? 'Active' : 'Inactive';
            $statusClass = $value->is_active == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
        <span>' . $status . '</span>';
            
            $data[] = [$value->title, $value->slug, $value->subject ,  $statusHtml, getFormatedDate($value->created_at, 'm/d/Y'), $action];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }


    public function updateInformation($request, $id)
    {
        $data = $request->except(['_token', '_method','cc']);
        
        
            try {
            
            $response = $this->emailTempRepo->update($data, $id);

            $cc = $request->cc;
            
            if(!empty($cc))
            {
                for($count = 0; $count < count($cc); $count++)
                {
                    $cc_email = array(
                        'template_id'    => $id,
                        'email_cc'   => $cc[$count]
                    );
                    $cc_email_data[] = $cc_email;
                }  
            }      
            
            EmailTemplatesCc::where('template_id',$id)->delete();

            if(!empty($cc))
            {
                EmailTemplatesCc::insert($cc_email_data); 
            }


                    // Save activity for email-template updated
            // $activityData['module_name'] = config('app.activityModules')["Email-Template"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$data['title'], config('app.activityDescriptions')['Updated']);
            // $this->activityRepo->create($activityData);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /** 
     * Delete email-template
     * @param object $request
     */
    public function delete($request)
    {   
        try {
            $emailTempData = $this->emailTempRepo->fetch($request->id);
            

            // Save activity for email-template deleted
            // $activityData['module_name'] = config('app.activityModules')["Email-Template"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$emailTempData->title, config('app.activityDescriptions')['Deleted']);
            // $this->activityRepo->create($activityData);


            // Delete facility
            $this->emailTempRepo->delete($request->id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }
}
