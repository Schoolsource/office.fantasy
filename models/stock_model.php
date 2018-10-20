<?php

class Stock_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $_table = 'products p LEFT JOIN categories c ON p.pds_categories_id=c.id';
    private $_field = '
          p.id
        , p.pds_name

        , c.name_en AS category_name_en
        , c.name_th AS category_name
    ';
    private $_prefixField = 'pds_';

    public function find($options = array())
    {
        $options = array_merge(array(
            // 'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            // 'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            // 'more' => true,

            // 'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'created_at',
            // 'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC',

            // 'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),

            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
            'period_start' => isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : null,
            'period_end' => isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : null,
            'category' => isset($_REQUEST['category']) ? $_REQUEST['category'] : null,
            'pds_has_vat' => isset($_REQUEST['pds_has_vat']) ? $_REQUEST['pds_has_vat'] : null,
        ), $options);

        $producs = array();

        $outputCondition = '';
        $outputParams = array();

        $receiveCondition = '';
        $receiveParams = array();

        $adjustCondition = '';
        $adjustParams = array();

        if (!empty($options['period_start']) && !empty($options['period_end'])) {
            $outputCondition .= !empty($outputCondition) ? ' AND ' : '';
            $outputCondition .= '(item.created_at BETWEEN :s AND :e)';
            $outputParams[':s'] = $options['period_start'];
            $outputParams[':e'] = $options['period_end'];

            $receiveCondition .= !empty($receiveCondition) ? ' AND ' : '';
            $receiveCondition .= '(item.item_created BETWEEN :s AND :e)';
            $receiveParams[':s'] = $options['period_start'];
            $receiveParams[':e'] = $options['period_end'];

            $adjustCondition .= !empty($adjustCondition) ? ' AND ' : '';
            $adjustCondition .= '(item.item_created BETWEEN :s AND :e)';
            $adjustParams[':s'] = $options['period_start'];
            $adjustParams[':e'] = $options['period_end'];
        }

        if (!empty($options['category'])) {
            $outputCondition .= !empty($outputCondition) ? ' AND ' : '';
            $outputCondition .= 'p.pds_categories_id=:category';
            $outputParams[':category'] = $options['category'];

            $receiveCondition .= !empty($receiveCondition) ? ' AND ' : '';
            $receiveCondition .= 'p.pds_categories_id=:category';
            $receiveParams[':category'] = $options['category'];

            $adjustCondition .= !empty($adjustCondition) ? ' AND ' : '';
            $adjustCondition .= 'p.pds_categories_id=:category';
            $adjustParams[':category'] = $options['category'];
        }

        if (!empty($options['pds_has_vat'])) {
            $outputCondition .= !empty($outputCondition) ? ' AND ' : '';
            $outputCondition .= 'p.pds_has_vat=1';

            $receiveCondition .= !empty($receiveCondition) ? ' AND ' : '';
            $receiveCondition .= 'p.pds_has_vat=1';

            $adjustCondition .= !empty($adjustCondition) ? ' AND ' : '';
            $adjustCondition .= 'p.pds_has_vat=1';
        }

        $outputWhere = !empty($outputCondition) ? "WHERE {$outputCondition}" : '';
        $output = $this->db->select("SELECT

              item.itm_id as id
            , SUM(item.itm_qty) as output

            , pds_name as name
            , pds_has_vat as vat

            , c.name_en AS category_name_en, c.name_th AS category_name

            FROM orders_item item INNER JOIN (
                products p LEFT JOIN categories c ON p.pds_categories_id=c.id
            ) ON item.itm_id=p.id

            {$outputWhere} GROUP BY item.itm_id", $outputParams);

        foreach ($output as $key => $value) {
            $producs[$value['id']] = $value;
            $producs[$value['id']]['receive'] = 0;
            $producs[$value['id']]['adjust'] = 0;
        }

        // echo "SELECT

        //     item.itm_id as id
        //   , SUM(item.itm_qty) as output

        //   , pds_name as name
        //   , pds_has_vat as vat

        //   , c.name_en AS category_name_en, c.name_th AS category_name

        //   FROM orders_item item INNER JOIN (
        //       products p LEFT JOIN categories c ON p.pds_categories_id=c.id
        //   ) ON item.itm_id=p.id

        //   {$outputWhere} GROUP BY item.itm_id";

        // print_r($outputParams);

        // print_r($producs);
        // exit;

        // Product Receive
        $receiveWhere = !empty($receiveCondition) ? "WHERE {$receiveCondition}" : '';
        $receive = $this->db->select("SELECT

              item.item_product_id as id
            , SUM(item.item_qty) as receive

            , pds_name as name
            , pds_has_vat as vat
            , c.name_en AS category_name_en, c.name_th AS category_name

            FROM import_products_item item INNER JOIN (
                products p LEFT JOIN categories c ON p.pds_categories_id=c.id
            ) ON item.item_product_id=p.id

        {$receiveWhere}
        GROUP BY item.item_product_id", $receiveParams);
        foreach ($receive as $key => $value) {
            // if (empty($producs[$value['id']])) {
            //     $producs[$value['id']] = $value;
            // }

            $producs[$value['id']]['receive'] = $value['receive'];
        }

        // Stock Adjust
        $adjustWhere = !empty($adjustCondition) ? "WHERE {$adjustCondition}" : '';
        $adjust = $this->db->select("SELECT

              item.item_pro_id as id
            , SUM(item.item_qty) as adjust

            , pds_name as name
            , pds_has_vat as vat
            , c.name_en AS category_name_en, c.name_th AS category_name

            FROM export_products_item item INNER JOIN (
                products p LEFT JOIN categories c ON p.pds_categories_id=c.id
            ) ON item.item_pro_id=p.id

        {$adjustWhere}
        GROUP BY item.item_pro_id", $adjustParams);
        foreach ($adjust as $key => $value) {
            if (empty($producs[$value['id']])) {
                $producs[$value['id']] = $value;
            }

            $producs[$value['id']]['adjust'] = $value['adjust'];
        }

        // echo '<pre>';
        // print_r($options);
        // exit;

        return array(
            'total' => count($producs),
            'options' => $options,
            'items' => $producs,
        );
    }

    /* -- convert data -- */
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

    public function convert($data, $options)
    {
        // $data = $this->__cutPrefixField($this->_prefixField, $data);
        return $data;
    }
}
