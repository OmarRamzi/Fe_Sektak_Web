<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use App\Requestt;
use App\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;


class RequesttsController extends Controller
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
        $user = User::findOrFail(request('user_id'));
        $this->content['requests'] =  $user->requestts;
        return response()->json($this->content);
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
        $data = request()->all();
        $rules = [
            'meetPointLatitude' => ['required' ],
            'meetPointLongitude' => ['required'],
            'endPointLatitude' => ['required'],
            'endPointLongitude' => ['required'],
            'numberOfNeededSeats' => ['required'],
            'time' => ['required'],
            'response' => [ 'boolean'],
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            Requestt::create([
            'meetpointLatitude' => request('meetPointLatitude'),
            'meetpointLongitude' => request('meetPointLongitude'),
            'destinationLatitude' => request('endPointLatitude'),
            'destinationLongitude' => $request->endPointLongitude,
            'neededSeats' => request('numberOfNeededSeats'),
            'time' => request('time'),
            'user_id' => request('user_id')

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
    public static function x($latitudeFrom, $longitudeFrom,
                                    $latitudeTo,  $longitudeTo)
      {
           $long1 = deg2rad($longitudeFrom);
           $long2 = deg2rad($longitudeTo);
           $lat1 = deg2rad($latitudeFrom);
           $lat2 = deg2rad($latitudeTo);

           //Haversine Formula
           $dlong = $long2 - $long1;
           $dlati = $lat2 - $lat1;

           $val = pow(sin($dlati/2),2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2),2);

           $res = 2 * asin(sqrt($val));

           $radius = 3958.756;

           return ($res*$radius);
      }



    public function viewAvailableRides(Request $request)
    {

        $requestt = Requestt::findOrFail(request('id'));
        if ($requestt->response == false ) {
            $rides = Ride::all()
            ->/*where('destination', request('destination')*/
            where('user_id', '<>', $requestt->user_id)
            ->where('time', '>=', $requestt->time)
            ->where('availableSeats', '>=', $requestt->neededSeats)
            ->where('available',true);
            //dd($rides);

           $filtered = $rides->filter(function ($value, $key) {
                 $requestt = Requestt::findOrFail(request('id'));
                 return   self::x($requestt->destinationLatitude,
                  $requestt->destinationLongitude, $value->destinationLatitude, $value->destinationLongitude)  ==5;
            });








            $this->content['rides'] = $rides;
            return response()->json($this->content);
        }else{
            //You already reserved a ride
            $this->content['rides'] = $requestt->ride;
            return response()->json($this->content);
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
