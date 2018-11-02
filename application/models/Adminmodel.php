<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminmodel extends MY_Model {

    function __construct(){
        parent::__construct();
    }


    //***************************************************
    //    Users
    //----------------------------------------

    function get_user( $email ){
        $q = $this->db->get_where( $this->users_table, ['email' => $email], 1 );
        if($q->num_rows() > 0){
            $r =  $q->result_array();
            return $r[0];
        }
        return array();
    }

    function add_user( $email, $name ){
	    $blank_portfolio = [];
        $data = [   'email' => $email,
                    'name' => $name,
                    'bank_balance' => $this->starting_balance,
                    'portfolio' => json_encode($blank_portfolio)   //will hold a JSON string of stocks owned
                ];
        $this->db->insert($this->users_table, $data);

        $new_id = $this->db->insert_id();

        $data2 = [
                'user_id' => $new_id,
                'portfolio' => '',
                'bank_balance' => $this->starting_balance,
                'timestamp' => date("Y-m-d H:i:s")
                ];
        $this->db->insert($this->portfolio_history_table, $data2);
    }

    function get_all_users(){
	    $q = $this->db->get( $this->users_table );
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }

    function reset_game(){
        $this->db->empty_table( $this->users_table );
        $this->db->empty_table( $this->ticker_table );
        $this->db->empty_table( $this->history_table );
        $this->db->empty_table( $this->portfolio_history_table );


    }

    //***************************************************
    //    Settings
    //----------------------------------------


    function get_all_settings(){
        $q = $this->db->get( $this->settings_table );
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }

    function get_setting( $name ){
        $q = $this->db->get_where( $this->settings_table, ['name' => $name], 1 );
        if($q->num_rows() > 0){
            $arr = $q->result_array();
            return $arr[0]['value'];
        }
        return array();
    }

    function set_setting( $name, $value ){
        $this->db->set('value', $value);
        $this->db->where('name', $name);
        $this->db->update($this->settings_table);
    }
}
