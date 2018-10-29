<?php

class Leaderboard extends MY_Controller {
	
	protected $stocksmodel;
	protected $adminmodel;
	protected $historymodel;
	
	
	public function __construct() {
		parent::__construct();
		
	}
	
	public function index() {
		
		$this->stocksmodel = new Stocksmodel();
		$this->adminmodel = new Adminmodel();
		$this->historymodel = new Historymodel();
		
		$data = [];
	
		
		if(isset( $_SESSION['user'] ) && !empty( $_SESSION['user'] ) ){
			$data = [
							'board' => $this->prepare_board()
							];
		}
		
		$template_data = [
					'title'	=> 'Leaderboard',
					'is_admin' => $this->is_admin,
					'active_nav' => 'leaderboard',
					'logged_in' => $this->logged_in,
					'authorized' => $this->authorized,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
					'page' 	=> $this->load->view('pages/leaderboard', $data ,TRUE)
				];
				
		$this->load->view('template', $template_data);
	}
	
	function prepare_board(){
		//name, worth, txs
		$board = [];
		$users = $this->adminmodel->get_all_users();

		
		foreach($users as $player){
			
			$worth = $player['bank_balance'];
			$portfolio_arr = json_decode( $player['portfolio'] );
			
			foreach($portfolio_arr as $stock_id => $stock_num_owned){
				$worth += $stock_num_owned * $this->stocksmodel->get_current_price( $stock_id );
			}

			$board[] = ['name' => $player['name'], 
						'worth' => $worth, 
						'txs' => $this->historymodel->get_num_trades($player['id']) ];
		}
		
		usort($board, $this->make_comparer(['worth', SORT_DESC]));
		
		return $board;
	}
	
	function make_comparer() {
    // Normalize criteria up front so that the comparer finds everything tidy
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
            ? array_pad($criterion, 3, null)
            : array($criterion, SORT_ASC, null);
    }

    return function($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
            // How will we compare this round?
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

            // If a projection was defined project the values now
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            }
            else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }

            // Do the actual comparison; do not return if equal
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            }
            else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }

        return 0; // tiebreakers exhausted, so $first == $second
    };
}
	
}