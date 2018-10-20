<?php

class Me extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // print_r($this->me); die;
        $this->error();
        // header('location:'.URL.'manage/products');
    }

    public function navTrigger()
    {
        if ($this->format != 'json') {
            $this->error();
        }

        if (isset($_REQUEST['status'])) {
            Session::init();
            Session::set('isPushedLeft', $_REQUEST['status']);
        }
    }

    /* updated */
    /**/
    public function updated($avtive = '')
    {
        if (empty($_POST) || empty($this->me) || $this->format != 'json' || $avtive == '') {
            $this->error();
        }

        /**/
        /* account */
        if ($avtive == 'account') {
            try {
                $form = new Form();
                $form->post('username')->val('username');

                $form->submit();
                $dataPost = $form->fetch();

                if ($this->model->query('admins')->is_user($dataPost['username']) && $this->me['username'] != $dataPost['username']) {
                    $arr['error']['username'] = 'ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว';
                }

                // Your username must be longer than 4 characters.

                if (empty($arr['error'])) {
                    $this->model->query('admins')->update($this->me['id'], $dataPost);

                    $arr['url'] = 'refresh';
                    $arr['message'] = 'บันทึกเรียบร้อย';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
            exit;
        }
        /**/
        /* basic */
        elseif ($avtive == 'basic') {
            try {
                $form = new Form();
                $form->post('name')->val('is_empty')
                        ->post('email');

                $form->submit();
                $dataPost = $form->fetch();

                if (empty($arr['error'])) {
                    $this->model->query('admins')->update($this->me['id'], $dataPost);

                    $arr['url'] = 'refresh';
                    $arr['message'] = 'บันทึกเรียบร้อย';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
            exit;
        }

        /**/
        /* password */
        if ($avtive == 'password') {
            $data = $_POST;
            $arr = array();
            if (!$this->model->query('admins')->loginLaravel($this->me['username'], $data['password_old'])) {
                $arr['error']['password_old'] = 'รหัสผ่านไม่ถูกต้อง';
            } elseif (strlen($data['password_new']) < 4) {
                $arr['error']['password_new'] = 'รหัสผ่านสั้นเกินไป อย่างน้อย 4 ตัวอักษรขึ้นไป';
            } elseif ($data['password_new'] == $data['password_old']) {
                $arr['error']['password_new'] = 'รหัสผ่านต้องต่างจากรหัสผ่านเก่า';
            } elseif ($data['password_new'] != $data['password_confirm']) {
                $arr['error']['password_confirm'] = 'คุณต้องใส่รหัสผ่านที่เหมือนกันสองครั้งเพื่อเป็นการยืนยัน';
            }

            if (!empty($arr['error'])) {
                $this->view->error = $arr['error'];
            } else {
                $this->model->query('admins')->update($this->me['id'], array(
                    'password' => $this->fn->q('password')->PasswordHash($_POST['password_new']),
                ));

                $arr['url'] = 'refresh';
                $arr['message'] = 'Thanks, your settings have been saved.';
            }

            echo json_encode($arr);
            exit;
        }

        $this->error();
    }

    /*public function change_password() {

        if( empty($this->me) || $this->format!='json' ) $this->error();

        if( !empty($_POST) ){
            try {
                $form = new Form();
                $form   ->post('password_old')->val('password')
                        ->post('password_new')->val('password')
                        ->post('password_confirm')->val('password');

                $form->submit();
                $dataPost = $form->fetch();

                $old_pass = Hash::create('sha256', $dataPost['password_old'], HASH_PASSWORD_KEY );
                if( !$this->model->query('employees')->is_checkpass($this->me['id'], $old_pass) ) {
                    $arr['error']['password_old'] = 'รหัสผ่านเดิมผิด !';
                }

                if( $dataPost['password_new']!=$dataPost['password_confirm'] ){
                    $arr['error']['password_confirm'] = 'รหัสผ่านไม่ตรงกัน';
                }

                if( empty($arr['error']) ){

                    // update
                    $this->model->query('employees')->update($this->me['id'], array(
                        'emp_password' => Hash::create('sha256', $dataPost['password_new'], HASH_PASSWORD_KEY )
                    ));

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        }
        else{
            $this->view->setData('item', $this->me );
            $this->view->render("Themes/crm/forms/my/change_password");
        }
    }*/
    public function change_password()
    {
        if (empty($this->me)) {
            $this->error();
        }

        if (!empty($_POST)) {
            try {
                $form = new Form();
                $form->post('new_password')->val('is_empty')
                        ->post('new_password2')->val('is_empty');
                $form->submit();
                $postData = $form->fetch();

                if (strlen($postData['new_password']) < 4) {
                    $arr['error']['new_password'] = 'กรุณากรอกรหัสผ่านตั้งแต่ 4 ตัวอักษรขึ้นไป';
                }
                if ($postData['new_password'] != $postData['new_password2']) {
                    $arr['error']['new_password2'] = 'กรุณากรอกรหัสผ่านให้ตรงกัน';
                }

                if (empty($arr['error'])) {
                    $password = $this->fn->q('password')->PasswordHash($postData['new_password']);

                    $this->model->query('sales')->update($this->me['id'], array('password' => $password));

                    $arr['message'] = 'สามารถใช้รหัสผ่านใหม่ในการ Login ครั้งต่อไป';
                    $arr['url'] = 'refresh';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }
            echo json_encode($arr);
        } else {
            $this->view->render('settings/password');
        }
    }
}
