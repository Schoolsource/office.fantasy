<?php 

class Project_Customer extends Model
{
	public function __construct() {
		parent::__construct();
    }

    private $_table = 'customer_project';
    private $_field = '*';
    private $_prefixField = 'project_';

    public function findById($id)
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE {$this->_prefixField}id=:id LIMIT 1");
        $sth->execute( array( ':id' => $id  ) );
        return $sth->rowCount()==1 ? $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) ): array();
    }

    public function find( $options=array() )
    {
    	$options = array_merge(array(
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'id',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),

        ), $options);

        $condition = "";
        $params = array();

        if( isset($options['enabled']) ){
            $condition = "{$this->_prefixField}enabled=:enabled";
            $params[':enabled'] = $options['enabled'];
        }

        $arr['total'] = $this->db->count($this->_table, $condition, $params);

        $limit = !empty($options['limit']) && !empty($options['pager']) ? $this->limited( $options['limit'], $options['pager'] ):'';
        $orderby = $this->orderby( $this->_prefixField.$options['sort'], $options['dir'] );
        $where = !empty($condition) ? "WHERE {$condition}":'';
        $sql = "SELECT {$this->_field} FROM {$this->_table} {$where} {$orderby} {$limit}";

        $arr['items'] = $this->buildFrag( $this->db->select($sql, $params), $options );


        if( !empty($options['limit']) ){
            if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        }
        else{
            $options['more'] = false;
        }
        
        $arr['options'] = $options;
        return $arr;
    }

    /* -- convert data -- */
    public function buildFrag($results, $options=array()) {
        $data = array();
        foreach ($results as $key => $value) { if( empty($value) ) continue; $data[] = $this->convert( $value ); }
        return $data;
    }
    public function convert($data){

        $data['totalOrder'] = $this->db->count('orders', 'ord_project_id=:id', array(':id'=>$data['project_id']));

        if( $data['totalOrder'] == 0 ){
            $data['permit']['del'] = 1;
        }

        
        return $data;
    }


    /* -- action -- */
    public function insert(&$data)
    {
        if( !isset($data[$this->_prefixField.'enabled']) ) $data[$this->_prefixField.'enabled'] = 1;

        $this->db->insert($this->_table, $data);
        $data['id'] = $this->db->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $this->db->update($this->_table, $data, "{$this->_prefixField}id={$id}");
    }

    public function delete($id)
    {
        $this->db->delete( $this->_table, "{$this->_prefixField}id={$id}" );
    }
}