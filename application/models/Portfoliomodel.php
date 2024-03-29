<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Portfoliomodel extends MY_Model {

    function __construct(){
        parent::__construct();
    }

		function get_portfolio( $user_id ){
			$this->db->select('portfolio');
			$q = $this->db->get_where( $this->users_table, ['id' => $user_id], 1 );
        if($q->num_rows() > 0){
            $result = $q->result_array();
            return json_decode( $result[0]['portfolio'], true);
        }
        return false;
		}

        function get_game_ticks( $user_id ){

        }

		function get_bank_balance( $user_id ){
		    $this->db->select('bank_balance');
			$q = $this->db->get_where( $this->users_table, ['id' => $user_id], 1 );
            if($q->num_rows() > 0){
                $result = $q->result_array();
                return $result[0]['bank_balance'];
		    }else{
		        return 0.0;
		    }
		}


}
