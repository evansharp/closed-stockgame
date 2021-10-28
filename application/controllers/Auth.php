<?php

class Auth extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}


		public function logout() {
		$reason = $this->session->flashdata('loggedoutreason');

		$this->session->sess_destroy(); // can't use flashdata again now... :/

		redirect( base_url() );

	}
}
