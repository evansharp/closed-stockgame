<?php

class Ticker extends MY_Controller {

	public function __construct() {
		parent::__construct();

	}


	public function index() {

		$stocksmodel = new Stocksmodel();
		$all_stocks = $stocksmodel->get_all_ticker();
		//loop through and split out the per-stock data for the big chart
		$per_stock = [];

		foreach($all_stocks as $stock){
			$per_stock[ $stock['code'] ][] = $stock;
		}

		//generate the two indexes and add them to the front


		$per_timestamp = [];
		foreach($all_stocks as $stock){
			$per_timestamp[ $stock['timestamp'] ][] = $stock;
		}

		// comodities index
		$i = 0;
		$p = 0;
		foreach($per_timestamp as $time){
			$sum = 0.0;
			$p = 0;
			foreach($time as $stock){
				//die("<pre>".print_r($stock, true)."</pre>");
				if( $stock['is_comodity'] ){
					$sum += floatval( $stock['price'] );
					$p++;
				}
			}
			$avg = round( $sum/ $p, 2 );
			$per_stock['Index2'][$i]['price'] = $avg;
			$per_stock['Index2'][$i]['timestamp'] = $time[0]['timestamp'];
			$per_stock['Index2'][$i]['code'] = 'Comodities';
			$i++;
		}
		array_unshift( $per_stock, array_pop( $per_stock ) );

		// Generl market index
		$i = 0;
		foreach($per_timestamp as $time){
			$sum = 0.0;
			foreach($time as $stock){
				if( !$stock['is_comodity'] ){
					$sum += floatval( $stock['price'] );
				}
			}
			$avg = round( $sum/ count( $time ) , 2);
			$per_stock['Index'][$i]['price'] = $avg;
			$per_stock['Index'][$i]['timestamp'] = $time[0]['timestamp'];
			$per_stock['Index'][$i]['code'] = 'Market Cap';
			$i++;
		}
		array_unshift( $per_stock, array_pop( $per_stock ) );




		//die("<pre>".print_r($per_timestamp, true)."</pre>");



		$data = [ 'ticker_all' => $per_stock ];

		$template_data = [
					'title'	=> 'Ticker',
					'active_nav' => 'ticker',
					'login_url' => $this->authUrl,
					'game_online' => $this->game_online,
					'page' 	=> $this->load->view('pages/ticker', $data ,TRUE)
				];

		$this->load->view('template', $template_data);

	}

}
