<?php

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->setPage('on', 'users');
        $this->view->setPage('title', 'จัดการผู้ใช้งาน');

        if (!empty($id)) {
            $item = $this->model->get($id, array('check' => true));
            if (empty($item)) {
                $this->error();
            }

            $events = $this->model->query('events')->lists(array('obj_id' => $id, 'obj_type' => 'users'));

            $this->view->setData('events', $events);
            $this->view->setData('item', $item);
            $render = 'users/profile/display';
        } else {
            if ($this->format == 'json') {
                $this->view->setData('results', $this->model->lists());
                $render = 'users/lists/json';
            } else {
                $render = 'users/lists/display';
            }
        }

        $this->view->render($render);
    }

    public function add()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        // $this->view->setData('region', $this->model->region());
        // $this->view->setData('status', $this->model->status());
        $this->view->setPage('path', 'Themes/manage/forms/users');
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

        // $this->view->setData('region', $this->model->region());
        // $this->view->setData('status', $this->model->status());
        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/users');
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
            $form->post('name')->val('is_empty')
                        ->post('email')->val('is_empty');
            $form->submit();
            $postData = $form->fetch();

            //ตรวจสอบชื่อซ้ำกับฐานข้อมูล
            $has_email = true;
            if (!empty($item)) {
                if ($item['email'] == $postData['email']) {
                    $has_email = false;
                }
            }
            if ($this->model->is_email($postData['email']) && $has_email) {
                $arr['error']['email'] = 'ตรวจพบ email นี้ในระบบ';
            }

            //ตรวจสอบกรอกข้อมูลว่าง
            if (empty($item)) {
                if (empty($_POST['password'])) {
                    $arr['error']['password'] = 'ช่องนี้เว้นว่างไว้ไม่ได้';
                } elseif (strlen($_POST['password']) < 4) {
                    //ดักความยาวรหัสผ่าน
                    $arr['error']['password'] = 'กรุณากรอกรหัสผ่านให้มากกว่า 4 ตัวอักศร';
                } else {
                    $postData['password'] = $this->fn->q('password')->encryptPasswordLaravel($_POST['password']);
                }
            }

            // //รับข้อมูล passrowd
            // if (empty($item)){
            //   $postData['password'] = $this->fn->q('password')->encryptPasswordLaravel($_POST['password']);
            // }

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

    //ฟักซ์ชั่นลบข้อมูล
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
            $this->view->setPage('path', 'Themes/manage/forms/users');
            $this->view->render('del');
        }
    }

    //ฟักซ์ชั่นเรียงข้อมูล
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
                $password = $this->fn->q('password')->encryptPasswordLaravel($_POST['password_1']);
                $this->model->update($id, array('password_1' => $password));
                $arr['message'] = 'บันทึกรหัสผ่านใหม่เรียบร้อย';
                $arr['url'] = 'refresh';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/users');
            $this->view->render('password');
        }
    }
}
