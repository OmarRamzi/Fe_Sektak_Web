<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\User;
use App\Car;
use App\Requestt;

class ProfilesController extends Controller
{
    public function showProfile($user_id)
    {
        $user = User::find($user_id);
        $profile = $user->profile;
        $car = $user->car;
        return view('profiles.profile', ['user' => $user, 'profile' => $profile, 'car' => $car]);
    }
    public function showCarForm()
    {
        return view('car.createForm');
        //dd($user);

    }
    public function fillDetails(Request $request, $user_id)
    {
        $user = User::find($user_id);
        $car = $user->car()->create([
            'user_id'=>$user_id,
            'license' => $request->license,
            'carModel' => $request->model,
            'color' => $request->color,
        ]);
        $user->update([
            'type'=>'driver',
        ]);
        return redirect(route('home'));
    }
    public function edit($id )
    {
        $user=User::find($id);
        $profile = $user->profile;
        $car = $user->car;
        return view('profiles.editProfile')->with('user',$user)->with('profile',$profile)->with('car',$car);
    }
    public function update($user_id, Request $request)
    {
        $user=User::find($user_id);
        $profile = $user->profile;
        $car = $user->car;
        $data = $request->all();
        if ($request->hasFile('picture')) {
          $picture = $request->picture->store('profilesPictures', 'public');
          $profile->update([
            'picture'=>$picture,
          ]);
        }

        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobileNum'=>$request->mobileNum,

        ]);

        $profile->update([
            'job'=>$request->job,

        ]);
        $car->update([
            'license' => $request->license,
            'carModel' => $request->model,
            'color' => $request->color,
        ]);

        session()->flash('flashMessage','Profile updated successfully');
        return redirect(route('users.showProfile',$user->id));
    }
}
