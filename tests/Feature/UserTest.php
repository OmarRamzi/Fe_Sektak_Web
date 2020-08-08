<?php

namespace Tests\Feature;
use App\User;
use APP\Car;
use App\Ride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('');

        $response->assertStatus(200);
    }

    /**
     * A login test.
     *
     * @test
     */

    public function LoginTest()
    {
        $response= $this->get('/home')
        ->assertRedirect('/login');
    }

    /**
     * Authorized test.
     * @test
     * 
     */


    public function Authenticated_users_can_enter_to_home()
    {
        $this->actingAs(factory(User::class)->create([
            'name'=>'ramzi',
            'email'=>'ramzi@g.com',
            'password'=>'123456789',
            'nationalId'=>'123456789',
            'phoneNumber'=>'123456789'

        ]));
        $response= $this->get('/home')->assertOk();
    }

    /**
     * 'Filling car details and store it' test.
     * @test
     */
    public function filling_car_details(){
        //built-in exception handler that allows you
        // to report and render exceptions easily and in a friendly manner.
        $this->withoutExceptionHandling();
        //to run it faster than ordinary times without firing events.
        Event::fake();

        $this->actingAs(factory(User::class)->create([
           'email'=>'test@test.com',
           'nationalId'=>'123456789',
           'phoneNumber'=>'123456789',
           'name'=>'ramzi',
           'password'=>'123456789',
           ]));

           $response= $this->post('/fillCarDetails/1',[
               'user_id'=>1,
               'userLicense'=>'123456789',
               'license'=>'123456789',
               'carModel'=>'bmw',
               'nationalId'=>'123456789',
               'color'=>'red'
           ]);
           
           $this->assertCount(1,Car::all());
    }
    /**
     * 'sending request' test.
     * @test
     */

    public function sending_request_details(){
      
        $this->withoutExceptionHandling();
      
        Event::fake();

        $this->actingAs(factory(User::class)->create([
            'email'=>'test@test.com',
            'nationalId'=>'123456789',
            'phoneNumber'=>'123456789',
            'name'=>'ramzi',
            'password'=>'123456789',
        ]));



        $response = $this->withSession([
            //request
            'user_id'=>1,
            'userLicense'=>'123456789',
            'license'=>'123456789',
            'carModel'=>'bmw',
            'nationalId'=>'123456789',
            'color'=>'red',
            
     ]) ->get('/requests/1/AvailableRides/1');

           $this->assertCount(1,Car::all());
    }
}
