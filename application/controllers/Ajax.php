<?php

class Ajax extends MY_Controller {

	protected $portfoliomodel;
	
	public function __construct() {
		parent::__construct();
		
		$this->portfoliomodel = new Portfoliomodel();
	}

	function get_balance(){
		//return $this->portfoliomodel->get_bank_balance( $_SESSION['user']['email'] );
		//return $_SESSION['user']['bank_balance'];
	}
	function get_portfolio(){
		//return $this->portfoliomodel->get_portfolio( $_SESSION['user']['email'] );
		//return $_SESSION['user']['portfolio'];
	}
}