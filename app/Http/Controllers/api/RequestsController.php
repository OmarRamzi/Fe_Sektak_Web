<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Notifications\RequestSent;

use App\Request;
use App\Ride;
use Illuminate\Http\Request as WebRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->content = array();
    }
    public function index()
    {
        $user = User::findOrFail(request('userId'));
        $this->content['requests'] =  $user->requests;
        return response()->json($this->content);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = request()->all();
        $rules = [
            'meetPointLatitude' => ['required'],
            'meetPointLongitude' => ['required'],
            'endPointLatitude' => ['required'],
            'endPointLongitude' => ['required'],
            'numberOfNeededSeats' => ['required'],
            'time' => ['required'],
            'response' => ['boolean'],
            'userId' => ['required']
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            Request::create([
            'meetPointLatitude' => request('meetPointLatitude'),
            'meetPointLongitude' => request('meetPointLongitude'),
            'destinationLatitude' => request('endPointLatitude'),
            'destinationLongitude' => request('endPointLongitude'),
            'neededSeats' => request('numberOfNeededSeats'),
            'time' => request('time'),
            'user_id' => request('userId')

        ]);
            $this->content['status'] = 'done';
            return response()->json($this->content);
        } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
            return response()->json($this->content);
        }
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Request  $requestt
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $data = request()->all();
        $rules = [
            'meetPointLatitude' => ['required'],
            'meetPointLongitude' => ['required'],
            'endPointLatitude' => ['required'],
            'endPointLongitude' => ['required'],
            'numberOfNeededSeats' => ['required'],
            'time' => ['required'],
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $request=Request::find(request('requestId'));
            $request->update([
            'meetPointLatitude' => request('meetPointLatitude'),
            'meetPointLongitude' =>request('meetPointLongitude'),
            'destinationLatitude' =>request('endPointLatitude'),
            'destinationLongitude' => request('endPointLongitude'),
            'neededSeats' => request('numberOfNeededSeats'),
            'time' => request('time'),
        ]);
            $this->content['status'] = 'done';
            return response()->json($this->content);
        } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
            return response()->json($this->content);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Request  $requestt
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $requestt = Request::find(request('requestId'));
        if ($requestt!=null) {
            $requestt->delete();
            $this->content['status'] = 'done';
            return response()->json($this->content);
        } else {
            $this->content['status'] = 'already deleted';
            return response()->json($this->content);
        }
    }
























 public function reject()
    {
        $requestt = Request::find(request('requestId'));
        $requestt->ride_id = null;
        $requestt->save();
	    $this->content['status'] = 'done';
        return response()->json($this->content);
    }


    public function sendRequest()
    {
        $requestt = Request::findOrFail(request('requestId'));
        $requestt->ride_id = request('rideId');
        $requestt->save();
        $requestt->ride->user->notify(new RequestSent($requestt));   //driver
        $this->content['status'] = 'done';
        return response()->json($this->content);
    }

    public function cancelRide($request_id, $ride_id)
    {
        $requestt = Request::find($request_id);
        $requestt->ride->availableSeats=$requestt->ride->availableSeats+ $requestt->neededSeats;
        $requestt->response=false;
        $requestt->ride_id = null;
        $requestt->save();
        session()->flash('flashMessage', 'Request to Ride is canceled ', ['timeout' => 100]);
        return redirect(route('requestts.index'));
    }









    public function acceptRequest()
    {
        $requestt = Request::find(request('requestId'));
        $ride = Ride::find(request('rideId'));
        if ($ride->availableSeats >= $requestt->neededSeats && $requestt->response == false) {
            $requestt->update([
                'response' => true,
                'ride_id' => $ride->id,
            ]);
            $ride->update([
                'availableSeats' => $ride->availableSeats - $requestt->neededSeats,
            ]);
            $requestt->user->notify(new RequestAccepted($ride));   //driver
            $this->content['status'] = 'done';
            return response()->json($this->content);

        } else {
            $this->content['status'] = 'unAvailable';
            return response()->json($this->content);

        }

    }

}
