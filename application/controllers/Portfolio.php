<?php

class Portfolio extends MY_Controller {

	protected $stocksmodel;
	protected $historymodel;
	protected $portfoliomodel;


	public function __construct() {
		parent::__construct();
		$this->stocksmodel = new Stocksmodel();
		$this->historymodel = new Historymodel();
		$this->portfoliomodel = new Portfoliomodel();
	}

	public function index() {
		$data = [];
		if( !empty($_SESSION['user']) ){

			$bank_series =  $this->prepare_bank_series( $_SESSION['user']['id'] );
			$bank_value = 0;
			if(!empty($bank_series)){
				$bank_value = $bank_series[count($bank_series)-1]['y'];
			}

			$portfolio_series = $this->prepare_portfolio_series( $_SESSION['user']['id'] );
			$portfolio_value = 0;
			if(!empty($portfolio_series)){
				$portfolio_value = $portfolio_series[count($portfolio_series)-1]['y'];
			}

			$total_series = $this->prepare_total_series( $portfolio_series, $bank_series);
			$total_value = 0;
			if(!empty($total_series)){
				$total_value = $total_series[count($total_series)-1]['y'];
			}

			$data = [
								'bank_series' => $bank_series,
								'bank_value' => $bank_value,
								'portfolio_series' => $portfolio_series,
								'portfolio_value' => $portfolio_value,
								'total_series' => $total_series,
								'total_value' => $total_value,
								'portfolio' => $this->prepare_portfolio($_SESSION['user']['email']),
								'updates_series' => $this->prepare_update_times($_SESSION['user']['id']),
								'trade_count' => $this->historymodel->get_num_trades($_SESSION['user']['id']),
								'game_start' => $this->historymodel->get_game_start()
							];
		}
		$template_data = [
					'title'	=> 'Your Portfolio',
					'is_admin' => $this->is_admin,
					'active_nav' => 'portfolio',
					'logged_in' => $this->logged_in,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
					'page' 	=> $this->load->view('pages/portfolio', $data ,TRUE)
				];

		$this->load->view('template', $template_data);

	}

	function prepare_update_times($user_id){
		$updates = $this->historymodel->get_portfolio_hist( $user_id );
		$series = [];

		foreach($updates as $snapshot){
			$series[] = [ $snapshot['timestamp'] ];

		}
		return $series;
	}

	function prepare_bank_series($user_id){
		$balance = $this->historymodel->get_starting_balance();
		$game_start = $this->historymodel->get_game_start();
		$txs = $this->historymodel->get_tx_hist( $user_id );
		$updates = $this->historymodel->get_portfolio_hist( $user_id );
		$sxdata = $this->stocksmodel->get_all_ticker();

		// x = time, y = balance
		$series[] = [ 'x' => $game_start, 'y' => $balance ];
		//$series = [];

		foreach($updates as $snapshot){
			//is update due to market update?
			$flag = true; //only once per market update
			foreach($sxdata as $sxdatum){
				if( $snapshot['timestamp'] == $sxdatum['timestamp'] && $flag){

					$series[] = [ 'x' => $snapshot['timestamp'], 'y' => $balance ];
					$flag = false; //block adding another timepoint until next update iteration
				}
			}

			//is update due to trade?
			foreach($txs as $tx){
				if( $snapshot['timestamp'] == $tx['timestamp']){
					$transaction = $tx['tx'] * $tx['tx_price'];
					$balance -= $transaction;
					$series[] = [ 'x' => $tx['timestamp'], 'y' => $balance ];
				}
			}

		}


		return $series;
	}

