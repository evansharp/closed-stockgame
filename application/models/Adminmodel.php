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

    function add_user( $email, $name, $avatar, $refresh ){
	    $blank_portfolio = [];
        $data = [   'email' => $email,
                    'name' => $name,
                    'google_avatar' => $avatar,
                    'google_refresh_token' => $refresh,
                    'bank_balance' => $this->starting_balance,
                    'portfolio' => json_encode($blank_portfolio)   //will hold a JSON string of stocks owned
                ];
        $this->db->insert($this->users_table, $data);

        $new_id = $this->db->insert_id();

        $data2 = [
                'user_id' => $new_id,
                'portfolio' => '',
                'bank_balance' => $this->starting_balance,
                'timestamp' => time()
                ];
        $this->db->insert($this->portfolio_history_table, $data2);

        return $this->get_user( $email );
    }

    function get_all_users(){
	    $q = $this->db->get( $this->users_table );
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }

    function record_login( $user_id ){
        $this->db->insert( $this->login_table, [ 'user_id' => $user_id ] );
    }

    function reset_game(){
        $this->db->empty_table( $this->users_table );
        $this->db->empty_table( $this->ticker_table );
        $this->db->empty_table( $this->history_table );
        $this->db->empty_table( $this->portfolio_history_table );
    }

    //***************************************************
    //    Status
    //----------------------------------------

    function get_login_history(){
        $this->db->select( 'id, name' );
        $r = $this->db->get( $this->users_table );

        $users = $r -> result_array();

        $this->db->order_by('user_id', 'DESC');
        $this->db->order_by('login', 'DESC');
        $this->db->where("login >= NOW() + INTERVAL -7 DAY AND login <  NOW() + INTERVAL 0 DAY");
        $this->db->limit(50); // get only most recent 50 active users
        $r2 = $this->db->get( $this->login_table );

        $logins = $r2 -> result_array();

        $totalled_logins_by_user = [];
        for($a = 6; $a >= 0; $a--){
            $date = new DateTime("today -" . $a . " days");
            $formatted_date = $date -> format('Y-m-d');

            foreach( $users as $user ){
                $totalled_logins_by_user[ $user['name'] ][ $formatted_date ] = 0;

                foreach ( $logins as $login ){

                    if( $user['id'] == $login['user_id'] ){
                        $second_date = new DateTime($login['login']);
                        $formatted_second_date = $second_date -> format('Y-m-d');

                        if($formatted_date == $formatted_second_date){
                            $totalled_logins_by_user[ $user['name'] ][ $formatted_date ] += 1;
                        }
                    }
                }
            }

        }

        return $totalled_logins_by_user;
    }

    function get_days_running(){
        $this->db->select('timestamp');
        $this->db->order_by('timestamp', 'ASC');
        $this->db->limit(1);
        $r = $this->db->get($this->ticker_table);

        $arr = $r->result_array();

        $now = new DateTime();
        $start = new DateTime();
        $start -> setTimestamp( $arr[0]['timestamp'] );
        return $now->diff( $start )->format('%a');
    }

    function get_total_trades(){
        return $this->db->count_all($this->history_table);
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
