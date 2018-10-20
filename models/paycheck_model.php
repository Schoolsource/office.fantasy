<?php 
class Paycheck_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "suppliers_paycheck";
    private $_table = "suppliers_paycheck p 
    				   LEFT JOIN suppliers s ON p.check_sup_id=s.sup_id
    				   LEFT JOIN admins a ON p.check_user_id=a.id
                       LEFT JOIN payments_bank b ON p.check_bank_id=b.bank_id";
    private $_field = "p.*
                       , a.name AS user_name

                       , s.sup_id
                       , s.sup_name
                       , s.sup_prefix_name
                       , s.sup_first_name
                       , s.sup_last_name
                       , s.sup_nickname
                       , s.sup_phone

                       , b.bank_code
                       , b.bank_name";
    private $_cutNamefield = "check_";

    public function lists( $options=array() ){

    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'date',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( isset($_REQUEST["bank"]) ){
            $options["bank"] = $_REQUEST["bank"];
        }
        if( !empty($options["bank"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "p.check_bank_id=:bank";
            $where_arr[":bank"] = $options["bank"];
        }

        if( isset($_REQUEST["period_start"]) && isset($_REQUEST["period_end"]) ){
            $options["period_start"] = $_REQUEST["period_start"];
            $options["period_end"] = $_REQUEST["period_end"];
        }
        if( !empty($options["period_start"]) && !empty($options["period_end"]) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "(p.check_date BETWEEN :s AND :e)";
            $where_arr[":s"] = $options["period_start"];
            $where_arr[":e"] = $options["period_end"];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $orderby = $this->orderby( $this->_cutNamefield.$options['sort'], $options['dir'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        if( !empty($options["unlimit"]) ) $limit = "";
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

        $prefix_name_str = $this->query('system')->getPrefixName($data['sup_prefix_name']);
        if( empty($prefix_name_str) ){
            $prefix_name_str = '';
        }
        $data['sup_fullname'] = "{$prefix_name_str}{$data['sup_first_name']} {$data['sup_last_name']}";
        if( !empty($data['sup_nickname']) ){
            $data['sup_fullname'] = $data['sup_fullname']." ({$data['sup_nickname']})";
        }

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
    public function is_check( $text ){
    	return $this->db->count($this->_table, "{$this->_cutNamefield}number=:text", array(":text"=>$text));
    }
}