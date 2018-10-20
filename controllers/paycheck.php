<?php

class paycheck extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->setPage('on', 'paycheck');
        $this->view->setPage('title', 'รายการจ่ายเช็ค');

        if ($this->format == 'json') {
            $results = $this->model->lists();
            $this->view->setData('results', $results);
            $render = 'paycheck/lists/json';
        } else {
            $this->view->setData('bank', $this->model->query('payments')->bank());
            $render = 'paycheck/lists/display';
        }
        $this->view->render($render);
    }

    //MANAGE
    public function add()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $sup = isset($_REQUEST['sup']) ? $_REQUEST['sup'] : null;
        $this->view->setData('currSup', $sup);

        $this->view->setData('bank', $this->model->query('payments')->bank());
        $this->view->setData('suppliers', $this->model->query('suppliers')->lists(array('status' => 'enabled', 'unlimit' => true, 'sort' => 'code', 'dir' => 'ASC')));
        $this->view->setPage('path', 'Themes/manage/forms/paycheck');
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

        $sup = isset($_REQUEST['sup']) ? $_REQUEST['sup'] : null;
        $this->view->setData('currSup', $sup);

        $this->view->setData('item', $item);
        $this->view->setData('bank', $this->model->query('payments')->bank());
        $this->view->setData('suppliers', $this->model->query('suppliers')->lists(array('status' => 'enabled', 'unlimit' => true, 'sort' => 'code', 'dir' => 'ASC')));
        $this->view->setPage('path', 'Themes/manage/forms/paycheck');
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
            $form->post('check_sup_id')->val('is_empty')
                    ->post('check_date')
                    ->post('check_up_date')
                    ->post('check_bank_id')->val('is_empty')
                    ->post('check_number')->val('is_empty')
                    ->post('check_price')->val('is_empty');
            $form->submit();
            $postData = $form->fetch();

            $has_check = true;
            if (!empty($item)) {
                if ($item['number'] == $postData['check_number']) {
                    $has_check = false;
                }
            }
            if ($this->model->is_check($postData['check_number']) && $has_check) {
                $arr['error']['check_number'] = 'ตรวจพบเช็คใบนี้อยู่ในระบบแล้ว';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $this->model->insert($postData);
                    $id = $postData['id'];
                }

                if (!empty($id) && !empty($_FILES['check_image_id'])) {
                    $userfile = isset($_FILES['check_image_id']) ? $_FILES['check_image_id'] : null;
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

                        if (!empty($item['image_id'])) {
                            $this->model->query('media')->del($item['image_id']);
                        }

                        $this->model->update($id, array('check_image_id' => $media['id']));
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
    public function exports($chueqe_id= null){
        ini_set("error_reporting", E_ALL);

        $item = $this->model->get($chueqe_id);
        if (empty($item)) {
            $this->error();
        }
      
      
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
                if (!empty($item['image_id'])) {
                    $this->model->query('media')->del($item['image_id']);
                }

                $this->model->delete($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/paycheck');
            $this->view->render('del');
        }
    }

    //Show
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

        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/paycheck');
        $this->view->render('showPicture');
    }
}
