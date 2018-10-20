<?php 
class Import_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "import_products";
    private $_table = "import_products i LEFT JOIN suppliers s ON i.imp_sup_id=s.sup_id";
    private $_field = "i.*, s.sup_code,s.sup_name";
    private $_cutNamefield = "imp_";

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
    	$this->db->delItems($id);
    	$this->db->delete($this->_objName, "{$this->_cutNamefield}id={$id}");
    }
    public function lists($options=array()){
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

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $this->_cutNamefield.$options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        if( !empty($options["unlimit"]) ) $limit = "";

        $groupby = !empty($groupby) ? "GROUP BY {$groupby}" :'';

        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$groupby} {$orderby} {$limit}", $where_arr ), $options );

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
            $data[] = $this->convert($value, $options);
        }
        return $data;
    }
    public function convert($data, $options=array()){
    	$data = $this->cut($this->_cutNamefield, $data);
    	if( !empty($options["items"]) ){
    		$data['items'] = $this->listsItem( $data['id'] );
    	}
        $data['permit']['del'] = true;

    	return $data;
    }

    /* ITEMS */
    public function listsItem($id){
    	return $this->buildFragItem( $this->db->select("SELECT * FROM import_products_item WHERE item_imp_id={$id}") );
    }
    public function buildFragItem($results){
    	$data = array();
    	foreach ($results as $key => $value) {
    		if( empty($value) ) continue;
    		$data[] = $this->convertItem($value);
    	}
    	return $data;
    }
    public function convertItem($data){
    	$data = $this->cut("item_", $data);
    	return $data;
    }
    public function setItem($data){
    	if( !empty($data["id"]) ){
    		$id = $data["id"];
    		unset($data["id"]);
    		$this->db->update("import_products_item", $data, "item_id={$id}");
    	}
    	else{
    		$this->db->insert("import_products_item", $data);
    	}
    }
    public function delItem($id){
    	$this->db->delete("import_products_item", "item_id={$id}");
    }
    public function delItems($id){
    	$this->db->delete("import_products_item", "item_imp_id={$id}", $this->db->count("import_products_item", "item_imp_id={$id}"));
    }
    public function listsProduct(){
        return $this->db->select("SELECT p.id, pds_name AS name, pds_barcode AS barcode, pp.frontend AS price, pds_unit AS unit FROM products p LEFT JOIN products_pricing pp ON p.id=pp.product_id");
    }

    #getProduct
    public function getProduct($id=null){
        $sth = $this->db->prepare("SELECT * FROM products WHERE id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        return $sth->rowCount()==1
            ? $sth->fetch( PDO::FETCH_ASSOC )
            : array();
    }
}