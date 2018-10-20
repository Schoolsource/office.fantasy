<?php

class Photos_Model extends Model
{
	public function __construct()
    {
        parent::__construct();
    }

    private $_tableAlbum = "photos_albums";
    private $_selectAlbum = "
		  album_id as id
		, album_name as name

		, album_obj_type as obj_type
		, album_obj_id as obj_id
    ";
    private $_firstFieldAlbum = "album_";
    public function album($id, $options=array())
    {
    	
    	if( is_array($id) ){

    		$condition = '`album_obj_id`=:id';
    		$params = array( ':id' => $id['obj_id'] );

    		$condition .= !empty($condition) ? " AND ":'';
    		$condition .= '`album_obj_type`=:obj';
    		$params[':obj'] = $id['obj'];
    	}
    	else{
    		$condition = '`album_id`=:id';
    		$params = array( ':id' => $id );
    	}

    	$condition = !empty($condition) ? "WHERE {$condition}":'';
    	// echo "SELECT {$this->_selectAlbum} FROM {$this->_tableAlbum} {$condition} LIMIT 1"; die;
    	$sth = $this->db->prepare("SELECT {$this->_selectAlbum} FROM {$this->_tableAlbum} {$condition} LIMIT 1");
        $sth->execute( $params );

        return $sth->rowCount()==1 
            ? $sth->fetch( PDO::FETCH_ASSOC )
            : array();
    }
    public function albumsList($options=array())
    {
    	return $this->db->select("SELECT {$this->_selectAlbum} FROM {$this->_tableAlbum}");
    }
    private function setDataPostAlbum($post)
    {
        $data = array();
        foreach ($post as $key => $value) {
            $data[ $this->_firstFieldAlbum.$key ] = trim($value);
        }

        return $data;
    }
    public function addAlbum(&$data)
    {
        $this->db->insert($this->_tableAlbum, $this->setDataPostAlbum($data));
        $data['id'] = $this->db->lastInsertId();
    }


    private $_objType = "photos";
    private $_table = "photos LEFT JOIN photos_albums ON photo_album_id=album_id";
    private $_select = "
		  photo_id
		, photo_name
		, photo_caption
		, photo_type
		, photo_size
	
		, album_id
		, album_name

		, album_obj_type as obj_type
		, album_obj_id as obj_id
    ";
    private $_firstFieldName = "photo_";
    public function get($id, $options=array())
    {
        $sth = $this->db->prepare("SELECT {$this->_select} FROM {$this->_table} WHERE {$this->_firstFieldName}id=:id LIMIT 1");
        $sth->execute( array( ':id' => $id ) );

        return $sth->rowCount()==1 
            ? $this->convert( $sth->fetch( PDO::FETCH_ASSOC ), $options )
            : array();
    }
    public function lists($options=array())
    {
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'more' => true,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']: 'created',
            'dir' => isset($_REQUEST['dir'])? $_REQUEST['dir']: 'DESC',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);
        
        $condition = '';
        $params = array();

        if( !empty($options['album']) ){
            $condition .= !empty($condition) ? ' AND ':'';
            $condition .= "`{$this->_firstFieldName}album_id`=:album";

            $params[':album'] = $options['album'];
        }

        $arr['total'] = $this->db->count($this->_table, $condition, $params);

        $condition = !empty($condition) ? "WHERE {$condition}":'';
        $orderby = $this->orderby( $this->_firstFieldName.$options['sort'], $options['dir'] );
        $limit = !empty($options['unlimit']) ? '' : $this->limited( $options['limit'], $options['pager'] );

