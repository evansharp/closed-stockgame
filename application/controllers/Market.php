<?php

class Market extends MY_Controller {

	protected $sm;
	protected $ad;

	public function __construct() {
		parent::__construct();

		$this->sm = new Stocksmodel();
		$this->ad = new Adminmodel();

	}

	public function gameTick(){
		// calculate a change for all stock prices based on
		// their segment colitility coefficeient and an random number

		// first make sure this IS our server's cron job calling...
		// key is md5 hash of "go"
		if( $_GET['key'] == '34d1f91fb2e514b8576fab1a75a89a6b' ){
			$val = $this->ad->get_setting('test');
			$this->ad->set_setting('test', (int)$val + 1 );
		}else{
			return false;
		}
	}


}
