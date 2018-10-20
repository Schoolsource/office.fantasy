<?php

class Reports_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function summaryComission($start = null, $end = null)
    {
        $data = array();

        $sales = $this->db->select('SELECT id, sale_code, sale_name , sale_fullname FROM sales ORDER BY sale_code ASC');
        foreach ($sales as $key => $value) {
            $where_arr[':id'] = $value['id'];
            $where_arr[':s'] = $start;
            $where_arr[':e'] = $end;

            $results = $this->db->select('SELECT SUM(pay_comission_amount) AS total_comission FROM payments WHERE pay_sale_id=:id AND (pay_date BETWEEN :s AND :e)', $where_arr);

            $data[$key] = $value;
            $data[$key]['comission'] = !empty($results[0]['total_comission'])
                                       ? $results[0]['total_comission']
                                       : 0;
        }

        return $data;
    }

    public function summaryRevenu($start, $end)
    {
        $start = date('Y-m-d 00:00:00', strtotime($start));
        $end = date('Y-m-d 00:00:00', strtotime($end));

        $_data['total'] = 0;
        $_data['price'] = 0;

        $field = 'SUM(ord_net_price) AS total_price, COUNT(*) AS total_order';
        $table = 'orders';

        $where = 'ord_dateCreate BETWEEN :s AND :e';
        $where_arr[':s'] = $start;
        $where_arr[':e'] = $end;

        $data = $this->db->select("SELECT {$field} FROM {$table} WHERE {$where}", $where_arr);
        if (!empty($data)) {
            $_data['total'] = $data[0]['total_order'];
            $_data['price'] = $data[0]['total_price'];
        }

        return $_data;
    }

    public function sale_due($options = array())
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
        $results = $this->db->select("SELECT orders.*, sales.sale_fullname FROM orders LEFT JOIN sales ON orders.ord_sale_code=sales.sale_code {$w} ORDER BY ord_dateCreate ASC", $w_arr);
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

    public function projects($options = [])
    {
        $where = '';
        $params = [];

        if (!empty($options['project'])) {
            $where .= !empty($where) ? ' AND ' : '';
            $where .= 'orders.ord_project_id=:project';
            $params[':project'] = $options['project'];
        } else {
            $where .= !empty($where) ? ' AND ' : '';
            $where .= 'orders.ord_project_id != 0';
        }

        if (!empty($options['sale'])) {
            $where .= !empty($where) ? ' AND ' : '';
            $where .= 'orders.ord_sale_code=:sale';
            $params[':sale'] = $options['sale'];
        }

        $where .= " AND orders.ord_status='A' ";

        $sql = "SELECT orders.*, sales.sale_fullname FROM orders LEFT JOIN sales ON orders.ord_sale_code=sales.sale_code WHERE {$where} ORDER BY ord_dateCreate ASC";

        $results = $this->db->select($sql, $params);
        $results = $this->getPay($results);

        $summary = $this->summaryProject($results);
        $summary = $this->getProject($summary);
        $summary = $this->getCustomer($summary);
        // echo '<pre>';
        // print_r($summary);
        // exit;

        return $summary;
    }

    private function getPay($results)
    {
        $ordersArr = [];
        foreach ($results as $result) {
            $ordersArr[$result['id']] = $result['id'];
        }

        $ordersId = implode(',', $ordersArr);

        $paymentsResult = $this->db->select("SELECT pay_id, pay_order_id, pay_amount FROM payments where pay_order_id IN({$ordersId})");

        $payments = [];
        foreach ($paymentsResult as $payment) {
            if (isset($payments[$payment['pay_order_id']])) {
                $payments[$payment['pay_order_id']] = $payments[$payment['pay_order_id']] + $payment['pay_amount'];
            } else {
                $payments[$payment['pay_order_id']] = $payment['pay_amount'];
            }
        }

        foreach ($results as $index => $result) {
            $results[$index]['payment'] = 0;
            if (isset($payments[$result['id']])) {
                $results[$index]['payment'] = $payments[$result['id']];
            }
        }

        return $results;
    }

    private function summaryProject($results)
    {
        $summary = [];

        foreach ($results as $result) {
            $customerId = $result['ord_customer_id'];
            if (!isset($summary[$customerId])) {
                $summary[$customerId] = [
                    'customer_id' => $result['ord_customer_id'],
                    'sale_code' => $result['ord_sale_code'],
                    'sale_fullname' => $result['sale_fullname'],
                    'ord_project_id' => $result['ord_project_id'],
                    'project_name' => '',
                    'project_target' => '',
                    'orders_count' => 1,
                    'orders_amount' => $result['ord_net_price'],
                    'orders_pay' => $result['payment'],
                ];
            } else {
                $summary[$customerId]['orders_count'] = $summary[$customerId]['orders_count'] + 1;
                $summary[$customerId]['orders_amount'] = $summary[$customerId]['orders_amount'] + $result['ord_net_price'];
                $summary[$customerId]['orders_pay'] = $summary[$customerId]['orders_pay'] + $result['payment'];
            }
        }

        return $summary;
    }

    private function getCustomer($results)
    {
        $customersArr = [];
        foreach ($results as $result) {
            $customersArr[$result['customer_id']] = $result['customer_id'];
        }

        $customerId = implode(',', $customersArr);

        $customerResult = $this->db->select("SELECT id, name_store FROM customers where id IN({$customerId})");

        $customers = [];
        foreach ($customerResult as $customer) {
            $customers[$customer['id']] = $customer['name_store'];
        }

        foreach ($results as $index => $result) {
            $results[$index]['customer'] = '';
            if (isset($customers[$result['customer_id']])) {
                $results[$index]['customer'] = $customers[$result['customer_id']];
            }
        }

        return $results;
    }

    private function getProject($results)
    {
        $projectsArr = [];
        foreach ($results as $result) {
            $projectsArr[$result['ord_project_id']] = $result['ord_project_id'];
        }

        $projectId = implode(',', $projectsArr);

        if (count($projectId) > 1) {
            $projectResult = $this->db->select("SELECT project_id, project_name, project_target FROM customer_project where project_id IN({$projectId})");
        } else {
            $projectResult = $this->db->select("SELECT project_id, project_name, project_target FROM customer_project where project_id = {$projectId} ");
        }

        $projects = [];
        foreach ($projectResult as $project) {
            $projects[$project['project_id']]['name'] = $project['project_name'];
            $projects[$project['project_id']]['target'] = $project['project_target'];
        }

        foreach ($results as $index => $result) {
            $results[$index]['project_name'] = '';
            $results[$index]['project_target'] = '';
            if (isset($projects[$result['ord_project_id']])) {
                $results[$index]['project_name'] = $projects[$result['ord_project_id']]['name'];
                $results[$index]['project_target'] = $projects[$result['ord_project_id']]['target'];
            }
        }

        return $results;
    }
}
