<?php

class Discounts extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id=null){

    	$this->view->setPage('on', 'discounts');
    	$this->view->setPage('title', 'จัดการส่วนลด');

    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
    	if( !empty($id) ){
    		$item = $this->model->get($id);
    		if( empty($item) ) $this->error();

    		$this->view->setData('item', $item);
    		$render = 'discounts/profile/display';
    	}
    	else{
    		if( $this->format=='json' ){
    			$results = $this->model->lists();
    			$this->view->setData('results', $results);
    			$render = 'discounts/lists/json';
    		}
    		else{
    			$render = 'discounts/lists/display';
    		}
    	}
    	$this->view->render($render);
    }

    public function add(){
    	if( empty($this->me) ) $this->error();

        // $products = $this->model->query('products')->lists( array('unlimit'=>true, 'sort'=>'pds_name', 'dir'=>'ASC') );
        $products = $this->model->listsProduct();
        $this->view->setData('products', $products);

    	$this->view->setPage('on', 'discounts');
    	$this->view->setPage('title', 'เพิ่มส่วนลด');

    	$this->view->render('discounts/forms/add');
    }
    public function edit($id=null){
    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
    	if( empty($id) || empty($this->me) ) $this->error();

    	$this->view->setPage('on', 'discounts');
    	$this->view->setPage('title', 'แก้ไขส่วนลด');

        // $products = $this->model->query('products')->lists( array('unlimit'=>true, 'sort'=>'pds_name', 'dir'=>'ASC') );
        $products = $this->model->listsProduct();
        $this->view->setData('products', $products);

    	$item = $this->model->get($id, array('items'=>true));
    	if( empty($item) ) $this->error();

    	$this->view->setData('item', $item);
    	$this->view->render('discounts/forms/add');
    }
    public function save(){
    	if( empty($_POST) ) $this->error();

    	$id = isset($_POST["id"]) ? $_POST["id"] : null;
    	if( !empty($id) ){
    		$item = $this->model->get($id, array('items'=>true));
    		if( empty($item) ) $this->error();
    	}

    	try{
    		$form = new Form();
    		$form 	->post('dis_name')->val('is_empty')
    				->post('dis_price_1')
    				->post('dis_price_2')
    				->post('dis_price_3')
    				->post('dis_price_4')
    				->post('dis_price_5')
    				->post('dis_price_6')
    				->post('dis_note');
    		$form->submit();
    		$postData = $form->fetch();

    		$has_name = true;
    		if( !empty($item) ){
    			if( $item['name'] == $postData['dis_name'] ) $has_name = false;
    		}
    		if( $this->model->is_name($postData['dis_name']) && $has_name ){
    			$arr['error']['dis_name'] = 'มีชื่อนี้อยู่ในระบบแล้ว';
    		}

    		if( empty($_POST["items"]) ){
    			$arr['error']['items'] = 'กรุณาเลือกสินค้า';
    		}

    		if( empty($arr['error']) ){
    			if( !empty($id) ){
    				$this->model->update($id, $postData);
    			}
    			else{
    				$postData['dis_user_id'] = $this->me['id'];
    				$this->model->insert($postData);
    				$id = $postData['id'];
    			}

    			if( !empty($id) ){
    				$total = 0;
    				$_items = array();
    				if( !empty($item['items']) ){
    					foreach ($item['items'] as $key => $value) {
    						$_items[] = $value['id'];
    					}
    				}
    				foreach ($_POST["items"] as $key => $item_id) {

    					if( empty($item_id) ) continue;

                        // $price = $this->model->query('products')->getPrice($item_id);
    					$data = array(
    						'item_parent_id'=>$item_id,
    						'item_dis_id'=>$id,
                            // 'item_price'=> !empty($price['frontend']) ? $price['frontend'] : "0.00" 
    					);

    					if( !empty($_items[$key]) ){
    						$data['id'] = $_items[$key];
    						unset($_items[$key]);
    					}

    					$this->model->setItem($data);
    					$total++;
    				}
    				if( !empty($_items) ){
    					foreach ($_items as $key => $item_id) {
    						$this->model->unsetItem($item_id);
    					}
    				}

    				$this->model->update($id, array('dis_item'=>$total));
    			}

    			$arr['message'] = 'บันทึกเรียบร้อย';
    			$arr['url'] = URL.'discounts';;
    		}

    	} catch (Exception $e){
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
    		$this->view->setData('item', $item);
    		$this->view->setPage('path', 'Themes/manage/forms/discounts');
    		$this->view->render('del');
    	}
    }
}