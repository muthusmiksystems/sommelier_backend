<?php

namespace App\Http\Controllers\Datatables;

use App\User;
use Yajra\DataTables\DataTables;
use Auth;
class RestaurantManagersDatatable
{
    /**
     * @return mixed
     */
    public function RestaurantManagersDatatable()
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

       
            $users = User::notRole(['Admin', 'Delivery Guy', 'Store Owner', 'Customer'])->with('wallet')
            ->whereIn('id', function ($query) use ($restaurantIds) {
                $query->select('user_id')
                    ->from('restaurant_user')
                    ->whereIn('restaurant_id', $restaurantIds);
            })->get(); 

        

        return Datatables::of($users)
            ->addColumn('role', function ($user) {
                return '<span class="badge badge-flat border-grey-800 text-default text-capitalize">'.implode(',', $user->roles->pluck('name')->toArray()).'</span>';
            })
            ->editColumn('email', function ($user) {
                return '<span class="small">'.$user->email.'</span>';
            })
            ->editColumn('phone', function ($user) {
                return '<span class="small">'.$user->phone.'</span>';
            })
            ->editColumn('created_at', function ($user) {
                return '<span class="small" data-popup="tooltip" data-placement="left" title="'.$user->created_at->diffForHumans().'">'.$user->created_at->format('Y-m-d - h:i A').'</span>';
            })
            ->addColumn('action', function ($user) {
                $html = '<a href="'.route('store.get.editUser', $user->id).'" class="btn btn-sm btn-primary"> View</a>';

                return $html;
            })
            ->rawColumns(['role', 'action', 'created_at', 'email', 'phone'])
            ->make(true);
    }
}
