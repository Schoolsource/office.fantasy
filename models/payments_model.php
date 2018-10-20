<?php

class Payments_model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "payments";
    private $_table = "payments p 
                        LEFT JOIN payments_type t ON p.pay_type_id=t.type_id
                        LEFT JOIN payments_bank b ON p.pay_bank_id=b.bank_id
                        LEFT JOIN payments_account a ON p.pay_account_id=a.account_id
                        LEFT JOIN orders o ON p.pay_order_id=o.id
                        LEFT JOIN customers c ON o.ord_customer_id=c.id
                        LEFT JOIN sales s ON p.pay_sale_id=s.id";
    private $_field = "p.*
                         , t.type_name
                         , t.type_is_cash
                         , t.type_is_bank
                         , t.type_is_check
                         , b.bank_name
                         , b.bank_code
                         , a.account_number
                         , a.account_name
                         , a.account_branch
                         , o.ord_code AS code
                         , o.ord_net_price
                         , c.name_store AS cus_name
                         , c.sub_code
                         , s.sale_code
                         , s.sale_name";
    private $_cutNamefield = "pay_";

    public function lists( $options=array() ){

    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'created',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( isset($_REQUEST["period_start"]) && isset($_REQUEST["period_end"]) ){
            $options["period_start"] = $_REQUEST["period_start"];
            $options["period_end"] = $_REQUEST["period_end"];
        }
        if( !empty($options["period_start"]) && !empty($options["period_end"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "({$this->_cutNamefield}date BETWEEN :s AND :e)";
            $where_arr[":s"] = $options["period_start"];
            $where_arr[":e"] = $options["period_end"];
        }

        if( isset($_REQUEST["type"]) ){
            $options['type'] = $_REQUEST["type"];
        }
        if( !empty($options["type"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "pay_type_id=:type";
            $where_arr[":type"] = $options["type"];
        }

        if( isset($_REQUEST["bank"]) ){
            $options['bank'] = $_REQUEST["bank"];
        }
        if( !empty($options["bank"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "pay_bank_id=:bank";
            $where_arr[":bank"] = $options["bank"];
        }

        if( isset($_REQUEST["account"]) ){
            $options["account"] = $_REQUEST["account"];
        }
        if( !empty($options["account"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "pay_account_id=:account";
            $where_arr[":account"] = $options["account"];
        }

        if( !empty($options['sale']) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "pay_sale_id=:sale";
            $where_arr[":sale"] = $options["sale"];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = !empty($options['unlimit']) ? '' : $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $this->_cutNamefield.$options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }
    public function get($id, $options=array() ){

        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE {$this->_cutNamefield}id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        return $sth->rowCount()==1
            ? $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) )
            : array();
    }
    public function buildFrag($results) {
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert( $value );
        }

        return $data;
    }
    public function convert($data){
    	$data = $this->cut($this->_cutNamefield, $data);

        if( !empty($data['image_id']) ){
            $image = $this->query('media')->get($data['image_id']);
            if( !empty($image) ){
                $data['image_url'] = $image['url'];
                $data['image_arr'] = $image;
            }
        }

        $data['permit']['del'] = true;

    	return $data;
    }
    public function _setData($data){
    	if( empty($data["{$this->_cutNamefield}created"]) ){
    		$data["{$this->_cutNamefield}created"] = date("c");
    	}
    	$data["{$this->_cutNamefield}updated"] = date("c");

    	return $data;
    }
    public function insert(&$data){
    	$data = $this->_setData($data);
    	$this->db->insert($this->_objName, $data);
        $data['id'] = $this->db->lastInsertId();
    }
    public function update($id, $data){
    	$data = $this->_setData($data);
    	$this->db->update($this->_objName, $data, "{$this->_cutNamefield}id={$id}");
    }
    public function delete($id){
    	$this->db->delete($this->_objName, "{$this->_cutNamefield}id={$id}");
    }
    public function deleteAll($oid){
    	$this->db->delete($this->_objName, "{$this->_cutNamefield}order_id={$oid}", $this->db->count($this->_objName, "{$this->_cutNamefield}order_id=:id", array(":id"=>$id)) );
    }

    #TYPE
    public function type(){
        return $this->db->select("SELECT type_id AS id, type_name AS name, type_is_bank AS is_bank, type_is_check AS is_check, type_is_cash AS is_cash FROM payments_type");
    }
    public function getType( $id ){
        $sth = $this->db->prepare("SELECT type_id AS id, type_name AS name, type_is_bank AS is_bank, type_is_check AS is_check, type_is_cash AS is_cash FROM payments_type WHERE type_id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        $fdata = $sth->fetch( PDO::FETCH_ASSOC );
        $fdata['permit']['del'] = true;
        if( $this->db->count('payments', "pay_type_id=:type", array(":type"=>$fdata['id'])) > 0 ){
            $fdata['permit']['del'] = false;
        }

        return $sth->rowCount()==1
            ? $fdata
            : array();
    }
    public function insertType(&$data){
        $this->db->insert("payments_type", $data);
    }
    public function updateType($id, $data){
        $this->db->update("payments_type", $data, "type_id=:id", array(":id"=>$id));
    }
    public function deleteType($id){
        $this->db->delete("payments_type", "type_id={$id}");
    }
    public function is_type($text){
        return $this->db->count("payments_type", "type_name=:text", array(":text"=>$text));
    }

    #Bank
    public function bank(){
        $data = array();
        $results = $this->db->select("SELECT bank_id AS id, bank_name AS name, bank_code AS code, bank_image_id AS image_id FROM payments_bank");

        foreach ($results as $key => $value) {
            if( !empty($value['image_id']) ){
                $image = $this->query('media')->get($value['image_id']);
                if( !empty($image) ){
                    $data[$key]['image_url'] = $image['url'];
                    $data[$key]['image_arr'] = $image;
                }
            }
            $data[$key] = $value;
        }

        return $data;
    }
    public function getBank($id){
        $sth = $this->db->prepare("SELECT bank_id AS id, bank_name AS name, bank_code AS code, bank_image_id AS image_id FROM payments_bank WHERE bank_id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        $fdata = $sth->fetch( PDO::FETCH_ASSOC );
        if( !empty($fdata['image_id']) ){
            $image = $this->query('media')->get($value['image_id']);
            if( !empty($image) ){
                $fdata['image_url'] = $image['url'];
                $fdata['image_arr'] = $image;
            }
        }

        return $sth->rowCount()==1
            ? $fdata
            : array();
    }
    public function insertBank(&$data){
        $this->db->insert("payments_bank", $data);
    }
    public function updateBank($id, $data){
        $this->db->update("payments_bank", $data, "bank_id={$id}");
    }
    public function deleteBank($id){
        $this->db->delete("payments_bank", "bank_id={$id}");
    }
    public function is_bank($text){
        return $this->db->count("payments_bank", "bank_name=:text", array(":text"=>$text));
    }

    #Acount
    private $a_field = "account_id AS id
                        , account_bank_id AS bank_id
                        , account_number AS number
                        , account_name AS name
                        , account_branch AS branch 
                        , b.bank_name
                        , b.bank_code";
    private $a_table = "payments_account a LEFT JOIN payments_bank b ON a.account_bank_id=b.bank_id";
    public function account(){
        $data = array();
        $results = $this->db->select("SELECT {$this->a_field} FROM {$this->a_table}");
        foreach ($results as $key => $value) {
            $data[$key] = $value;
            $data[$key]['name_str'] = $value['bank_code'].' - '.$value['number']; // .' ('.$value['name'].')'
        }
        return $data;
    }
    public function getAccount($id){
        $sth = $this->db->prepare("SELECT {$this->a_field} FROM {$this->a_table} WHERE account_id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        return $sth->rowCount()==1
            ? $sth->fetch( PDO::FETCH_ASSOC )
            : array();
    }
    public function insertAccount(&$data){
        $this->db->insert("payments_account", $data);
    }
    public function updateAccount($id, $data){
        $this->db->update("payments_account", $data, "account_id={$id}");
    }
    public function deleteAccount($id){
        $this->db->delete("payments_account", "account_id={$id}");
    }
    public function is_number($text){
        return $this->db->count("payments_account", "account_number=:text", array(":text"=>$text));
    }

    #SALES
    public function sales(){
        return $this->db->select("SELECT id, sale_name AS name, sale_code AS code, sale_fullname AS fullname FROM sales");
    }
    public function getSale($id=null){
        $sth = $this->db->prepare("SELECT id, sale_name AS name, sale_code AS code, sale_fullname AS fullname FROM sales WHERE id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        return $sth->rowCount()==1
            ? $sth->fetch( PDO::FETCH_ASSOC )
            : array();
    }
    public function getSaleCode($code){
        $sth = $this->db->prepare("SELECT id, sale_name AS name, sale_code AS code, sale_fullname AS fullname FROM sales WHERE sale_code=:code LIMIT 1");
        $sth->execute( array(':code'=>$code) );

        return $sth->rowCount()==1
            ? $sth->fetch( PDO::FETCH_ASSOC )
            : array();
    }
}