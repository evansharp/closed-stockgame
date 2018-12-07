<?php

class Ticker extends MY_Controller {

	public function __construct() {
		parent::__construct();

	}

	public function index() {

		$stocksmodel = new Stocksmodel();

		$all_segments = $stocksmodel->get_all_ticker();


		//loop through and split out the per-stock data for the big chart
		$per_stock = [];

		foreach($all_segments as $stock){
			$per_stock[ $stock['code'] ][] = $stock;
		}

		//loop through and split out the per-segment data for those charts
		$per_segment = [];

		foreach($all_segments as $stock){

				$per_segment[ $stock['segment_name'] ][ $stock['stock_id'] ][] = $stock;

		}


		$data = [
						'ticker_all' => $per_stock,
						'ticker_segments' => $per_segment,
						'user' => $this->googleUserData,
						'test' => $stocksmodel->get_stocks()
						];

		$template_data = [
					'title'	=> 'Ticker',
					'is_admin' => $this->is_admin,
					'active_nav' => 'ticker',
					'logged_in' => $this->logged_in,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
					'page' 	=> $this->load->view('pages/ticker', $data ,TRUE)
				];

		$this->load->view('template', $template_data);

	}

}
