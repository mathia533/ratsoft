<?php

namespace AppBundle\Services;

class Helpers
{
	public $jwt_auth;
	
	public function __construct($jwt_auth) {
		$this->jwt_auth = $jwt_auth;
	}

	public function authCheck($hash, $getIdentity = false) {
		$jwt_auth = $this->jwt_auth;

		$auth = false;
		if ($hash != null) {
			if ($getIdentity == false) {
				$check_token = $jwt_auth->checkToken($hash);
				if ($check_token == true) {
					$auth = true;
				}
			} else {
				$check_token = $jwt_auth->checkToken($hash, true);
				if (is_object($check_token)) {
					$auth = $check_token;
				}
			}
		}

		return $auth;
	}
}