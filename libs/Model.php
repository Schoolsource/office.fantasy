<?php

class Model {

    function __construct() {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $this->fn = new _function();

        $this->lang = new Langs();

        Session::init();
        $lang = Session::get('lang');

        if( isset($lang) ){
            $this->lang->set( $lang );
        }
    }

    // private query protected
    private $_query = array();

    // Public query
    public function query( $table=null ){

        // echo $this->lang->getCode(); die;

        $path = "models/{$table}_model.php";
        
        if(!array_key_exists($table, $this->_query) && file_exists($path)){

            require_once $path;
            $modelName = $table . '_Model';
            $this->_query[$table] = new $modelName();
           
        }

        return $this->_query[$table];
        
    }
    protected function limited($limit=0, $pager=1, $del=0){
        return "LIMIT ".((($pager*$limit)-$limit)-$del) .",". $limit;
    }

    protected function orderby($sort, $dir='DESC'){
        return "ORDER BY ".( $dir=='rand'  ? "rand()": "{$sort} {$dir}" );
    }
    protected function cut($search, $results)  {
        $data = array();
        foreach ($results as $key => $value) {
            $data[ str_replace($search, '', $key) ] = $value;
        }
        return $data;
    }
    public function permitOnPages() {
        return array(
            'settings' => array(
                'shop' => true,
                'admin' => false,
                'department' => false
            ),
            
        );
    }

    public function _convert($_data, $options=array()) {

        $options = array_merge( array('color','status','refer','sale', 'cus', 'product', 'model', 'car', 'brand', 'dealer', 'pro', 'emp', 'tec', 'type'), $options );

        $data = array();
        foreach ($_data as $key => $value) {
            $ex = explode('_', $key, 2);

            if( in_array($ex[0], $options ) && count($ex)==2 ){
                $data[ $ex[0] ][ $ex[1] ] = $value;
            }
            else{
                $data[ $key ] = $value;
            }
        }

        if( !empty($data['cus']) ){
            $data['cus'] = $this->query('customers')->convert( $data['cus'] );
        }

        if( !empty($data['emp']) ){
            $data['emp'] = $this->query('employees')->convert( $data['emp'] );
        }

        if( !empty($data['tec']) ){
            $data['tec'] = $this->query('employees')->convert( $data['tec'] );
        }

        if( !empty($data['sale']) ){
            $data['sale'] = $this->query('employees')->convert( $data['sale'] );
        }
        
        $data['is_convert'] = true;

        return $data;
    }

    public function _setFieldFirstName($_first, $data){
        $_data = array();
        foreach ($$data as $key => $value) {
            $_data[] = $_first.$value;
        }

        return $_data;
    }

    
}