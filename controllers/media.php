<?php

class Media extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->error();
    }

    // รูปเดียว
    public function set()
    {
        $userfile = isset($_FILES['file1']) ? $_FILES['file1'] : null;
        $_type = isset($_FILES['type']) ? $_FILES['type'] : null;
        if (empty($this->me) || $this->format != 'json' || empty($userfile)) {
            $this->error();
        }

        /**/
        /* get Data Album */
        /**/
        $options = array(
            'album_obj_type' => isset($_REQUEST['obj_type']) ? $_REQUEST['obj_type'] : 'public',
            'album_obj_id' => isset($_REQUEST['obj_id']) ? $_REQUEST['obj_id'] : 1,
        );

        if (isset($_REQUEST['album_name'])) {
            $options['album_name'] = $_REQUEST['album_name'];
        }
        $album = $this->model->searchAlbum($options);

        if (empty($album)) {
            $this->model->setAlbum($options);
            $album = $options;
        }

        // set Media Data
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

        $this->model->set($userfile, $media, $options);

        if (empty($media['error'])) {
            $media = $this->model->convert($media);
        }

        echo json_encode($media);
    }

    public function remove($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($this->me) || $this->format != 'json' || empty($id)) {
            $this->error();
        }

        /*$item = $this->model->get( $id );
        if( empty($item) ) $this->error();*/

        $this->model->del($id);

        $arr['message'] = 'ลบรูปแล้ว';
        echo json_encode($arr);
    }

    public function del($id = '')
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($this->me) || $this->format != 'json' || empty($id)) {
            $this->error();
        }

        $item = $this->model->query('media')->get($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            $this->model->query('media')->del($id);

            $arr['message'] = 'ลบรูปแล้ว';
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/media');
            $this->view->render('del');
        }
    }

    public function set_caption()
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($this->me) || $this->format != 'json' || empty($id)) {
            $this->error();
        }

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        $caption = $_POST['text'];

        if (is_array($caption)) {
            $a = array();
            foreach ($caption as $key => $value) {
                if (!empty($value)) {
                    $a[$key] = $value;
                }
            }
            $caption = json_encode($a);
        }

        $this->model->set_caption($id, $caption);

        $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
        echo json_encode($arr);
    }

    public function set_sequence()
    {
        $ids = $_POST['ids'];
        if (empty($this->me) || $this->format != 'json' || empty($ids)) {
            $this->error();
        }

        $seq = 0;
        foreach ($ids as $key => $id) {
            ++$seq;
            $this->model->set_sequence($id, $seq);
        }

        $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
        echo json_encode($arr);
    }

    public function set_data_slideshow()
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        if (empty($this->me) || $this->format != 'json' || empty($id)) {
            $this->error();
        }

        $item = $this->model->query('albums')->get_media($id);
        if (empty($item)) {
            $this->error();
        }

        $data = array();
        if (!empty($_POST['title'])) {
            $data['title'] = $_POST['title'];
        }

        if (!empty($_POST['text'])) {
            $data['text'] = $_POST['text'];
        }

        if (!empty($_POST['link'])) {
            $data['link'] = $_POST['link'];
        }

        $this->model->query('albums')->up_media($id, array('media_caption' => json_encode($data)));

        $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
        echo json_encode($arr);
    }

    public function lists()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        echo json_encode($this->model->lists());
    }
}
