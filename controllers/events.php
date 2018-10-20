<?php

class Events extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($id=null){
    	$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;

        $this->view->setPage('on', 'events');
        $this->view->setPage('title', 'รายการนัดหมาย');

    	if( !empty($id) ){
            $item = $this->model->get($id);
            if( empty($item) ) $this->error();
    	}
    	else{
    		if( $this->format=='json' ){
    			$this->view->setData('results', $this->model->lists());
    			$render = "events/lists/json";
    		}
    		else{
    			$render = "events/lists/display";
    		}
    	}

    	$this->view->render($render);
    }

    #Manage
    public function add(){
        if( empty($this->me) || $this->format!='json' ) $this->error();

        $this->view->setPage('path', 'Themes/manage/forms/events');
        $this->view->render("add");
    }
    public function edit($id=null){
        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : $id;
        if( empty($id) || empty($this->me) || $this->format!='json' ) $this->error();

        $item = $this->model->get($id);
        if( empty($item) ) $this->error();

        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/events');
        $this->view->render('add');
    }
    public function save(){
        if( empty($_POST) ) $this->error();

        $start_time = !empty($_POST['start_time']) ? $_POST['start_time'].':00' : '00:00:00';
        $end_time = !empty($_POST['end_time']) ? $_POST['end_time'].':00' : '00:00:00';

        $id = isset($_POST['id']) ? $_POST['id']: null;
        if( !empty($id) ){
            $item = $this->model->get($id);
            if( empty($item) ) $this->error();
        }

        try{
            $form = new Form();
            $form   ->post('event_title')->val('is_empty')
                    ->post('event_text')->val('is_empty')
                    ->post('event_location')
                    ->post('event_start')
                    ->post('event_end');
            $form->submit();
            $postData = $form->fetch();

            $has_invite = isset($_REQUEST['has_invite']) ? $_REQUEST['has_invite']: 1;

            if( empty($_POST['invite']) && $has_invite==1 ){
                $arr['error']['event_invite'] = 'กรุณาเลือกผู้ที่เกี่ยวข้อง';
                $arr['message'] = 'กรุณาเลือกผู้ที่เกี่ยวข้อง';
            }

            $invite = isset($_POST['invite'])? $_POST['invite']: null;
            $postData['event_has_invite'] = $has_invite;
            $postData['event_start'] = $postData['event_start'].' '.$start_time;
            $postData['event_end'] = $postData['event_end'].' '.$end_time;
            $postData['event_allday'] = !empty($_POST['allday']) ? 1 : 0;

            if( empty($arr['error']) ){
                if( !empty($item) ){

                    if( $has_invite==1 ){
                        $this->model->deleteJoinEvent( $id );
                    }
                    
                    $this->model->update( $id, $postData );
                    $postData['id'] = $id;
                }
                else{
                    $postData['event_user_id'] = $this->me['id'];
                    $this->model->insert( $postData );
                    $id = $postData['id'];
                }

                if( !empty($invite) && !empty($id) ){
                    foreach ($invite['id'] as $key => $value) {
                        if( empty($invite['type'][$key]) ) continue;

                        $join = array(
                            'event_id'=>$id,
                            'obj_type'=>$invite['type'][$key],
                            'obj_id'=>$invite['id'][$key]
                        );

                        $this->model->insertJoinEvent( $join );

                        if( $invite['type'][$key] == 'orders' ){

                            $data = $this->model->query($invite['type'][$key])->get($value);
                            $_join = array(
                                'event_id'=>$id,
                                'obj_type'=>'customers',
                                'obj_id'=>$data['customer_id'],
                            );

                            $this->model->insertJoinEvent( $_join );
                        }
                    }
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

        if (!empty($_POST)) {

            if ( !empty($item['permit']['del']) ) {
                $this->model->delete($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }

            $arr['url'] = 'refresh';
            echo json_encode($arr);
        }
        else{
            $this->view->item = $item;
            
            $this->view->setPage('path', 'Themes/manage/forms/events');
            $this->view->render("del");
        }
    }
}