<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class EagleViewController extends Controller
{
    public function deliveryEagleView(\Illuminate\Http\Request $request): View
    {
        $url = $request->getSchemeAndHttpHost().'/delivery-google-services.json';
        $data = \Ixudra\Curl\Facades\Curl::to($url)->asJson()->get();
        if ($data) {
            if (session()->has('selectedZone')) {
                $deliveryUserIds = \App\User::role('Delivery Guy')->where('zone_id', session('selectedZone'))->get()->pluck('id')->toArray();
            } else {
                $deliveryUserIds = \App\User::role('Delivery Guy')->get()->pluck('id')->toArray();
            }

            return view('admin.deliveryEagleView', ['project_number' => $data->project_info->project_number, 'firebase_url' => $data->project_info->firebase_url, 'project_id' => $data->project_info->project_id, 'storage_bucket' => $data->project_info->storage_bucket, 'deliveryUserIds' => $deliveryUserIds]);
        }
        print_r('Error: delivery-google-services.json file is either not found or is invalid.');
    }

    public function getAllDeliveryInfoEagleView(\Illuminate\Http\Request $request): JsonResponse
    {
        if (session()->has('selectedZone')) {
            $deliveryUsers = \App\User::whereIn('id', $request->ids)->where('zone_id', session('selectedZone'))->with('delivery_guy_detail')->get();
        } else {
            $deliveryUsers = \App\User::whereIn('id', $request->ids)->with('delivery_guy_detail')->get();
        }
        $arr = [];
        foreach ($deliveryUsers as $deliveryUser) {
            $arr[$deliveryUser->id] = [];
            $arr[$deliveryUser->id]['name'] = $deliveryUser->name;
            $nonCompleteOrderCount = \App\AcceptDelivery::whereHas('order', function ($query) {
                $query->whereIn('orderstatus_id', ['3', '4']);
            })->where('user_id', $deliveryUser->id)->where('is_complete', 0)->count();
            if (0 < $nonCompleteOrderCount) {
                $hasOrders = true;
            } else {
                $hasOrders = false;
            }
            if ($deliveryUser->delivery_guy_detail->status == 1 && ! $hasOrders) {
                $statusCode = 'GREEN';
            } else {
                if ($deliveryUser->delivery_guy_detail->status == 1 && $hasOrders) {
                    $statusCode = 'ORANGE';
                } else {
                    if ($deliveryUser->delivery_guy_detail->status == 0 && $hasOrders) {
                        $statusCode = 'BLACK';
                    } else {
                        $statusCode = 'RED';
                    }
                }
            }
            $arr[$deliveryUser->id]['status'] = $statusCode;
        }

        return response()->json($arr);
    }

    public function getDeliveryInfoEagleView($id): JsonResponse
    {
        $deliveryUser = \App\User::where('id', $id)->with('delivery_guy_detail')->first();
        if ($deliveryUser) {
            $completedOrderCountOverall = \App\AcceptDelivery::where('user_id', $deliveryUser->id)->where('is_complete', 1)->count();
            $nonCompleteOrderCount = \App\AcceptDelivery::where('user_id', $deliveryUser->id)->where('is_complete', 0)->count();
            $completedOrdersToday = \App\AcceptDelivery::where('user_id', $deliveryUser->id)->where('is_complete', 1)->whereBetween('created_at', [\Carbon\Carbon::now()->startOfDay(), \Carbon\Carbon::now()])->count();
            $onGoingOrders = \App\AcceptDelivery::whereHas('order', function ($query) {
                $query->whereIn('orderstatus_id', ['3', '4']);
            })->where('user_id', $deliveryUser->id)->with(['order' => function ($q) {
                $q->select('id', 'orderstatus_id', 'unique_order_id', 'address', 'payment_mode', 'payable', 'total');
            }])->with(['order.restaurant' => function ($q) {
                $q->select('id', 'name');
            }])->orderBy('created_at', 'DESC')->get();
            $deliveryCollection = \App\DeliveryCollection::where('user_id', $deliveryUser->id)->first();
            if ($deliveryCollection) {
                $cashInHand = $deliveryCollection->amount;
            } else {
                $cashInHand = 0;
            }
            $cashLimit = $deliveryUser->delivery_guy_detail->cash_limit;
            $walletBalance = $deliveryUser->balanceFloat;
            $render = view('admin.partials.eagleViewDeliveryInfo', ['success' => true, 'deliveryUser' => $deliveryUser, 'status' => $deliveryUser->delivery_guy_detail->status, 'completedOrderCountOverall' => $completedOrderCountOverall, 'nonCompleteOrderCount' => $nonCompleteOrderCount, 'completedOrdersToday' => $completedOrdersToday, 'onGoingOrders' => $onGoingOrders, 'cashInHand' => $cashInHand, 'cashLimit' => $cashLimit, 'walletBalance' => $walletBalance])->render();
            $response = ['success' => true, 'html' => $render];

            return response()->json($response);
        }
    }
}
