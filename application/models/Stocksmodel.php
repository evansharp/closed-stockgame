<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Stocksmodel extends MY_Model {

    function __construct(){
        parent::__construct();
    }

    
    //***************************************************
    //    Tickers & History
    //----------------------------------------
    function get_all_ticker(){
        $this->db->select('*');
        $this->db->from($this->ticker_table);
        $this->db->join($this->stocks_table, $this->stocks_table. '.stock_id = '. $this->ticker_table . '.stock_id' );
        $this->db->join($this->segments_table, $this->segments_table. '.segment_id = '. $this->stocks_table . '.segment_id' );
        $this->db->order_by($this->ticker_table.'.stock_id', 'ASC'); //sort into stock groupings
        $this->db->order_by('timestamp', 'ASC'); //draw graphs oldest-to-newest data
        
        
        $q = $this->db->get();
        
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }
    
    function get_update_times(){
        $this->db->select('stock_id, timestamp');
        $this->db->from($this->ticker_table);
        $q = $this->db->get();
        
        if($q->num_rows() > 0){
             $results = $q->result_array();
        }else{
            return array();
        }
        
        $a_valid_id = $results[0]['stock_id'];
        $updates = [];
        foreach($results as $row){
            if($row['stock_id'] == $a_valid_id){
               $updates[] = $row['timestamp']; 
            }
        }
        return $updates;
        
    }
    
        
    //***************************************************
    //    Buy & Sell
    //----------------------------------------
    
		function buy_stocks( $user_email, $num, $id ){
		    $now = time();
		    $user_id = $_SESSION['user']['id'];
		    $bank = $_SESSION['user']['bank_balance'];
		    $portfolio = json_decode($_SESSION['user']['portfolio'], true);
		    $price = $this->get_current_price( $id );
		    
		    $necessary_cash = (float)$price * $num;
		    
		    if( $bank >= $necessary_cash ){
		        //take monies
		        $new_balance = $bank - $necessary_cash;
		        $this->db->set('bank_balance', $new_balance);
		        $this->db->where('email', $user_email);
		        $this->db->update( $this->users_table );
		        $_SESSION['user']['bank_balance'] = $new_balance;
		        
		        //give stocks
		        $portfolio[ $id ] = (int)$portfolio[ $id ] + $num;
			    $this->db->set( 'portfolio', json_encode( $portfolio ) );
			    $this->db->set('bank_balance', $new_balance);
			    $this->db->where('email', $user_email);
			    $this->db->update($this->users_table);
			    
			    //record Tx
			    $txdata = [    'user_id' => $user_id,
			                   'stock_id' => $id, 
			                   'timestamp' => $now, 
			                   'tx_price' => $price, 
			                   'tx' => $num
			             ];
			    $this->db->insert($this->history_table, $txdata);
			    
			    //record portfolio in history
			    $q = $this->db->get_where( $this->users_table, ['id' => $user_id] );
                if($q->num_rows() > 0){
                    $user = $q->result_array();
                    $data = [
                            'user_id' => $user[0]['id'],
                            'portfolio' => json_encode($portfolio),
                            'bank_balance' => $new_balance,
                            'timestamp' => $now
                            ];
                    $this->db->insert($this->portfolio_history_table, $data);
                }
			    
			    
			    return ['suc', '+'.$num.' '.$this->get_code($id)];
		    }else{
		        return ['err','You have insufficent funds for that transaction'];
		    }
		}
		
		function sell_stocks($user_email, $num, $id){
		    $now = time();
		    $user_id = $_SESSION['user']['id'];
		    $bank = $_SESSION['user']['bank_balance'];
		    $portfolio = json_decode($_SESSION['user']['portfolio'], true);
		    $price = $this->get_current_price( $id );
		    
		    if( array_key_exists($id, $portfolio) ){
    		    $necessary_stock = $portfolio[$id];
    		    
    		    if($portfolio[$id] >= $num ){
    		        //take stocks
    		        $portfolio[ $id ] = (int)$portfolio[ $id ] - $num;
    		        if( $portfolio[$id] < 1 ){         //if 0 stock, remove from portfolio
    		            unset( $portfolio[$id] ); 
    		        }
    		        
    		        $this->db->set( 'portfolio', json_encode($portfolio) );
    		        $this->db->where( 'email', $user_email );
    			    $this->db->update( $this->users_table );
    			    
    			    //give monies
    			    $profit = $num * $price;
    			    $this->db->set('bank_balance', $bank + $profit);
    		        $this->db->where('email', $user_email);
    		        $this->db->update( $this->users_table );
    		        
    		        //record Tx
                    $txdata = [ 'user_id' => $user_id,
                                'stock_id' => $id, 
                                'timestamp' => $now, 
                                'tx_price' => $price, 
                                'tx' => ($num * -1) ];
    			    $this->db->insert($this->history_table, $txdata);
    			    
    			    //record portfolio in history
    			    $q = $this->db->get_where( $this->users_table, ['id' => $user_id] );
                    if($q->num_rows() > 0){
                        $user = $q->result_array();
                        $data = [
                                'user_id' => $user[0]['id'],
                                'portfolio' => json_encode($portfolio),
                                'bank_balance' => $bank + $profit,
                                'timestamp' => $now
                                ];
                        $this->db->insert($this->portfolio_history_table, $data);
                    }
    			    
    		        return ['suc', '+ $'. $profit];
    		    }else{
    		        return ['err', 'You have insufficent stock for that transaction'];
    		    }
		    }else{
		        return ['err',"You don't own any of that stock"];
		    }
		}
    
    
    //***************************************************
    //    Segments Admin
    //----------------------------------------
  
    function get_segments(){
        $q = $this->db->get( $this->segments_table );
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }
    function add_segment($name){
        $data = ['segment_name' => $name];
        $this->db->insert($this->segments_table,$data);
    }
    function delete_segment( $id ){
        $this->db->delete($this->segments_table, array('segment_id' => $id));
    }
 
     
    //***************************************************
    //    Stocks Admin
    //----------------------------------------
    
    function update_stocks( $updates ){
        $data = [];
        $now = time();
        foreach($updates as $update){
            $data = [
                    'stock_id' => $update['id'],
                    'price'    => $update['price'],
                    'timestamp' => $now
                    ];
            $this->db->insert($this->ticker_table, $data);
        }
        
        $q = $this->db->get( $this->users_table );
        if($q->num_rows() > 0){
            $users = $q->result_array();
        }else{
            $users = array();
        }
        
        foreach($users as $user){
            $data2 = [
                    'user_id' => $user['id'],
                    'portfolio' => $user['portfolio'],
                    'bank_balance' => $user['bank_balance'],
                    'timestamp' => $now
                    ];
            $this->db->insert($this->portfolio_history_table, $data2);
        }
    }
    
    function update_prospectus( $stock_id, $new_text ){
        $this->db->set('prospectus', $new_text);
        $this->db->where('stock_id', $stock_id);
        $this->db->update($this->stocks_table);
    }
    
    function get_prospecti(){
        $this->db->select('*');
        $this->db->from($this->stocks_table);
        $this->db->join($this->segments_table, $this->segments_table. '.segment_id = '. $this->stocks_table . '.segment_id' );
        $q = $this->db->get();
        
        if($q->num_rows() > 0){
             return $q->result_array();
        }
        
        return array();
    }
    
    function get_all_current_prices(){
        //get the most recent price of all stocks
        // SQL from 
        
        
        $q = $this->db->query("
            SELECT t1.*
            FROM `". $this->ticker_table ."` AS t1
            LEFT OUTER JOIN `". $this->ticker_table ."` AS t2
                ON t1.stock_id = t2.stock_id 
                AND (t1.timestamp < t2.timestamp 
                OR (t1.timestamp = t2.timestamp AND t1.id < t2.id))
            WHERE t2.stock_id IS NULL
        ");
         
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
        
    }
    
    
    function get_current_price( $stock_id ){
        $this->db->select('price');
        $this->db->from( $this->ticker_table );
        $this->db->where( 'stock_id', $stock_id );
        $this->db->order_by('timestamp', 'DESC');
        $this->db->limit( 1 );
        $q = $this->db->get();
         
        if($q->num_rows() > 0){
            $r = $q->result_array();
            return $r[0]['price'];
        }
        return null;
    }
    
    function get_stocks(){
        $this->db->select('*');
        $this->db->from( $this->stocks_table );
        $this->db->join($this->segments_table, $this->segments_table. '.segment_id = '. $this->stocks_table . '.segment_id' );
        $q = $this->db->get( );

        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return array();
    }
    
    function get_code($id){
        $this->db->select('code');
        $this->db->where( 'stock_id', $id );
        $this->db->from( $this->stocks_table );
        $this->db->limit( 1 );
        $q = $this->db->get( );
        $r = $q->result_array();
        if($q->num_rows() > 0){
            return $r[0]['code'];
        }
        return '';
    }
    
    function add_stock($name, $code, $seg_id, $initprice){
        $data1 = ['name' => $name, 'code' => $code, 'segment_id' => $seg_id];
        $this->db->insert($this->stocks_table,$data1);
        
        $newstock_id = $this->db->insert_id();
        $data2 = ['stock_id' => $newstock_id, 'price' => $initprice];
        
        $this->db->insert($this->ticker_table,$data2);
    }
    function delete_stock( $id ){
        $this->db->delete($this->stocks_table, array('stock_id' => $id));
    }
    
}