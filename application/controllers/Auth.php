<?php

class Auth extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}
	
	public function logout() {
		unset($_SESSION['access_token']);
		unset($_SESSION['time']);
		unset($_SESSION['user']);
		redirect(base_url());	

	}
}