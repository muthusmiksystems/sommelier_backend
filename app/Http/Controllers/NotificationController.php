<?php

namespace App\Http\Controllers;

use App\Alert;
use App\PushToken;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Image;
use Ixudra\Curl\Facades\Curl;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\MessageTarget;
use stdClass;

// use Kreait\Firebase\Messaging\SendRawMessage();

class NotificationController extends Controller
{
    public function saveToken(Request $request): JsonResponse
    {
        // $user = auth()->user();
        $user = User::where('id', $request->id)->get()->first();

        if ($user) {
            $subscriber = PushToken::where('token', $request->push_token)->first();
            if (!$subscriber) {
                $pushToken = new PushToken();
                $pushToken->token = $request->push_token;
                $pushToken->user_id = $user->id;
                $pushToken->save();
            } else {
                $subscriber->user_id = $request->id;
                $subscriber->save();
            }
            $success = $request->push_token;

            return response()->json($success);
        }

        return response()->json(['success' => false], 401);
    }

    public function saveTokenNoUser(Request $request): JsonResponse
    {
        // $pushToken = new PushToken();
        // $pushToken->token = $request->push_token;
        // $pushToken->save();

        // $success = $request->push_token;

        // return response()->json($success);
        $token = $request->push_token;

        // Check if the token already exists in the database
        $existingToken = PushToken::where('token', $token)->first();
        // return response()->json(['message' => $existingToken]);

        if ($existingToken) {
            // Token already exists, update the existing record

            $existingToken->user_id = null;
            // $existingToken->touch();
            $existingToken->save();
            $message = 'Token updated successfully';
        } else {
            // Token does not exist, create a new record

            $pushToken = new PushToken();
            $pushToken->token = $token;
            $pushToken->save();
            $message = 'Token added successfully';
        }

        return response()->json(['message' => $message]);
    }

    public function updateAppTokenForUser(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ($user) {
            if ($user->hasRole(['Delivery Guy', 'Store Owner'])) {
                //do nothing
            } else {
                //else delete old tokens (customer)
                $getAllTokens = PushToken::where('user_id', $user->id)->get();
                if (count($getAllTokens) > 0) {
                    foreach ($getAllTokens as $userToken) {
                        $userToken->delete();
                    }
                }
            }

            $nullToken = PushToken::where('token', $request->push_token)->first();
            if ($nullToken) {
                $nullToken->delete();
            }

            $pushToken = new PushToken();
            $pushToken->token = $request->push_token;
            $pushToken->user_id = $user->id;
            $pushToken->save();

            $success = $request->push_token;

            return response()->json($success);
        }

        return response()->json(['success' => false], 401);
    }

    public function saveRestaurantOwnerNotificationToken(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ($user) {
            $pushToken = PushToken::where('user_id', $user->id)->first();

            if ($pushToken) {
                //update the existing token
                $pushToken->token = $request->push_token;
                $pushToken->save();
            } else {
                //create new token for user
                $pushToken = new PushToken();
                $pushToken->token = $request->push_token;
                $pushToken->user_id = $user->id;
                $pushToken->save();
            }
            $success = $request->push_token;

            return response()->json($success);
        }

        return response()->json(['success' => false], 401);
    }

    public function notifications(): View
    {
        $usersCount = User::count();
        $subscriberCount = PushToken::whereNotNull('user_id')->count();
        $appUsers = PushToken::whereNull('user_id')->count();

        $countJunkData = Alert::whereDate('created_at', '<', Carbon::now()->subDays(7))->count();

        return view('admin.notifications', [
            'subscriberCount' => $subscriberCount,
            'usersCount' => $usersCount,
            'appUsers' => $appUsers,
            'countJunkData' => $countJunkData,
        ]);
    }

