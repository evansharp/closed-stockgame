<?php

class Prospectus extends MY_Controller {

	protected $stocksmodel;

	public function __construct() {
		parent::__construct();
		$this->stocksmodel = new Stocksmodel();
	}


	public function index(){

		$data = ['prospecti' => $this->stocksmodel->get_prospecti() ];

		$template_data = [
					'title'	=> 'Prospectus',
					'active_nav' => 'prospectus',
					'login_url' => $this->authUrl,
					'userData' => $this->google_user_data,
					'game_online' => $this->game_online,
					'page' 	=> $this->load->view('pages/prospectus', $data ,TRUE)
				];

		$this->load->view('template', $template_data);
	}
}
