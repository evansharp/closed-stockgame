<?php

class Admin extends MY_Controller {

	public function __construct() {
		parent::__construct();

	}

	public function index() {

		if( !$this->logged_in || !$this->is_admin){
			redirect( base_url() );
		}


		$stocksmodel = new Stocksmodel();
		$adminmodel = new Adminmodel();

		if( isset($_POST) && $_POST ){

			// stocks

			if( isset($_POST['delete_stock_id']) && !empty($_POST['delete_stock_id']) ){
				$stocksmodel -> delete_stock( $_POST['delete_stock_id'] );
			}
			if( isset($_POST['add_stock_name']) && !empty($_POST['add_stock_name'])
					&& isset($_POST['add_stock_code']) && !empty($_POST['add_stock_code'])
					&& isset($_POST['add_stock_segment']) && !empty($_POST['add_stock_segment'])
					&& isset($_POST['add_stock_initprice']) && !empty($_POST['add_stock_initprice'])){
				$stocksmodel -> add_stock( $_POST['add_stock_name'], $_POST['add_stock_code'], $_POST['add_stock_segment'], $_POST['add_stock_initprice']);
			}
			if(isset($_POST['edit_prospectus_text']) && !empty($_POST['edit_prospectus_text']) ){
				$stocksmodel->update_prospectus( $_POST['edit_prospectus_stock'], $_POST['edit_prospectus_text'] );
			}

			if( isset($_POST['update_stock_price']) && !empty($_POST['update_stock_price']) ){
				$stocksmodel -> update_stocks( $_POST['update_stock_price'] );
			}
			if( isset($_POST['add_segment_name']) && !empty($_POST['add_segment_name']) ){
				$stocksmodel -> add_segment( $_POST['add_segment_name'] );
			}
			if( isset($_POST['delete_segment_id']) && !empty($_POST['delete_segment_id']) ){
				$stocksmodel -> delete_segment( $_POST['delete_segment_id'] );
			}

			// settings

			if( isset($_POST['set_setting_game_on']) && !empty($_POST['set_setting_game_on']) ){
				$adminmodel->set_setting( 'game_active', $_POST['set_setting_game_on'] );
			}
			if( isset($_POST['reset']) && !empty($_POST['reset']) ){
				$adminmodel->reset_game();
				redirect('logout', 'refresh');
			}

		}



		$data = [ 			'segments' => $stocksmodel->get_segments(),
							'stocks' => $stocksmodel -> get_stocks(),
							'stock_prices' => $stocksmodel -> get_all_current_prices(),
							'game_on' => $adminmodel -> get_setting('game_active')
						];

		$template_data = [
					'title'	=> 'Admin',
					'is_admin' => $this->is_admin,
					'active_nav' => '',
					'logged_in' => $this->logged_in,
					'login_url' => $this->authUrl,
					'userData' => $this->googleUserData,
					'page' 	=> $this->load->view('pages/admin', $data ,TRUE)
				];

		$this->load->view('template', $template_data);

	}

	function add_segment(){

	}

}
