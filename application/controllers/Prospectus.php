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
					'is_admin' => $this->is_admin,
					'active_nav' => 'prospectus',
					'logged_in' => $this->logged_in,
					'authorized' => $this->authorized,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
					'page' 	=> $this->load->view('pages/prospectus', $data ,TRUE)
				];
				
		$this->load->view('template', $template_data);
	}
}