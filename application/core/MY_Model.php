<?php

class MY_Model extends CI_Model {

	protected $stocks_table = "stocks";
 	protected $segments_table = "segments";
  	protected $ticker_table ="sxdata";
  	protected $users_table = "users";
  	protected $history_table = "trade_history";
  	protected $settings_table = "settings";
  	protected $portfolio_history_table = "portfolio_value_history";
	protected $market_table = "market";
	protected $login_table = "user_logins";

  	protected $starting_balance = 10000;


	public function __construct() {
		parent::__construct();

		if( !defined( 'DB_BUYING' ) ){
			define('DB_BUYING', '1');
		}
		if( !defined( 'DB_SELLING' ) ){
			define('DB_SELLING', '0');
		}
		if( !defined( 'DB_UPDATE' ) ){
			define('DB_UPDATE', '2');
		}
		if( !defined( 'UPDATES_LIMIT' ) ){
			define('UPDATES_LIMIT', 20);
		}
	}
}
