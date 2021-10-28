<?php

class History extends MY_Controller {

	public function __construct() {
		parent::__construct();

	}
	

	public function index() {
		$this->stocksmodel = new Stocksmodel();
		$this->historymodel = new Historymodel();
		$this->portfoliomodel = new Portfoliomodel();

		$data = [ 'history' => $this->prepare_history( $_SESSION['user']['id'] ) ];

		$template_data = [
					'title'	=> 'Trade History',
					'active_nav' => 'history',
					'login_url' => $this->authUrl,
					'game_online' => $this->game_online,
					'page' 	=> $this->load->view('pages/history', $data ,TRUE)
				];

		$this->load->view('template', $template_data);
	}

	function prepare_history($user_id){
		$txs = $this->historymodel->get_tx_hist($user_id);
		$all_stocks = $this->stocksmodel -> get_stocks();
		$series = [];

		foreach($txs as $tx){
			$time = date('M j G:i A', $tx['timestamp']);
			foreach($all_stocks as $stock){
				if($stock['stock_id'] == $tx['stock_id']){
					$code = $stock['code'];
				}
			}

			$trade = $tx['tx'];
			$price = $tx['tx_price'];
			$value = abs($trade * $price);

			$series[] = ['time' => $time, 'code' => $code, 'trade' => $trade, 'price' => number_format($price,2), 'trade_val' => number_format($value,2)];
		}


		return $series;
	}

}
