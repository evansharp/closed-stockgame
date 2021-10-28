<?php

class Admin extends MY_Controller {

	public function __construct() {
		parent::__construct();

		$this->stocksmodel = new Stocksmodel();
		$this->adminmodel = new Adminmodel();
		$this->historymodel = new Historymodel();
		$this->portfoliomodel = new Portfoliomodel();

		if( $_SESSION['user_role'] != "admin" ){
			redirect( base_url() );
		}
	}

	public function index( $nav_active = 'dashboard' ) {

		die('here');

		if( isset($_POST) && $_POST ){

			// stocks

			if( isset($_POST['delete_stock_id']) && !empty($_POST['delete_stock_id']) ){
				$this->stocksmodel -> delete_stock( $_POST['delete_stock_id'] );
			}
			if( isset($_POST['edit_stock_id']) && !empty($_POST['edit_stock_id'])
		 			&& isset($_POST['edit_market_cap']) && !empty($_POST['edit_market_cap']) ){
				$this->stocksmodel -> update_market_cap( $_POST['edit_stock_id'], $_POST['edit_market_cap'] );
			}
			if( isset($_POST['add_stock_name']) && !empty($_POST['add_stock_name'])
					&& isset($_POST['add_stock_code']) && !empty($_POST['add_stock_code'])
					&& isset($_POST['add_stock_segment']) && !empty($_POST['add_stock_segment'])
					&& isset($_POST['add_stock_initprice']) && !empty($_POST['add_stock_initprice'])
					&& isset($_POST['add_stock_initnumshares']) && !empty($_POST['add_stock_initnumshares'])){
				$this->stocksmodel -> add_stock( $_POST['add_stock_name'], $_POST['add_stock_code'], $_POST['add_stock_segment'], $_POST['add_stock_initprice'], $_POST['add_stock_initnumshares']);
			}
			if(isset($_POST['edit_prospectus_text']) && !empty($_POST['edit_prospectus_text']) ){
				$this->stocksmodel->update_prospectus( $_POST['edit_prospectus_stock'], $_POST['edit_prospectus_text'] );
			}

			if( isset($_POST['update_stock_price']) && !empty($_POST['update_stock_price']) ){
				$this->stocksmodel -> update_stocks( $_POST['update_stock_price'] );
			}
			if( isset($_POST['add_segment_name']) && !empty($_POST['add_segment_name']) ){
				$this->stocksmodel -> add_segment( $_POST['add_segment_name'] );
			}
			if( isset($_POST['delete_segment_id']) && !empty($_POST['delete_segment_id']) ){
				$this->stocksmodel -> delete_segment( $_POST['delete_segment_id'] );
			}
			if( isset($_POST['edit_segment_id']) && !empty($_POST['edit_segment_id']) ){
				$this->stocksmodel -> edit_segment( $_POST['edit_segment_id'], $_POST['edit_segment_name'], $_POST['edit_segment_vol'] );
			}

			// settings

			if( isset($_POST['set_setting_game_on']) && !empty($_POST['set_setting_game_on']) ){
				$this->adminmodel->set_setting( 'game_active', $_POST['set_setting_game_on'] );
			}
			if( isset($_POST['reset']) && !empty($_POST['reset']) ){
				$this->adminmodel->reset_game();
				$this->stocksmodel -> reset_stocks();
				redirect('logout', 'refresh');
			}

		}


		$data = [ 	'segments' => $this->stocksmodel->get_segments(),
					'stocks' => $this->stocksmodel -> get_stocks(),
					'player_activity' => $this->adminmodel -> get_login_history(),
					'explorer' => $this->prepare_explorer(),
					'stock_prices' => $this->stocksmodel -> get_all_current_prices(),
					'authorized_classroom' => $this->adminmodel -> get_setting('classroom'),
					'possible_classrooms' => $classrooms,

					'game_online_selected' => $this->adminmodel -> get_setting('game_online'),
					'game_on' => $this->adminmodel -> get_setting('game_active'),

					'auto_updates' => $this->adminmodel -> get_setting('auto_updates'),
					'auto_update_info' => $this->adminmodel -> get_status(),
					'auto_update_toggle_result' => ( isset( $auto_update_toggle_result ) ) ? $auto_update_toggle_result : false,
					'auto_updates_template' => $this ->adminmodel -> get_auto_updates_template(),
					'show_worth' => $this->adminmodel -> get_setting('show_worth'),
					'days_running' => $this->adminmodel->get_days_running() + 1,
					'total_trades' => $this->adminmodel->get_total_trades(),
					'admin_nav' => $nav_active
				];

		$data['pane'] = $this->load->view('admin_pages/'.$nav_active, $data , TRUE);

		$template_data = [
					'title'	=> 'Admin',
					'active_nav' => 'admin',
					'game_online' => $this->game_online,
					'login_url' => $this->authUrl,
					'page' 	=> $this->load->view('admin_pages/base_page', $data ,TRUE)
				];

		$this->load->view('template', $template_data);

	}

