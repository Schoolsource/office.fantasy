<?php

class Suppliers extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;

        $this->view->setPage('title', 'Suppliers');
        $this->view->setPage('on', 'suppliers');

        if (!empty($id)) {
            $item = $this->model->get($id, array('check' => true));
            if (empty($item)) {
                $this->error();
            }

            $events = $this->model->query('events')->lists(array('obj_id' => $id, 'obj_type' => 'suppliers'));

            $this->view->setData('events', $events);
            $this->view->setData('item', $item);
            $render = 'suppliers/profile/display';
        } else {
            $this->view->setData('status', $this->model->status());
            if ($this->format == 'json') {
                $this->view->setData('results', $this->model->lists());
                $render = 'suppliers/lists/json';
            } else {
                $this->view->setData('type', $this->model->type());
                $render = 'suppliers/lists/display';
            }
        }

        $this->view->render($render);
    }

    //MANAGE
    public function add()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setData('type', $this->model->type());
        $this->view->setData('prefixName', $this->model->query('system')->prefixName());
        $this->view->setData('city', $this->model->query('system')->city());
        $this->view->setData('country', $this->model->query('system')->country());
        $this->view->setPage('path', 'Themes/manage/forms/suppliers');
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
        $this->view->setData('type', $this->model->type());
        $this->view->setData('prefixName', $this->model->query('system')->prefixName());
        $this->view->setData('city', $this->model->query('system')->city());
        $this->view->setData('country', $this->model->query('system')->country());
        $this->view->setPage('path', 'Themes/manage/forms/suppliers');
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
            $form->post('sup_code')
                    ->post('sup_type_id')->val('is_empty')
                    ->post('sup_name')->val('is_empty')
                    ->post('sup_prefix_name')
                    ->post('sup_first_name')
                    ->post('sup_last_name')
                    ->post('sup_nickname')
                    ->post('sup_address')->val('is_empty')
                    ->post('sup_street')
                    ->post('sup_supdistrict')
                    ->post('sup_district')
                    ->post('sup_province_id')
                    ->post('sup_zip')
                    ->post('sup_country_id')->val('is_empty')
                    ->post('sup_mobile_phone')
                    ->post('sup_phone')
                    ->post('sup_fax');
            $form->submit();
            $postData = $form->fetch();

            $postData['sup_name'] = trim($postData['sup_name']);
            $postData['sup_first_name'] = trim($postData['sup_first_name']);
            $postData['sup_last_name'] = trim($postData['sup_last_name']);

            $has_name = true;
            if (!empty($item)) {
                if ($item['name'] == $postData['sup_name']) {
                    $has_name = false;
                }
            }
            if ($this->model->is_name($postData['sup_name']) && $has_name) {
                $arr['error']['sup_name'] = 'ตรวจพบชื่อซ้ำในระบบ';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $postData['sup_user_id'] = $this->me['id'];
                    $postData['sup_status'] = 'enabled';
                    $this->model->insert($postData);
                }

                $arr['message'] = 'บันทึกเรียบร้อย';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());

            if (!empty($arr['error']['sup_prefix_name'])) {
                $arr['error']['name'] = $arr['error']['sup_prefix_name'];
            } elseif (!empty($arr['error']['sup_first_name'])) {
                $arr['error']['name'] = $arr['error']['sup_first_name'];
            } elseif (!empty($arr['error']['sup_last_name'])) {
                $arr['error']['name'] = $arr['error']['sup_last_name'];
            }
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
                $arr['url'] = !empty($_POST['next']) ? $_POST['next'] : 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/suppliers');
            $this->view->render('del');
        }
    }

    public function setData($id = null, $field = null)
    {
        if (empty($id) || empty($field) || empty($this->me)) {
            $this->error();
        }

        $data['sup_'.$field] = isset($_REQUEST['value']) ? $_REQUEST['value'] : '';
        $this->model->update($id, $data);

        $arr['message'] = 'บันทึกเรียบร้อย';
        echo json_encode($arr);
    }

    //TYPE
    public function add_type()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setPage('path', 'Themes/manage/forms/suppliers/type');
        $this->view->render('add');
    }

    public function edit_type($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getType($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/suppliers/type');
        $this->view->render('add');
    }

    public function save_type()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->getType($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('type_name')->val('is_empty');
            $form->submit();
            $postData = $form->fetch();

            $has_name = true;
            if (!empty($item)) {
                if ($item['name'] == $postData['name']) {
                    $has_name = false;
                }
            }
            if ($this->model->is_type($postData['name']) && $has_name) {
                $arr['error']['type_name'] = 'ตรวจพบชื่อซ้ำในระบบ';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->updateType($id, $postData);
                } else {
                    $this->model->insertType($postData);
                }
                $arr['message'] = 'บันทึกเรียบร้อย';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del_type($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->getType($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->deleteType($id);
                $arr['message'] = 'ลบข้อมูลเรียบร้อย';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'ไม่สามารถลบข้อมูลได้';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/suppliers/type');
            $this->view->render('del');
        }
    }

    public function import()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        if (!empty($_POST)) {
            try {
                if (!empty($_FILES)) {
                    $target_file = $_FILES['file']['tmp_name'];
                    $type_file = strrchr($_FILES['file']['name'], '.');

                    if ($type_file == '.xls' || $type_file == '.xlsx') {
                        require WWW_LIBS.'PHPOffice/PHPExcel.php';
                        require WWW_LIBS.'PHPOffice/PHPExcel/IOFactory.php';

                        // print_r($_FILES['file1']); die;
                        // $import->userfile = ;

                        $inputFileName = $target_file;
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objReader->setReadDataOnly(true);
                        $objPHPExcel = $objReader->load($inputFileName);

                        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                        $highestRow = $objWorksheet->getHighestRow();
                        $highestColumn = $objWorksheet->getHighestColumn();

                        $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1', null, true, true, true);
                        $headingsArray = $headingsArray[1];

                        $r = -1;
                        $data = array();
                        $startRow = isset($_REQUEST['start_row']) ? $_REQUEST['start_row'] : 1;

                        for ($row = $startRow; $row <= $highestRow; ++$row) {
                            $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, true, true, true);

                            ++$r;
                            $col = 0;
                            foreach ($headingsArray as $columnKey => $columnHeading) {
                                $val = $dataRow[$row][$columnKey];

                                $text = '';
                                foreach (explode(' ', trim($val)) as $value) {
                                    if (empty($value)) {
                                        continue;
                                    }
                                    $text .= !empty($text) ? ' ' : '';
                                    $text .= $value;
                                }

                                $data[$r][$col] = $text;
                                ++$col;
                            }
                            $type = 1;
                            if ($data[$r][6] == 'กรุงเทพ ฯ') {
                                $data[$r][6] = 'กรุงเทพมหานคร';
                            }
                            $province_id = $this->model->query('system')->city_id($data[$r][6]);
                            $country = '';
                            switch ($data[$r][8]) {
                                case 'Thailand':
                                    $country = 3;
                                    break;
                                case 'Taiwan R.O.C.':
                                    $country = 7;
                                    break;
                                case 'Myanmar':
                                    $country = 6;
                                    break;
                                case 'Italy':
                                    $country = 8;
                                    break;
                                case 'Italy':
                                    $country = 8;
                                    break;
                                case 'South Korea':
                                    $country = 9;
                                    // no break
                                default:
                                    $country = 0;
                                    break;
                            }

                            $input = array(
                                'sup_type_id' => $type,
                                'sup_code' => $data[$r][0],
                                'sup_name' => $data[$r][1],
                                'sup_address' => $data[$r][2],
                                'sup_street' => $data[$r][3],
                                'sup_supdistrict' => $data[$r][4],
                                'sup_district' => $data[$r][5],
                                'sup_province_id' => $province_id,
                                'sup_zip' => $data[$r][7],
                                'sup_country_id' => $country,
                                'sup_mobile_phone' => $data[$r][9],
                                'sup_phone' => $data[$r][10],
                                'sup_fax' => $data[$r][11],
                            );

                            $this->model->import($input);
                        }

                        $arr['message'] = 'Save successfully';
                        $arr['url'] = 'refresh';
                    } else {
                        $arr['error']['file'] = '.xls or .xlsx only';
                    }
                } else {
                    $arr['error']['file'] = 'Please select a file.';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        } else {
            $this->view->setPage('path', 'Themes/manage/forms/suppliers/import');
            $this->view->render('upload');
        }
    }

    /* JSON DATA */
    public function get($id = null)
    {
        if (empty($this->me) || empty($id)) {
            $this->error();
        }
        echo json_encode($this->model->get($id));
    }
}
