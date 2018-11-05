<?php

class MY_Model extends CI_Model {

	protected $stocks_table = "stocks";
 	protected $segments_table = "segments";
  protected $ticker_table ="sxdata";
  protected $users_table = "users";
  protected $history_table = "trade_history";
  protected $settings_table = "settings";
  protected $portfolio_history_table = "portfolio_value_history";

  protected $starting_balance = 10000;


	public function __construct() {
		parent::__construct();

		define('DB_BUYING', '1');
		define('DB_SELLING', '0');
	}
}
