<?php

class Customers extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;

        $this->view->setPage('on', 'customers');
        $this->view->setPage('title', 'รายชื่อลูกค้า');

        if (!empty($id)) {
            $options = [
                'orders' => true,
            ];

            $project = isset($_REQUEST['project']) ? $_REQUEST['project'] : null;
            $options['project'] = $project;

            $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 'A';
            $options['status'] = $status;

            $due = isset($_REQUEST['due']) ? $_REQUEST['due'] : null;
            $options['due'] = $due;

            $item = $this->model->get($id, $options);
            if (empty($item)) {
                $this->error();
            }

            $this->view->setData('item', $item);
            $this->view->setData('projectList', $this->model->query('orders')->projects());
            $render = 'customers/profile/display';
        } else {
            if ($this->format == 'json') {
                $this->view->setData('results', $this->model->lists());
                $render = 'customers/lists/json';
            } else {
                $this->view->setData('sales', $this->model->query('payments')->sales());
                $render = 'customers/lists/display';
            }
        }

        $this->view->render($render);
    }

    public function due($id = null)
    {
        if (!$id) {
            $this->error();
        }
        $options = [
            'orders' => true,
            'process' => 3,
            'due' => true,
            'dir' => 'ASC',
        ];

        $customers = $this->model->get($id, $options);
        if (!$customers) {
            $this->error();
        }
        // echo '<pre>';
        // print_r($customers);
        // exit;

        $this->view->setData('customers', $customers);
        $render = 'customers/due/display';
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
            $this->view->setPage('path', 'Themes/manage/forms/customers');
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

                            // $address = explode("ต.", $data[$r][3]);
                            // $addrStr = $address[0];

                            // $sub_dis = "";
                            // $sub_dis = !empty($address[1]) ? $address[1] : "";
                            // if( !empty($address[1]) ){
                            //     $cut_dis = explode("อ.", $address[1]);
                            //     $sub_dis = empty($cut_dis[1]) ? $address[1] : $cut_dis[0];

                            //     $add = explode(" ", $address[1]);
                            //     $addrStr .= !empty($add[1]) ? $add[1] : "";
                            // }

                            $postData = array(
                                'sub_code' => $data[$r][0],
                                'name_store' => $data[$r][1],
                                'sale_code' => $data[$r][2],
                                'sale_name' => $data[$r][3],
                                'address' => $data[$r][4],
                                'road' => $data[$r][5],
                                'district' => $data[$r][6],
                                'province' => $data[$r][8],
                                'post_code' => $data[$r][9],
                                'country' => 3,
                                'phone' => $data[$r][10],
                                'status' => 'A',
                                'note' => $data[$r][4].' '.$data[$r][5].' '.$data[$r][6].' '.$data[$r][7].' '.$data[$r][8],
                                'updated_at' => date('c'),
                            );

                            $customer = $this->model->getCode($data[$r][0]);
                            if (!empty($customer)) {
                                // $postData['username'] = "C".$data[$r][1];
                                // $postData['password'] = $this->fn->q('password')->PasswordHash('1234');
                                $this->model->update($customer['id'], $postData);
                                $id = $customer['id'];
                            } else {
                                $postData['username'] = 'C'.$data[$r][0];
                                $postData['password'] = $this->fn->q('password')->PasswordHash('1234');
                                $this->model->insert($postData);
                                $id = $postData['id'];
                            }

                            if (!empty($id)) {
                                $_data = array(
                                    'customer_id' => $id,
                                    'address' => $data[$r][4],
                                    'road' => $data[$r][5],
                                    'district' => $data[$r][6],
                                    'area' => $data[$r][7],
                                    'province' => $data[$r][8],
                                    'post_code' => $data[$r][9],
                                    'country' => 3,
                                    'main' => 1,
                                    'ship' => 1,
                                    'bill' => 1,
                                    'sorting' => 1,
                                    'status' => 'A',
                                );
                                if (!empty($customer['address'][0]['id'])) {
                                    $_data['id'] = $customer['address'][0]['id'];
                                }
                                $this->model->setAddress($_data);
                            }
                        }
                    } else {
                        $arr['error']['file'] = 'กรุณาเลือกไฟล์ .xls หรือ .xlsx เท่านั้น';
                    }

                    if (empty($arr['error'])) {
                        $arr['message'] = 'บันทึกเรียบร้อย';
                        $arr['url'] = 'refresh';
                    }
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }
            echo json_encode($arr);
        } else {
            $this->view->setPage('path', 'Themes/manage/forms/customers');
            $this->view->render('import');
        }
    }

    public function export()
    {
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="customers-'.date('Y-m-d').'".xlsx"');

        $customers = $this->model->lists();

        require WWW_LIBS.'PHPOffice/PHPExcel.php';
        require WWW_LIBS.'PHPOffice/PHPExcel/IOFactory.php';

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()
                        ->setCreator('MF Fantasy Customers')
                        ->setLastModifiedBy('MF Fantasy Customers')
                        ->setTitle('Office 2007 XLSX Customer Document')
                        ->setSubject('Office 2007 XLSX Customer Document')
                        ->setDescription('Customer Document for Office 2007 XLSX, generated using PHP classes.')
                        ->setKeywords('office 2007 openxml php')
                        ->setCategory('Customer File');

        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'รหัสลูกค้า')
                        ->setCellValue('B1', 'ชื่อลูกค้า')
                        ->setCellValue('C1', 'จังหวัด')
                        ->setCellValue('D1', 'เซลล์')
                        ->setCellValue('E1', 'เบอร์โทร')
                        ->setCellValue('F1', 'เบอร์โทร (อื่นๆ)')
                        ->setCellValue('G1', 'LineID');

        $i = 2;
        foreach ($customers['lists'] as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $value['sub_code']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $value['name_store']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $value['address'][0]['province']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, '('.$value['sale_code'].') '.$value['sale_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $value['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $value['phone_other']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $value['line_id']);
            ++$i;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Customers '.date('Y-m-d'));
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        echo '<script type="text/javascript">window.close();</script>';
    }

    public function set_userpass()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        if (!empty($_POST)) {
            $this->model->setUserPass();
            $arr['message'] = 'ตั้งค่าเรียบร้อย';
            $arr['url'] = 'refresh';

            echo json_encode($arr);
        } else {
            $this->view->setPage('path', 'Themes/manage/forms/customers');
            $this->view->render('setUserPass');
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

    public function insertProject()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setPage('path', 'Themes/manage/forms/customers/project');
        $this->view->render('input');
    }

    public function updateProject($id = '')
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;

        $item = $this->model->project->findById($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->insertProject();
    }

    public function saveProject()
    {
        if (empty($this->me) || $this->format != 'json' || empty($_POST)) {
            $this->error();
        }
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

        if (!empty($id)) {
            $item = $this->model->project->findById($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('project_name')->val('is_empty');
            $form->post('project_target')->val('is_empty');

            $form->submit();
            $postData = $form->fetch();

            if (empty($arr['error'])) {
                if (!empty($item)) {
                    $this->model->project->update($id, $postData);
                } else {
                    $this->model->project->insert($postData);
                    $id = $postData['id'];
                }

                $arr['message'] = 'Saved!';
                $arr['url'] = !empty($_REQUEST['next']) ? $_REQUEST['next'] : 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }

    public function deleteProject($id = '')
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;

        $item = $this->model->project->findById($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->project->delete($id);

                $arr['message'] = 'Deleted project!';
            } else {
                $arr['message'] = "You can't delete project: {$item['project_name']}, Please try again.";
            }
            $arr['url'] = !empty($_REQUEST['next']) ? $_REQUEST['next'] : 'refresh';

            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/customers/project');
            $this->view->render('delete');
        }
    }

    public function enabledProject($id = '', $val = 0)
    {
        if (empty($this->me) || $this->format != 'json' || empty($_POST)) {
            $this->error();
        }
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        $val = isset($_REQUEST['val']) ? $_REQUEST['val'] : null;

        $item = $this->model->project->findById($id);
        if (empty($item)) {
            $this->error();
        }

        $this->model->project->update($id, array('project_enabled' => $val));
        $arr['message'] = ($val == 1 ? 'Enabled' : 'Disabled').' project';
        echo json_encode($arr);
    }
}