    public function getUsersToSendNotification(Request $request): JsonResponse
    {
        $search = $request->search;

        if ($search == '') {
            $users = User::orderby('id', 'desc')->select('id', 'name', 'email')->limit(5)->get();
        } else {
            $users = User::orderby('name', 'asc')->select('id', 'name', 'email')->where('name', 'like', '%' . $search . '%')->limit(5)->get();
        }

        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'id' => $user->id,
                'text' => $user->name . ' (' . $user->email . ')',
            ];
        }

        return response()->json($response);
    }


    public function deleteAlertsJunk(): RedirectResponse
    {
        DB::statement('DELETE FROM alerts WHERE created_at < NOW() - INTERVAL 7 DAY;');
        DB::statement('OPTIMIZE TABLE alerts;');

        return redirect()->back()->with(['success' => 'Junk data deleted successfully.']);
    }

    public function sendNotifiaction(Request $request): RedirectResponse
    {
        $secretKey = config('setting.firebaseSecret');
        $pushNotification = config('setting.enablePushNotification');

        $notification = $request->except(['_token']);

        $alertData = $request->except(['_token']);
        $alertData = json_encode($alertData);
        $alertData = json_decode($alertData);
        $alertData = [
            'title' => $alertData->data->title,
            'message' => $alertData->data->message,
            'badge' => $alertData->data->badge,
            'icon' => $alertData->data->icon,
            'click_action' => $alertData->data->click_action,
            'unique_order_id' => null,
            'custom_notification' => true,
            'custom_image' => $alertData->data->image,
        ];

        /* Save to Alerts table */
        $subscribers = User::all();

        $alertsInsertArray = [];
        foreach ($subscribers as $subscriber) {
            $alert = new Alert();
            $alert->data = json_encode($alertData);
            $alert->user_id = $subscriber->id;
            $alert->is_read = 0;
            $alert->created_at = Carbon::now();
            $alert->updated_at = Carbon::now();
            $alertsInsertArray[] = $alert->attributesToArray();
        }
        $alertsInsertCollection = collect($alertsInsertArray);
        $alertChunks = $alertsInsertCollection->chunk(1000);
        foreach ($alertChunks as $chunk) {
            Alert::insert($chunk->toArray());
        }

        // dd(count($alertsInsertArray));

        /*  END Save to Alerts Table */

        // $notification = json_encode($notification);

        // $notification = substr($notification, 0, -1);

        //get all push tokens excluding delivery guys and store owners...
        $toExclude = User::role(['Delivery Guy', 'Store Owner'])->pluck('id');
        $pushTokens = PushToken::where('is_active', '1')
            ->whereNotIn('user_id', $toExclude)
            ->get(['token'])
            ->pluck('token');

        if (count($pushTokens)) {
            $i = 0;
            $len = count($pushTokens);
            $last = $len - 1;

            $chunks = $pushTokens->chunk(900)->toArray();
            foreach ($chunks as $chunk) {
                $i = 0;
                $len = count($chunk);
                $last = $len - 1;

                // $tokens = '';

                foreach ($chunk as $key => $value) {
                    if ($len == 1) {
                        $tokens = $value;
                    } elseif ($i == 0) {
                        $tokens = '["' . $value . '",';
                    } elseif ($i == $last) {
                        $tokens .= '"' . $value . '"]';
                    } else {
                        $tokens .= '"' . $value . '",';
                    }
                    $i++;
                }
                // $main_picture = $notification['data']['image'];
                // $notifications['notification'] = [
                //     'title' => $data['data']['title'],
                //     'body' => $data['data']['message']
                //     // Add other properties if needed
                // ];
                // $mainPicture = [
                //     // 'main_picture' => [
                //         'main_picture' => $notification['data']['image']
                //     // ]/
                // ];
                // dd($notification['data']['title']);
                $notificationData = [
                    'registration_ids' => json_decode($tokens, true), // Use registration_ids instead of to
                    'notification' => [
                        'title' => $notification['data']['title'],
                        'body' => $notification['data']['message'],
                        'click_action' => $notification['data']['click_action'],
                        'unique_order_id' => null,
                        'custom_notification' => true,
                        'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
                    ]
                ];

                $notificationJson = json_encode($notificationData, JSON_UNESCAPED_SLASHES);

                // dd($notificationJson);

                if ($pushNotification == 'true') {
                    $response = Curl::to('https://fcm.googleapis.com/fcm/send')
                        ->withHeader('Content-Type: application/json')
                        ->withHeader("Authorization: Bearer $secretKey")
                        ->withData($notificationJson)
                        ->post();
                    // dd($response);
                } else {
                    return redirect()->back()->with(['message' => 'Enable Push Notification']);
                }

            }
        }

        if ($pushNotification) {
            return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
        } else {
            return redirect()->back()->with(['message' => 'Enable Push Notification']);
        }
    }

    public function sendNotificationToSelectedUsers(Request $request): RedirectResponse
    {
        $secretKey = config('setting.firebaseSecret');
        $pushNotification = config('setting.enablePushNotification');
        // $secretKey = 'eHINrnwWSEiPA95zFLkRWm:APA91bHG1vS43mIudtFOFGrLYXfT9l2dKBuO5qn_I7rBxxd-TBZF1RtBVy9nGDgsJ1miClTGTJmVPnTsGib679qOzN8b1Z9bSXV6BdzEkp2WVyTAz34qrOSLOamQP8S2t0aR12DH8H4k';

        // $secretKey = 'key=3ceKqYvPKAYh_0x3dI8yMi7Kytp4hSs7RZZ_Cx_b9eE';
        // dd($secretKey);
        $notification = $request->except(['_token']);
        // dd($data);
        $alertData = $request->except(['_token']);
        $alertData = json_encode($alertData);
        $alertData = json_decode($alertData);
        $alertData = [
            'title' => $alertData->data->title,
            'message' => $alertData->data->message,
            'badge' => $alertData->data->badge,
            'icon' => $alertData->data->icon,
            'click_action' => $alertData->data->click_action,
            'unique_order_id' => null,
            'custom_notification' => true,
            'custom_image' => $alertData->data->image,
        ];
        /* Save to Alerts table */
        $subscribers = User::whereIn('id', $request->users)->get();
        $alertsInsertArray = [];
        foreach ($subscribers as $subscriber) {
            $alert = new Alert();
            $alert->data = json_encode($alertData);
            $alert->user_id = $subscriber->id;
            $alert->is_read = 0;
            $alert->created_at = Carbon::now();
            $alert->updated_at = Carbon::now();
            $alertsInsertArray[] = $alert->attributesToArray();
        }
        $alertsInsertCollection = collect($alertsInsertArray);
        $alertChunks = $alertsInsertCollection->chunk(1000);
        foreach ($alertChunks as $chunk) {
            Alert::insert($chunk->toArray());
        }
        /*  END Save to Alerts Table */
        // $notifications = [];

        // // Assign properties to the array
        // $notifications['notification'] = [
        //     'title' => $data['data']['title'],
        //     'body' => $data['data']['message']
        //     // Add other properties if needed
        // ];
        // $notifications['click_action'] = $data['data']['click_action'];
        // $notifications['badge'] = $data['data']['badge'];
        // $notifications['icon'] = $data['data']['icon'];
        // $notifications['image'] = $data['data']['image'];
        // $notification = json_encode($notification);
        // dd($notification);
        // $notification = $data;
        // $notification=substr($notification, 0, -1);
        // $notification = json_encode($notification);

        // dd($notification);
        // $data = substr($notification, 0, -1);
        $pushTokens = PushToken::where('is_active', '1')
            ->whereIn('user_id', $request->users)
            ->get(['token'])
            ->pluck('token')
            ->toArray();
        // dd($pushTokens);
        if (count($pushTokens)) {
            $i = 0;
            $len = count($pushTokens);
            $last = $len - 1;
            // $tokens = '';

            foreach ($pushTokens as $key => $value) {
                if ($len == 1) {
                    $tokens = $value;
                } elseif ($i == 0) {
                    $tokens = '["' . $value;
                } elseif ($i == $last) {
                    $tokens .= '"' . $value . '"]';
                } else {
                    $tokens .= '"' . $value . '",';
                }
                $i++;
            }
            $notificationData = [
                // 'to' => "eHINrnwWSEiPA95zFLkRWm:APA91bHG1vS43mIudtFOFGrLYXfT9l2dKBuO5qn_I7rBxxd-TBZF1RtBVy9nGDgsJ1miClTGTJmVPnTsGib679qOzN8b1Z9bSXV6BdzEkp2WVyTAz34qrOSLOamQP8S2t0aR12DH8H4k",
                // $tokens,
                'to' => $tokens,
                'notification' => [
                    'title' => $notification['data']['title'],
                    'body' => $notification['data']['message'],
                    // 'badge' => $notification['data']['badge'],
                    'icon' => $notification['data']['icon'],
                    'click_action' => $notification['data']['click_action'],
                    'unique_order_id' => null,
                    'custom_notification' => true,
                    'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
                ]
            ];

            // $fullData = $notification . $tokens;
            $fullData = json_encode($notificationData);
            // dd($fullData);
            // $secretKey = 'AAAABkn2H-c:APA91bHO4ywoNMivE0SIWVzrXo8W-7E5D8X7LT08cdgk2pDeWZCiEGD8jqlONua09R4Nycn5I_Op6jVr8UuRP_kc0E1pJ8B3tCfy-6S4iidT3OTT8K3zZomDOkRVKtahAyJuH9JwefA7';
            \Log::info("image ===  " . $fullData);
            if ($pushNotification == 'true') {
                $response = Curl::to('https://fcm.googleapis.com/fcm/send')
                    ->withHeader('Content-Type: application/json')
                    ->withHeader("Authorization: Bearer $secretKey")
                    ->withData($fullData)
                    ->post();
                // dd($response);
                $response = json_decode($response);
                return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
            } else {
                return redirect()->back()->with(['message' => 'Enable Push Notification']);
            }
            // return redirect()->back()->with(['success' => 'Success: ' . $response->success . ' & Failed: ' . $response->failure]);
        }

        if ($pushNotification) {
            return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
        } else {
            return redirect()->back()->with(['message' => 'Enable Push Notification']);
        }
    }

    public function sendNotificationToNonRegisteredAppUsers(Request $request): RedirectResponse
    {
        $secretKey = config('setting.firebaseSecret');
        $pushNotification = config('setting.enablePushNotification');


        $notification = $request->except(['_token']);


        $pushTokens = PushToken::where('user_id', null)->get(['token'])->pluck('token');

        if (count($pushTokens)) {
            $i = 0;
            $len = count($pushTokens);
            $last = $len - 1;

            $chunks = $pushTokens->chunk(900)->toArray();
            foreach ($chunks as $chunk) {
                $i = 0;
                $len = count($chunk);
                $last = $len - 1;

                // $tokens = '{';

                foreach ($chunk as $key => $value) {
                    if ($len == 1) {
                        $tokens = $value;
                    } elseif ($i == 0) {
                        $tokens = '["' . $value . '",';
                    } elseif ($i == $last) {
                        $tokens .= '"' . $value . '"]';
                    } else {
                        $tokens .= '"' . $value . '",';
                    }
                    $i++;
                }

                $notificationData = [
                    'registration_ids' => json_decode($tokens, true), // Use registration_ids instead of to
                    'notification' => [
                        'title' => $notification['data']['title'],
                        'body' => $notification['data']['message'],
                        'click_action' => $notification['data']['click_action'],
                        'unique_order_id' => null,
                        'custom_notification' => true,
                        'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
                        // 'image' => 'http://192.168.1.34/assets/img/restaurants/1717688167xzsQjK7GY4.jpg'
                    ]
                ];

                $notificationJson = json_encode($notificationData, JSON_UNESCAPED_SLASHES);

                if ($pushNotification == 'true') {
                    $response = Curl::to('https://fcm.googleapis.com/fcm/send')
                        ->withHeader('Content-Type: application/json')
                        ->withHeader("Authorization: Bearer $secretKey")
                        ->withData($notificationJson)
                        ->post();
                    return redirect()->back()->with(['success' => 'Notifications set to Non-Registered App Users']);

                } else {
                    return redirect()->back()->with(['message' => 'Enable Push Notification']);
                }

            }
        }

        if ($pushNotification) {
            return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
        } else {
            return redirect()->back()->with(['message' => 'Enable Push Notification']);
        }
    }

    public function uploadNotificationImage(Request $request): JsonResponse
    {
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $filename = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            Image::make($request->file)->resize(1600, 1100)->save(base_path('/assets/img/various/' . $filename));

            return response()->json(['success' => $filename]);
        }
    }

    public function getUserNotifications(Request $request): JsonResponse
    {
        // $user = auth()->user();
        $user = User::where('id', $request->id)->get()->first();
        if ($user) {
            $notifications = Alert::where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->whereDate('created_at', '>', Carbon::now()->subDays(7))
                ->get()
                ->take(20);

            return response()->json($notifications);
        }

        return response()->json(['success' => false], 401);
    }
    public function sendPushNotifications()
    {
        $messaging = app('firebase.messaging');

        $notification = Notification::fromArray([
            'title' => 'New Message',
            'body' => 'You have a new message'
        ]);

        $message = CloudMessage::withTarget(MessageTarget::token('etFC9hJ-QwuuXWIeyL-cmw:APA91bGnVZE6v5gdf5lfrYgPR8VazBOPM4OvCSYgPu0RZNN-8Hh4iqCvNJGbgwR2myHasg83i-ClfPtPX_URZLQg6rjpJuxIrUNwp4PEUmgzM2DOT7C6cPg6DQnwzQt5x0uwBoCVKcmt'))
            ->withNotification($notification);

        $messaging->send($message);

        return response()->json(['message' => 'Notification sent successfully']);
    }
    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $notifications = Alert::where('user_id', $user->id)->get();
            foreach ($notifications as $notification) {
                $notification->is_read = true;
                $notification->save();
            }
            $notifications = Alert::where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->whereDate('created_at', '>', Carbon::now()->subDays(7))
                ->get()
                ->take(20);

            return response()->json($notifications);
        }

        return response()->json(['success' => false], 401);
    }

    public function markOneNotificationRead(Request $request): JsonResponse
    {
        $user = auth()->user();
        $notification = Alert::where('id', $request->notification_id)->first();

        if ($user && $notification) {
            $notification->is_read = true;
            $notification->save();

            $notifications = Alert::where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->whereDate('created_at', '>', Carbon::now()->subDays(7))
                ->get()
                ->take(20);

            return response()->json($notifications);
        }

        return response()->json(['success' => false], 401);
    }
}
