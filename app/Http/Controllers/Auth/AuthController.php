<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Laravel\Socialite\Contracts\Factory as Socialite;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Symfony\Component\HttpFoundation\Session\Flash;
use Illuminate\Auth\Guard;

/**
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    /**
     * @var Socialite
     */
    private $socialite;
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct(Socialite $socialite, Guard $auth)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->socialite = $socialite;
        $this->auth = $auth;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $confirmation_code = str_random(30);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'confirmation_code' => $confirmation_code,
        ]);
        $this->sendVerifyEmail($confirmation_code);

        return $user;
    }

    /**
     * @param array $data
     * @return static
     */
    protected function createSocialUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'provider' => $data['provider'],
            'provider_id' => $data['id'],
        ]);

        return $user;
    }

    /**
     * @param null $provider
     * @return mixed
     */
    public function getSocialAuth($provider=null)
    {
        if (!config("services.$provider")) {abort('404');} //just to handle providers that doesn't exist

        return $this->socialite->with($provider)->redirect();
    }

    /**
     * @param $user
     * @param $provider
     */
    protected function checkSocialUser($user, $provider)
    {
        $registeredUser = User::where('provider_id', '=', $user->id)->first(); //@todo would rather verify provider and provider_id, in case of duplicated provider_ids
        $user->provider = $provider;
        if (!$registeredUser and $user->email !=null) { //if no user create one as long as there is an email returned
            $registeredUser = User::where('email', '=', $user->email)->first();
            if (!$registeredUser) {
                $registeredUser = $this->createSocialUser((array)$user);
            }
        } else if (!$registeredUser) {
            $registeredUser = User::create((array) $user);
        }
        $this->auth->login($registeredUser);
    }

    /**
     * @param null $provider
     * @return string
     */
    public function getSocialAuthCallback($provider=null)
    {
        if ($user = $this->socialite->with($provider)->user()){
            $this->checkSocialUser($user, $provider);

            return redirect('home');
        }else{
            return 'something went wrong';
        }
    }

    /**
     * @param $confirmation_code
     * @throws InvalidConfirmationCodeException if the confirmation_code or user are not found
     * @return mixed
     */
    public function confirm($confirmation_code)
    {
        if( ! $confirmation_code)
        {
            throw new InvalidConfirmationCodeException;  //@todo setup Exception
        }

        $user = User::whereConfirmationCode($confirmation_code)->first();

        if ( ! $user)
        {
            throw new InvalidConfirmationCodeException; //@todo setup Exception
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->save();

//        Flash::message('You have successfully verified your account.'); //@todo flash messaging untested, commenting out for now
        $this->sendWelcomeEmail($user);
        $this->auth->login($user);

        return redirect('home');
    }

    /**
     * @param $confirmation_code
     */
    protected function sendVerifyEmail($confirmation_code)
    {
        Mail::send('emails.verify', ['confirmation_code' => $confirmation_code], function ($message) {
            $message->to(Input::get('email'), Input::get('username'))->subject('Verify your email address');
        });
    }

    /**
     * @param $user
     */
    protected function sendWelcomeEmail($user)
    {
        Mail::send('emails.welcome', ['name' => $user->name], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Welcome to ROI!');
        });
    }
}
