<?php

namespace App\Http\Controllers;

use App\Requestt;
use App\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RequesttsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requestts = Requestt::all()->where('user_id', Auth::user()->id);
        return view('requestts.index')->with('requestts', $requestts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('requestts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Requestt::create([
            'meetpointLatitude' => $request->meetpointLatitude,
            'meetpointLongitude' => $request->meetpointLongitude,
            'destinationLatitude' => $request->destinationLatitude,
            'destinationLongitude' => $request->destinationLongitude,
            'neededSeats' => $request->neededSeats,
            'time' => $request->time,
            'user_id' => $request->user_id

        ]);
        session()->flash('flashMessage', 'Request is created successfully',['timeout' => 100]);
        return redirect(route('requestts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Requestt  $requestt
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Requestt  $requestt
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $requestt, $id)
    {
        $requestt = Requestt::find($id);
        return view('requestts.create', ['requestt' => $requestt]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Requestt  $requestt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requestt = Requestt::find($id);
        $requestt->update([
            'meetpointLatitude' => $request->meetpointLatitude,
            'meetpointLongitude' => $request->meetpointLongitude,
            'destinationLatitude' => $request->destinationLatitude,
            'destinationLongitude' => $request->destinationLongitude,
            'neededSeats' => $request->neededSeats,
            'time' => $request->time,
            'user_id' => $request->user_id
        ]);

        session()->flash('flashMessage', 'request is updated successfully',['timeout' => 100]);
        return redirect(route('requestts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Requestt  $requestt
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $requestt = Requestt::find($id);
        $requestt->delete();

        session()->flash('flashMessage', 'Request deleted successfully',['timeout' => 100]);
        return redirect(route('requestts.index'));
    }

    public function viewAvailableRides($id)
    {
        $requestt = Requestt::find($id);
        if ($requestt->response == false ) {
            $rides = Ride::all()->where('destination', $requestt->destination)->where('user_id', '<>', $requestt->user_id)->where('time', '>=', $requestt->time)->where('availableSeats', '>=', $requestt->neededSeats)->where('available',true);
            return view('requestts.viewAvailableRides')->with('rides', $rides)->with('requestt', $requestt);
        }else{
            session()->flash('flashMessage', 'You already reserved a ride',['timeout' => 100]);
            $ride=$requestt->ride;
            //dd($ride);
           return view('requestts.myRide')->with('ride', $ride)->with('requestt', $requestt);
        }
    }
    public function sendRequest($request_id, $ride_id)
    {
        $requestt = Requestt::find($request_id);
        $requestt->ride_id = $ride_id;
        $requestt->save();
        session()->flash('flashMessage', 'Request is sent',['timeout' => 100]);
        $requestts=Requestt::all()->where('id','<>',$requestt->id);
        return view('requestts.index')->With('requestts',$requestts);
    }
    public function cancelRide($request_id, $ride_id)
    {
        $requestt = Requestt::find($request_id);
        $requestt->ride->availableSeats=$requestt->ride->availableSeats+ $requestt->neededSeats;
        $requestt->response=false;
        $requestt->ride_id = NULL;
        $requestt->save();
        session()->flash('flashMessage', 'Request to Ride is canceled ',['timeout' => 100]);
        return redirect(route('requestts.index'));
    }
}
