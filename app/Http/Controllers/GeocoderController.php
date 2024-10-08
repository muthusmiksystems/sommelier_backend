<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Geocoder\Facades\Geocoder;

class GeocoderController extends Controller
{
    public function coordinatesToAddress(Request $request): JsonResponse
    {
        try {
            $response = \Geocoder::getAddressForCoordinates($request->lat, $request->lng);

            if (config('setting.googleFullAddress') == 'false') {
                $allowedTypes = ['street_address', 'sublocality', 'subpremise', 'premise', 'street_number', 'floor', 'establishment', 'point_of_interest', 'parking', 'post_box', 'postal_town', 'room', 'bus_station', 'train_station', 'transit_station'];
                $finalAddress = '';
                $count = count($response['address_components']);

                foreach ($response['address_components'] as $key => $address) {
                    if (isset($address->types)) {
                        foreach ($address->types as $type) {
                            $allowed = false;
                            if (!in_array($type, $allowedTypes)) {
                                $allowed = true;
                            }
                        }
                        if ($allowed) {
                            $finalAddress .= $address->long_name;
                            if ($key + 1 != $count) {
                                $finalAddress .= ', ';
                            }
                        }
                    }
                }

                return response()->json($finalAddress);
            } else {
                return response()->json($response['formatted_address']);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function addressToCoordinates(Request $request): JsonResponse
    {
        $address = \Geocoder::getCoordinatesForAddress($request->string);

        return response()->json($address);
    }
    function get_address_lat_lng(Request $request): JsonResponse
    {

        // Validate the request to ensure 'address' is provided
        $request->validate([
            'address' => 'required|string',
        ]);

        // Get the address from the request
        $address = $request->input('address');

        // Fetch the coordinates for the address
        $coordinates = Geocoder::getCoordinatesForAddress($address);

        // Return the coordinates as a JSON response
        return response()->json($coordinates);

    }
}
