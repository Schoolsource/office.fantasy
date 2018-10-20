<?php
class Export_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "export_products";
    private $_table = "export_products exp LEFT JOIN export_categories ec ON exp.exp_cate_id=ec.cate_id";
    private $_field = "exp.*, ec.cate_name";
    private $_cutNamefield = "exp_";

    public function insert(&$data){
    	$data["{$this->_cutNamefield}created"] = date("c");
    	$data["{$this->_cutNamefield}updated"] = date("c");

    	$this->db->insert($this->_objName, $data);
    	$data["id"] = $this->db->LastInsertId();
    }
    public function update($id, $data){
    	$data["{$this->_cutNamefield}updated"] = date("c");
    	$this->db->update($this->_objName, $data, "{$this->_cutNamefield}id={$id}");
    }
    public function delete($id){
    	$this->db->delete($this->_objName, "{$this->_cutNamefield}id={$id}");
    	$this->delItem($id);
    }
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

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $orderby = $this->orderby( $this->_cutNamefield.$options['sort'], $options['dir'] );
        $limit = $this->limited( $options['limit'], $options['pager'] );

        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ), $options  );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;
        
        return $arr;
    }
    public function get($id, $options=array()){
    	$sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE {$this->_cutNamefield}id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        return $sth->rowCount()==1
            ? $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) , $options )
            : array();
    }
    public function buildFrag($results, $options=array()){
    	$data = array();
    	foreach ($results as $key => $value) {
    		if( empty($value) ) continue;
    		$data[] = $this->convert( $value, $options );
    	}
    	return $data;
    }
    public function convert($data, $options=array()){
    	$data = $this->cut($this->_cutNamefield, $data);

    	if( !empty($options["items"]) ){
    		$data["items"] = $this->listsItem( $data["id"] );
    	}

    	$data["permit"]["del"] = true;

    	return $data;
    }

    #items
    public function listsItem($id, $options=array()){
    	return $this->buildFragItem( $this->db->select("SELECT * FROM export_products_item WHERE item_exp_id=:id", array(":id"=>$id)), $options );
    }
    public function buildFragItem($results, $options=array()){
    	$data = array();
    	foreach ($results as $key => $value) {
    		if( empty($value) ) continue;
    		$data[] = $this->convertItem( $value, $options );
    	}
    	return $data;
    }
    public function convertItem( $data, $options=array() ){
    	$data = $this->cut("item_", $data);
    	return $data;
    }
    public function setItem($data){
    	$data["item_updated"] = date("c");
    	if( !empty($data["id"]) ){
    		$id = $data["id"];
    		unset($data["id"]);
    		$this->db->update("export_products_item", $data, "item_id={$id}");
    	}
    	else{
    		$data["item_created"] = date("c");
    		$this->db->insert("export_products_item", $data);
    	}
    }
    public function unsetItem($id){
    	$this->db->delete("export_products_item", "item_id={$id}");
    }
    public function delItem($id){
    	$this->db->delete("export_products_item", "item_exp_id={$id}", $this->db->count("export_products_item", "item_exp_id={$id}"));
    }

    /* categories */
    public function category($id=null){
        if( !empty($id) ){
            $sth = $this->db->prepare("SELECT cate_id as id, cate_name as name FROM export_categories WHERE cate_id=:id");
            $sth->execute( array(
                ':id' => $id
            ) );

            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            $fdata["total_adjust"] = $this->db->count($this->_objName, "exp_cate_id=:id", array(":id"=>$id));
            $fdata['permit']['del'] = !empty($fdata["total_adjust"]) ? false : true;

            return $sth->rowCount()==1
            ? $fdata
            : array();
        }
        else{
            return $this->db->select("SELECT cate_id as id, cate_name as name FROM export_categories ORDER BY cate_name ASC");
        }
    }
    public function insertCategory( $data ){
        $this->db->insert("export_categories", $data);
    }
    public function updateCategory($id, $data){
        $this->db->update("export_categories", $data, "cate_id={$id}");
    }
    public function deleteCategory($id){
        $this->db->delete("export_categories", "cate_id={$id}");
    }
    public function is_category($name){
        return $this->db->count("export_categories", "cate_name=:name", array(":name"=>$name));
    }
}