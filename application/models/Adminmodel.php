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

    function add_user( $email, $name, $role, $avatar, $refresh ){
	    $blank_portfolio = [];
        $data = [   'email' => $email,
                    'name' => $name,
                    'user_role' => $role,
                    'google_avatar' => $avatar,
                    'google_refresh_token' => $refresh,
                    'bank_balance' => $this->starting_balance,
                    'portfolio' => json_encode($blank_portfolio)
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

    function delete_user($user_id){
        $this->db->where('id', $user_id);
        $this->db->delete($this->history_table);

        $this->db->where('id', $user_id);
        $this->db->delete($this->portfolio_history_table);

        $this->db->where('id', $user_id);
        $this->db->delete($this->users_table);

        $this->db->where('id', $user_id);
        $this->db->delete($this->login_table);
    }

    function get_all_users(){
	    $q = $this->db->get( $this->users_table );
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }

    function update_refresh_token($user_id, $new_token){
        $this->db->where('id',$user_id);
        $this->db->update($this->users_table, ['google_refresh_token' => $new_token]);
    }
    function get_refresh_token($user_email){
        $q = $this->db->get_where( $this->users_table, ['email' => $email], 1 );
        if($q->num_rows() > 0){
            $r =  $q->result_array();
            return $r[0]['google_refresh_token'];
        }
        return '';
    }

    function record_login( $user_id ){
        $this->db->update( $this->users_table, array('last_login' => date("Y-m-d H:i:s") ), array('id' => $user_id) );
        $this->db->insert( $this->login_table, [ 'user_id' => $user_id ] );
    }

    //***************************************************
    //    Run Game
    //----------------------------------------

    function game_running_toggle( $action ){
        //only do something if there is a state change
        if( $action == $this->get_setting('game_running') ){
            return;
        }elseif( $action == true ){

            $tmpfilepath = '/tmp/crontab.txt';

            // https://crontab.guru/#0_9,13_*_*_*
            // "At minute 0 past hour 9 and 13"

            // production
            //$job  = "0 9,13 * * * /usr/bin/php ". FCPATH ."index.php marketupdate 2>&1 | logger \n";

            //dev
            $job  = "*/5 * * * * /usr/bin/php ". FCPATH ."index.php market gameTick 2>&1 | logger\n";

            // exec cron control script here using $action as the param
            // https://stackoverflow.com/questions/6548746/how-to-start-stop-a-cronjob-using-php

            //exec ci_CLI
            //https://riptutorial.com/codeigniter/example/24826/cronjob-in-codeigniter

            //get current www-data crontab and append new job to temp textfile
            $output = shell_exec('crontab -l');
            file_put_contents($tmpfilepath, $output.$job.PHP_EOL);

            //set www-data crontab to include job
            exec('crontab /tmp/crontab.txt');

            $this->set_setting( 'game_running', true );
            return;
        }

        elseif( $action == false ){
            $this->set_setting( 'game_running', false );
            //exec('crontab -r'); // empty cron table
            exec('crontab | grep -v "/usr/bin/php '. FCPATH .'index.php" | crontab -'); // just remove game's entry
            return;
        }

    }

    // function get_auto_updates_template(){
    //     $this->db->select(
    //             $this->auto_updates_source_table . '.id, ' .
    //             $this->auto_updates_source_table . '.update_num, ' .
    //             $this->auto_updates_source_table . '.stock_id, ' .
    //             $this->auto_updates_source_table . '.price, ' .
    //             $this->stocks_table . '.name'
    //         );
    //     $this->db->from($this->auto_updates_source_table);
    //     $this->db->join($this->stocks_table, $this->stocks_table . '.stock_id = ' . $this->auto_updates_source_table . '.stock_id');
    //     $query = $this->db->get();
    //
    //     $results = $query->result_array();
    //
    //     $formatted_array['by_stock'] = [];
    //     $formatted_array['by_update'] = [];
    //
    //     //by stock
    //         foreach($results as $rec){
    //             if( !array_key_exists($rec['stock_id'], $formatted_array['by_stock'] ) ){
    //                 $formatted_array['by_stock'][ $rec['stock_id'] ] = [];
    //             }
    //
    //             foreach($rec as $k => $v){
    //                 if($k != 'stock_id' && $k != 'update_num'){
    //                     if($k == 'price'){
    //                         $v = number_format($v, 2);
    //                     }
    //                     $formatted_array['by_stock'][ $rec['stock_id'] ][ $rec['update_num'] ][$k] = $v;
    //                 }
    //             }
    //
    //         }
    //     //by update
    //         foreach($results as $rec){
    //             if( !array_key_exists($rec['update_num'], $formatted_array['by_update'] ) ){
    //                 $formatted_array['by_update'][ $rec['update_num'] ] = [];
    //             }
    //
    //             foreach($rec as $k => $v){
    //                 if( $k != 'update_num' && $k != 'stock_id' ){
    //                     if($k == 'price'){
    //                         $v = number_format($v, 2);
    //                     }
    //                     $formatted_array['by_update'][ $rec['update_num'] ][ $rec['stock_id'] ] [$k] = $v;
    //                 }
    //             }
    //
    //         }
    //
    //     return $formatted_array;
    // }
    // function edit_auto_updates_template( $newData ){
    //
    //     $this->db->set( 'price', $newData['price'] );
    //     $this->db->where( 'id', $newData['id'] );
    //     $this->db->update($this->auto_updates_source_table);
    //
    //     return ($this->db->affected_rows() > 0) ? true : false;
    // }
    //
    // function add_update_to_template( $after, $new_prices ){
    //     $stock_ids = array_keys($new_prices);
    //     $to_be_shifted = [];
    //
    //     $this->db->where('update_num >', $after);
    //     $r1 = $this->db->get_where( $this->auto_updates_source_table );
    //
    //     if($r1->num_rows() > 0){
    //         $to_be_shifted = $r1->result_array();
    //         //move all updates up one
    //         foreach( $to_be_shifted as $i => $rec ){
    //             $to_be_shifted[$i]['update_num'] = intval( $rec['update_num'] ) + 1;
    //         }
    //         $this->db->update_batch($this->auto_updates_source_table, $to_be_shifted, 'id');
    //     }
    //
    //     foreach($stock_ids as $stock_id){
    //         // create new inset fieldset
    //         $new_updates[] = [
    //             'update_num'    => intval( $after ) + 1,
    //             'stock_id'      => $stock_id,
    //             'price'         => $new_prices[ $stock_id ]
    //         ];
    //     }
    //
    //     $this->db->insert_batch($this->auto_updates_source_table, $new_updates);
    // }
    //
    // function delete_auto_update( $target ){
    //     $this->db->delete($this->auto_updates_source_table, ['update_num' => $target]);
    //
    //     $to_be_shifted = [];
    //
    //     $this->db->where('update_num >', $target);
    //     $r1 = $this->db->get_where( $this->auto_updates_source_table );
    //
    //     if($r1->num_rows() > 0){
    //         $to_be_shifted = $r1->result_array();
    //         //move all updates down one
    //         foreach( $to_be_shifted as $i => $rec ){
    //             $to_be_shifted[$i]['update_num'] = intval( $rec['update_num'] ) - 1;
    //         }
    //         $this->db->update_batch($this->auto_updates_source_table, $to_be_shifted, 'id');
    //     }
    //
    // }

    //***************************************************
    //    Status
    //----------------------------------------

    function get_status(){
        $str = '';
        if( $this->get_setting('last_update_was_auto') ){

            $str .= "The last update was <em>automatic</em>.";

            $last_update_num = $this->get_setting('last_auto_update_num');
            $total_update_num = $this->db->count_all( $this->auto_updates_source_table ) / $this->db->count_all( $this->stocks_table ) - 1;

            $previous_update_time = $this->get_setting('auto_update_last_time');

            $str .= "<br>It was number $last_update_num out of $total_update_num on ". $previous_update_time;//->format("d M,  g:i a");


            // if auto updates are on, show the FUTURE!
            if( $this -> get_setting('auto_updates') ){

                $now = new DateTime();

                // determine irregular interval to next update
                $thishr = date('H');

                if( $thishr < 9 ){
                    //today morning comes next
                    $next_update = new DateTime('today 9am');

                }elseif( $thishr >= 9 && $thishr < 13 ){
                    // today afternoon comes next
                    $next_update = new DateTime('today 1pm');

                }elseif( $thishr >= 13){
                    //tomorrow morning comes next
                    $next_update = new DateTime('tomorrow 9am');
                }

                $interval_to_next_update = $now->diff($next_update);

                // determine regular intervals remaining
                $updates_remaining = $total_update_num-$last_update_num;
                $days_remaining = round($updates_remaining / 2);
                $reg_interval_remaining = new DateInterval('P'.$days_remaining.'D');

                $finish = $now->add($interval_to_next_update);
                $finish = $finish->add($reg_interval_remaining);

                $str .= "<br>The next update will occur at " . $next_update->format("d M,  g:i a");
                $str .= "<br>The game will finish at " . $finish->format("d M,  g:i a");
            }else{
                $str .= '<br>Autoupdates are currently <span class="tag tag_red>Not Active</span>';
            }
        }else{

            $this->load->model('Stocksmodel');
            $this->stocksmodel = new Stocksmodel();

            $previous_update_time = new DateTime( $this->stocksmodel->get_latest_update_time());

            $str .= "The last update was <em>manual</em>.";

            $str .= "<br>It was on ". $previous_update_time->format("d M,  g:i a");
        }
        return $str;
    }

    function get_login_history(){
        $this->db->select( 'id, name' );
        $this->db->where("last_login >= NOW() + INTERVAL -7 DAY AND last_login <  NOW() + INTERVAL 0 DAY");
        $r = $this->db->get( $this->users_table );

        $users = $r -> result_array();

        $this->db->order_by('user_id', 'DESC');
        $this->db->order_by('login', 'DESC');
        $this->db->where("login >= NOW() + INTERVAL -7 DAY AND login <  NOW() + INTERVAL 0 DAY");
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
        $start = new DateTime($arr[0]['timestamp']);

        return $now->diff( $start )->format('%a');
    }

    function get_total_trades(){
        return $this->db->count_all($this->history_table);
    }

    //***************************************************
    //    Utilities
    //----------------------------------------

    function reset_game(){
        // offline
        $this->set_setting( 'game_online', 0 );
        $this->set_setting( 'game_active', 0 );

        //delete all past game data
        $this->db->empty_table( $this->users_table );
        $this->db->empty_table( $this->ticker_table );
        $this->db->empty_table( $this->history_table );
        $this->db->empty_table( $this->portfolio_history_table );
        $this->db->empty_table( $this->login_table );

        // $this->db->set('prospectus', NULL);
        // $this->db->where('prospectus is not null');
        // $this->db->update( $this->stocks_table );



        //reset prices to start
        //$this->load->model('Stocksmodel');
        //$this->Stocksmodel->auto_update_prices( 0 );
    }

    function purge_stock_history(){
        // offline
        $this->set_setting( 'game_online', 0 );
        $this->set_setting( 'game_active', 0 );

        //delete past stock data
        $this->db->empty_table( $this->ticker_table );
        $this->db->query("
            ALTER TABLE ". $this->ticker_table ."
            AUTO_INCREMENT = 1");
    }

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
