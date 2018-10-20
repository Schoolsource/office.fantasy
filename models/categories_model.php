<?php

class Categories_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $_objName = 'categories';
    private $_table = 'categories';
    private $_field = '*';

    public function lists($options = array())
    {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            'more' => true,

            'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'created_at',
            'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC',

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),

            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = '';
        $where_arr = array();

        if (!empty($options['q'])) {
            $arrQ = explode(' ', $options['q']);
            $wq = '';
            foreach ($arrQ as $key => $value) {
                $wq .= !empty($wq) ? ' OR ' : '';
                $wq .= "sub_code LIKE :q{$key}
                        OR name_store LIKE :q{$key}
                        OR phone LIKE :q{$key}
                        OR phone_other LIKE :q{$key}";
                $where_arr[":q{$key}"] = "%{$value}%";
                $where_arr[":s{$key}"] = "{$value}%";
                $where_arr[":f{$key}"] = $value;
            }

            if (!empty($wq)) {
                $where_str .= !empty($where_str) ? ' AND ' : '';
                $where_str .= "($wq)";
            }
        }

        if (!empty($options['not_is_sub'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'is_sub=0 OR is_sub is null';
        }

        if (!empty($options['not'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'id!=:not';
            $where_arr[':not'] = $options['not'];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby($options['sort'], $options['dir']);
        $where_str = !empty($where_str) ? "WHERE {$where_str}" : '';
        if (!empty($options['unlimit'])) {
            $limit = '';
        }

        $groupby = !empty($groupby) ? "GROUP BY {$groupby}" : '';

        $arr['lists'] = $this->buildFrag($this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$groupby} {$orderby} {$limit}", $where_arr), $options);

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
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id,
        ));

        return $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC), $options)
            : array();
    }

    public function convert($data, $options = array())
    {
        $data['permit']['del'] = true;
        $data['product_count'] = $this->db->count('products', 'pds_categories_id=:id', array(':id' => $data['id']));
        if (!empty($data['product_count'])) {
            $data['permit']['del'] = false;
        }
        if (!empty($options['show_sub'])) {
            $data['sub_categories'] = $this->listsSubCategories($data['id']);
        }
        if (!empty($data['cate_img_id'])) {
            $image = $this->query('media')->get($data['cate_img_id']);
            if (!empty($image)) {
                $data['image_url'] = $image['url'];
                $data['image_arr'] = $image;
            }
        }

        return $data;
    }

    public function insert(&$data)
    {
        $this->db->insert($this->_objName, $data);
        $data['id'] = $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $this->db->update($this->_objName, $data, "id={$id}");
    }

    public function delete($id)
    {
        $this->db->delete($this->_objName, "id={$id}");
    }

    public function is_name_th($text)
    {
        return $this->db->count($this->_objName, 'name_th=:text', array(':text' => $text));
    }

    public function is_name_en($text)
    {
        return $this->db->count($this->_objName, 'name_en=:text', array(':text' => $text));
    }

    //status
    public function status()
    {
        $a[] = array('id' => 'A', 'name' => 'Active');
        $a[] = array('id' => 'I', 'name' => 'Inactive');

        return $a;
    }

    public function getStatus($id)
    {
        $data = array();
        foreach ($this->status() as $key => $value) {
            if ($id == $value['id']) {
                $data = $value;
                break;
            }
        }

        return $data;
    }

    //SUB CATEGORIES
    public function listsSubCategories($id)
    {
        return $this->buildFrag($this->db->select("SELECT {$this->_field} FROM {$this->_table} WHERE cate_id={$id} AND is_sub=1 ORDER BY seq ASC"));
    }

    public function getFirst($options = array())
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE seq=:seq LIMIT 1");
        $sth->execute(array(
            ':seq' => 1,
        ));

        return $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC), $options)
            : array();
    }

    public function listByName()
    {
        return $this->db->select("SELECT id, name_en, name_th FROM categories WHERE status='A' ORDER BY name_en ASC");
    }
}