	function prepare_portfolio_series($user_id){
		$updates = $this->historymodel->get_portfolio_hist( $user_id );
		$series = [];

		//if game is new, init first data point and return
		if( count( $updates ) < 2 ){
			$series[] = ['x'=>$updates[0]['timestamp'], 'y'=>0];
			return $series;
		}


		$sxdata = $this->stocksmodel->get_all_ticker();
		$txs = $this->historymodel->get_tx_hist( $user_id );
		$portfolio_value = 0;

		foreach($updates as $snapshot){
			$update_ts = $snapshot['timestamp'];
			$flag = false;
			$temp_val = 0;
			$log = '';

			if(!empty($snapshot['portfolio'])){

				//is this portfolio update due to a market update?
				foreach($sxdata as $sxdatum){
					if($update_ts == $sxdatum['timestamp']){
						$flag = true;
						foreach(json_decode($snapshot['portfolio']) as $stock_id => $stock_num_owned){
							if( $stock_id == $sxdatum['stock_id'] ){

								//$portfolio_value += $sxdatum['price'] * $stock_num_owned;
								$temp_val += $sxdatum['price'] * $stock_num_owned;
								$log = "market update" . $snapshot['portfolio'];

							}

						}

					}
				}
				if( $flag ){
					$portfolio_value = $temp_val;
				}

				//is this portfolio update due to a transaction?
				foreach($txs as $tx){
					if($update_ts == $tx['timestamp']){

						$portfolio_arr = json_decode( $snapshot['portfolio'], true );

						if( $tx['tx'] > 0 ){
							foreach($portfolio_arr as $stock_id => $stock_num_owned){
								//IS BUY OR SELL?

									//IS BUY
									if( $stock_id == $tx['stock_id'] ){

										$log[] = "buy ". $tx['stock_id'] ." , portfolio_value is ".  $portfolio_value . ", tx is + " . $tx['tx_price'] * $tx['tx'];
										$portfolio_value += $tx['tx_price'] * $tx['tx'];


									}

							}
						}else{
							//IS SELL
							$log[] = "sell " . $tx['stock_id'] . ", portfolio_value is ".  $portfolio_value . ", tx is + " . $tx['tx_price'] * $tx['tx'];

							$portfolio_value += $tx['tx_price'] * $tx['tx'];
						}

					}
				}
			}
			$series[] = [	'x' => $snapshot['timestamp'],
										'y' => $portfolio_value,
										'log' => $log];
		}

		return $series;
	}

	function prepare_total_series( $portfolio_series, $bank_series ){
		// $game_start = $this->historymodel->get_game_start();
		// $updates = $this->historymodel->get_portfolio_hist( $user_id );
		// $sxdata = $this->stocksmodel->get_all_ticker();
		// $txs = $this->historymodel->get_tx_hist( $user_id );
		// $portfolio_value = 0;
		// $action = '';

		// foreach($updates as $snapshot){
		// 	$sum = 0;
		// 	$portfolio_value = 0;

		// 	$update_ts = $snapshot['timestamp'];
		// 	$bank = $snapshot['bank_balance'];

		// 	if(!empty($snapshot['portfolio'])){

		// 		//is this portfolio update due to a market update?
		// 		foreach($sxdata as $sxdatum){
		// 			if($update_ts == $sxdatum['timestamp']){

		// 				foreach(json_decode($snapshot['portfolio']) as $stock_id => $stock_num_owned){
		// 					if( $stock_id == $sxdatum['stock_id'] ){

		// 						$portfolio_value += $sxdatum['price'] * $stock_num_owned;

		// 						$action = "market update";

		// 					}

		// 				}

		// 			}
		// 		}

		// 		//is this portfolio update due to a transaction?
		// 		foreach($txs as $tx){
		// 			if($update_ts == $tx['timestamp']){

		// 				$portfolio_arr = json_decode( $snapshot['portfolio'] );

		// 				foreach($portfolio_arr as $stock_id => $stock_num_owned){
		// 					if( $stock_id == $tx['stock_id'] ){

		// 						$portfolio_value += $tx['tx_price'] * $stock_num_owned;

		// 						$action = "buy-sell";

		// 					}
		// 				}

		// 			}
		// 		}
		// 	}

		$series = [];
		foreach($portfolio_series as $i => $val){

			$sum = $bank_series[$i]['y'] + $portfolio_series[$i]['y'];

			$series[] = [	'x' => $portfolio_series[$i]['x'],
										'y' => $sum];
		}
		return $series;
	}

	function prepare_portfolio($user_email){
		$portfolio = $this->portfoliomodel->get_portfolio( $user_email );
		$stocks = $this->stocksmodel->get_stocks();
		$prices = $this->stocksmodel->get_all_current_prices();
		$output = [];

		if(!$portfolio){
			return $output;
		}
		foreach($stocks as $stock){
			foreach($portfolio as $id => $num){
				if($stock['stock_id'] == $id){
					foreach($prices as $price){
						if($price['stock_id'] == $id){
							$output[] = [	'code' => $stock['code'],
														'segment' => $stock['segment_name'],
														'price' => $price['price'],
														'owned' => $num,
														'value' => $num * $price['price']
													];
						}
					}
				}
			}
		}
		return $output;
	}
}
