<?php

class Tax_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $_objName = 'tax';
    private $_field = 't.*, tc.category_name';
    private $_table = 'tax t LEFT JOIN tax_categories tc ON t.tax_category_id=tc.category_id';
    private $_cutNamefield = 'tax_';

    public function insert(&$data)
    {
        $data["{$this->_cutNamefield}created"] = date('c');
        $data["{$this->_cutNamefield}updated"] = date('c');

        $this->db->insert($this->_objName, $data);
    }

    public function update($id, $data)
    {
        $data["{$this->_cutNamefield}updated"] = date('c');
        $this->db->update($this->_objName, $data, "{$this->_cutNamefield}id={$id}");
    }

    public function delete($id)
    {
        $this->db->delete($this->_objName, "{$this->_cutNamefield}id={$id}");
    }

    public function lists($options = array())
    {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            'more' => true,

            'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'created',
            'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC',
            'category' => isset($_REQUEST['category']) ? $_REQUEST['category'] : null,

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),

            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
        ), $options);

        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = '';
        $where_arr = array();

        if (isset($_REQUEST['credit'])) {
            $options['credit'] = $_REQUEST['credit'];
        }

        if (!empty($options['credit'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= "{$this->_cutNamefield}credit=:credit";
            $where_arr[':credit'] = $options['credit'];
        }

        if (isset($_REQUEST['period_start']) && isset($_REQUEST['period_end'])) {
            $options['period_start'] = $_REQUEST['period_start'];
            $options['period_end'] = $_REQUEST['period_end'];
        }
        if (!empty($options['period_start']) && !empty($options['period_end'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= '(tax_date BETWEEN :s AND :e)';
            $where_arr[':s'] = $options['period_start'];
            $where_arr[':e'] = $options['period_end'];
        }

        if (!empty($options['report'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'tax_is_report=:report';
            $where_arr[':report'] = $options['report'];
        }

        if (!empty($options['category'])) {
            $where_str .= !empty($where_str) ? ' AND ' : '';
            $where_str .= 'tax_category_id=:category';
            $where_arr[':category'] = $options['category'];
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby($this->_cutNamefield.$options['sort'], $options['dir']);
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

    public function get($id, $options = array())
    {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE {$this->_cutNamefield}id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id,
        ));

        return $sth->rowCount() == 1
            ? $this->convert($sth->fetch(PDO::FETCH_ASSOC), $options)
            : array();
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

    public function convert($data, $options = array())
    {
        $data = $this->cut($this->_cutNamefield, $data);
        $data['credit_arr'] = $this->getCredit($data['credit']);
        $data['permit']['del'] = true;

        return $data;
    }

    // Category
    public function category($id = null)
    {
        if (!empty($id)) {
            $sth = $this->db->prepare('SELECT category_id AS id, category_name AS name FROM tax_categories WHERE category_id=:id LIMIT 1');
            $sth->execute(array(
                ':id' => $id,
            ));

            $fdata = $sth->fetch(PDO::FETCH_ASSOC);
            $fdata['total'] = $this->db->count('tax', 'tax_category_id=:id', array(':id' => $id));
            $fdata['permit']['del'] = empty($fdata['total']) ? true : false;

            return $sth->rowCount() == 1
            ? $fdata
            : array();
        } else {
            return $this->db->select('SELECT category_id AS id, category_name AS name FROM tax_categories ORDER BY category_id DESC');
        }
    }

    public function insertCategory($data)
    {
        $this->db->insert('tax_categories', $data);
    }

    public function updateCategory($id, $data)
    {
        $this->db->update('tax_categories', $data, "category_id={$id}");
    }

    public function deleteCategory($id)
    {
        $this->db->delete('tax_categories', "category_id={$id}");
    }

    public function is_category($text)
    {
        return $this->db->count('tax_categories', 'category_name=:text', array(':text' => $text));
    }

    // Credit
    public function credit()
    {
        $a[] = array('id' => 1, 'name' => 'เงินสด');
        $a[] = array('id' => 2, 'name' => 'เครดิต 30 วัน');

        return $a;
    }

    public function getCredit($id = null)
    {
        $data = array();
        foreach ($this->credit() as $key => $value) {
            if ($value['id'] == $id) {
                $data = $value;
                break;
            }
        }

        return $data;
    }
}
