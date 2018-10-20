<?php

class Media_Model extends Model
{
    private $_albumKeyName = 'album';
    private $_mediaKeyName = 'media';
    private $_shape = array('o', 'n', 'a');
    private $_logFolderName = array('slideshow', 'public', 'cover', 'clipboard', 'building', 'property');

    private $_objType = 'media';
    private $_table = 'media m
        INNER JOIN media_albums a ON a.album_id=m.media_album_id
        LEFT JOIN admins emp ON m.media_emp_id=emp.id';

    private $_field = '

          media_id
        , media_name
        , media_caption
        , media_type
        , media_created

        , album_id
        , album_name
        , album_folder

        , emp.name
    ';

    private $_fristNamefield = 'media_';

    public function __construct()
    {
        parent::__construct();
    }

    public function searchAlbum($options = array())
    {
        $whe = '';
        $arr = array();

        $keys = array('album_obj_type', 'album_obj_id'); //, 'album_name'

        foreach ($keys as $key) {
            if (isset($options[$key])) {
                $whe .= !empty($whe) ? ' AND ' : '';
                $whe .= "{$key}=:{$key}";

                $arr[":{$key}"] = $options[$key];
            }
        }

        $sth = $this->db->prepare("SELECT * FROM media_albums WHERE {$whe} LIMIT 1");
        $sth->execute($arr);

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    public function setAlbum(&$data)
    {
        if (isset($data['album_name'])) {
            if (in_array($data['album_name'], $this->_logFolderName)) {
                $data['album_log'] = 1;
            }
        }

        if (!isset($data['album_created'])) {
            $data['album_created'] = date('c');
        }

        $this->db->insert('media_albums', $data);
        $data['album_id'] = $this->db->lastInsertId();
    }

    public function lists($options = array())
    {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 12,

            'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'sequence',
            'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC',

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
            'more' => true,
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = '';
        $where_arr = array();
        // customer_file

        if (isset($_REQUEST['obj_type']) || isset($options['obj_type'])) {
            if (isset($_REQUEST['obj_type'])) {
                $options['obj_type'] = $_REQUEST['obj_type'];
            }
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'a.album_obj_type=:obj_type';
            $where_arr[':obj_type'] = $options['obj_type'];
        }

        if (isset($_REQUEST['obj_id']) || isset($options['obj_id'])) {
            if (isset($_REQUEST['obj_id'])) {
                $options['obj_id'] = $_REQUEST['obj_id'];
            }
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'a.album_obj_id=:obj_id';
            $where_arr[':obj_id'] = $options['obj_id'];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $where_str = !empty($where_str) ? "WHERE {$where_str}" : '';
        $orderby = $this->orderby($this->_fristNamefield.$options['sort'], $options['dir']);
        $limit = $this->limited($options['limit'], $options['pager']);

        // echo "SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}"; die;
        $arr['lists'] = $this->buildFrag($this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr));

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }
        $arr['options'] = $options;

        return $arr;
    }

    public function buildFrag($results)
    {
        $data = array();
        foreach ($results as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $data[] = $this->convert($value);
        }

        return $data;
    }

    public function _getAlbumKey($id)
    {
        return Hash::create('crc32b', $id, $this->_albumKeyName);
    }

    public function _getMediaKey($id)
    {
        return Hash::create('md5', $id, $this->_mediaKeyName);
    }

    public function set($userfile, &$data, $options = array())
    {
        $options = array_merge(array(
            'folder' => 1,
            'dir' => WWW_UPLOADS,
            'url' => UPLOADS,

            'size' => 25,

            'minimize' => isset($_REQUEST['minimize']) ? explode(',', $_REQUEST['minimize']) : array(270, 270),
            'type' => 'picture',
            'has_quad' => isset($_REQUEST['has_quad']) ? 1 : 0,
        ), $options);

        $upload = new Upload();
        $upload->current = $userfile;

        $folder = rtrim($options['folder'], '/');
        $folder .= '/';

        $dir = $options['dir'].$options['folder'];
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($upload->validate($data['error'], array('type' => $options['type'], 'size' => $options['size']))) {
            unset($data['error']);

            $extension = $upload->getExtension($userfile['name']);
            $type = isset($data['media_type']) ? $data['media_type'] : $upload->getType($userfile['name']);

            if (!isset($data['media_name'])) {
                $data['media_name'] = $userfile['name'];
            }

            if (!isset($data['media_created'])) {
                $data['media_created'] = date('c');
            }

            if (!isset($data['updated_at'])) {
                $data['updated_at'] = date('c');
            }

            // if (!isset($data['pds_id'])) {
            //     $data['pds_id'] = $options['pds_id'];
            // }

            // if (!isset($data['seq'])) {
            //     $data['seq'] = $options['seq'];
            // }

            $this->db->insert('media', $data);
            $data['media_id'] = $this->db->lastInsertId();
            $this->sequenceForAlbum($data['media_id'], $data['media_album_id']);

            $aid = $this->_getAlbumKey($data['media_album_id']);
            $mid = $this->_getMediaKey($data['media_id']);

            $filename = "{$folder}{$aid}_{$mid}_";

            $this->db->update('media', ['media_name' => $filename, 'renamed' => 1], "media_id={$mid}");

            $original = $options['dir'].$filename."o{$extension}";
            if ($upload->copies($userfile['tmp_name'], $original)) {
                $data['dir']['original'] = $original;
                // $data['url']['original'] = $options['url'].$filename."o.jpg";

                if ($options['type'] == 'picture') {
                    // cropimage
                    /*if( !empty($_POST['cropimage']) ){
                        $this->model->query('media')->resize($data['media_id'], $_POST['cropimage']);
                    }*/

                    $path = $options['dir'].$filename."o.{$type}";
                    if (isset($data['media_type'])) {
                        if ($data['media_type'] == 'jpg' && $type != 'jpg') {
                            $upload->imageToJpg($original, $path); // เปลียนประเภทรูป
                        }
                    }

                    // covert to size Normal
                    $normal = $options['dir'].$filename."n.{$type}";
                    if ($upload->copies($path, $normal)) {
                        $upload->minimize($normal, $options['minimize']);
                        $data['dir']['normal'] = $normal;
                        // $data['url']['normal'] = $options['url'].$filename."n.jpg";
                    }

                    // covert to size Quad 4 เหลียมจตุรัส
                    if (!empty($options['has_quad'])) {
                        $quad = $options['dir'].$filename."a.{$type}";
                        if ($upload->copies($path, $quad)) {
                            $upload->quad($quad);
                        }
                    }

                    $upload->minimize($path);
                }
            } else {
                $data['error'] = 1;
                $data['message'] = 'Failed copy file.';
            }
        } // ตรวจสอบไฟล์
        else {
            $data['error'] = 1;
            $data['message'] = 'Failed copy file.';
        }
    }

