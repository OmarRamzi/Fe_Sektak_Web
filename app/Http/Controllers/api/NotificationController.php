<?php
namespace App\Http\Controllers\api;
use App\User;
use App\Events\LocationsSent;
use App\Http\Controllers\Controller;

class NotificationController extends Controller{
    public function send(){
       event(new LocationsSent(
           request('rideId'),
           request('userId'),
           request('locationLatitude'),
           request('locationLongitude')));
    }
    public function showNotifications(){
        $user= User::find(request('userId'));
        $this->content['notifications'] = $user->notifications;
        $user->unreadNotifications->markAsRead();
	return response()->json($this->content);
    }
    public function getUnReadNotificationsCount(){
        $user= User::find(request('userId'));
        $this->content['count'] = $user->unreadNotifications->count();
        return response()->json($this->content);

    }

}
