<?php

class History extends MY_Controller {

	public function __construct() {
		parent::__construct();

	}

	public function index() {
		$this->stocksmodel = new Stocksmodel();
		$this->historymodel = new Historymodel();
		$this->portfoliomodel = new Portfoliomodel();

		$data = [];


		if(isset( $_SESSION['user'] ) && !empty( $_SESSION['user'] ) ){




			$data = [
							'history' => $this->prepare_history( $_SESSION['user']['id'] )
							];
		}

		$template_data = [
					'title'	=> 'Trade History',
					'is_admin' => $this->is_admin,
					'active_nav' => 'history',
					'logged_in' => $this->logged_in,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
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
