<?php

class Photos extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($options = array())
    {
        if (count($options) < 2) {
            $this->error();
        }

        $model = $options[1];
        $filename = $options[1];

        $title = $model;

        $source = WWW_PHOTOS.$filename;
        $path = PHOTOS.$filename;

        if (!file_exists($source)) {
            $this->error();
        }

        list($original_width, $original_height) = getimagesize($source);

        $set_width = isset($_REQUEST['w']) ? $_REQUEST['w'] : null;
        $set_height = isset($_REQUEST['h']) ? $_REQUEST['h'] : null;

        if ($set_width && $set_height) {
            if ($original_width > $original_height && $original_width > $set_width) {
                $width = $set_width;
                $height = round(($set_width * $original_height) / $original_width);

                if ($height < $set_height) {
                    $height = $set_height;
                    $width = round(($set_height * $original_width) / $original_height);
                }
            } elseif ($original_height > $set_height) {
                $height = $set_height;
                $width = round(($set_height * $original_width) / $original_height);

                if ($width < $set_width) {
                    $width = $set_width;
                    $height = round(($set_width * $original_height) / $original_width);
                }
            } else {
                $width = $set_width;
                $height = $set_height;
            }

            $dst = array(0, 0);
            $dst[0] = 0;
            if ($width > $set_width) {
                $dst[0] = ($width - $set_width) / 2;
            }

            $dst[1] = 0;
            if ($height > $set_height) {
                $dst[1] = ($height - $set_height) / 2;
            }

            // echo 1; die;
        } elseif ($set_width && !$set_height) {
            $width = $set_width;
            $height = ($original_height * $set_width) / $original_width;

            $set_height = $height;

            $dst = array(0, 0);
        } elseif (!$set_width && $set_height) {
            $height = $set_height;
            $width = ($original_width * $set_height) / $original_height;

            $set_width = $width;
            $dst = array(0, 0);
        } else {
            $width = $original_width;
            $height = $original_height;

            $set_width = $original_width;
            $set_height = $original_height;
            $dst = array(0, 0);
        }

        // echo $width.'__'.$height; die;
        // echo $set_width.'__'.$set_height; die;
        // echo $original_width.'__'.$original_height; die;

        // echo $path; die;

        $tmp = imagecreatefromjpeg($path);
        $image = imagecreatetruecolor($set_width, $set_height);

        // $background_color = imagecolorclosest($image, 180, 180, 180);
        // $background_color = imagecolorallocate($image, 233, 14, 91);
        /*imagesize( $image, 0, 0, $background_color );
        imagealphablending( $image, false );
        imagesavealpha( $image, true );*/

        // Demo
        /*$image = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($image, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);*/

        imagecopyresampled($image, $tmp, 0, 0, $dst[0], $dst[1], $width, $height, $original_width, $original_height);

        // Set the content type header - in this case image/jpeg
        header('Content-Type: image/jpeg');
        header('Content-Disposition: inline; filename="'.$title.'"');

        // Output the image
        imagejpeg($image);

        // Free up memory
        imagedestroy($image);
    }

    public function albumsList()
    {
        $albumsList = $this->model->albumsList();
        if (empty($albumsList)) {
            $album = array(
                'obj_type' => 'photo',
                'obj_id' => '',
                'name' => 'Gallery',
            );

            $this->model->addAlbum($album);
            $albumsList = $this->model->albumsList();
        }

        echo json_encode($albumsList);
    }

    public function lists()
    {
        echo json_encode($this->model->lists());
    }

    public function album()
    {
        $res = $this->model->lists(array('getSize' => 1));
        // print_r($res); die;
        echo json_encode($res);
    }

    public function upload()
    {
        // $arr['file'] = $_FILES['file1'];
        //

        $arr = $this->model->set($_FILES['file1'], array(
            'album' => isset($_REQUEST['album']) ? $_REQUEST['album'] : null,
            // 'minimize' => array($item['width'], $item['height']),
            // 'primalink' => isset($_POST['link']) ? $_POST['link']: ''
        ));

        if (empty($arr['error'])) {
            $arr['item'] = $this->model->get($arr['id'], array('getSize' => 1));
        }

        echo json_encode($arr);
    }

    public function remove($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->delete($id);
                $arr['message'] = 'ลบรูปเรียบร้อย';
            } else {
                $arr['message'] = 'ไม่สามารถลบรูปได้';
            }

            $arr['url'] = isset($_REQUEST['next']) ? $_REQUEST['next'] : 'refresh';
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Forms/photos');
            $this->view->render('remove');
        }
    }
}
