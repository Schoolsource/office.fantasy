<?php 
class Suppliers_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "suppliers";
    private $_table = "suppliers s 
    				   LEFT JOIN admins ad ON s.sup_user_id=ad.id
                       LEFT JOIN suppliers_types t ON s.sup_type_id=t.type_id
                       LEFT JOIN province p ON s.sup_province_id=p.PROVINCE_ID
                       LEFT JOIN country ct ON s.sup_country_id=ct.id";
    private $_field = "s.*, ad.name AS user_name, t.type_name, p.PROVINCE_NAME AS province_name, ct.name AS country_name";
    private $_cutNamefield = "sup_";

    public function lists( $options=array() ){
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'created',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

            'more' => true
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( isset($_REQUEST["status"]) ){
        	$options["status"] = $_REQUEST["status"];
        }
        if( !empty($options["status"]) ){
        	$where_str .= !empty($where_str) ? " AND " : "";
        	$where_str .= "sup_status=:status";
        	$where_arr[":status"] = $options["status"];
        }

        if( !empty($options['q']) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "(s.sup_name LIKE :q
                        OR s.sup_code LIKE :q
                        OR s.sup_first_name LIKE :q
                        OR s.sup_last_name LIKE :q
                        OR s.sup_nickname LIKE :q
                        OR s.sup_phone LIKE :q)";
            $where_arr[":q"] = "%{$options["q"]}%";

        }

        if( isset($_REQUEST["type"]) ){
            $options["type"] = $_REQUEST["type"];
        }
        if( !empty($options["type"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "sup_type_id=:type";
            $where_arr[":type"] = $options["type"];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $this->_cutNamefield.$options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        if( !empty($options["unlimit"]) ) $limit = "";
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ), $options );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }
    public function get($id, $options=array() ){

        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE {$this->_cutNamefield}id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        return $sth->rowCount()==1
            ? $this->convert( $sth->fetch( PDO::FETCH_ASSOC ), $options )
            : array();
    }
    public function buildFrag($results, $options=array()) {
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert( $value, $options );
        }

        return $data;
    }
    public function convert($data, $options=array()){
    	$data = $this->cut($this->_cutNamefield, $data);

        $data['name_str'] = $data['name'];
        if( !empty($data['code']) ){
            $data['name_str'] = '('.$data['code'].') '.$data['name'];
        }

    	$prefix_name_str = $this->query('system')->getPrefixName($data['prefix_name']);
        if( empty($prefix_name_str) ){
            $prefix_name_str = '';
        }
        $data['sup_contact'] = "{$prefix_name_str}{$data['first_name']} {$data['last_name']}";

        if( !empty($data['nickname']) ){
        	$data['sup_contact'] = $data['sup_contact'].' ('.$data['nickname'].')';
        }

        $data['status_arr'] = $this->getStatus($data['status']);

        $data['permit']['del'] = true;
        $data['total_check'] = $this->db->count("suppliers_paycheck", "check_sup_id=:id", array(":id"=>$data['id']));
        if( !empty($data['total_check']) ){
        	$data['permit']['del'] = false;
        }

        if( !empty($options['check']) ){
            $data['check'] = $this->listsCheck($data['id']);
        }

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
        // $data = $this->_setData($data);
        $data["{$this->_cutNamefield}created"] = date("c");
        $data["{$this->_cutNamefield}updated"] = date("c");
        $this->db->insert($this->_objName, $data);
        $data['id'] = $this->db->lastInsertId();
    }
    public function update($id, $data){
        // $data = $this->_setData($data);
        $data["{$this->_cutNamefield}updated"] = date("c");
        $this->db->update($this->_objName, $data, "{$this->_cutNamefield}id={$id}");
    }
    public function delete($id){
    	$this->db->delete($this->_objName, "{$this->_cutNamefield}id={$id}");
    }
    public function is_name( $text ){
    	return $this->db->count($this->_objName, "{$this->_cutNamefield}name=:text", array(":text"=>$text));
    }
    public function import($data){
        $check = $this->db->select("SELECT sup_id AS id , sup_code FROM {$this->_objName} WHERE sup_code=:code LIMIT 1", array(':code'=>$data['sup_code']));

        if( empty($check) ){
            $this->insert( $data );
        }
        else{
            $this->update( $check[0]['id'], $data );
        }
    }

    #STATUS
    public function status(){
    	$a[] = array('id'=>'enabled', 'name'=>'เปิดใช้งาน');
    	$a[] = array('id'=>'disabled', 'name'=>'ปิดใช้งาน');

    	return $a;
    }
    public function getStatus($id){
    	$data = array();
    	foreach ($this->status() as $key => $value) {
    		if( $id == $value['id'] ){
    			$data = $value;
    			break;
    		}
    	}
    	return $data;
    }

    #LISTCHECKING
    public function listsCheck($id){

        $field = "check_id AS id, check_date AS date, check_up_date AS up_date, check_bank_id AS bank_id, check_number AS number, check_price AS price, check_image_id AS image_id, b.bank_name, b.bank_code";

        $results = $this->db->select("SELECT {$field} FROM suppliers_paycheck p LEFT JOIN payments_bank b ON p.check_bank_id=b.bank_id WHERE check_sup_id=:id", array(":id"=>$id));

        return $results;
    }

    #TYPE
    public function type(){
        return $this->db->select("SELECT type_id AS id, type_name AS name FROM suppliers_types ORDER BY id ASC");
    }
    public function getType($id){

        $sth = $this->db->prepare("SELECT type_id AS id, type_name AS name FROM suppliers_types WHERE type_id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        $fdata = $sth->fetch( PDO::FETCH_ASSOC );

        $fdata['permit']['del'] = true;
        $fdata['total_supplier'] = $this->db->count($this->_objName, "sup_type_id=:id", array(":id"=>$fdata['id']));
        if( !empty($fdata['total_supplier']) ){
            $fdata['permit']['del'] = false;
        }

        return $sth->rowCount()==1
            ? $fdata
            : array();
    }
    public function insertType(&$data){
        $this->db->insert("suppliers_types", $data);
    }
    public function updateType($id, $data){
        $this->db->update("suppliers_types", $data, "type_id={$id}");
    }
    public function deleteType($id){
        $this->db->delete("suppliers_types", "type_id={$id}");
    }
    public function is_type($text){
        return $this->db->count("suppliers_types", "type_name=:text", array(":text"=>$text));
    }
}