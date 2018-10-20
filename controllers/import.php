<?php

class Import extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listsSupplier()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        echo json_encode($this->model->query('suppliers')->lists());
    }

    public function index()
    {
        $this->view->setPage('title', 'Import Product');
        $this->view->setPage('on', 'import');

        if ($this->format == 'json') {
            $results = $this->model->lists();
            $this->view->setData('results', $results);
            $render = 'import/lists/json';
        } else {
            $render = 'import/lists/display';
        }
        $this->view->render($render);
    }

    public function set($id = null)
    {
        $this->view->setPage('title', 'Set Import Product');
        $this->view->setPage('on', 'import');

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($this->me)) {
            $this->error();
        }

        if (!empty($id)) {
            $item = $this->model->get($id, array('items' => true));
            // print_r($item);die;
            if (empty($item)) {
                $this->error();
            }

            $this->view->setData('item', $item);
        }

        $this->view->setData('products', $this->model->listsProduct());
        $this->view->render('import/forms/set');
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

        try {
            $form = new Form();
            $form->post('imp_date')
                    ->post('imp_code')->val('is_empty')
                    ->post('imp_total_price');
            $form->submit();
            $postData = $form->fetch();

            if (empty($_POST['imp_sup_id'])) {
                $arr['error']['imp_supplier'] = 'กรุณาเลือก Supplier';
            } else {
                $postData['imp_sup_id'] = $_POST['imp_sup_id'];
            }

            //SUM PRODUCT IF SAME
            $setItems = array();
            $postItem = $_POST['item'];
            for ($i = 0; $i <= count($postItem['pro_id']); ++$i) {
                if (empty($postItem['pro_id'][$i]) || empty($postItem['qty'][$i])) {
                    continue;
                }
                $product = $this->model->getProduct($postItem['pro_id'][$i]);
                if (empty($product)) {
                    continue;
                }

                if (empty($setItems[$product['id']])) {
                    $setItems[$product['id']]['item_qty'] = $postItem['qty'][$i];
                    $setItems[$product['id']]['item_unit'] = $postItem['unit'][$i];
                    $setItems[$product['id']]['item_price'] = $postItem['price'][$i];
                    $setItems[$product['id']]['item_amount'] = $postItem['amount'][$i];
                } else {
                    $setItems[$product['id']]['item_qty'] += $postItem['qty'][$i];
                    $setItems[$product['id']]['item_amount'] += $postItem['amount'][$i];
                }
            }

            //BUILD REAL DATA ITEMS
            $items = array();
            $c = 0;
            foreach ($setItems as $key => $value) {
                $items[$c]['item_product_id'] = $key;
                $items[$c]['item_qty'] = $value['item_qty'];
                $items[$c]['item_unit'] = $value['item_unit'];
                $items[$c]['item_price'] = $value['item_price'];
                $items[$c]['item_amount'] = $value['item_amount'];
                ++$c;
            }

            $postData['imp_total_qty'] = $c;

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $this->model->insert($postData);
                    $id = $postData['id'];

                    $this->model->update($id, array('imp_number' => 'IMP'.sprintf('%03d', $id)));
                }

                if (!empty($id)) {
                    $_items = array();
                    if (!empty($item['items'])) {
                        foreach ($item['items'] as $key => $value) {
                            $_items[] = $value['id'];

                            $getProduct = $this->model->getProduct($value['product_id']);

                            if (!empty($getProduct['qty'])) {
                                $upProduct = array(
                                    'pds_qty' => $getProduct['qty'] - $value['qty'],
                                );
                                $this->model->query('products')->update($getProduct['id'], $upProduct);
                            }
                        }
                    }

                    foreach ($items as $key => $value) {
                        if (!empty($_items[$key])) {
                            $value['id'] = $_items[$key];
                            $value['item_updated'] = date('c');
                            unset($_items[$key]);
                        }
                        $value['item_imp_id'] = $id;
                        $this->model->setItem($value);

                        $getProduct = $this->model->getProduct($value['item_product_id']);

                        $qty = $getProduct['qty'] + $value['item_qty'];
                        $this->model->query('products')->update($value['item_product_id'], array('pds_qty' => $qty));
                    }

                    if (!empty($_items)) {
                        foreach ($_items as $key => $value) {
                            $this->model->unsetItem($value);
                        }
                    }
                }

                $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
                $arr['url'] = URL.'import';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }
}
