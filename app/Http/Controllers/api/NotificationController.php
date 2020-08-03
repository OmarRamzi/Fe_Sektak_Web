<?php
namespace App\Http\Controllers\api;

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
}