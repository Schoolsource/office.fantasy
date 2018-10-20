<?php

class PDF extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->error();
    }

    public function reports()
    {
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'revenue';
        $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');
        $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] : '';
        $term_of_payment = isset($_REQUEST['term_of_payment']) ? $_REQUEST['term_of_payment'] : '';

        $this->view->setData('start', $start);
        $this->view->setData('end', $end);
        $this->view->setData('section', 'reports/'.$type);
        $this->view->setData('periodStr', $this->fn->q('time')->str_event_date($start, $end).' '.date('Y', strtotime($end)));

        if ($type == 'revenue') {
            $options = array(
                'period_start' => $start,
                'period_end' => $end,
                'not_process' => 7,
                'unlimit' => true,
                'dir' => 'ASC',
            );
            if ($sale != '') {
                $options['sale'] = $sale;
            }

            if ($term_of_payment != '') {
                $options['term_of_payment'] = $term_of_payment;
            }
            $results = $this->model->query('orders')->lists($options);
            $this->view->setData('results', $results);
        } else {
            $this->error();
        }

        $this->view->render('display');
    }
    public function costan($id=null){

            $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'revenue';
            $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
            $end = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');
            $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] : '';
            $term_of_payment = isset($_REQUEST['term_of_payment']) ? $_REQUEST['term_of_payment'] : '';     
            $this->view->setData('section', 'reports/chueqe');
            $results = $this->model->query('paycheck')->get($id);
          
            $this->view->setData('results', $results);
            

        $this->view->render('chueqe');
    }
    public function vat_buy()
    {
        $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');

        $options = array(
            'period_start' => $start,
            'period_end' => $end,
            'report' => true,
            'unlimit' => true,
            'dir' => 'ASC',
        );
        $results = $this->model->query('tax')->lists($options);
        $this->view->setData('results', $results);
        $this->view->setData('month', $month);
        $this->view->setData('year', $year);
        $this->view->render('vatbuy');
    }

    public function vat_sale($id = null)
    {
        $item = $this->model->query('bills')->get($id, array('items' => true));
        if (empty($item)) {
            $this->error();
        }

        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'full';
        $title = '';
        if ($type == 'full') {
            $title = 'ต้นฉบับใบกำกับภาษี/ใบส่งของ/<br/>ใบแจ้งหนี้';
        } elseif ($type == 'copy') {
            $title = 'สำเนาใบกำกับภาษี/ใบส่งของ/<br/>ใบแจ้งหนี้';
        } elseif ($type == 'slip') {
            $title = 'ใบเสร็จรับเงิน';
        }
        $this->view->setData('title', $title);
        $this->view->setData('item', $item);
        $this->view->render('vatsale', array(), true);
    }

    public function listsVatsale()
    {
        $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end   = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');
        $term_of_payment = isset($_REQUEST['term_of_payment']) ? $_REQUEST['term_of_payment'] : '';

        $options = array(
            'period_start' => $start,
            'period_end' => $end,
            'term_of_payment' => $term_of_payment,
            'unlimit' => true,
            'sort' => 'id',
            'dir' => 'ASC',
        );

        $results = $this->model->query('bills')->lists($options);
        $this->view->setData('results', $results);
        $this->view->setData('month', $month);
        $this->view->setData('year', $year);
        $this->view->render('lists_vat_sale', array(), true);
    }

    public function receiptReport()
    {
        if (empty($_POST) || empty($this->me)) {
            $this->error();
        }

        $results = $this->model->query('payments')->lists(array('unlimit' => 1));

        $this->view->setData('datalist', $results['lists']);
        $this->view->setData('section', 'reports/receipt');

        $dateStr = '';
        if (isset($_REQUEST['period_start']) && isset($_REQUEST['period_end'])) {
            $dateStr = ': '.$this->fn->q('time')->str_event_date($_REQUEST['period_start'], $_REQUEST['period_end'], true, 'en');
        }

        $this->view->setData('pages', array(
            // 'format' => 'A4-L',
            'title' => 'Receipt Report'.$dateStr,
            'margin_left' => 5,
        ));

        $this->view->render('display');
    }

    public function stockBalance()
    {
        if (empty($_POST) || empty($this->me)) {
            $this->error();
        }

        $results = $this->model->query('stock')->find(array('unlimit' => 1));

        // echo '<pre>';
        // print_r($results);
        // exit;

        $this->view->setData('datalist', $results['items']);

        $dateStr = '';
        if (isset($_REQUEST['period_start']) && isset($_REQUEST['period_end'])) {
            $dateStr = ': '.$this->fn->q('time')->str_event_date($_REQUEST['period_start'], $_REQUEST['period_end'], true, 'en');
        }

        $this->view->setData('pages', array(
            // 'format' => 'A4-L',
            'title' => 'Stock Balance'.$dateStr,
            'margin_left' => 5,
        ));

        $this->view->setData('section', 'stock');

        $this->view->render('display');
    }

    public function pay_check($id = null)
    {
        $item = $this->model->query('paycheck')->get($id, array('items' => true));
        if (empty($item)) {
            $this->error();
        }

        $title = 'Pay Check';
        
        $this->view->setData('title', $title);
        $this->view->setData('item', $item);
        $this->view->render('check', array(), true);
    }
}
