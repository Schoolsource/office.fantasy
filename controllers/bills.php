<?php

class Bills extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listsCustomer()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        echo json_encode($this->model->query('customers')->lists());
    }

    public function index()
    {
        $this->view->setPage('title', 'VAT SALE');
        $this->view->setPage('on', 'bills');

        if ($this->format == 'json') {
            $results = $this->model->lists();
            $this->view->setData('results', $results);
            $render = 'bills/lists/json';
        } else {
            $render = 'bills/lists/display';
        }
        $this->view->render($render);
    }

    public function add()
    {
        $this->view->setPage('title', 'VAT SALE en Create');
        $this->view->setPage('on', 'bills');

        if (empty($this->me)) {
            $this->error();
        }

        $this->view->setData('products', $this->model->listsProduct());
        $this->view->render('bills/forms/create');
    }

    public function edit($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($this->me) || empty($id)) {
            $this->error();
        }

        $this->view->setPage('title', 'VAT SALE en Create');
        $this->view->setPage('on', 'bills');

        $item = $this->model->get($id, array('items' => true));
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setData('products', $this->model->listsProduct());
        $this->view->render('bills/forms/create');
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
            $form->post('bill_send_date')
                    ->post('bill_submit_date')
                    ->post('bill_term_of_payment')
                    ->post('bill_total')
                    ->post('bill_vat')
                    ->post('bill_amount');
            $form->submit();
            $postData = $form->fetch();

            if (empty($_POST['vat'])) {
                $arr['error']['vat'] = 'กรุณาระบุ VAT';
            } else {
                $postData['bill_vat_persent'] = $_POST['vat'];
            }

            if (empty($_POST['bill_cus_id'])) {
                $arr['error']['bill_customer'] = 'กรุณาเลือกลูกค้า';
            } else {
                $postData['bill_cus_id'] = $_POST['bill_cus_id'];
                $customer = $this->model->query('customers')->get($postData['bill_cus_id']);

                //SET CUSTOMER
                $postData['bill_name_store'] = $customer['name_store'];
                if (!empty($customer['address'][0])) {
                    $postData['bill_address'] = !empty($customer['address'][0]['address'])
                                                ? $customer['address'][0]['address']
                                                : '';
                    $postData['bill_road'] = !empty($customer['address'][0]['road'])
                                             ? $customer['address'][0]['road']
                                             : '';
                    $postData['bill_area'] = !empty($customer['address'][0]['area'])
                                             ? $customer['address'][0]['area']
                                             : '';
                    $postData['bill_district'] = !empty($customer['address'][0]['district'])
                                                 ? $customer['address'][0]['district']
                                                 : '';
                    $postData['bill_province'] = !empty($customer['address'][0]['province'])
                                                 ? $customer['address'][0]['province']
                                                 : '';
                    $postData['bill_post_code'] = !empty($customer['address'][0]['post_code'])
                                                  ? $customer['address'][0]['post_code']
                                                  : '';
                    $postData['bill_country'] = !empty($customer['address'][0]['country_name'])
                                                ? $customer['address'][0]['country_name']
                                                : '';
                }
            }

            //SUM PRODUCT IF SAME
            $setItems = array();
            $postItem = $_POST['item'];
            for ($i = 0; $i <= count($postItem['pro_id']); ++$i) {
                if (empty($postItem['pro_id'][$i]) || empty($postItem['qty'][$i])) {
                    continue;
                }
                $product = $this->model->query('products')->get($postItem['pro_id'][$i]);
                if (empty($product)) {
                    continue;
                }

                if (empty($setItems[$product['id']])) {
                    $setItems[$product['id']]['item_pro_code'] = $product['pds_barcode'];
                    $setItems[$product['id']]['item_pro_name'] = $product['pds_name'];
                    $setItems[$product['id']]['item_qty'] = $postItem['qty'][$i];
                    $setItems[$product['id']]['item_unit'] = $postItem['unit'][$i];
                    $setItems[$product['id']]['item_sales'] = $postItem['sales'][$i];
                    $setItems[$product['id']]['item_amount'] = $postItem['amount'][$i];
                    $setItems[$product['id']]['item_remark'] = $postItem['remark'][$i];
                } else {
                    $setItems[$product['id']]['item_qty'] += $postItem['qty'][$i];
                    $setItems[$product['id']]['item_amount'] += $postItem['amount'][$i];
                }
            }

            //BUILD REAL DATA ITEMS
            $items = array();
            $c = 0;
            foreach ($setItems as $key => $value) {
                $items[$c]['item_pro_id'] = $key;
                $items[$c]['item_pro_code'] = $value['item_pro_code'];
                $items[$c]['item_pro_name'] = $value['item_pro_name'];
                $items[$c]['item_qty'] = $value['item_qty'];
                $items[$c]['item_unit'] = $value['item_unit'];
                $items[$c]['item_sales'] = $value['item_sales'];
                $items[$c]['item_amount'] = $value['item_amount'];
                $items[$c]['item_remark'] = $value['item_remark'];
                ++$c;
            }

            if (empty($items)) {
                $arr['error']['lists'] = 'กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการ';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $postData['bill_user_id'] = $this->me['id'];
                    $this->model->insert($postData);
                    $id = $postData['id'];
                }

                if (!empty($id)) {
                    $this->model->update($id, array('bill_number' => 'IN'.sprintf('%05d', $id)));

                    $_items = array();
                    if (!empty($item['items'])) {
                        foreach ($item['items'] as $key => $value) {
                            $_items[] = $value['id'];
                        }
                    }

                    foreach ($items as $key => $value) {
                        if (!empty($_items[$key])) {
                            $value['id'] = $_items[$key];
                            unset($_items[$key]);
                        }
                        $value['item_bill_id'] = $id;
                        $this->model->setItem($value);
                    }

                    if (!empty($_items)) {
                        foreach ($_items as $key => $value) {
                            $this->model->unsetItem($value);
                        }
                    }
                }

                $arr['message'] = 'Saved !';
                $arr['url'] = URL.'bills';
            }
        } catch (Expcetion $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($this->me) || empty($id)) {
            $this->error();
        }

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->delete($id);
                $arr['message'] = 'Deleted !';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'Access Error !';
            }
        } else {
            $this->view->setData('item', $item);
            $this->view->render('bills/forms/del');
        }
    }

    public function plate()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        $this->view->render('bills/forms/print');
    }

    public function updateProvince()
    {
        $this->model->updateProvince();
    }
}
