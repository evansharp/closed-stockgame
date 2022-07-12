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
        $this->db->where($this->ticker_table.'.timestamp > NOW() - INTERVAL 1 DAY', null, false);

        $this->db->join($this->stocks_table, $this->stocks_table. '.stock_id = '. $this->ticker_table . '.stock_id' );
        $this->db->join($this->segments_table, $this->segments_table. '.segment_id = '. $this->stocks_table . '.segment_id' );

        $this->db->order_by($this->ticker_table.'.stock_id', 'ASC'); //sort into stock groupings
        $this->db->order_by('timestamp', 'ACS'); //draw graphs newest-to-limit data

        $q = $this->db->get();

        if($q->num_rows() > 0){
            return array_reverse( $q->result_array() );
        }
        return [];
    }

    function get_update_times(){
        $this->db->select('stock_id, timestamp');
        $this->db->from($this->ticker_table);
        $this->db->limit(UPDATES_LIMIT); //limit to just the last 50 ticks
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

    function get_latest_update_time(){
        $this->db->select('timestamp');
        $this->db->from($this->ticker_table);
        $this->db->order_by('timestamp', 'DESC');
        $this->db->limit( 1 );
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $r = $q->result_array();
            return $r[0]['timestamp'];
        }else{
            return null;
        }
    }


    //***************************************************
    //    Buy & Sell
    //----------------------------------------

		function buy_stocks( $user_email, $num, $id ){
		    $now = date("Y-m-d H:i:s");
		    $user_id = $_SESSION['user']['id'];
		    $bank = $_SESSION['user']['bank_balance'];
		    $portfolio = json_decode($_SESSION['user']['portfolio'], true);

            $price = $this->get_current_price( $id );
            $available = $this->get_available_shares( $id );

            // filter out impossible situations
            if( (float)$price < 0.01 ){
                return ['err','Company is out of business.'];
            }elseif( $available < 1){
                return ['err','There are no shares of that company on the market.'];
            }

            // check that there are enough shares
            if( $num > $available ){
                return ['err','There are not enough shares on the market for that trade.'];
            }

		    $necessary_cash = (float)$price * $num;

		    if( $bank >= $necessary_cash ){
		        //take monies
		        $new_balance = $bank - $necessary_cash;
		        $this->db->set('bank_balance', $new_balance);
		        $this->db->where('email', $user_email);
		        $this->db->update( $this->users_table );
		        $_SESSION['user']['bank_balance'] = $new_balance;

		        //give stocks
                if( isset($portfolio[ $id ]) ){
                    //first time buy? means index doesn;t exist
                    $portfolio[ $id ] =  (int)$portfolio[ $id ] + $num;
                } else{
                    $portfolio[ $id ] = $num;
                }

			    $this->db->set( 'portfolio', json_encode( $portfolio ) );
			    $this->db->set('bank_balance', $new_balance);
			    $this->db->where('email', $user_email);
			    $this->db->update($this->users_table);

			    //record Tx
			    $txdata = [    'user_id' => $user_id,
			                   'stock_id' => $id,
			                   'timestamp' => $now,
			                   'tx_price' => $price,
			                   'tx' => $num,
                               'event_type' => DB_BUYING
			             ];
			    $this->db->insert($this->history_table, $txdata);

                //adjust market avaialbility
                $this->db->set('num_shares', $available - $num);
			    $this->db->where('stock_id', $id);
			    $this->db->update($this->market_table);

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
		        return ['err','You have insufficent funds for that trade'];
		    }
		}

		function sell_stocks($user_email, $num, $id){
		    $now = date("Y-m-d H:i:s");
		    $user_id = $_SESSION['user']['id'];
		    $bank = $_SESSION['user']['bank_balance'];
		    $portfolio = json_decode($_SESSION['user']['portfolio'], true);
		    $price = $this->get_current_price( $id );
            $available = $this->get_available_shares( $id );

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
                                'tx' => ($num * -1),
                                'event_type' => DB_SELLING
                            ];
    			    $this->db->insert($this->history_table, $txdata);

                    //adjust market avaialbility
                    $this->db->set('num_shares', $available + $num);
    			    $this->db->where('stock_id', $id);
    			    $this->db->update($this->market_table);

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
    		        return ['err', 'You have insufficent stock for that trade'];
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
    function add_segment($name, $vol){
        $data = ['segment_name' => $name,
                'segment_volitility' => $vol];
        $this->db->insert($this->segments_table,$data);
    }
    function delete_segment( $id ){
        $this->db->delete($this->segments_table, array('segment_id' => $id));
    }
    function edit_segment( $id, $name, $vol ){
        $this->db->set('segment_name', $name);
        $this->db->set('segment_volitility', $vol);
        $this->db->where('segment_id', $id);
        $this->db->update( $this->segments_table );
    }


    //***************************************************
    //    Stocks Admin
    //----------------------------------------

    function update_stocks( $updates ){
        $data = [];
        $data2 = [];

        $now = date("Y-m-d H:i:s");
        foreach($updates as $update){
            $data[] = [
                    'stock_id' => $update['id'],
                    'price'    => $update['price'],
                    'timestamp' => $now,
                    'event_type' => DB_UPDATE
                    ];
        }

        // Why am I just duplicating player portolios each gametick?
        // seems pointless

        $q = $this->db->get( $this->users_table );
        if($q->num_rows() > 0){
            $users = $q->result_array();

            foreach($users as $user){
                $data2[] = [
                        'user_id' => $user['id'],
                        'portfolio' => $user['portfolio'],
                        'bank_balance' => $user['bank_balance'],
                        'timestamp' => $now
                        ];

            }

            if( $this->db->insert_batch($this->ticker_table, $data)  &&
                $this->db->insert_batch($this->portfolio_history_table, $data2)
            ){
                return true;
            }else{
                return false;
            }

        }else{

            if( $this->db->insert_batch($this->ticker_table, $data) ){
                return true;
            }else{
                return false;
            }

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

        $q = $this->db->query("
            SELECT
                stock_id,
                price,
                MAX(timestamp) AS timestamp
            FROM
                `". $this->ticker_table ."`
            GROUP BY
                stock_id
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

    function get_all_current_prices_with_trend(){
        //get the most recent price of all stocks
        $q = $this->db->query("
            SELECT
                stock_id,
                price,
                MAX(timestamp) AS timestamp
            FROM
                `". $this->ticker_table ."`
            GROUP BY
                stock_id
        ");

        $latest_prices = [];
        if( $q -> num_rows() > 0 ){
            $latest_prices = $q->result_array();
        }


        // get all next-oldest stock prices
        // requires non-strict GROUPBY mode on the db:
        // https://stackoverflow.com/a/41887627/267786

        $q2 = $this->db->query("
            SELECT
                stock_id,
                price,
                timestamp
            FROM
                `". $this->ticker_table ."`
            WHERE
                timestamp < (SELECT MAX(timestamp) AS timestamp FROM `". $this->ticker_table ."`)
            ORDER BY
                timestamp DESC
            LIMIT
                ". count($latest_prices) .";
        ");

        if( $q2 -> num_rows() > 0 ){
            $prior_prices = $q2->result_array();

            foreach ( $latest_prices as $i => $latest ) {
                foreach( $prior_prices as $prior ){

                    if( $prior['stock_id'] == $latest['stock_id'] ){
                        if( $prior['price'] > $latest['price'] )
                            // dropping
                            $latest_prices[$i]['trend'] = "down";
                        elseif($prior['price'] < $latest['price'] )
                            // rising
                            $latest_prices[$i]['trend'] = "up";
                        else{
                            $latest_prices[$i]['trend'] = "same";
                        }
                    }
                }
            }

        }else{
            // game is reset rn, so set 'trend' to 'same'
            foreach($latest_prices as $i => $latest){
                $latest_prices[$i]['trend'] = "same";
            }
        }

        $this->debug($latest_prices);

        return $latest_prices;
    }

    function get_available_shares( $stock_id ){
        $this->db->select('num_shares');
        $this->db->from( $this->market_table );
        $this->db->where( 'stock_id', $stock_id );
        $this->db->limit( 1 );
        $q = $this->db->get();

        if($q->num_rows() > 0){
            $r = $q->result_array();
            return $r[0]['num_shares'];
        }
        return null;
    }

    function get_cap( $stock_id ){
        $this->db->select('total_shares');
        $this->db->from($this->stocks_table);
        $this->db->where( 'stock_id', $stock_id );
        $this->db->limit( 1 );
        $q = $this->db->get();

        if($q->num_rows() > 0){
            $r = $q->result_array();
            return $r[0]['total_shares'];
        }
        return null;
    }

    function get_stocks(){

        $this->db->select('*');
        $this->db->from( $this->stocks_table );
        $this->db->join($this->segments_table, $this->segments_table. '.segment_id = '. $this->stocks_table . '.segment_id' );
        $this->db->join($this->market_table, $this->market_table. '.stock_id = '. $this->stocks_table . '.stock_id' );
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

    function add_stock($name, $code, $seg_id, $initprice, $initnumshares){
        $data1 = ['name' => $name, 'code' => $code, 'segment_id' => $seg_id, 'total_shares' => $initnumshares];
        $this->db->insert($this->stocks_table, $data1);

        $newstock_id = $this->db->insert_id();

        $data2 = ['stock_id' => $newstock_id, 'price' => $initprice];
        $this->db->insert($this->ticker_table, $data2);

        $data3 = ['stock_id' => $newstock_id, 'num_shares' => $initnumshares];
        $this->db->insert($this->market_table, $data3);

    }
    function delete_stock( $id ){
        $this->db->delete($this->stocks_table, array('stock_id' => $id));
    }

    function update_market_cap($id, $new_cap){
        $forsale = $this->get_available_shares( $id );
        $old_cap = $this->get_cap( $id );
        $delta_shares = $new_cap - $old_cap;
        $new_available = $forsale + $delta_shares;
        if($new_available < 0){
            $new_available = 0;
        }

        //adjust market cap
        $this->db->set('total_shares', $new_cap);
        $this->db->where('stock_id', $id);
        $this->db->update($this->stocks_table);

        //adjust shares available
        $this->db->set('num_shares', $new_available );
        $this->db->where('stock_id', $id);
        $this->db->update($this->market_table);
    }

    function reset_stocks(){
        $stocks = $this->get_stocks();
        $data = [];
        foreach($stocks as $k => $stock){
            $data[$k]['id'] = $stock['stock_id'];
            $data[$k]['price'] = 1;
        }
        $this->update_stocks( $data );
    }

}
