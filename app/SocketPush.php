<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App;

class SocketPush extends \Illuminate\Database\Eloquent\Model
{
    public function pushNewOrder($newOrderToPush, $usersToSelect)
    {
        $serviceAccount = \Kreait\Firebase\ServiceAccount::fromJsonFile(base_path('service-account.json'));
        $firebase = (new \Kreait\Firebase\Factory())->withDatabaseUri(config('setting.firebaseRealtimeDatabaseUrl'))->withServiceAccount($serviceAccount)->create();
        $db = $firebase->getDatabase();
        $reference = $db->getReference('User');
        $snapshot = $reference->getSnapshot();
        $firebaseData = $snapshot->getValue();
        foreach ($firebaseData as $userId => $fireDb) {
            if (in_array($userId, $usersToSelect)) {
                $db->getReference('User/'.$userId.'/order')->set($newOrderToPush);
            }
        }
    }

    public function pushNewOrderStore($newOrderToPush, $usersToSelect)
    {
        $serviceAccount = \Kreait\Firebase\ServiceAccount::fromJsonFile(base_path('service-account.json'));
        $firebase = (new \Kreait\Firebase\Factory())->withDatabaseUri(config('setting.firebaseRealtimeDatabaseUrl'))->withServiceAccount($serviceAccount)->create();
        $db = $firebase->getDatabase();
        $reference = $db->getReference('User');
        $snapshot = $reference->getSnapshot();
        foreach ($usersToSelect as $selectedStoreOwners) {
            $db->getReference('User/'.$selectedStoreOwners.'/order')->set(intval($newOrderToPush));
        }
    }

    public function removeOrder($orderToRemove, $usersToSelect)
    {
        $serviceAccount = \Kreait\Firebase\ServiceAccount::fromJsonFile(base_path('service-account.json'));
        $firebase = (new \Kreait\Firebase\Factory())->withDatabaseUri(config('setting.firebaseRealtimeDatabaseUrl'))->withServiceAccount($serviceAccount)->create();
        $db = $firebase->getDatabase();
        $reference = $db->getReference('User');
        $snapshot = $reference->getSnapshot();
        $firebaseData = $snapshot->getValue();
        if ($firebaseData != null) {
            foreach ($firebaseData as $userId => $fireDb) {
                if (in_array($userId, $usersToSelect) && isset($fireDb['order'])) {
                    $currentOrder = $fireDb['order'];
                    if ($currentOrder == $orderToRemove) {
                        $db->getReference('User/'.$userId.'/order')->remove();
                    }
                }
            }
        }
    }
}
