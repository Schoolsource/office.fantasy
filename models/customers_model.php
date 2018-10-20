<?php

require_once 'Customer/Project.php';

class Customers_Model extends Model
{
    public function __construct()
    {
        parent::__construct();

        $this->project = new Project_Customer();
    }

    private $_objName = 'customers';
    private $_table = 'customers';
    private $_field = '*';

    public function lists($options = array())
    {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,

            'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'sub_code',
            'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC',

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,

            'more' => true,
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = '';
        $where_arr = array();

        if (isset($_REQUEST['sale'])) {
            $options['sale'] = $_REQUEST['sale'];
        }
        if (!empty($options['sale'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'sale_code=:sale';
            $where_arr[':sale'] = $options['sale'];
        }

        if (!empty($options['q'])) {
            $arrQ = explode(' ', $options['q']);
            $wq = '';
            foreach ($arrQ as $key => $value) {
                $wq .= !empty($wq) ? ' OR ' : '';
                $wq .= "sub_code LIKE :q{$key}
                        OR name_store LIKE :q{$key}
                        OR phone LIKE :q{$key}
                        OR phone_other LIKE :q{$key}";
                $where_arr[":q{$key}"] = "%{$value}%";
                $where_arr[":s{$key}"] = "{$value}%";
                $where_arr[":f{$key}"] = $value;
            }

            if (!empty($wq)) {
                $where_str .= !empty($where_str) ? ' AND ' : '';
                $where_str .= "($wq)";
            }
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $where_str = !empty($where_str) ? "WHERE {$where_str}" : '';
        $orderby = $this->orderby($options['sort'], $options['dir']);
        $limit = $this->limited($options['limit'], $options['pager']);

        $arr['lists'] = $this->buildFrag($this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr), $options);

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }
        $arr['options'] = $options;

        return $arr;
    }

    public function buildFrag($results, $options = array())
    {
        $data = array();
        foreach ($results as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $data[] = $this->convert($value, $options);
        }

        return $data;
    }

    public function get($id, $options = array())
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id,
        ));

        return $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC), $options)
            : array();
    }

    public function convert($data, $options = array())
    {
        $data['name_str'] = $data['sub_code'].' - '.$data['name_store'];

        if (!empty($options['orders'])) {
            // if (!empty($options['project'])) {
            //     $data['orders'] = $this->listsOrders($data['id'], $options['project']);
            // } else {
            //     $data['orders'] = $this->listsOrders($data['id'], $options);
            // }

            $data['orders'] = $this->listsOrders($data['id'], $options);

            $data['orders'] = $this->getPaid($data['orders']);

            $data['orders'] = $this->getProject($data['orders']);
        }

        $data['address'] = $this->getAddress($data['id']);

        $address = '';
        if (!empty($data['address'][0]['address'])) {
            $address .= $data['address'][0]['address'];
        }
        if (!empty($data['address'][0]['road'])) {
            $address .= ' ถ.'.$data['address'][0]['road'];
        }
        if (!empty($data['address'][0]['district'])) {
            $address .= ' ต.'.$data['address'][0]['district'];
        }
        if (!empty($data['address'][0]['area'])) {
            $address .= ' อ.'.$data['address'][0]['area'];
        }
        if (!empty($data['address'][0]['province'])) {
            $address .= ' จ.'.$data['address'][0]['province'];
        }
        if (!empty($data['address'][0]['post_code'])) {
            $address .= ' '.$data['address'][0]['post_code'];
        }
        if (!empty($data['address'][0]['country_name'])) {
            $address .= ' '.$data['address'][0]['country_name'];
        }
        $data['address_str'] = $address;

        $data['permit']['del'] = true;
        $data['total_order'] = $this->db->count('orders', 'ord_customer_id=:id', array(':id' => $data['id']));
        if (!empty($data['total_order'])) {
            $data['permit']['del'] = false;
        }

        return $data;
    }

    public function insert(&$data)
    {
        $data['created_at'] = date('c');
        $this->db->insert($this->_objName, $data);
        $data['id'] = $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $this->db->update($this->_objName, $data, "id={$id}");
    }

    public function delete($id)
    {
        $this->db->delete($this->_objName, "id={$id}");
        $this->delAddress($id);
    }

    public function getCode($code, $options = array())
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE sub_code=:code LIMIT 1");
        $sth->execute(array(':code' => $code));

        return $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC), $options)
            : array();
    }

    public function setAddress($data)
    {
        if (!empty($data['id'])) {
            $this->db->update('customers_address', $data, "id={$data['id']}");
        } else {
            $this->db->insert('customers_address', $data);
        }
    }

    public function getAddress($id)
    {
        return $this->db->select('SELECT ca.id AS id, address, road, area, district, province, post_code, country, c.name AS country_name FROM customers_address ca LEFT JOIN country c ON ca.country=c.id WHERE customer_id=:id', array(':id' => $id));
    }

    public function delAddress($id)
    {
        $this->db->delete('customers_address', "customer_id={$id}", $this->db->count('customers_address', "customer_id={$id}"));
    }

    //LISTS ORDERS
    public function listsOrders($id, $options)
    {
        $projectId = $options['project'];
        $status = $options['status'];

        $sql = 'SELECT
        orders.id
      , orders.ord_code AS code
      , orders.ord_sale_code AS sale_code
      , orders.ord_type_commission AS comission
      , orders.term_of_payment AS payments
      , orders.ord_net_price AS price
      , orders.ord_dateCreate
      , orders.ord_project_id
      , orders.ord_process
      , orders.ord_status

      , sales.sale_name
      , sales.sale_fullname

    FROM orders
      LEFT JOIN sales ON orders.ord_sale_code=sales.sale_code ';

        $params = [':id' => $id];

        $where = '';
        if (!empty($options['due'])) {
            $where .= ' AND (SELECT COALESCE(SUM(pay_amount), 0) FROM payments WHERE pay_order_id=orders.id) < orders.ord_net_price ';
        }

        if ($status == 'I') {
            $where .= ' AND orders.ord_process=:process ';
        } else {
            $where .= ' AND orders.ord_process!=:process ';
        }
        $params[':process'] = 7;

        if (!empty($projectId)) {
            $where .= ' AND orders.ord_project_id=:pid ';
            $params[':pid'] = $projectId;
        }

        $sql .= ' WHERE orders.ord_customer_id=:id '.$where.' ORDER BY orders.ord_dateCreate ASC';

        // echo $sql;
        // echo '<pre>';
        // print_r($params);
        // echo '</pre>';

        // exit;

        $data = $this->db->select($sql, $params);

        $arr = $this->query('orders')->buildFrag($data);

        return $arr;
    }

    private function getPaid($orders)
    {
        $paymentArray = [];
        foreach ($orders as $order) {
            $paymentArray[$order['id']] = $order['id'];
        }

        $orderId = implode(',', $paymentArray);

        if (count($paymentArray) > 1) {
            $result = $this->db->select("SELECT * FROM payments WHERE pay_order_id IN({$orderId})");
        } else {
            $result = $this->db->select("SELECT * FROM payments WHERE pay_order_id = '{$orderId}' ");
        }

        $payments = [];

        if ($result) {
            foreach ($result as $payment) {
                if (isset($payments[$payment['pay_order_id']])) {
                    $payments[$payment['pay_order_id']] = $payments[$payment['pay_order_id']] + $payment['pay_amount'];
                } else {
                    $payments[$payment['pay_order_id']] = $payment['pay_amount'];
                }
            }
        }

        foreach ($orders as $key => $order) {
            $orders[$key]['payment']['pay_amount'] = 0;
            if (isset($payments[$order['id']])) {
                $orders[$key]['payment']['pay_amount'] = $payments[$order['id']];
            }
        }
        // foreach ($orders as $key => $value) {
        //     $sth = $this->db->prepare('SELECT * FROM payments WHERE pay_order_id=:id LIMIT 1');
        //     $sth->execute(array(':id' => $value['id']));
        //     $orders[$key]['payment'] = $sth->fetch(PDO::FETCH_ASSOC);
        // }

        return $orders;
    }

    private function getProject($orders)
    {
        $projectArray = [];
        foreach ($orders as $order) {
            $projectArray[$order['ord_project_id']] = $order['ord_project_id'];
        }

        $projectId = implode(',', $projectArray);

        if (count($projectArray) > 1) {
            $result = $this->db->select("SELECT * FROM customer_project WHERE project_id IN({$projectId})");
        } else {
            $result = $this->db->select("SELECT * FROM customer_project WHERE project_id = '{$projectId}' ");
        }

        // $result = $this->db->select("SELECT * FROM customer_project WHERE project_name IN({$projectId})");

        // echo '<pre>';
        // print_r($result);
        // exit;

        $projects = [];
        if ($result) {
            foreach ($result as $project) {
                $projects[$project['project_id']] = $project['project_name'];
            }
        }

        foreach ($orders as $index => $order) {
            $orders[$index]['project'] = '-';
            if (isset($projects[$order['ord_project_id']])) {
                $orders[$index]['project'] = $projects[$order['ord_project_id']];
            }
        }

        return $orders;
    }

    //UPDATE USER&PASS
    public function setUserPass()
    {
        $pass = $this->fn->q('password')->PasswordHash('1234');
        $results = $this->db->select("SELECT * FROM {$this->_objName} WHERE username is null");
        foreach ($results as $key => $value) {
            $data = array(
                'username' => 'C'.$value['sub_code'],
                'password' => $pass,
            );
            $this->update($value['id'], $data);
        }
    }
}
