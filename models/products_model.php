<?php

class Products_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $_objName = 'products';
    private $_table = 'products p 
                       LEFT JOIN products_pricing pr ON p.id=pr.product_id
                       LEFT JOIN categories c ON p.pds_categories_id=c.id';
    private $_field = 'p.*
                       , c.name_en AS category_name_en
                       , c.name_th AS category_name
                       , pr.frontend AS frontend
                       , pr.website AS website';
    // private $_cutNamefield = "pds_";

    public function lists($options = array())
    {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,

            'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id',
            'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC',

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,

            'more' => true,
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = '';
        $where_arr = array();

        if (isset($_REQUEST['category'])) {
            $options['category'] = $_REQUEST['category'];
        }
        if (!empty($options['category'])) {
            $where_str .= !empty($where_str) ? $where_str : '';
            $where_str .= 'pds_categories_id=:category';
            $where_arr[':category'] = $options['category'];
        }

        if (!empty($options['q'])) {
            $arrQ = explode(' ', $options['q']);
            $wq = '';
            foreach ($arrQ as $key => $value) {
                $wq .= !empty($wq) ? ' OR ' : '';
                $wq .= "pds_code LIKE :q{$key}
                        OR pds_barcode LIKE :q{$key}
                        OR pds_name LIKE :q{$key} ";
                $where_arr[":q{$key}"] = "%{$value}%";
                $where_arr[":s{$key}"] = "{$value}%";
                $where_arr[":f{$key}"] = $value;
            }

            if (!empty($wq)) {
                $where_str .= !empty($where_str) ? ' AND ' : '';
                $where_str .= "($wq)";
            }
        }

        if (isset($_REQUEST['status'])) {
            $options['status'] = $_REQUEST['status'];
        }
        if (!empty($options['status'])) {
            $where_str .= !empty($where_str) ? $where_str : '';
            $where_str .= 'pds_status=:status';
            $where_arr[':status'] = $options['status'];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $where_str = !empty($where_str) ? "WHERE {$where_str}" : '';
        $orderby = $this->orderby($options['sort'], $options['dir']);
        $limit = $this->limited($options['limit'], $options['pager']);
        if (!empty($options['unlimit'])) {
            $limit = '';
        }

        $arr['lists'] = $this->buildFrag($this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr), $options);

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }
        $arr['options'] = $options;

        return $arr;
    }

    public function buildFrag($results, $options = array())
    {
        $data = array();
        foreach ($results as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $data[] = $this->convert($value, $options);
        }

        return $data;
    }

    public function get($id, $options = array())
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE p.id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id,
        ));

        return $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC), $options)
            : array();
    }

    public function convert($data, $options = array())
    {
        // $data = $this->cut($this->_cutNamefield, $data);
        if (!empty($data['pds_FilePhoto'])) {
            $data['image_url'] = "http://fantasy.co.th/fileUploads/products/{$data['pds_FilePhoto']}";
        }

        $data['permit']['del'] = false;
        // $data['total_order'] = $this->db->count('orders_item', 'itm_id=:id' , array(':id'=>$data['id']));
        // if( !empty($data['total_order']) ){
        //     $data['permit']['del'] = false;
        // }

        $data['photos'] = $this->getPhotos($data['id']);
        $data['pricing'] = $this->getPrice($data['id']);

        return $data;
    }

    public function insert(&$data)
    {
        $data['created_at'] = date('c');
        $data['updated_at'] = date('c');
        $this->db->insert($this->_objName, $data);
        $data['id'] = $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('c');
        $this->db->update($this->_objName, $data, "id={$id}");
    }

    public function delete($id)
    {
        $this->db->delete($this->_objName, "id={$id}");
    }

    //Update price on dialog
    public function updatePrice($id, $data){
        $this->db->update("products_pricing", $data, "product_id={$id}");
    }

    //Price
    public function setPrice($data)
    {
        $data['updated_at'] = date('c');

        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->update('products_pricing', $data, "id={$id}");
        } else {
            $data['created_at'] = date('c');
            $this->db->insert('products_pricing', $data);
        }
    }

    public function getPrice($id)
    {
        $sth = $this->db->prepare('SELECT id, frontend, seller, wholesales, employee, vat, website, cost FROM products_pricing WHERE product_id=:id LIMIT 1');
        $sth->execute(array(
            ':id' => $id,
        ));

        return $sth->rowCount() == 1
            ? $sth->fetch(PDO::FETCH_ASSOC)
            : array();
    }

    //CATEGORY
    public function category()
    {
        return $this->db->select("SELECT id,name_en,name_th FROM categories WHERE status='A' ORDER BY seq ASC");
    }

    //PHOTOS
    public function getPhotos($id)
    {
        $data = array();

        $results = $this->db->select('SELECT * FROM products_media_permit WHERE pds_id=:id ORDER BY seq ASC', array(':id' => $id));
        foreach ($results as $key => $value) {
            $image = $this->query('media')->get($value['media_id']);
            if (!empty($image)) {
                $data[$value['seq']]['url'] = $image['url'];
                $data[$value['seq']] = $image;
            }
        }

        return $data;
    }

    public function setPermitPhotos($data)
    {
        $this->db->insert('products_media_permit', $data);
    }

    public function delPermitPhotos($pds_id, $media_id)
    {
        $this->db->delete('products_media_permit', "pds_id={$pds_id} AND media_id={$media_id}");
    }
}
