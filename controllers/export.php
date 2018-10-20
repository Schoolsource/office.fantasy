<?php

class Export extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->setPage('title', 'Export Products');
        $this->view->setPage('on', 'export');

        if ($this->format == 'json') {
            $results = $this->model->lists();
            $this->view->setData('results', $results);
            $render = 'export/lists/json';
        } else {
            $render = 'export/lists/display';
        }
        $this->view->render($render);
    }

    public function set($id = null)
    {
        $this->view->setPage('title', 'Stock Adjust');
        $this->view->setPage('on', 'export');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (!empty($id)) {
            $item = $this->model->get($id, array('items' => true));
            if (empty($item)) {
                $this->error();
            }

            // print_r($item);die;

            $this->view->setData('item', $item);
        }

        $this->view->setData('category', $this->model->category());
        $this->view->setData('products', $this->model->query('import')->listsProduct());
        $this->view->render('export/forms/set');
    }

    public function save()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->get($id, array('items' => true));
            if (empty($item)) {
                $this->error();
            }
        }

        //SUM PRODUCT IF SAME
        $setItems = array();
        $postItem = $_POST['item'];
        for ($i = 0; $i <= count($postItem['pro_id']); ++$i) {
            if (empty($postItem['pro_id'][$i]) || empty($postItem['qty'][$i])) {
                continue;
            }

            if (empty($setItems[$postItem['pro_id'][$i]])) {
                $setItems[$postItem['pro_id'][$i]]['item_qty'] = $postItem['qty'][$i];
                $setItems[$postItem['pro_id'][$i]]['item_unit'] = $postItem['unit'][$i];
                $setItems[$postItem['pro_id'][$i]]['item_price'] = $postItem['price'][$i];
                $setItems[$postItem['pro_id'][$i]]['item_amount'] = $postItem['amount'][$i];
                $setItems[$postItem['pro_id'][$i]]['item_remark'] = $postItem['remark'][$i];
            } else {
                $setItems[$postItem['pro_id'][$i]]['item_qty'] += $postItem['qty'][$i];
                $setItems[$postItem['pro_id'][$i]]['item_amount'] += $postItem['amount'][$i];
            }
        }

        //BUILD REAL DATA ITEMS
        $items = array();
        $c = 0;
        foreach ($setItems as $key => $value) {
            $items[$c]['item_pro_id'] = $key;
            $items[$c]['item_qty'] = $value['item_qty'];
            $items[$c]['item_unit'] = $value['item_unit'];
            $items[$c]['item_price'] = $value['item_price'];
            $items[$c]['item_amount'] = $value['item_amount'];
            $items[$c]['item_remark'] = $value['item_remark'];
            ++$c;
        }

        if (empty($items)) {
            $arr['error']['lists'] = 'กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการ';
        }

        try {
            $form = new Form();
            $form->post('exp_date')
                    ->post('exp_cate_id')->val('is_empty')
                    ->post('exp_ref')->val('is_empty');
            $form->submit();
            $postData = $form->fetch();

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $this->model->insert($postData);
                    $id = $postData['id'];

                    $this->model->update($id, array('exp_code' => 'SA'.sprintf('%04d', $id)));
                }

                if (!empty($id)) {
                    $_items = array();
                    if (!empty($item['items'])) {
                        foreach ($item['items'] as $key => $value) {
                            $_items[] = $value['id'];

                            $getProduct = $this->model->query('products')->get($value['pro_id']);

                            if (!empty($getProduct['qty'])) {
                                $upProduct = array(
                                    'pds_qty' => $getProduct['qty'] + $value['qty'],
                                );
                                $this->model->query('products')->update($getProduct['id'], $upProduct);
                            }
                        }
                    }

                    $total_amount = 0;
                    $total_qty = 0;
                    foreach ($items as $key => $value) {
                        if (!empty($_items[$key])) {
                            $value['id'] = $_items[$key];
                            unset($_items[$key]);
                        }
                        $value['item_exp_id'] = $id;
                        $this->model->setItem($value);

                        $getProduct = $this->model->query('products')->get($value['item_pro_id']);

                        $qty = $getProduct['qty'] - $value['item_qty'];
                        $this->model->query('products')->update($value['item_pro_id'], array('pds_qty' => $qty));

                        $total_amount += $value['item_amount'];
                        $total_qty += $value['item_qty'];
                    }

                    if (!empty($_items)) {
                        foreach ($_items as $key => $value) {
                            $this->model->unsetItem($value);
                        }
                    }

                    /* SET TOTAL */
                    $this->model->update($id, array(
                        'exp_total_qty' => $total_qty,
                        'exp_total_price' => $total_amount,
                    ));
                }

                $arr['message'] = 'Saved !';
                $arr['url'] = URL.'export';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $item = $this->model->get($id, array('items' => true));
        if (empty($item)) {
            $this->error();
        }
        if (!empty($_POST)) {
        } else {
            $this->view->setData('item', $item);
            $this->view->render('export/forms/del');
        }
    }

    /* category */
    public function add_category()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->render('export/forms/add_category');
    }

    public function edit_category($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->category($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->render('export/forms/add_category');
    }

    public function save_category()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->category($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('cate_name')->val('is_empty');
            $form->submit();
            $postData = $form->fetch();

            $has_name = true;
            if (!empty($item)) {
                if ($item['name'] == $postData['cate_name']) {
                    $has_name = false;
                }
            }
            if ($this->model->is_category($postData['cate_name']) && $has_name) {
                $arr['error']['cate_name'] = 'พบชื่อซ้ำในระบบ';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->updateCategory($id, $postData);
                } else {
                    $this->model->insertCategory($postData);
                }

                $arr['message'] = 'Saved !';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del_category($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->category($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->deleteCategory($id);

                $arr['message'] = 'Deleted !';
                $arr['url'] = 'refresh';
            }
        } else {
            $this->view->setData('item', $item);
            $this->view->render('export/forms/del_category');
        }
    }
}
