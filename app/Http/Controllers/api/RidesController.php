<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use App\Ride;
use App\User;
use App\Request;
use Illuminate\Http\Request as WebRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RidesController extends Controller
{
    public function __construct()
    {
        $this->content = array();
    }
    public function index()
    {
        $user = User::findOrFail(request('userId'));
        $rides = $user->rides;
        foreach ($rides as $ride){
            if($ride->requests){
                $ride['requests'] = $ride->requests;
            }
        }
        $this->content['rides'] = $rides;
        return response()->json($this->content);
     }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebRequest $request)
    {
        $data = request()->all();
        $rules = [
            'startPointLatitude' => ['required'],
            'startPointLongitude' => ['required'],
            'endPointLatitude' => ['required'],
            'endPointLongitude' => ['required'],
            'availableSeats' => ['required'],
            'time' => ['required'],
            'userId' => ['required']

        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            //dd(request('startPointLatitude'));
            Ride::create([
                'startPointLatitude' =>request('startPointLatitude'),
                'startPointLongitude' =>request('startPointLongitude'),
                'destinationLatitude' =>request('endPointLatitude'),
                'destinationLongitude' =>request('endPointLongitude'),
                'availableSeats' =>request('availableSeats'),
                'time' => request('time'),
                'available' => true,
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
     * Display the specified resource.
     *
     * @param  \App\Ride  $ride
     * @return \Illuminate\Http\Response
     */
    public function show(Ride $ride)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ride  $ride
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ride = Ride::find($id);
        return view('rides.create', ['ride' => $ride]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ride  $ride
     * @return \Illuminate\Http\Response
     */
    public function update(WebRequest $request, $id)
    {
        $ride = Ride::find($id);
        $ride->update([
            'startPoint' => $request->startPoint,
            'destination' => $request->destination,
            'availableSeats' => $request->availableSeats,
            'time' => $request->time,
            'user_id' => $request->user_id
        ]);
        session()->flash('flashMessage', 'Ride is updated successfully', ['timeout' => 100]);
        return redirect(route('rides.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ride  $ride
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ride = Ride::find($id);
        $ride->delete();
        session()->flash('flashMessage', 'Ride is deleted successfully', ['timeout' => 100]);
        return redirect(route('rides.index'));
    }
    public function viewSentRequests($id)
    {
        $ride = Ride::find($id);
        $requestts = Ride::find($id)->requestts->where('neeededSeats', '<=', $ride->availableSeats)->where('response', false);
        return view('rides.viewSentRequests')->with('requestts', $requestts)->with('ride', $ride);
    }
    public function acceptRequest($request_id, $ride_id)
    {
        $requestt = Request::find($request_id);
        $ride = Ride::find($ride_id);
        if ($ride->availableSeats >= $requestt->neededSeats && $requestt->response == false) {
            $requestt->update([
                'response' => true,
                'ride_id' => $ride->id,
            ]);
            $ride->update([
                'availableSeats' => $ride->availableSeats - $requestt->neededSeats,
            ]);
            session()->flash('flashMessage', 'Request is accepted successfully', ['timeout' => 100]);
            $requests = Request::where('id', '<>', $request_id)->get();
            return view('rides.viewSentRequests')->with('requestts', $requests)->with('ride', $ride);
        } else {
            $requestts = Request::where('id', '<>', $request_id)->where('response',false);
            if ($requestts->count() > 0) {
                session()->flash('flashMessage', 'You do not have enough seats for this request', ['timeout' => 100]);
            }
            return view('rides.viewSentRequests')->with('requestts', $requestts)->with('ride', $ride);
        }

    }
}
