<?php

class Admins_Model extends Model{

    public function __construct() {
        parent::__construct();
    }

    private $_objName = "admins";
    private $_table = "admins";
    private $_field = "*";

    public function lists( $options=array() ){

        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'created_at',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,

            'more' => true
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "";
        $where_arr = array();

        if( !empty($options['q']) ){
            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "name LIKE :q OR username LIKE :q";
            $where_arr[":q"] = "%{$options['q']}%";

            // $arrQ = explode(' ', $options['q']);
            // $wq = '';
            // foreach ($arrQ as $key => $value) {
            //     $wq .= !empty( $wq ) ? " OR ":'';
            //     $wq .= "name LIKE :q{$key}
            //             OR username LIKE :q{$key}";
            //     $where_arr[":q{$key}"] = "%{$value}%";
            //     $where_arr[":s{$key}"] = "{$value}%";
            //     $where_arr[":f{$key}"] = $value;
            // }

            // if( !empty($wq) ){
            //     $where_str .= !empty( $where_str ) ? " AND ":'';
            //     $where_str .= "($wq)";
            // }
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $orderby = $this->orderby( $options['sort'], $options['dir'] );
        $limit = $this->limited( $options['limit'], $options['pager'] );

        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ), $options  );

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
        // permission
        if( !empty($data['permission']) ){
            $data['permission'] = json_decode($data['permission'], true);
        }

        $data['access'] = $this->setAccess( $data['role_id'] );

        $data['permit']['del'] = true;
        return $data;
    }
    public function setAccess($id)  {
        $access = array();
        
        if( $id == 1 ){
            $access = array(1);
        }

        return $access;
    }


    public function insert(&$data){
        $data['created_at'] = date("c");
        $data['updated_at'] = date("c");
        $this->db->insert($this->_objName, $data);
        $data['id'] = $this->db->lastInsertId();
    }
    public function update($id, $data){
        $data['updated_at'] = date("c");
        $this->db->update($this->_objName, $data, "id={$id}");
    }
    public function delete($id){
        $this->db->delete($this->_objName, "id={$id}");
    }

    /**/
    /* LOGIN */
    /**/
    public function login($user, $pass){

        $sth = $this->db->prepare("SELECT id FROM {$this->_table} WHERE username=:login AND password=:pass");

        $sth->execute( array(
            ':login' => $user,
            ':pass' => Hash::create('sha256', $pass, HASH_PASSWORD_KEY)
        ) );

        $fdata = $sth->fetch( PDO::FETCH_ASSOC );
        return $sth->rowCount()==1 ? $fdata['id']: false;
    }
    public function loginLaravel($user, $pass){
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
    public function is_user($text){
        return $this->db->count($this->_objName, "username='{$text}'");
    }
}