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
			$prices = $this->sm->get_all_current_prices();
			$updates = [];


			foreach($stocks as $stock){
				$volco = $stock['segment_volitility'];
				$old_price = 0;

				foreach($prices as $price){
					if($price['stock_id'] == $stock['stock_id']){
							$old_price = $price['price'];
					}
				}

				//calculate new price
				$new_price = act_of_god( $volco, $old_price );

				//build array of new prices for stock model update
				$updates[] = ['stock_id' => $stock_id, 'price' => $new_price];
			}

			//write new prices to database
			$this->sm->update_stocks($new_prices);

		}else{
			return false;
		}
	}

	private function act_of_god( $volco, $old_price){
		//volco is someshere between 0.01 and 1
		// 0.1 is barely any chance to change and


		if ( ( rand(1, 100) ) /50 > $volco){

			return $old_price * (1 + ( rand(1, 100) / 1000 ) * $volco + ($volco/100) );

		}else{

    		return $old_price * (1 + ( rand(1, 100) / 1000 ) * $volco );

		}
	}


}
