<?php

use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\PermissionUser;
use App\Models\Cart;


/**
 * This function is use to checked role based permissions
 * @param array $role_type
 * @param $permission_slug
 * @return bool
 */
function whoCanCheck($role_type = array(), $permission_slug)
{
    if (Auth::user()->role_id == 1) {
        return true;
    } else {
        $role_id = Auth::user()->role_id;
        $permission = Permission::getIdBySlug($permission_slug);
        if ($permission) {
            $hasAccess = false;
            $rolePermission = PermissionRole::where('role_id', $role_id)->where('permission_id', $permission['id'])->first();
            if ($rolePermission) {
                $hasAccess = true;
            }

            if ($hasAccess) {
                return true;
            } else {
                return false;
            }
        }
    }
}

function checkCartActive()
{

   // Retrieve a piece of data from the session...
    $cart_master_id = session('cart_master_id');

    // if session is not set
    if(empty($cart_master_id))
    {
        // redirect to customer shopping address page
        return false;
    }
    else
    {
        // fetch all cart prescriptions
        $cartItems = array();
        $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray();

        if(empty($cartData))
        {
            return false;
        }
        return true;
    }
}