	function prepare_explorer(){
		$explorer = [];
		$users = $this->adminmodel->get_all_users();

		foreach ($users as $user) {
			if($user['email'] == "evan.sharp@coastmountainacademy.ca"){
				continue;
			}
			$explorer[ $user['email'] ]['name'] = $user['name'];
			$explorer[ $user['email'] ]['id'] = $user['id'];
			$explorer[ $user['email'] ]['portfolio'] = $this->prepare_portfolio( $user['email'] );
			$explorer[ $user['email'] ]['bank_balance'] = $this->portfoliomodel->get_bank_balance($user['email']);
			$explorer[ $user['email'] ]['history'] = $this->historymodel->get_tx_hist($user['id']);
			$explorer[ $user['email'] ]['num_trades'] = count($explorer[ $user['email'] ]['history']);
			$explorer[ $user['email'] ]['portfolio_history'] = $this->prepare_portfolio_history( $user['id'] );
			$explorer[ $user['email'] ]['last_trade'] = $this->historymodel->get_last_trade_time( $user['id'] );
		}

		return $explorer;
	}

	function prepare_portfolio($user_email){
		$portfolio = $this->portfoliomodel->get_portfolio( $user_email );
		$stocks = $this->stocksmodel->get_stocks();
		$prices = $this->stocksmodel->get_all_current_prices();
		$output = [];

		if( !$portfolio ){
			return $output;
		}
		foreach($stocks as $stock){
			foreach($portfolio as $id => $num){
				if($stock['stock_id'] == $id){
					foreach($prices as $price){
						if($price['stock_id'] == $id){
							$output[] = [	'code' => $stock['code'],
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

	function prepare_portfolio_history($user_id){
		$updates = $this->historymodel->get_portfolio_hist( $user_id );
		$series = [];

		//if game is new, init first data point and return
		if( count( $updates ) < 2 ){
			$series[] = ['x'=> $updates[0]['timestamp'], 'y' => 0];
			return $series;
		}


		$sxdata = $this->stocksmodel->get_all_ticker();
		$txs = $this->historymodel->get_tx_hist( $user_id );
		$portfolio_value = 0;
		$net_worth = 0;

		foreach($updates as $snapshot){
			$update_ts = $snapshot['timestamp'];
			$flag = false;
			$temp_val = 0;
			$log = [];
			$action = '';

			if(!empty($snapshot['portfolio'])){

				//is this portfolio update due to a market update?
				foreach($sxdata as $sxdatum){
					if($update_ts == $sxdatum['timestamp']){
						$flag = true;
						foreach(json_decode($snapshot['portfolio']) as $stock_id => $stock_num_owned){
							if( $stock_id == $sxdatum['stock_id'] ){
								$temp_val += $sxdatum['price'] * $stock_num_owned;
								$action = 'update';
							}
						}

					}
				}
				if( $flag ){
					$portfolio_value = $temp_val;
					$net_worth = $portfolio_value + $snapshot['bank_balance'];
					$log[] = "market update" . $snapshot['portfolio'];
				}

				//is this portfolio update due to a transaction?
				foreach($txs as $tx){
					if($update_ts == $tx['timestamp']){

						$portfolio_arr = json_decode( $snapshot['portfolio'], true );
						//IS BUY OR SELL?

						if( $tx['tx'] > 0 ){
							//IS BUY
							foreach($portfolio_arr as $stock_id => $stock_num_owned){
								if( $stock_id == $tx['stock_id'] ){
									$log[] = "buy ". $tx['stock_id'] ." , portfolio_value is ".  $portfolio_value . ", tx is + " . $tx['tx_price'] * $tx['tx'];
									$portfolio_value += $tx['tx_price'] * $tx['tx'];
									$net_worth = $portfolio_value + $snapshot['bank_balance'];
									$action = 'buy';
								}

							}
						}else{
							//IS SELL
							$log[] = "sell " . $tx['stock_id'] . ", portfolio_value is ".  $portfolio_value . ", tx is + " . $tx['tx_price'] * $tx['tx'];
							$portfolio_value += $tx['tx_price'] * $tx['tx'];
							$net_worth = $portfolio_value + $snapshot['bank_balance'];
							$action = 'sell';
						}

					}
				}
			}
			$series[] = [	'timestamp' => $snapshot['timestamp'],
							'portfolio_value' => $portfolio_value,
							'net_worth' => $net_worth,
							'log' => $log,
							'action' => $action
						];
		}

		return $series;
	}
}
