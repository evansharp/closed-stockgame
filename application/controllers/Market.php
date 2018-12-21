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
		// their segment volitility coefficeient

		// first make sure this IS our server's cron job calling...
		// key is md5 hash of "go"
		if( $_GET['key'] == '34d1f91fb2e514b8576fab1a75a89a6b' ){

			//ensure gameticks are happeneing with db counter
			//$tmp = $this->ad->get_setting( 'test' );
			//$this->ad->set_setting( 'test', $tmp + 1 );

			$stocks = $this->sm->get_stocks();
			$prices = $this->sm->get_all_current_prices();
			$updates = [];

			foreach($stocks as $stock){
				$volco = $stock['segment_volitility'];
				$percent_free_market_cap = $stock['num_shares'] / $stock['total_shares'];

				$old_price = 0;

				foreach($prices as $price){
					if( $price['stock_id'] == $stock['stock_id'] ){
						$old_price = $price['price'];
					}
				}

				//calculate new price
				$new_price = $this->act_of_god( $volco, $old_price, $percent_free_market_cap );

				// if price goes negative. Set to 0, which will trigger 'bankrupt' logic
				if( $new_price < 0.01 ){
					$new_price = 0;

					$this->bankrupcy( $stock['stock_id'] );
				}

				//build array of new prices for stock model update
				$updates[] = ['id' => $stock['stock_id'], 'price' => $new_price];
			}

			//write new prices to database
			$this->sm->update_stocks( $updates );
			return "done";
		}else{
			return false;
		}
	}

	private function act_of_god( $volco, $old_price, $free_cap){
		//volco is someshere between 0 and 1
		// 0.1 is barely any chance to go down, but small changes,
		// while 1 is 50% of the time and up to 11% changes

		// the more shares of a stock on the market, the less valuable it should be
		// so bias the change calc using the percent of shares $available

		$calc = 0;

		if ( ( rand(1, 100) /50 ) > $volco ){
			// price should go up

			$calc = $old_price * (1 + ( rand(1, 100) / 1000 ) * $volco + ($volco/100) );

			//debug occurances with DB coutner
			//$tmp = $this->ad->get_setting( 'stock_should_up' );
			//$this->ad->set_setting( 'stock_should_up', $tmp + 1 );

		}else{
			// price should go down
			$calc = $old_price * (1 - ( rand(1, 100) / 1000 ) * $volco );

			//debug occurances with DB coutner
			//$tmp = $this->ad->get_setting( 'stock_should_down' );
			//$this->ad->set_setting( 'stock_should_down', $tmp + 1 );
		}


		return floor($calc * 100) / 100; // return as rounded to a hundreth
	}

	private function bankrupcy( $stock_id ){
		return false;
	}

}
