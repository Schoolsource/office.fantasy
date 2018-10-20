<?php 
class Events_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "events";
    private $_table = "events e
    				   LEFT JOIN admins a ON e.event_user_id=a.id";
    private $_field = "e.*,a.name AS user_name";
    private $_cutNamefield = "event_";

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

        if( isset($_REQUEST['view_stype']) ){
            $options['view_stype'] = $_REQUEST['view_stype'];
        }

        if( !empty($_REQUEST['obj_id']) && !empty($_REQUEST['obj_type']) ){
            $options['obj_id'] = $_REQUEST['obj_id'];
            $options['obj_type'] = $_REQUEST['obj_type'];
        }
        if( !empty($options['obj_id']) && !empty($options['obj_type']) ){

            $this->_table .= ' LEFT JOIN events_obj_permit ej ON e.event_id=ej.event_id';

            $where_str .= !empty( $where_str ) ? " AND ":'';
            $where_str .= "(ej.obj_id=:obj_id AND ej.obj_type=:obj_type)";
            $where_arr[':obj_id'] = $options['obj_id'];
            $where_arr[':obj_type'] = $options['obj_type'];
        }

        if( isset($options['upcoming']) ){
            $where_str .= !empty( $where_str ) ? " AND ":'';
            $where_str .= "ev.event_start>=:upcoming";
            $where_arr[':upcoming'] = date('Y-m-d 00:00:00');

            $options['dir'] = 'ASC';
        }

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

        $view_stype = !empty($options['view_stype']) ? $options['view_stype']:'convert';
        if( !in_array($view_stype, array('bucketed', 'convert')) ) $view_stype = 'convert';

        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->{$view_stype}($value, $options);
        }

        return $data;
    }
    public function bucketed($data, $options=array()) {
        
        $data = $this->convert( $data, $options );

        $subtext = '';
        if( !empty($data['location']) ){
            $subtext = '<i class="icon-map-marker"></i>'.$data['location'];
        }
        else{
            $subtext = $this->fn->q('text')->more( $data['text'], 30 );
        }

        if( $data['end']=='0000-00-00 00:00:00' ){
            $data['end'] = $data['start'];
        }

        return array(
            'id'=> $data['id'],
            "type"=>"events",
            'url' => URL.'events/'.$data['id'],
            'plugin' => 'dialog',
            'text'=> $data['title'],
            "category"=> '<i class="icon-clock-o"></i>'.$this->fn->q('time')->str_event_date($data['start'], $data['end']),
            "subtext"=> $subtext,
            'start_date' => $data['start'],
            'end_date' => $data['end'],
            'color_code' => trim($data['color_code'], '#'),
        );
    }
    public function convert($data, $options=array()){
    	$data = $this->cut($this->_cutNamefield, $data);

    	$data['invite'] = $this->listInvite( $data['id'] );
        $data['permit']['del'] = true;

    	return $data;
    }
    public function insert(&$data){
    	$data["{$this->_cutNamefield}created"] = date("c");
    	$data["{$this->_cutNamefield}updated"] = date("c");
    	$this->db->insert($this->_objName,$data);
    	$data['id'] = $this->db->lastInsertId();
    }
    public function update($id, $data){
    	$data["{$this->_cutNamefield}updated"] = date("c");
    	$this->db->update($this->_objName, $data , "{$this->_cutNamefield}id={$id}");
    }
    public function delete($id){
    	$this->db->delete($this->_objName, "{$this->_cutNamefield}id={$id}");
    	$this->deleteJoinEvent($id);
    }

    #Permit
    public function insertJoinEvent( $data ){
        $this->db->insert('events_obj_permit', $data);
    }
    public function deleteJoinEvent( $id ){
        $this->db->delete( 'events_obj_permit', "event_id={$id}" , $this->db->count('events_obj_permit' , "event_id={$id}") );
    }
    public function deleteEventObj( $obj_id , $obj_type ){
        $this->db->delete( 'events_obj_permit', "obj_id={$obj_id} AND obj_type={$obj_type}", $this->db->count('events_obj_permit', "obj_id={$obj_id} AND obj_type={$obj_type}") );
    }

    #Invite List
    public function listInvite( $id ){

        $admins = $this->db->select("SELECT a.id, a.name , a.email FROM admins a LEFT JOIN events_obj_permit p ON a.id=p.obj_id WHERE p.event_id=:id AND p.obj_type='admins'",array(':id'=>$id));

        $customers = $this->db->select("SELECT c.id, c.sub_code AS code, c.name_store AS name, c.email FROM customers c LEFT JOIN events_obj_permit p ON c.id=p.obj_id WHERE p.event_id=:id AND p.obj_type='customers'", array(':id'=>$id));

        $sales = $this->db->select("SELECT s.id, s.sale_code AS code, s.sale_name AS name FROM sales s LEFT JOIN events_obj_permit p ON s.id=p.obj_id WHERE p.event_id=:id AND p.obj_type='sales'", array(':id'=>$id));

        $users = $this->db->select("SELECT u.id, u.name, u.email FROM users u LEFT JOIN events_obj_permit p ON u.id=p.obj_id WHERE p.event_id=:id AND p.obj_type='users'", array(':id'=>$id));

        $orders = $this->db->select("SELECT o.id, o.ord_code AS code FROM orders o LEFT JOIN events_obj_permit p ON o.id=p.obj_id WHERE p.event_id=:id AND p.obj_type='orders'", array(":id"=>$id));

        $suppliers = $this->db->select("SELECT s.sup_id as id, s.sup_code AS code, s.sup_name as name FROM suppliers s LEFT JOIN events_obj_permit p ON s.sup_id=p.obj_id WHERE p.event_id=:id AND p.obj_type='suppliers'", array(':id'=>$id));

        $data['admin'] = $admins;
        $data['customers'] = $customers;
        $data['sales'] = $sales;
        $data['users'] = $users;
        $data['orders'] = $orders;
        $data['suppliers'] = $suppliers;

        return $data;
    }
}