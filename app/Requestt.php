<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Ride;

class Requestt extends Model
{
    protected $fillable=['meetpointLatitude','meetpointLongitude','destinationLatitude','destinationLongitude','neededSeats','time','response','user_id','ride_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }


}
