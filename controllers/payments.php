<?php

class Payments extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = null)
    {
        $this->view->setPage('on', 'payments');
        $this->view->setPage('title', 'รายการเก็บเงิน');

        $this->view->setData('projectList', $this->model->query('orders')->projects());

        if (!empty($id)) {
            $options = array(
                'items' => true,
                'payment' => true,
            );
            $item = $this->model->query('orders')->get($id, $options);
            if (empty($item)) {
                $this->error();
            }

            $options['customer'] = $item['customer_id'];
            $options['cut'] = $item['id'];
            $orders = $this->model->query('orders')->lists($options);

            $events = $this->model->query('events')->lists(array('obj_type' => 'orders', 'obj_id' => $id));

            $this->view->setData('events', $events);
            $this->view->setData('item', $item);
            $this->view->setData('orders', $orders);
            $render = 'payments/profile/display';
        } else {
            // echo '<pre>';
            // print_r($this->model->query('orders')->lists(array('payment' => true)));
            // exit;

            if ($this->format == 'json') {
                $this->view->setData('results', $this->model->query('orders')->lists(array('payment' => true)));
                $render = 'payments/lists/json';
            } else {
                $this->view->setData('sales', $this->model->sales());
                $this->view->setData('term_of_payment', $this->model->query('orders')->term_of_payment());
                $render = 'payments/lists/display';
            }
        }

        $this->view->render($render);
    }

    public function listsPayments($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->query('orders')->get($id);
        if (empty($item)) {
            $this->error();
        }

        $payment = $this->model->query('orders')->listsPayment($id);

        $this->view->setData('item', $item);
        $this->view->setData('payment', $payment);
        $this->view->setPage('path', 'Themes/manage/forms/payments');
        $this->view->render('lists');
    }

    public function showPicture($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        $order = $this->model->query('orders')->get($item['order_id']);
        if (empty($order)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setData('order', $order);
        $this->view->setPage('path', 'Themes/manage/forms/payments');
        $this->view->render('picture');
    }

    public function add($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $order = $this->model->query('orders')->get($id, array('items' => true));
        if (empty($order)) {
            $this->error();
        }

        $this->view->setData('type', $this->model->type());
        $this->view->setData('account', $this->model->account());
        $this->view->setData('bank', $this->model->bank());
        $this->view->setData('order', $order);
        $this->view->setData('sales', $this->model->getSaleCode($order['sale_code']));
        $this->view->setPage('path', 'Themes/manage/forms/payments');
        $this->view->render('add');
    }

    public function edit($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        $order = $this->model->query('orders')->get($item['order_id'], array('items' => true));
        if (empty($order)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setData('type', $this->model->type());
        $this->view->setData('account', $this->model->account());
        $this->view->setData('bank', $this->model->bank());
        $this->view->setData('order', $order);
        $this->view->setData('sales', $this->model->getSaleCode($order['sale_code']));
        $this->view->setPage('path', 'Themes/manage/forms/payments');
        $this->view->render('add');
    }

    public function save()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->get($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('pay_date')
                    ->post('pay_type_id')->val('is_empty')
                    ->post('pay_account_id')
                    ->post('pay_check_number')
                    ->post('pay_check_bank')
                    ->post('pay_check_date')
                    ->post('bank_date')
                    ->post('pay_amount')->val('is_empty')
                    ->post('pay_note')
                    ->post('pay_sale_id')
                    ->post('pay_order_id')
                    ->post('pay_point');

            $form->submit();
            $postData = $form->fetch();

            //GET ORDER FOR CALCULATE
            $order = $this->model->query('orders')->get($_POST['pay_order_id'], array('items' => true, 'payment' => true));
            $has_check = true;
            if (!empty($item)) {
                if ($postData['pay_amount'] == $item['amount']) {
                    $has_check = false;
                }
            }
            if (($postData['pay_amount'] > $order['balance']) && $has_check) {
                $arr['error']['pay_amount'] = 'ไม่สามารถกรอกราคาเกินจากยอดคงค้างได้';
            }

            //CHECK TYPE FOR CONDITION
            $type = $this->model->getType($postData['pay_type_id']);
            if (!empty($type['is_bank']) || !empty($type['is_check'])) {
                if (empty($postData['pay_account_id'])) {
                    $arr['error']['pay_account_id'] = 'กรุณาเลือกบัญชีธนาคาร';
                }
            }

            //CHECK NUMBER
            if (!empty($type['is_check'])) {
                if (empty($postData['pay_check_date'])) {
                    $arr['error']['pay_check_date'] = 'กรุณากรอกวันที่เช็ค';
                }
                if (empty($postData['pay_check_bank'])) {
                    $arr['error']['pay_check_bank'] = 'กรุณาเลือกธนาคารเช็ค';
                }
                if (empty($postData['pay_check_number'])) {
                    $arr['error']['pay_check_number'] = 'กรุณากรอกเลขที่เช็ค';
                }
            }

            //GET FOR BANK ID
            if (!empty($postData['pay_account_id'])) {
                $account = $this->model->getAccount($postData['pay_account_id']);
                $postData['pay_bank_id'] = $account['bank_id'];
            }

            //SET TIME
            $hour = $_POST['time']['hour'];
            $min = $_POST['time']['min'];
            $postData['pay_time'] = date('H:i', strtotime("{$hour}:{$min}"));

            //SET COMISSION
            $postData['pay_comission_amount'] = isset($_POST['pay_comission_amount'])
                                              ? $_POST['pay_comission_amount']
                                              : null;
            if (!empty($postData['pay_comission_amount'])) {
                if (!is_numeric($postData['pay_comission_amount'])) {
                    $arr['error']['pay_comission_amount'] = 'กรุณากรอกเป็นตัวเลขเท่านั้น';
                }
                /* if( $postData['pay_comission_amount'] > $order['total_comission'] ){
                    $arr['error']['pay_comission_amount'] = 'ไม่สามารถกรอกค่าคอมมิชชั่นเกิน '.$order['total_comission'].' ได้';
                } */
            }

            //SET ORDER
            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $postData['pay_user_id'] = $this->me['id'];
                    $this->model->insert($postData);
                    $id = $postData['id'];
                }

                if (!empty($id) && !empty($_FILES['pay_image_id'])) {
                    $userfile = isset($_FILES['pay_image_id']) ? $_FILES['pay_image_id'] : null;
                    $_type = isset($_FILES['type']) ? $_FILES['type'] : null;

                    $options = array(
                        'album_obj_type' => isset($_REQUEST['obj_type']) ? $_REQUEST['obj_type'] : 'public',
                        'album_obj_id' => isset($_REQUEST['obj_id']) ? $_REQUEST['obj_id'] : 1,
                    );
                    if (isset($_REQUEST['album_name'])) {
                        $options['album_name'] = $_REQUEST['album_name'];
                    }
                    $album = $this->model->query('media')->searchAlbum($options);

                    if (empty($album)) {
                        $this->model->query('media')->setAlbum($options);
                        $album = $options;
                    }

                    $media = array(
                        'media_album_id' => $album['album_id'],
                        'media_type' => isset($_REQUEST['media_type']) ? $_REQUEST['media_type'] : strtolower(substr(strrchr($userfile['name'], '.'), 1)),
                    );

                    $options = array(
                        'folder' => $album['album_id'],
                    );

                    if (!isset($media['media_emp_id'])) {
                        $media['media_emp_id'] = $this->me['id'];
                    }

                    $this->model->query('media')->set($userfile, $media, $options);

                    if (empty($media['error'])) {
                        $media = $this->model->query('media')->convert($media);
                    }

                    if (!empty($item['image_id'])) {
                        $this->model->query('media')->del($item['image_id']);
                    }

                    $this->model->update($id, array('pay_image_id' => $media['id']));
                }

                //SET POINT FOR CUSTOMER
                $customer = $this->model->query('customers')->get($order['customer_id']);
                $point = $customer['point'];
                if (!empty($item)) {
                    $point -= $item['point'];
                }
                $this->model->query('customers')->update($customer['id'], array(
                    'point' => $point + $postData['pay_point'],
                ));

                $arr['message'] = 'บันทึกการจ่ายเงินเรียบร้อยแล้ว';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }

    public function del($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->delete($id);
                $this->model->query('media')->del($item['image_id']);

                //SET POINT
                $customer = $this->model->query('customers')->get($order['customer_id']);
                $this->model->query('customers')->update($order['customer_id'], array(
                    'point' => $customer['point'] - $item['point'],
                ));

                $arr['message'] = 'ลบข้อมูลการชำระเงินเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }

            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/payments');
            $this->view->render('del');
        }
    }

    //Bank
    public function add_bank()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setPage('path', 'Themes/manage/forms/payments/bank');
        $this->view->render('add');
    }

    public function edit_bank($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getBank($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/payments/bank');
        $this->view->render('add');
    }

    public function save_bank()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->getBank($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('bank_name')->val('is_empty');

            $form->submit();
            $postData = $form->fetch();

            $has_name = true;
            if (!empty($item)) {
                if ($item['name'] == $postData['bank_name']) {
                    $has_name = false;
                }
            }
            if ($this->model->is_bank($postData['bank_name']) && $has_name) {
                $arr['error']['bank_name'] = 'มีธนาคารนี้อยู่ในระบบแล้ว';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->updateBank($id, $postData);
                } else {
                    $this->model->insertBank($postData);
                }

                $arr['message'] = 'บันทึกเรียบร้อย';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del_bank($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getBank($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->deleteBank($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/payments/bank');
            $this->view->render('del');
        }
    }

    //Account Bank
    public function add_account()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setData('bank', $this->model->bank());
        $this->view->setPage('path', 'Themes/manage/forms/payments/account');
        $this->view->render('add');
    }

    public function edit_account($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getAccount($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setData('bank', $this->model->bank());
        $this->view->setPage('path', 'Themes/manage/forms/payments/account');
        $this->view->render('add');
    }

    public function save_account()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->getAccount($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('account_bank_id')->val('is_empty')
                    ->post('account_number')->val('is_empty')
                    ->post('account_name')->val('is_empty')
                    ->post('account_branch');

            $form->submit();
            $postData = $form->fetch();

            $has_number = true;
            if (!empty($item)) {
                if ($item['number'] == $postData['account_number']) {
                    $has_number = false;
                }
            }
            if ($this->model->is_bank($postData['account_number']) && $has_number) {
                $arr['error']['bank_number'] = 'มีบัญชีธนาคารนี้อยู่ในระบบแล้ว';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->updateAccount($id, $postData);
                } else {
                    $this->model->insertAccount($postData);
                }

                $arr['message'] = 'บันทึกเรียบร้อย';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del_account($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getAccount($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->deleteAccount($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/payments/account');
            $this->view->render('del');
        }
    }

    //Type
    public function add_type()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setPage('path', 'Themes/manage/forms/payments/type');
        $this->view->render('add');
    }

    public function edit_type($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getType($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/payments/type');
        $this->view->render('add');
    }

    public function save_type()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->getType($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('type_name')->val('is_empty');

            $form->submit();
            $postData = $form->fetch();

            if (empty($_POST['type_is'])) {
                $arr['error']['type_is'] = 'กรุณาเลือกประเภทการชำระเงิน';
            }

            $has_name = true;
            if (!empty($item)) {
                if ($item['name'] == $postData['type_name']) {
                    $has_name = false;
                }
            }
            if ($this->model->is_type($postData['type_name']) && $has_name) {
                $arr['error'] = 'ตรวจพบเลขบัญชีนี้ในระบบ';
            }

            if ($_POST['type_is'] == 'cash') {
                $postData['type_is_cash'] = 1;
            }

            if ($_POST['type_is'] == 'bank') {
                $postData['type_is_bank'] = 1;
            }

            if ($_POST['type_is'] == 'check') {
                $postData['type_is_check'] = 1;
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->updateType($id, $postData);
                } else {
                    $this->model->insertType($postData);
                }

                $arr['message'] = 'บันทึกเรียบร้อย';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del_type($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getType($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->deleteBank($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/payments/type');
            $this->view->render('del');
        }
    }

    public function lists()
    {
        if (empty($this->me)) {
            $this->error();
        }

        $this->view->setPage('on', 'lists');
        $this->view->setPage('title', 'รายการรับเงิน');

        // print_r($this->model->lists( array('unlimit'=>1) )); die;

        if ($this->format == 'json') {
            $this->view->setData('results', $this->model->lists(array('unlimit' => 1)));
            $render = 'paylists/json';
        } else {
            $this->view->setData('type', $this->model->type());
            $this->view->setData('account', $this->model->account());
            $render = 'paylists/display';
        }

        $this->view->render($render);
    }

    /* public function cash(){
        if( empty($this->me) ) $this->error();

        $type = $this->model->getType(1);
        if( empty($type) ) $this->error();

        $this->view->setPage('on', 'lists1');
        $this->view->setPage('title', 'รายการรับเงิน-'.$type['name']);

        if( $this->format=='json' ){
            $this->view->setData('results', $this->model->lists( array('type'=>1) ));
            $render = 'paylists/cash/json';
        }
        else{
            // $this->view->setData('type', $this->model->type());
            $this->view->setData('bank', $this->model->bank());
            $render = 'paylists/cash/display';
        }

        $this->view->setData('type', $type);
        $this->view->render( $render );
    }
    public function bank(){
        if( empty($this->me) ) $this->error();

        $type = $this->model->getType(2);
        if( empty($type) ) $this->error();

        $this->view->setPage('on', 'lists2');
        $this->view->setPage('title', 'รายการรับเงิน-'.$type['name']);

        if( $this->format=='json' ){
            $this->view->setData('results', $this->model->lists( array('type'=>2) ));
            $render = 'paylists/bank/json';
        }
        else{
            // $this->view->setData('type', $this->model->type());
            $this->view->setData('bank', $this->model->bank());
            $render = 'paylists/bank/display';
        }

        $this->view->setData('type', $type);
        $this->view->render( $render );
    }
    public function check(){
        if( empty($this->me) ) $this->error();

        $type = $this->model->getType(3);
        if( empty($type) ) $this->error();

        $this->view->setPage('on', 'lists3');
        $this->view->setPage('title', 'รายการรับเงิน-'.$type['name']);

        if( $this->format=='json' ){
            $this->view->setData('results', $this->model->lists( array('type'=>3) ));
            $render = 'paylists/check/json';
        }
        else{
            // $this->view->setData('type', $this->model->type());
            $this->view->setData('bank', $this->model->bank());
            $render = 'paylists/check/display';
        }

        $this->view->setData('type', $type);
        $this->view->render( $render );
    } */

    //FUNCTION FOR JSON
    public function get_type($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        echo json_encode($this->model->getType($id));
    }

    public function fakeOrder()
    {
        for ($i = 16; $i <= 41; ++$i) {
            $arr = array(
                'create_user_id' => 1,
                'create_user_type' => 'Sale',
                'ord_code' => 'ORNC1711000'.$i,
                'ord_customer_id' => '2248',
                'ord_sale_code' => '007',
                'ord_dateCreate' => date('Y-m-d 00:00:00'),
                'ord_type_commission' => 'sales',
                'user_name' => 'ร้าน ไอรินทรื',
                'user_code' => '600808',
                'term_of_payment' => 2,
                'ord_status' => 'A',
                'created_at' => date('c'),
                'updated_at' => date('c'),
            );
            $this->model->query('orders')->insert($arr);
        }
        echo 'Success !';
    }
}
