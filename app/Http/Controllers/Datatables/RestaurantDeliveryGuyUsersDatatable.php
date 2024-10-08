<?php

namespace App\Http\Controllers\Datatables;

use App\User;
use Auth;
use Nwidart\Modules\Facades\Module;
use Yajra\DataTables\DataTables;

class RestaurantDeliveryGuyUsersDatatable
{
    /**
     * @return mixed
     */
    public function RestaurantDeliveryGuyUsersDatatable()
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        // Fetch the users who are Delivery Guys associated with the specified restaurantIds
        $users = User::role('Delivery Guy')->with('wallet')
            ->whereIn('id', function ($query) use ($restaurantIds) {
                $query->select('user_id')
                    ->from('restaurant_user')
                    ->whereIn('restaurant_id', $restaurantIds);
            })->get(); 

        return Datatables::of($users)
            ->addColumn('role', function ($user) {
                return '<span class="badge badge-flat border-grey-800 text-default text-capitalize">' . implode(',', $user->roles->pluck('name')->toArray()) . '</span>';
            })
            ->addColumn('wallet', function ($user) {
                return config('setting.currencyFormat') . $user->wallet->balance;
            })
            ->editColumn('email', function ($user) {
                return '<span class="small">' . $user->email . '</span>';
            })
            ->editColumn('phone', function ($user) {
                return '<span class="small">' . $user->phone . '</span>';
            })
            ->addColumn('status', function ($user) {
                if ($user->delivery_guy_detail && $user->delivery_guy_detail->status) {
                    return '<span class="badge badge-success text-white">Online</span>';
                } else {
                    return '<span class="badge badge-danger text-white">Offline</span>';
                }
            })
            ->editColumn('created_at', function ($user) {
                return '<span class="small" data-popup="tooltip" data-placement="left" title="' . $user->created_at->diffForHumans() . '">' . $user->created_at->format('Y-m-d - h:i A') . '</span>';
            })
            ->addColumn('action', function ($user) {
                return '<div class="btn-group btn-group-justified"> <a href="'.route('store.get.manageDeliveryGuysRestaurants', $user->id).'" class="btn btn-sm btn-secondary mr-2"> Manage Delivery Stores</a> <a href="'.route('store.get.editUser', $user->id).'" class="btn btn-sm btn-primary"> View</a> </div>';
            })
            ->rawColumns(['role', 'action', 'created_at', 'email', 'phone', 'status']) // Include 'status' in rawColumns to prevent escaping HTML
            ->make(true);
    }
}
