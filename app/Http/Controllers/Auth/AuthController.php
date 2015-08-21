<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator, Auth, Lang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller {

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

      // use AuthenticatesAndRegistersUsers, ThrottlesLogins;
 
      /**
      * Create a new authentication controller instance.
      *
      * @return void
      */
      public function __construct() {
           $this->middleware('guest', ['except' => 'getLogout']);
      }

      /**
      * Get a validator for an incoming registration request.
      *
      * @param  array  $data
      * @return \Illuminate\Contracts\Validation\Validator
      */
      protected function validator(array $data) {
           return Validator::make($data, 
                array(
                     'email' => 'required|email|max:255|unique:users',
                     'password' => 'required|confirmed|min:6'
                )
           );
      }

      /**
      * Create a new user instance after a valid registration.
      *
      * @param  array  $data
      * @return User
      */

      protected function create(array $data) {
           return User::create(
                array(
                     'email' => $data['email'],
                     'password' => bcrypt($data['password'])
                )
           );
      }

      protected function redirectPath() {
           return '/auth/login';
      }
      
      /**
      * Show the application login form.
      *
      * @return \Illuminate\Http\Response
      */
      public function getLogin() {
           if (view()->exists('auth.authenticate')) {
                return view('auth.authenticate');
           }
           return view('auth.login');
      }

      /**
      * Handle a login request to the application.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
      public function postLogin(Request $request) {
           $this->validate($request, [
                $this->loginUsername() => 'required', 'password' => 'required',
           ]);
           // If the class is using the ThrottlesLogins trait, we can automatically throttle
           // the login attempts for this application. We'll key this by the username and
           // the IP address of the client making these requests into this application.
           $throttles = $this->isUsingThrottlesLoginsTrait();
           if ($throttles && $this->hasTooManyLoginAttempts($request)) {
                return $this->sendLockoutResponse($request);
           }
           $credentials = $this->getCredentials($request);
           $credentials['role'] = 'user';
           if (Auth::attempt($credentials, $request->has('remember'))) {
                // return $this->handleUserWasAuthenticated($request, $throttles);
                return redirect($this->loginPath());
           }
           // If the login attempt was unsuccessful we will increment the number of attempts
           // to login and redirect the user back to the login form. Of course, when this
           // user surpasses their maximum number of attempts they will get locked out.
           if ($throttles) {
                $this->incrementLoginAttempts($request);
           }
           return redirect($this->loginPath())->withInput($request->only($this->loginUsername(), 'remember'))->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
           ]);
      }


      /**
      * Show the application registration form.
      *
      * @return \Illuminate\Http\Response
      */
      public function getRegister() {
           return view('auth.register');
      }

      /**
      * Handle a registration request for the application.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
      public function postRegister(Request $request) {
           $validator = $this->validator($request->all());
           if ($validator->fails()) {
                $this->throwValidationException($request, $validator);
           }
           $this->create($request->all());
           // Auth::login($this->create($request->all()));
           return redirect($this->redirectPath())->withMessage("You Are Successfully Registered.Please Confirm your mail to Activate your Account.");
      }

      /**
      * Send the response after the user was authenticated.
      *
      * @param  \Illuminate\Http\Request  $request
      * @param  bool  $throttles
      * @return \Illuminate\Http\Response
      */
      protected function handleUserWasAuthenticated(Request $request, $throttles) {
           if ($throttles) {
                $this->clearLoginAttempts($request);
           }
           if (method_exists($this, 'authenticated')) {
                return $this->authenticated($request, Auth::user());
           }
           return redirect()->intended($this->redirectPath());
      }

      /**
      * Get the needed authorization credentials from the request.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return array
      */
      protected function getCredentials(Request $request) {
           return $request->only($this->loginUsername(), 'password');
      }

      /**
      * Get the failed login message.
      *
      * @return string
      */
      protected function getFailedLoginMessage() {
           return Lang::has('auth.failed') ? Lang::get('auth.failed') : 'These credentials do not match our records.';
      }

      /**
      * Log the user out of the application.
      *
      * @return \Illuminate\Http\Response
      */
      public function getLogout() {
           Auth::logout();
           return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
      }

      /**
      * Get the path to the login route.
      *
      * @return string
      */
      public function loginPath() {
           return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
      }

      /**
      * Get the login username to be used by the controller.
      *
      * @return string
      */
      public function loginUsername() {
           return property_exists($this, 'username') ? $this->username : 'email';
      }

      /**
      * Determine if the class is using the ThrottlesLogins trait.
      *
      * @return bool
      */
      protected function isUsingThrottlesLoginsTrait() {
           return in_array( ThrottlesLogins::class, class_uses_recursive(get_class($this)) );
      }
}