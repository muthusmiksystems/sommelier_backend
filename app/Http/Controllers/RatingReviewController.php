<?php

namespace App\Http\Controllers;

use App\Order;
use App\Rating;
use App\Restaurant;
use App\User;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RatingReviewController extends Controller
{
    public function rateOrder(Request $request): JsonResponse
    {
        $authUser = auth()->user();
        //check if order exists
        $order = Order::where('id', $request->order_id)->first();

        if ($order && $order->is_completed()) {
            //check if order belongs to the auth user
            if ($order->user->id == $authUser->id) {
                //check if order already rated,
                $rating = Rating::where('order_id', $order->id)->first();

                //not rated yet...
                if (! $rating) {
                    $rating = new Rating();

                    $rating->user_id = $authUser->id;
                    $rating->order_id = $order->id;
                    $rating->restaurant_id = $order->restaurant_id;

                    $rating->delivery_id = $order->accept_delivery ? $order->accept_delivery->user_id : null;

                    $rating->rating_store = $request->rating_store;
                    $rating->rating_delivery = $request->rating_delivery;
                    $rating->review_store = $request->review_store ? $request->review_store : null;
                    $rating->review_delivery = $request->review_delivery ? $request->review_delivery : null;
                    $rating->save();

                    Cache::forget('stores-delivery-active');
                    Cache::forget('stores-delivery-inactive');

                    $response = [
                        'success' => true,
                    ];

                    return response()->json($response);
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Order doesnt belongs to user',
                ];

                return response()->json($response);
            }
        }

        $response = [
            'success' => false,
            'message' => 'No order found',
        ];

        return response()->json($response);
    }

    public function viewDeliveryReviews($user_id): View
    {
        $user = User::where('id', $user_id)->firstOrFail();
        $reviews = Rating::where('delivery_id', $user_id)->with('user', 'order.accept_delivery.user', 'restaurant')->orderBy('id', 'DESC')->paginate(20);
        $averageRating = number_format((float) $reviews->avg('rating_delivery'), 1, '.', '');

        return view('admin.viewDeliveryReviews', [
            'user' => $user,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
        ]);
    }

    public function viewStoreReviews($restaurant_id): View
    {
        $restaurant = Restaurant::where('id', $restaurant_id)->firstOrFail();
        $reviews = Rating::where('restaurant_id', $restaurant_id)->with('user', 'order.accept_delivery.user')->orderBy('id', 'DESC')->paginate(20);
        $averageRating = number_format((float) $reviews->avg('rating_store'), 1, '.', '');

        return view('admin.viewStoreReviews', [
            'restaurant' => $restaurant,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
        ]);
    }

    public function updateStoreReview(Request $request): RedirectResponse
    {
        $review = Rating::where('id', $request->review_id)->firstOrFail();

        $review->rating_delivery = $request->rating_delivery;
        $review->rating_store = $request->rating_store;
        $review->review_store = $request->review_store;
        $review->review_delivery = $request->review_delivery;
        $review->save();

        return redirect()->back()->with(['success' => 'Review updated.']);
    }

    public function getDeliveryReviews(Request $request): JsonResponse
    {
        $ratings = Rating::where('delivery_id', $request->delivery_id)->select(['rating_delivery', 'review_delivery'])->orderBy('id', 'DESC')->get();

        $averageRating = number_format((float) $ratings->avg('rating_delivery'), 1, '.', '');

        $response = [
            'ratings' => $ratings,
            'averageRating' => $averageRating,
        ];

        return response()->json($response);
    }

    /**
     * @param $restaurant_id
     */
    public function getRatingAndReview($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $restaurant->avgRating = storeAvgRating($restaurant->ratings);
        $restaurant->makeHidden(['delivery_areas', 'ratings', 'schedule_data']);

        $reviews = Rating::where('restaurant_id', $restaurant->id)
            ->with('user')
            ->orderBy('id', 'DESC')
            ->get();

        $reviews = $reviews->map(function ($review) {
            $review->username = $review->user->name;

            return $review->only(['id', 'username', 'rating_store', 'review_store']);
        });

        $response = [
            'restaurant' => $restaurant,
            'reviews' => $reviews,
        ];

        return response()->json($response);
    }

    public function getRatableOrder(Request $request): JsonResponse
    {
        $order = Order::where('id', $request->order_id)->with('restaurant', 'orderitems', 'rating')->first();
        if ($order) {
            return response()->json($order);
        }
    }
}
