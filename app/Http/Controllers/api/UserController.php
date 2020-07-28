<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Car;
use App\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->content = array();
    }
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();

           $this->content['user'] = $user;
           $this->content['user']['profile'] = $user->profile;
           $this->content['user']['car'] = $user->car;
            return response()->json($this->content);
        } else {
            $this->content['error'] = "Unauthorized";
            return response()->json($this->content);
        }
    }
    public function getById(){
        return User::findOrFail(request('userId'));
    }
    public function register()
    {
        $data = request()->all();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phoneNumber' => ['required', 'string', 'min:8', 'unique:users'],
            'nationalId' => ['required', 'string', 'min:8', 'unique:users']
        ];
        $carRules = [
            'license' => ['required','min:8','unique:cars'],
            'model' => 'required|string',
            'color' => 'required|string',
            'userLicense' => 'required|min:8|unique:cars',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = new User;
            $user->name = request('name');
            $user->email = request('email');
            $user->phoneNumber = request('phoneNumber');
            $user->nationalId = request('nationalId');
            $user->password = Hash::make(request('password'));
//            dd(request('car')['license']);
            if (request('car') != null) {
                $validator = Validator::make(request('car'), $carRules);
                if ($validator->passes()) {
                    $car = new Car;
                    $car->license = request('car')['license'];
                    $car->carModel = request('car')['model'];
                    $car->color = request('car')['color'];
                    $car->userLicense = request('car')['userLicense'];
                    $user->save();
                    $car->user_id = $user->id;
                    $car->save();
                    $profile = Profile::create([
                        'user_id' => $user->id,
                        'picture' => $user->getGravatar(),
                    ]);
                    $this->content['status'] = 'done';
                    return response()->json($this->content);
                } else {
                    $this->content['status'] = 'undone';
                    $this->content['details'] = $validator->errors()->all();
                    return response()->json($this->content);
                }
            }
            $user->save();
            $profile = Profile::create([
                'user_id' => $user->id,
                'picture' => $user->getGravatar(),
            ]);
            $this->content['status'] = 'done';
        } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
        }
        return response()->json($this->content);
    }
    public function details()
    {
        return response()->json(['user' => Auth::user()]);
    }

}
