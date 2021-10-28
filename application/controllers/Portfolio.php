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

		$bank_series =  $this->prepare_bank_series( $_SESSION['user']['id'] );
		$bank_value = $_SESSION['user']['bank_balance'];

		$portfolio_series = $this->prepare_portfolio_series( $_SESSION['user']['id'] );
		$portfolio_value = 0;
		if(!empty($portfolio_series)){
			$portfolio_value = $portfolio_series[count($portfolio_series)-1]['y']; // last value
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

		$template_data = [
					'title'	=> 'My Portfolio',
					'active_nav' => 'portfolio',
					'login_url' => $this->authUrl,
					'game_online' => $this->game_online,
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

		//game start
		// x = time, y = balance
		$series[] = [ 'x' => $game_start, 'y' => $balance ];

		foreach($updates as $snapshot){
			//is update due to market update?
			$flag = true; //only once per market update
			foreach($sxdata as $sxdatum){
				if( $snapshot['timestamp'] == $sxdatum['timestamp'] && $flag){
					$series[] = [ 'x' => $snapshot['timestamp'], 'y' => round($balance, 2) ];
					$flag = false; //block adding another timepoint until next update iteration
				}
			}

			//is update due to trade?
			foreach($txs as $tx){
				if( $snapshot['timestamp'] == $tx['timestamp']){
					$transaction = $tx['tx'] * $tx['tx_price'];
					$balance -= $transaction;
					$series[] = [ 'x' => $tx['timestamp'], 'y' => round($balance, 2) ];
				}
			}

		}


		return $series;
	}

	function prepare_portfolio_series($user_id){
		$updates = $this->historymodel->get_portfolio_hist( $user_id );
		$series = [];

		//if game is new, init first data point and return
		$series[] = ['x'=>$updates[0]['timestamp'], 'y'=>0];
		if( count( $updates ) < 2 ){
			return $series;
		}

		$sxdata = $this->stocksmodel->get_all_ticker();
		$txs = $this->historymodel->get_tx_hist( $user_id );
		$portfolio_value = 0;

		foreach($updates as $snapshot){
			$update_ts = $snapshot['timestamp'];
			$flag = false;
			$temp_val = 0;
			$log = [];

			if(!empty($snapshot['portfolio'])){

				//is this portfolio update due to a market update?
				foreach($sxdata as $sxdatum){
					if($update_ts == $sxdatum['timestamp']){
						$flag = true;
						foreach(json_decode($snapshot['portfolio']) as $stock_id => $stock_num_owned){
							if( $stock_id == $sxdatum['stock_id'] ){
								$temp_val = $temp_val + ( floatval($sxdatum['price']) * floatval($stock_num_owned) );
								$log[] = "market update" . $snapshot['portfolio'];

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
										$portfolio_value = $portfolio_value + (floatval($tx['tx_price']) * floatval($tx['tx']));
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
							'y' => number_format( $portfolio_value, 2),
							'log' => $log
						];
		}

		return $series;
	}

	function prepare_total_series( $portfolio_series, $bank_series ){
		$series = [];

		foreach($portfolio_series as $i => $val){

			// account for users not registering before first update occurs...
			// causes mismatched datset lengths
			// dang disengaged teenagers...
			if( isset($portfolio_series[$i]) && isset($bank_series[$i]) ){
				$sum = $bank_series[$i]['y'] + $portfolio_series[$i]['y'];
			}

			$series[] = [
							'x' => $portfolio_series[$i]['x'],
							'y' => round($sum, 2)
						];
		}
		return $series;
	}

	function prepare_portfolio($user_email){
		$portfolio = $this->portfoliomodel->get_portfolio( $_SESSION['user']['email'] );
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
							$output[] = [
											'code' => $stock['code'],
											'segment' => $stock['segment_name'],
											'price' => round( $price['price'], 2),
											'owned' => intval($num),
											'value' => round( $num * $price['price'], 2)
										];
						}
					}
				}
			}
		}
		return $output;
	}
}
