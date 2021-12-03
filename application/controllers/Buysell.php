<?php

class Buysell extends MY_Controller {

	public function __construct() {
		parent::__construct();

	}

	public function index() {
		$stocksmodel = new Stocksmodel();
		$portfoliomodel = new Portfoliomodel();
		$histmodel = new Historymodel();



		$data = [];
		$result = [null,null,null];
		$all_stocks = $stocksmodel -> get_stocks();

		$all_stock_prices = $stocksmodel -> get_all_current_prices_with_trend();
		
		$this->debug( $stocksmodel -> get_all_current_prices_with_trend() );


		// do buy or sell actions
		if( isset($_POST['buy_num_stock']) && !empty($_POST['buy_num_stock'])
			&& isset($_POST['buy_which_stock']) && !empty($_POST['buy_which_stock'])){

			$result = $stocksmodel -> buy_stocks( $_SESSION['user']['email'], $_POST['buy_num_stock'], $_POST['buy_which_stock'] );

			//check num trades for a highscore
			if($result[0] == 'success'){
				$hs = new Highscoremodel();
				$hs->check_new_highscore( [$_SESSION['user']['name'], $result[1]], MOST_TRADES);
			}
		}

		if(isset($_POST['sell_num_stock']) && !empty($_POST['sell_num_stock'])
			&& isset($_POST['sell_which_stock']) && !empty($_POST['sell_which_stock'])){

			$result = $stocksmodel -> sell_stocks( $_SESSION['user']['email'], $_POST['sell_num_stock'], $_POST['sell_which_stock'] );

			//check for high scores
			if($result[0] == 'success'){
				$hs = new Highscoremodel();
				$hs->check_new_highscore([$_SESSION['user']['name'], $result[1]], MOST_PROFIT);
				$hs->check_new_highscore( [$_SESSION['user']['name'], $result[1]], MOST_TRADES);
			}
		}

		// load their bank balance for display
		$bank_balance = $portfoliomodel->get_bank_balance( $_SESSION['user']['email'] );

		//prepare the user's porfolio array
		$portfolio = [];
		foreach ( $portfoliomodel->get_portfolio($_SESSION['user']['email']) as $k => $v ){
			$code = '';
			$owned = 0;
			foreach($all_stocks as $stock){
				if($stock['stock_id'] == $k){
					$code = $stock['code'];
				}
			}
			$portfolio[] = ['code' => $code, 'stock_id' => $k, 'num_owned' => $v];
		}

		// prepare user's bank history
		$bank_history =[];
		$data = $histmodel -> get_portfolio_hist( $_SESSION['user']['id'] );

		foreach( $data as $i => $row ){
			$bank_history['timestamps'][] = $row['timestamp'];
			$bank_history['balances'][] = $row['bank_balance'];
		}

		//prepare max buy values

		$max_buy = [];
		foreach($all_stocks as $stock){
			$price = 0;
			foreach($all_stock_prices as $rec){
				if($rec['stock_id'] == $stock['stock_id']){
					$price = $rec['price'];
				}
			}
			if($bank_balance > 0 && $price > 0){
				$num = intval( floor( $bank_balance / $price ) );
			}else{
				$num = 0;
			}
			$max_buy[ $stock['stock_id'] ] =  $num;
		}

		$data = [
				'stocks' => $all_stocks,
				'max_buy' => $max_buy,
				'stock_prices' => $all_stock_prices,
				'bank_balance' => $bank_balance,
				'bank_history' => $bank_history,
				'portfolio_stocks' => $portfolio,
				'result' => $result,
				];

		$template_data = [
				'title'	=> 'Buy & Sell Stocks',
				'active_nav' => 'buysell',
				'login_url' => $this->authUrl,
				'game_online' => $this->game_online,
				'page' 	=> $this->load->view('pages/buysell', $data ,TRUE)
			];

		$this->load->view('template', $template_data);
	}

}