        // echo "SELECT {$this->_select} FROM {$this->_table} {$condition} {$orderby} {$limit}"; die;
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_select} FROM {$this->_table} {$condition} {$orderby} {$limit}", $params ), $options );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }

    /* convert */
    public function buildFrag($results, $options=array()) {
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert( $value, $options );
        }

        return $data;
    }
    public function convert($data, $options=array())
    {
        $data = $this->cut($this->_firstFieldName, $data);

        if( empty($data['type']) ) $data['type'] = 'jpg';

        $name = $this->createName($data['album_id'], $data['id']);
        $data['url'] = URL."public/photos/{$name}.{$data['type']}";
        $data['size'] = json_decode($data['size'],1);


        $data['permit']['del'] = true;
        return $data;
    }


    /* -- Action -- */
    private function insert($data)
    {
        if( empty($data["{$this->_firstFieldName}created"]) ) $data["{$this->_firstFieldName}created"] = date('c');
       
        $this->db->insert($this->_objType, $data);
        return $this->db->lastInsertId();
    }
    public function update($id, $data)
    {
        $this->db->update($this->_objType, $data, "`{$this->_firstFieldName}id`={$id}");
    }
    public function delete($id)
    {
        if( is_array($id) ){
            $item = $id;
            $id = $item['id'];
        }
        else{
            $item = $this->get($id);
        }

        // print_r($item); die;
        $name = $this->createName($item['album_id'], $id);

        $type = $item['type'];
        $filename = "{$name}.{$type}";

        $source = WWW_PHOTOS.$filename;
        $path = PHOTOS.$filename;
        if( file_exists($source) ){
            unlink($source);
        }

        $this->del( $id );
    }
    public function del($id)
    {
        $this->db->delete($this->_objType, "`{$this->_firstFieldName}id`={$id}");
    }

    public function set($userfile, $options=array()) {

        $options = array_merge(array(
            // 'obj' => 'site', // obj_type
            // 'obj_id' => 0, // obj_id

            'type' => 'jpg',
            'minimize' => array(950, 950),
            'caption' => '',
            'primalink' => ''

            // 'width'
        ), $options);
    	

        if( empty($options['album']) ){
            // error 
            $arr['message'] = 'กำหนด อัลบั้ม';
            $arr['error'] = 'album';
            return $arr;
        }

        // get album
        $album = $this->album( $options['album'] );

        if( empty($album) ){
            // error 
            $arr['error'] = 'album';
            return $arr;
        }

        $source = $userfile['tmp_name'];
        $filename = $userfile['name'];

        list($original_width, $original_height) = getimagesize($source);

        $media = array(
            'photo_album_id' => $album['id'],
            'photo_name' => $filename,
            'photo_caption' => $options['caption'],
            'photo_type' => $options['type'],
            'photo_size' => json_encode(array(
            	'width' => $original_width,
            	'height' => $original_height
            ))
            // 'media_primalink' => $options['primalink'],
        );

        if( !empty($options['id']) ){
            $media_id = $options['id'];
            $this->update( $media_id, $media );
        }
        else{
            // create media
            $media_id = $this->insert( $media );
        }

        $u = new Upload();
        $u->current = $userfile;

        $extension = strtolower(strrchr($filename, '.'));
        $type = strtolower(substr(strrchr($filename,"."),1));

        $name = $this->createName($album['id'], $media_id);
        $dest = WWW_PHOTOS.$name.$extension;

        if( $u->copies($source, $dest) ){
            if($type!='jpg'){
                $dest_new = WWW_PHOTOS.$name.".jpg";
                $u->convertImage( $dest, $dest_new );

                if( file_exists($dest_new) ){
                    $u->minimize( $dest_new, $options['minimize'] );                        
                }
                else{
                    $arr['error'] = 'ไม่สามารถใช้รูปนี้ได้';
                    $this->del( $media_id );
                }

                unlink($dest);
                $dest = $dest_new;

            }
            else{
                // $u->minimize( $dest, $options['minimize'] );
            }

            if( !empty($_POST['cropimage']) ){

                $u->cropimage($_POST['cropimage'], $dest);
                // print_r($_POST['cropimage']); die;
            }


            $arr = array_merge(array('id'=>$media_id), $options);
        }
        else{
            $arr['error'] = 'ไม่สามารถใช้รูปนี้ได้';
        }


        return $arr;
    }
    public function createName($aid, $mid)
    {
    	$aid =  Hash::create('crc32b', $aid, 'album');
        $mid =  Hash::create('md5', $mid, 'media');

    	return "{$aid}_{$mid}";
    }
    
}