<?php

class Collectdaily_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    public function collect_daily($options = array())
    {
        $data = array();
        $_data = array();
        $w = '';
        $w_arr = array();

        if (!empty($options['sale'])) {
            $w .= !empty($w) ? ' AND ' : '';
            $w .= 'ord_sale_code=:sale';
            $w_arr[':sale'] = $options['sale'];
        }

        if (!empty($options['month']) && !empty($options['year'])) {
            $w .= !empty($w) ? ' AND ' : '';
            $w .= 'ord_dateCreate LIKE :month';
            $w_arr[':month'] = "{$options['year']}-{$options['month']}%";
        }

        if (!empty($options['process'])) {
            $w .= !empty($w) ? ' AND ' : '';
            $w .= 'ord_process=:process';
            $w_arr[':process'] = $options['process'];
        }

        $w = !empty($w) ? "WHERE {$w}" : '';
        $results = $this->db->select("SELECT orders.*, sales.sale_fullname FROM orders LEFT JOIN sales ON orders.ord_sale_code=sales.sale_code {$w} GROUP BY sales.sale_code, orders.id, orders.ord_dateCreate ORDER BY ord_dateCreate  DESC LIMIT 100", $w_arr);
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

        return $_data;
    }
} 