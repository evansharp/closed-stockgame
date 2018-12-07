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
		// their segment volitility coefficeient and an random number

		// first make sure this IS our server's cron job calling...
		// key is md5 hash of "go"
		if( $_GET['key'] == '34d1f91fb2e514b8576fab1a75a89a6b' ){

			$stocks = $this->sm->get_stocks();
			$new_prices = [];
			$entropy = act_of_god();

			foreach($stocks as $stock){
				$price = $stock['']
				$volco = $stock['segment_volitility'];
				$percent_free = $stock['num_shares'] / $stock['total_shares'];
			}

		}else{
			return false;
		}
	}

	private function act_of_god(){
		//get random 1-100 number 60% biased greater than 50
		if (rand(0, 10) > 6){
    		return rand(50, 100);
		}else{
    		return rand(1, 49);
		}
	}


}
