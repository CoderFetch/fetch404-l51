<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Fetch404\Core\Events\UserWasRegistered;
use Fetch404\Core\Repositories\SettingsRepository;
use Fetch404\Core\Repositories\UsersRepository;

use Illuminate\Contracts\Auth\Guard;

use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Hash;
use Laracasts\Flash\Flash;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. 
	|
	*/

	private $auth;

	private $usersRepository;

	private $session;

	private $settingsRepository;

	/**
	 * Show the login page
	 *
	 * @return void
	 */
	public function showLogin()
	{
		return view('core.auth.login');
	}

	/**
	 * Show the signup page
	 *
	 * @return void
	 */
	public function showRegister()
	{
		return view('core.auth.register');
	}

	/**
	 * Attempt to log in a user
	 *
	 * @param Request $request
	 * @return void
	 */
	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required'
		], [
			'email.required' => 'Please enter an email address.',
			'email.email' => 'Please enter a valid email address.',
			'password.required' => 'Please enter a password.'
		]);

		$user = $this->usersRepository->getFirstBy('email', $request->input('email'));

		if (!$user)
		{
			Flash::error('User not found.');
			return redirect()->back();
		}

		if (!Hash::check($request->input('password'), $user->password))
		{
			Flash::error('Invalid password.');
			return redirect()->back();
		}

		$this->auth->loginUsingId($user->id, $request->has('remember'));

		Flash::success('Logged in!');

		return redirect()->to('/');
	}

	/**
	 * Attempt to register a user in the database
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function postRegister(Request $request)
	{
		$rules = [
			'name' => 'required|min:5|max:13|regex:/[A-Za-z0-9\-_!\.\s]/|unique:users',
			'email' => 'required|unique:users|email',
			'password' => 'required|min:8|max:30|confirmed|regex:/[A-Za-z0-9\-_!\$\^\@\#]/'
		];

//		if ($this->settingsRepository->getByName('auth.captcha', false, true) == true)
//		{
//			$rules['g-recaptcha-response'] = 'required';
//		}

		$this->validate($request, $rules, [
			'name.required' => 'A username is required.',
			'name.min' => 'Usernames must be at least 5 characters long.',
			'name.max' => 'Usernames can be up to 13 characters long.',
			'name.regex' => 'You are using characters that are not allowed. Allowed characters: A through Z, a through z, 0 through 9, -, _, !, and . (period)',
			'name.unique' => 'That username is taken. Try another!',
			'email.required' => 'An email address is required.',
			'email.unique' => 'Another account is using this email.',
			'email.email' => 'Please enter a valid email.',
			'password.required' => 'A password is required.',
			'password.min' => 'Passwords must be at least 8 characters long.',
			'password.max' => 'Passwords can be up to 30 characters long.',
			'password.confirmed' => 'Your passwords do not match. Please verify that the confirmation matches the original.',
			'password.regex' => 'You are using characters that are not allowed. Allowed characters: A through Z, a through z, 0 through 9, !, -, _, $, ^, @, #',
			'g-recaptcha-response.required' => 'Please complete the captcha.'
		]);

		$name = $request->input('name');
		$email = $request->input('email');
		$password = $request->input('password');

		$user = $this->usersRepository->create(array(
			'name' => $name,
			'email' => $email,
			'password' => Hash::make($password),
			'confirmed' => 0,
			'slug' => str_slug($name, "-")
		));

		event(new UserWasRegistered($user));

		if ($this->auth->attempt(array('email' => $email, 'password' => $password), 1))
		{
			Flash::success('You have registered on our site! Please check your inbox for a confirmation email.');
			return redirect()->to('/');
		}
		else
		{
			Flash::success('You have registered on our site! Please check your inbox for a confirmation email.');
			return redirect()->to('/');
		}
	}

	public function getLogout()
	{
		if ($this->auth->check())
		{
			$this->auth->user()->update(array(
				'is_online' => 0
			));

			$this->auth->logout();
			$this->session->flush();

			return redirect()
				->to('/');
		}
		else
		{
			return redirect()
				->to('/');
		}
	}


	/**
	 * Create a new authentication controller instance.
	 *
	 * @param Guard $auth
	 * @param Store $session
	 * @param UsersRepository $usersRepository
	 * @param SettingsRepository $settingsRepository
	 * @internal param User $user
	 */
	public function __construct(Guard $auth, Store $session, UsersRepository $usersRepository, SettingsRepository $settingsRepository)
	{
		$this->auth = $auth;
		$this->session = $session;
		$this->usersRepository = $usersRepository;
		$this->settingsRepository = $settingsRepository;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

}
