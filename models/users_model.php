<?php

class Users_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "users";
    private $_table = "users";
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
          $where_str .= "name LIKE :q OR email LIKE :q";
          $where_arr[":q"] = "%{$options['q']}%";
        }


        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        if( !empty($options["unlimit"]) ) $limit = "";

        $groupby = !empty($groupby) ? "GROUP BY {$groupby}" :'';

        // print_r("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$groupby} {$orderby} {$limit}");die;

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

    	$data['permit']['del'] = true;

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
    public function is_email($text) {
      return $this->db->count($this->_table, 'email=:text', array(':text'=>$text));
    }
}
