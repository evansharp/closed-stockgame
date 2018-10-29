<?php

class Buysell extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
	}
	
	public function index() {
		$stocksmodel = new Stocksmodel();
		$adminmodel = new Adminmodel();
		
		$data = [];
		$result = [null,null];
		
		if(isset( $_SESSION['user'] ) && !empty( $_SESSION['user'] ) ){
			
			if( isset($_POST['buy_num_stock']) && !empty($_POST['buy_num_stock']) 
				&& isset($_POST['buy_which_stock']) && !empty($_POST['buy_which_stock'])){
				$result = $stocksmodel -> buy_stocks( $_SESSION['user']['email'], $_POST['buy_num_stock'], $_POST['buy_which_stock'] );
				$_SESSION['user'] = $adminmodel->get_user( $this->googleUserData['email'] );
			}
			if(isset($_POST['sell_num_stock']) && !empty($_POST['sell_num_stock']) 
				&& isset($_POST['sell_which_stock']) && !empty($_POST['sell_which_stock'])){
				$result = $stocksmodel -> sell_stocks( $_SESSION['user']['email'], $_POST['sell_num_stock'], $_POST['sell_which_stock'] );
				$_SESSION['user'] = $adminmodel->get_user( $this->googleUserData['email'] );
			}
			
			
			$all_stocks = $stocksmodel -> get_stocks();
			
			//prepare the user's porfolio array
			$portfolio = [];
			if(!empty($_SESSION['user']['portfolio'])){
				foreach ( json_decode($_SESSION['user']['portfolio'], true) as $k => $v ){
					$code = '';
					$owned = 0;
					foreach($all_stocks as $stock){
						if($stock['stock_id'] == $k){
							$code = $stock['code'];
						}
					}
					$portfolio[] = ['code' => $code, 'stock_id' => $k, 'num_owned' => $v];
				}
			}
			
			$data = [
							'stocks' => array_chunk( $all_stocks, 8, true),
							'stock_prices' => array_chunk( $stocksmodel -> get_all_current_prices(), 8, true),
							'bank_balance' => $_SESSION['user']['bank_balance'],
							'portfolio_stocks' => $portfolio,
							'result' => $result
							];
		}
		
		$template_data = [
					'title'	=> 'Buy & Sell Stocks',
					'is_admin' => $this->is_admin,
					'active_nav' => 'buysell',
					'logged_in' => $this->logged_in,
					'authorized' => $this->authorized,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
					'page' 	=> $this->load->view('pages/buysell', $data ,TRUE)
				];
				
		$this->load->view('template', $template_data);
	}

}