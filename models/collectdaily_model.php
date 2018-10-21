<?php

class Collectdaily_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_COLUM = "orders.id as order_id, orders.user_name, orders.ord_dateCreate as delivery_date, collection.created_sales as sale, collection.expected_amount as expected_amount, collection.created_At as due_date";
    private $_TABLE = "collection LEFT JOIN orders ON collection.order_id = orders.id";
    private $_WHERE = "collection.created_At = CURDATE()";

    public function collect_daily($options = array())
    {
       
        $results = $this->db->select("SELECT {$this->_COLUM} FROM {$this->_TABLE} WHERE {$this->_WHERE}");
       
    
        foreach ($results as $key => $value) {
            $data[$key] = $value;
            $data[$key]['pay'] = 0;
            $data[$key]['total_get_comission'] = 0;
            $data[$key]['balance'] = 0;

            $payment = $this->query('orders')->listsPayment($value['id']);
            foreach ($payment as $i => $val) {
                $data[$key]['pay'] += $val['amount'];
                $data[$key]['total_get_comission'] += $val['comission_amount'];
            }
            $data[$key]['balance'] = $data[$key]['ord_net_price'] - $data[$key]['pay'];
        }

        foreach ($data as $key => $value) {
            if (empty($value['balance'])) {
                continue;
            }
            $_data[] = $value;
        }

        return $results;
    }
    public function save($data){
        $results = $this->db->select("SELECT * FROM orders WHERE id = $data LIMIT 1 ");
        if(empty($results)){
            throw new Exception("Error not found you billing number");
        }
        $arr = array(
                        'order_id'=>$data,
                        'created_At'=>date('Y-m-d'),
                        'expected_amount'=>$results[0]['ord_net_price'],
                        'created_sales'=>$this->me['id']
        );
      
          $respose = $this->db->insert("collection", $arr);
      
    }
} 