    public function get($id)
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE media_id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id,
        ));

        return  $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC))
            : array();
    }

    public function convert($fdata)
    {
        if (isset($fdata['media_album_id'])) {
            $fdata['album_id'] = $fdata['media_album_id'];
        }

        $aid = $this->_getAlbumKey($fdata['album_id']);
        $mid = $this->_getMediaKey($fdata['media_id']);
        $type = $fdata['media_type'];

        $data = $this->_convert($fdata);
        $data['album_id'] = $fdata['album_id'];
        $data['id'] = $fdata['media_id'];
        $data['type'] = $type;
        $data['name'] = $fdata['media_name'];

        // original
        $data['original'] = "{$aid}_{$mid}_o.{$type}";
        $data['original_url'] = UPLOADS."{$data['album_id']}/{$aid}_{$mid}_o.{$type}";

        // filename
        $data['filename'] = "{$aid}_{$mid}_n.{$type}";
        $data['url'] = UPLOADS."{$data['album_id']}/".$data['filename'];
        $data['src'] = UPLOADS."{$data['album_id']}/".$data['filename'];

        // area
        $data['quad'] = "{$aid}_{$mid}_a.{$type}";
        $data['quad_url'] = UPLOADS."{$data['album_id']}/".$data['quad'];

        // caption
        $data['caption'] = isset($fdata['media_caption']) ? $fdata['media_caption'] : '';

        if (!empty($fdata['folder'])) {
            $data['folder'] = $fdata['folder'];
        }

        if (isset($fdata['media_created'])) {
            $data['created'] = $fdata['media_created'];
        }

        return $data;
    }

    public function resize($id, $fdata, $folder = '', $minimize = array(500, 500))
    {
        $data = $this->get($id);
        if (!empty($data)) {
            $aid = $this->_getAlbumKey($data['album_id']);
            $mid = $this->_getMediaKey($data['id']);
            $type = $data['type'];

            $filename = "{$folder}{$aid}_{$mid}_";
            $original = WWW_UPLOADS.$filename."o.{$type}";

            if (file_exists($original)) {
                $upload = new Upload();

                $dest = WWW_UPLOADS.$filename."n.{$type}";
                if ($upload->copies($original, $dest)) {
                    $upload->cropimage($fdata, $dest);

                    $upload->minimize($dest, $minimize);
                }
            }
        }
    }

    public function del($id, $folder = '')
    {
        $data = $this->get($id);

        if (!empty($data)) {
            $aid = $this->_getAlbumKey($data['album_id']);
            $mid = $this->_getMediaKey($data['id']);
            $type = $data['type'];

            $filename = "{$folder}{$aid}_{$mid}_";

            foreach ($this->_shape as $key => $shape) {
                $dest = WWW_UPLOADS."{$data['album_id']}/".$filename."{$shape}.{$type}";

                if (file_exists($dest)) {
                    unlink($dest);
                }
            }

            $this->db->delete('media', "`media_id`={$id}");
        }
    }

    public function sequenceForAlbum($mid, $aid)
    {
        $sth = $this->db->prepare('SELECT media_id as id, media_sequence as seq FROM media WHERE media_album_id=:id ORDER BY media_sequence DESC LIMIT 1');
        $sth->execute(array(':id' => $aid));

        // $seq = 1;
        if ($sth->rowCount() == 1) {
            $fdata = $sth->fetch(PDO::FETCH_ASSOC);

            $seq = $fdata['seq'] + 1;
            $this->db->update('media', array('media_sequence' => $seq), "`media_id`={$mid}");
        }
    }
}
