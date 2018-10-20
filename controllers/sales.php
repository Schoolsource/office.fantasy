<?php

class Sales extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = null)
    {
        $this->view->setPage('on', 'sales');
        $this->view->setPage('title', 'จัดการผู้ขาย');

        if (!empty($id)) {
            $item = $this->model->get($id);
            if (empty($item)) {
                $this->error();
            }

            if ($this->format == 'json') {
                $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'orders';
                $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : date('Y-m-01');
                $end = isset($_REQUEST['end']) ? $_REQUEST['end'] : date('Y-m-t');

                if ($type == 'orders') {
                    $options = array(
                        'sale' => $item['sale_code'],
                        'period_start' => $start,
                        'period_end' => $end,
                        'unlimit' => true,
                    );
                    $orders = $this->model->query('orders')->lists($options);
                    $this->view->setData('orders', $orders);
                    $render = 'sales/profile/json/orders';
                } elseif ($type == 'payment') {
                    $options = array(
                        'sale' => $id,
                        'start' => $start,
                        'end' => $end,
                    );
                    $payment = $this->model->listsPayment($options);
                    $this->view->setData('results', $payment);
                    $render = 'sales/profile/json/payment';
                } else {
                    $this->error();
                }
            } else {
                $this->view->setData('item', $item);
                $render = 'sales/profile/display';
            }
        } else {
            if ($this->format == 'json') {
                $this->view->setData('results', $this->model->lists());
                $render = 'sales/lists/json';
            } else {
                $this->view->setData('status', $this->model->status());
                $render = 'sales/lists/display';
            }
        }
        $this->view->render($render);
    }

    public function add()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setData('region', $this->model->region());
        $this->view->setData('status', $this->model->status());
        $this->view->setData('department', $this->model->department());
        $this->view->setPage('path', 'Themes/manage/forms/sales');
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

        $this->view->setData('region', $this->model->region());
        $this->view->setData('status', $this->model->status());
        $this->view->setData('department', $this->model->department());
        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/sales');
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
            $form->post('sale_code')->val('is_empty')
                    ->post('sale_name')->val('is_empty')
                    ->post('sale_fullname')
                    ->post('username')->val('is_empty')
                    ->post('region')
                    ->post('status')->val('is_empty')
                    ->post('department');
            $form->submit();
            $postData = $form->fetch();

            $has_username = true;
            if (!empty($item)) {
                if ($item['username'] == $postData['username']) {
                    $has_username = false;
                }
            }
            if ($this->model->is_username($postData['username']) && $has_username) {
                $arr['error']['username'] = 'ตรวจพบ Username นี้ในระบบ';
            }

            if (empty($item)) {
                if (empty($_POST['password'])) {
                    $arr['error']['password'] = 'ช่องนี้เว้นว่างไว้ไม่ได้';
                } elseif (strlen($_POST['password']) < 4) {
                    $arr['error']['password'] = 'กรุณากรอกรหัสผ่านให้มากกว่า 4 ตัวอักศร';
                } else {
                    $postData['password'] = $this->fn->q('password')->PasswordHash($_POST['password']);
                }
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $this->model->insert($postData);
                }

                $arr['message'] = 'บันทึกเรียบร้อย';
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
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/sales');
            $this->view->render('del');
        }
    }

    public function sort()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : '';
        if (empty($ids) || empty($this->me)) {
            $this->error();
        }

        $seq = 0;
        foreach ($ids as $id) {
            ++$seq;
            $this->model->update($id, array('seq' => $seq));
        }

        $arr['message'] = 'บันทึกเรียบร้อย';
    }

    //ฟักชั่นเปลี่ยนรหัสผ่าน
    public function change_password($id = null)
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
            if (strlen($_POST['password_1']) < 4) {
                $arr['error']['password_1'] = 'รหัสผ่านต้องยาวกว่า 4 ตัวอักศร';
            }
            if ($_POST['password_1'] != $_POST['password_2']) {
                $arr['error']['password_2'] = 'รหัสผ่านไม่ตรงกัน';
            }

            if (empty($arr['error'])) {
                $password = $this->fn->q('password')->PasswordHash($_POST['password_1']);
                $this->model->update($id, array('password_1' => $password));
                $arr['message'] = 'บันทึกรหัสผ่านใหม่เรียบร้อย';
                $arr['url'] = 'refresh';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/sales');
            $this->view->render('password');
        }
    }
}
