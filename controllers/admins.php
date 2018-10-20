<?php

class Admins extends Controller {

    public function __construct() {
        parent::__construct();
    }
    public function index(){
    	$this->error();
    }
    public function add(){
    	if( empty($this->me) || $this->format!='json' ) $this->error();

    	$this->view->setPage('path', 'Themes/manage/forms/admins');
    	$this->view->render('add');
    }
    public function edit($id=null){
    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
    	if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

    	$item = $this->model->get($id);
    	if( empty($item) ) $this->error();

    	$this->view->item = $item;
    	$this->view->setPage('path', 'Themes/manage/forms/admins');
    	$this->view->render('add');
    }
    public function save(){
    	if( empty($_POST) ) $this->error();

    	$id = isset($_POST["id"]) ? $_POST["id"] : null;
    	if( !empty($id) ){
    		$item = $this->model->get($id);
    		if( empty($item) ) $this->error();
    	}

    	try{
    		$form = new Form();
    		$form 	->post('name')->val('is_empty')
    				->post('username')->val('is_empty')
    				->post('email');
    		$form->submit();
    		$postData = $form->fetch();

    		if( empty($item) ){
    			if( empty($_POST["password"]) ) {
    				$arr['error']['password'] = 'กรุณากรอกพาสเวิร์ด';
    			}
    			elseif( strlen($_POST['password']) < 4 ){
    				$arr['error']['password'] = 'กรุณากรอกรหัสผ่านตั้งแต่ 4 ตัวอักษรขึ้นไป';
    			}
    			else{
    				$postData['password'] = $this->fn->q('password')->PasswordHash($_POST["password"]);
    			}
    		}

    		$has_user = true;
    		if( !empty($item) ){
    			if( $item['username'] == $postData['username'] ){
    				$has_user = false;
    			}
    		}
    		if( $this->model->is_user($postData['username']) && $has_user ){
    			$arr['error']['username'] = 'มีชื่อผู้ใช้นี้ในระบบแล้ว';
    		}

    		if( empty($arr['error']) ){
    			if( !empty($id) ){
    				$this->model->update($id, $postData);
    			}
    			else{
                    $postData['site_id'] = 1;
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
    public function del($id=null){
    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
    	if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

    	$item = $this->model->get($id);
    	if( empty($item) ) $this->error();

    	if( !empty($_POST) ){
    		if( !empty($item['permit']['del']) ){
    			$this->model->delete($id);
    			$arr['message'] = 'ลบข้อมูลเรียบร้อย';
    			$arr['url'] = 'refresh';
    		}
    		else{
    			$arr['message'] = 'ไม่สามารถลบข้อมูลได้';
    		}
    		echo json_encode($arr);
    	}
    	else{
    		$this->view->item = $item;
    		$this->view->setPage('path', 'Themes/manage/forms/admins');
    		$this->view->render('del');
    	}
    }
    public function change_password($id=null){
    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
    	if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

    	$item = $this->model->get($id);
    	if( empty($item) ) $this->error();

    	if( !empty($_POST) ){
    		if( strlen($_POST["password"]) < 4 ){
    			$arr['error']['password'] = 'กรุณากรอกรหัสผ่านมากกว่า 4 ตัวอักษร';
    		}
    		if( $_POST["password"] != $_POST["password2"] ){
    			$arr['error']['password2'] = 'รหัสผ่านไม่ตรงกัน';
    		}

    		if( empty($arr['error']) ){
    			$password = $this->fn->q('password')->PasswordHash($_POST["password"]);
    			$this->model->update($id, array('password'=>$password));

    			$arr['message'] = 'บันทึกรหัสผ่านใหม่เรียบร้อย';
    			$arr['url'] = 'refresh';
    		}

    		echo json_encode($arr);
    	}
    	else{
    		$this->view->item = $item;
    		$this->view->setPage('path', 'Themes/manage/forms/admins');
    		$this->view->render('password');
    	}
    }

    public function permission($id=null){
        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
        if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

        $item = $this->model->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){
            $data["permission"] = !empty($_POST["permission"]) ? json_encode($_POST["permission"]) : "";
            $this->model->update($id, $data);

            $arr['message'] = "Set Permission Completed !";
            $arr['url'] = 'refresh';

            echo json_encode($arr);
        }
        else{
            $this->view->setData('item', $item);
			$this->view->setData('pageMenu', $this->model->query('system')->pageMenu());
            $this->view->setPage('path','Themes/manage/forms/admins');
			$this->view->render("permission");
			
        }
    }
}