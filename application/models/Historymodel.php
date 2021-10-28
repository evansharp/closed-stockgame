<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Historymodel extends MY_Model {

    function __construct(){
        parent::__construct();
    }

    function get_num_trades( $user_id ){
        $q = $this->db->get_where($this->history_table, ['user_id' => $user_id]);
        if($q->num_rows() > 0){
            return count( $q->result_array() );
        }
        return 0;

    }

    function get_tx_hist( $user_id ){
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->order_by("id", "desc");
        $this->db->limit(UPDATES_LIMIT);
        $q = $this->db->get($this->history_table);

        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }

    function get_last_trade_time( $user_id ){
        $this->db->order_by('timestamp', 'DESC');
        $this->db->limit(1);
        $q = $this->db->get_where($this->history_table, ['user_id' => $user_id]);
        if($q->num_rows() > 0){
            $a = $q->result_array();
            return date('M j, g:ia', $a[0]['timestamp']);
        }
         return "None";
    }

    function get_portfolio_hist( $user_id ){
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->order_by("timestamp", "desc");
        $this->db->limit(UPDATES_LIMIT);
        $q = $this->db->get($this->portfolio_history_table);

       if($q->num_rows() > 0){
            return array_reverse( $q->result_array() );
        }
        return array();
    }

    function get_game_start(){
        $this->db->select('timestamp');
        $this->db->from($this->ticker_table);
        $this->db->order_by('timestamp', 'ASC');
        $this->db->limit( 1 );
        $q = $this->db->get();

        if($q->num_rows() > 0){
            $first_update = $q->result_array();
            return $first_update[0]['timestamp'];
        }
        return date("Y-m-d H:i:s"); // right now in the mysql timestamp format
    }

    function get_starting_balance(){
        return $this->starting_balance;
    }
}
