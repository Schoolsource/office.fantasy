<?php

class Categories extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->setPage('on', 'categories');
        $this->view->setPage('title', 'จัดการหมวดหมู่สินค้า');

        $this->view->js('jquery/jquery-ui.min');

        $this->view->setData('results', $this->model->lists(array('sort' => 'seq', 'dir' => 'ASC')));
        $this->view->render('categories/lists');
    }

    public function add()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setData('category', $this->model->lists());
        $this->view->setData('status', $this->model->status());
        $this->view->setPage('path', 'Themes/manage/forms/categories');
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

        $this->view->setData('item', $item);
        $this->view->setData('category', $this->model->lists(array('not' => $item['id'])));
        $this->view->setData('status', $this->model->status());
        $this->view->setPage('path', 'Themes/manage/forms/categories');
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

        if (!empty($_FILES['img'])) {
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = getimagesize($_FILES['img']['tmp_name']);
            if ($imageWidth > 350 || $imageHeight < 350) {
                $arr['error']['img'] = 'ขนาดภาพต้องไม่เกิน 350px * 350px';
            }
        }

        try {
            $form = new Form();
            $form->post('firstCode')
                    ->post('name_th')->val('is_empty')
                    ->post('name_en')->val('is_empty')
                    ->post('status')
                    ->post('cate_id');
            $form->submit();
            $postData = $form->fetch();

            $has_th = true;
            $has_en = true;
            if (!empty($item)) {
                if ($item['name_th'] == $postData['name_th']) {
                    $has_th = false;
                }
                if ($item['name_en'] == $postData['name_en']) {
                    $has_en = false;
                }
            }
            if ($this->model->is_name_th($postData['name_th']) && $has_th) {
                $arr['error']['name_th'] = 'ตรวจพบชื่อนี้ในระบบ';
            }
            if ($this->model->is_name_en($postData['name_en']) && $has_en) {
                $arr['error']['name_en'] = 'ตรวจพบชื่อนี้ในระบบ';
            }

            $postData['name_th'] = trim($postData['name_th']);
            $postData['name_en'] = trim($postData['name_en']);

            $postData['is_sub'] = !empty($_POST['is_sub']) ? $_POST['is_sub'] : 0;
            if (!empty($postData['is_sub'])) {
                if (empty($postData['cate_id'])) {
                    $arr['error']['cate_id'] = 'กรุณาเลือกหมวดหมู่ที่ต้องการให้เป็นเมนูหลัก';
                }
            } else {
                $postData['cate_id'] = 0;
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $this->model->insert($postData);
                    $id = $postData['id'];
                }

                if (!empty($_FILES['img']) && !empty($id)) {
                    if (!empty($item['cate_img_id'])) {
                        $this->model->query('media')->del($item['cate_img_id']);
                    }

                    $userfile = $_FILES['img'];

                    $album_options = array(
                        'album_obj_type' => 'categories',
                        'album_obj_id' => 3,
                    );
                    $album = $this->model->query('media')->searchAlbum($album_options);
                    if (empty($album)) {
                        $this->model->query('media')->setAlbum($album_options);
                        $album = $album_options;
                    }

                    // set Media
                    $media = array(
                        'media_album_id' => $album['album_id'],
                        'media_type' => isset($_REQUEST['media_type']) ? $_REQUEST['media_type'] : strtolower(substr(strrchr($userfile['name'], '.'), 1)),
                    );
                    $media_options = array(
                        'folder' => $album['album_id'],
                    );

                    $this->model->query('media')->set($userfile, $media, $media_options);

                    if (!empty($media['media_id'])) {
                        $this->model->update($id, array('cate_img_id' => $media['media_id']));
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
            $this->view->setPage('path', 'Themes/manage/forms/categories');
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

    public function del_image($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->query('media')->get($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            $this->model->query('media')->del($id);
            $arr['message'] = 'Deleted !';
            // $arr['url'] = 'refresh';

            echo json_encode($arr);
        } else {
            $this->view->item = $item;
            $this->view->setPage('path', 'Themes/manage/forms/products');
            $this->view->render('del_image');
        }
    }

    public function _update($id, $field = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $field = isset($_REQUEST['field']) ? $_REQUEST['field'] : $field;
        if (empty($id) || empty($field) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        if ($field == 'pds_status') {
            $value = 'I';
            if ($_POST['value'] == '1') {
                $value = 'A';
            }
            $data['pds_status'] = $value;
        } else {
            $data[$field] = $_POST['value'];
        }

        $this->model->update($id, $data);

        $arr['message'] = 'บันทึกเรียบร้อย';

        echo json_encode($arr);
    }
}
