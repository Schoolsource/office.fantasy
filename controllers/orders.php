<?php

class Orders extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->error();
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

                            $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date('Y-m-d');

                            $customer = $this->model->query('customers')->getCode($data[$r][3]);
                            $cus_id = !empty($customer) ? $customer['id'] : 0;

                            $postData = array(
                                'ord_code' => $data[$r][0],
                                'ord_customer_id' => $cus_id,
                                'ord_dateCreate' => date("{$date} 00:00:00", PHPExcel_Shared_Date::ExcelToPHP($data[$r][1])),
                                'ord_sale_code' => $data[$r][5],
                                'ord_type_commission' => 2,
                                'user_name' => $data[$r][4],
                                'user_code' => $data[$r][3],
                                'ord_process' => 0,
                                'ord_status' => 'A',
                                'ord_type_commission' => 'sales',
                                'ord_net_price' => $data[$r][8],
                                'order_note' => $data[$r][10],
                                'create_user_id' => 1,
                                'create_user_type' => 'Sale',
                            );

                            $order = $this->model->getCode($data[$r][0]);
                            if (!empty($order)) {
                                $this->model->update($order['id'], $postData);
                            } else {
                                $postData['created_at'] = date("{$date} H:i:s");
                                $postData['updated_at'] = date("{$date} H:i:s");
                                $this->model->insert($postData);
                            }
                        }

                        $arr['message'] = 'บันทึกเรียบร้อย';
                        $arr['url'] = 'refresh';
                    } else {
                        $arr['error']['file'] = 'รองรับ .xls หรือ .xlsx เท่านั้น';
                    }
                } else {
                    $arr['error']['file'] = 'กรุณาเลือกไฟล์';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        } else {
            $this->view->setPage('path', 'Themes/manage/forms/orders');
            $this->view->render('import');
        }
    }

    public function updateProject($id = '', $val = '')
    {
        if (empty($this->me) || $this->format != 'json' || empty($_POST)) {
            $this->error();
        }
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $val = isset($_REQUEST['val']) ? $_REQUEST['val'] : $val;

        $item = $this->model->get($id);
        if (empty($item)) {
            $this->error();
        }

        $this->model->update($id, array('ord_project_id' => $val));
        $arr['message'] = 'Updated project';
        echo json_encode($arr);
    }
}
