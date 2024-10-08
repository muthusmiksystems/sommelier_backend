<?php

namespace App\Http\Controllers\Datatables;
use Auth;
use App\User;
use Yajra\DataTables\DataTables;

class RestaurantStoreOwnerUsersDatatable
{
    public function RestaurantStoreOwnerUsersDatatable()
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

       
            $users = User::role('Store Owner')->with('roles', 'wallet')
            ->whereIn('id', function ($query) use ($restaurantIds) {
                $query->select('user_id')
                    ->from('restaurant_user')
                    ->whereIn('restaurant_id', $restaurantIds);
            })->get(); 
        return Datatables::of($users)
            ->editColumn('created_at', function ($user) {
                return '<span data-popup="tooltip" data-placement="left" title="'.$user->created_at->diffForHumans().'">'.$user->created_at->format('Y-m-d - h:i A').'</span>';
            })
            ->addColumn('action', function ($user) {
                return '<div class="btn-group btn-group-justified"> <a href="'.route('store.get.getManageRestaurantOwnersRestaurants', $user->id).'" class="btn btn-sm btn-secondary mr-2"> Manage Owner\'s Stores</a> <a href="'.route('store.get.editUser', $user->id).'" class="btn btn-sm btn-primary mr-2"> View</a> </div>';
            })
            ->rawColumns(['role', 'action', 'created_at'])
            ->make(true);
    }
}
