<?php

namespace App\Http\Controllers; 

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller{

	public function updateUsers($users)
	{
		foreach ($users as $user) {
			try {
				if (validateUser($user)){
					DB::table('users')->where('id', $user['id'])->update([
						'name' => $user['name'],
						'login' => $user['login'],
						'email' => $user['email'],
						'password' => password_hash($user['password'], PASSWORD_DEFAULT)
					]);
			} catch (\Throwable $e) {
				return Redirect::back()->withErrors(['error', ['We couldn\'t update user: ' . $e->getMessage()]]);
			}
		}
		return Redirect::back()->with(['success', 'All users updated.']);
	}

	public function storeUsers($users)
	{

		foreach ($users as $user) {
			try {
				if (validateUser($user)){
					DB::table('users')->insert([
						'name' => $user['name'],
						'login' => $user['login'],
						'email' => $user['email'],
						'password' => password_hash($user['password'], PASSWORD_DEFAULT)
				]);
			} catch (\Throwable $e) {
				return Redirect::back()->withErrors(['error', ['We couldn\'t store user: ' . $e->getMessage()]]);
			}
		}
		$this->sendEmail($users);
		return Redirect::back()->with(['success', 'All users created.']);
	}

	private function sendEmail($users)
	{
		foreach ($users as $user) {
			$message = 'Account has beed created. You can log in as <b>' . $user['login'] . '</b>';
			if ($user['email']) {
				Mail::to($user['email'])
					->cc('support@company.com')
					->subject('New account created')
					->queue($message);
			}
		}
		return true;
	}
	
	private function validateUser($user)
	{
        return ( $user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10 );
	}

}