<?php

class Sales_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "sales";
    private $_table = "sales";
    private $_field = "*";

    public function lists($options=array()){
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'created_at',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),

            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( !empty($options['q']) ){
          $where_str .= !empty($where_str) ? " AND " : "";
          $where_str .= "sale_code LIKE :q 
                        OR sale_name LIKE :q 
                        OR sale_fullname LIKE :q 
                        OR username LIKE :q";
          $where_arr[":q"] = "%{$options['q']}%";
        }

        if( isset($_REQUEST["status"]) ){
            $options["status"] = $_REQUEST["status"];
        }
        if( !empty($options["status"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "status=:status";
            $where_arr[":status"] = $options["status"];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        if( !empty($options["unlimit"]) ) $limit = "";

        $groupby = !empty($groupby) ? "GROUP BY {$groupby}" :'';

        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$groupby} {$orderby} {$limit}", $where_arr ), $options );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }

    public function buildFrag($results, $options=array()) {
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert($value , $options);
        }
        return $data;
    }
    public function get($id, $options=array()){

        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        return $sth->rowCount()==1
            ? $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) , $options )
            : array();
    }

    public function convert($data , $options=array()){

        $data['total_order'] = $this->db->count('orders', "ord_sale_code=:code", array(':code'=>$data['sale_code']));
        if( !empty($data['region']) ){
            $data['region_arr'] = $this->getRegion($data['region']);
        }
        $data['status_arr'] = $this->getStatus($data['status']);
        $data['department_arr'] = $this->getDepartment($data['department']);
    	$data['permit']['del'] = true;
        if( !empty($data['total_order']) ){
            $data['permit']['del'] = false;
        }

    	return $data;
    }

    public function insert(&$data) {
      $data['created_at'] = date('c');
      $data['updated_at'] = date('c');
    	$this->db->insert($this->_objName, $data);
    	$data['id'] = $this->db->lastInsertId();
    }
    public function update($id, $data) {
      $data['updated_at'] = date('c');
    	$this->db->update($this->_objName, $data, "id={$id}");
    }
    public function delete($id) {
    	$this->db->delete($this->_objName, "id={$id}");
    }

    public function region() {
        $a[] = array('id'=>'north', 'name'=>'ภาคเหนือ');
        $a[] = array('id'=>'north_east', 'name'=>'ภาคตะวันออกเฉียงเหนือ');
        $a[] = array('id'=>'center', 'name'=>'ภาคกลาง');
        $a[] = array('id'=>'east', 'name'=>'ภาคตะวันออก');
        $a[] = array('id'=>'west', 'name'=>'ภาคตะวันตก');
        $a[] = array('id'=>'southern', 'name'=>'ภาคใต้');
        return $a;
    }
    public function getRegion($id=null){
        $data = array();
        foreach ($this->region() as $key => $value) {
            if( $value['id'] == $id ){
                $data = $value;
                break;
            }
        }
        return $data;
    }
    public function status() {
        $a[] = array('id'=>'A', 'name'=>'Active');
        $a[] = array('id'=>'I', 'name'=>'Inactive');
        return $a;
    }
    public function getStatus($id=null){
        $data = array();
        foreach ($this->status() as $key => $value) {
            if( $value['id'] == $id ){
                $data = $value;
                break;
            }
        }
        return $data;
    }
    public function department() {
        $a[] = array('id'=>'artist', 'name'=>'Artist');
        $a[] = array('id'=>'accountant', 'name'=>'Accountant');
        $a[] = array('id'=>'messenger', 'name'=>'Messenger');
        $a[] = array('id'=>'officer', 'name'=>'Officer');
        $a[] = array('id'=>'packer', 'name'=>'Packer');
        $a[] = array('id'=>'seller', 'name'=>'Seller');
        $a[] = array('id'=>'technician', 'name'=>'Technician');  
       
        return $a;
    }
    public function getDepartment($id=null){
        $data = array();
        foreach ($this->department() as $key => $value) {
            if( $value['id'] == $id){
                $data = $value;
                break;
            }
        }
        return $data;
    }

    public function is_username($text) {
      return $this->db->count($this->_table, 'username =:text', array(':text'=>$text));
    }

    public function login($user, $pass){
        $sth = $this->db->prepare("SELECT id,password FROM {$this->_table} WHERE username=:login");

        $sth->execute( array(
            ':login' => $user
        ) );

        $fdata = $sth->fetch( PDO::FETCH_ASSOC );
        if( $sth->rowCount()==1 ){
            if( password_verify($pass, $fdata['password']) ){
                return $fdata['id'];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    /* PROFILE */
    public function listsPayment( $options=array() ){
        $data = array();
        $data['total_amount'] = 0;

        $table = "payments p 
                  LEFT JOIN orders o ON p.pay_order_id=o.id
                  LEFT JOIN sales s ON p.pay_sale_id=s.id
                  LEFT JOIN customers c ON o.ord_customer_id=c.id";
        $field = "p.*
                  , o.ord_code
                  , o.ord_customer_id AS cus_id

                  , s.sale_code
                  , s.sale_name

                  , c.sub_code
                  , c.name_store";

        $condition = '';
        $params = array();

        if( !empty($options["start"]) && !empty($options["end"]) ){
            $condition .= !empty($condition) ? " AND " : "";
            $condition .= "(p.pay_date BETWEEN :s AND :e)";
            $params[":s"] = $options["start"];
            $params[":e"] = $options["end"];
        }
        if( !empty($options["sale"]) ){
            $condition .= !empty($condition) ? " AND " : "";
            $condition .= "p.pay_sale_id=:sale";
            $params[":sale"] = $options["sale"];
        }

        $condition .= !empty($condition) ? " AND " : "";
        $condition .= "o.ord_process=:process";
        $params[":process"] = 3;

        $condition = !empty($condition) ? "WHERE {$condition}" : "";
        $sql = "SELECT {$field} FROM {$table} {$condition} ORDER BY pay_date ASC";
        $results = $this->db->select( $sql, $params );

        foreach ($results as $key => $value) {
            $data['lists'][$key] = $value;
            $data['total_amount'] += $value["pay_amount"];
        }

        return $data;
    }
}
