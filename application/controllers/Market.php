<?php

class Market extends MY_Controller {

	protected $sm;

	public function __construct() {
		parent::__construct();

		$this->sm = new Stocksmodel();
	}

	public function gameTick(){
		// calculate a change for all stock prices based on
		// their segment colitility coefficeient and an random number
	}


}
