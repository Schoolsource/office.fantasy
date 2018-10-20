<?php

class Reports extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function comission()
    {
        $this->view->setPage('on', 'comission');
        $this->view->setPage('title', 'รายการค่าคอมมิชชั่น');

        $month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date('m');
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');

        $start = date('Y-m-d', strtotime("{$year}-{$month}-01"));
        $end = date('Y-m-t', strtotime($start));

        $this->view->setData('results', $this->model->summaryComission($start, $end));

        $monthStr = $this->fn->q('time')->month($month, true);
        $this->view->setData('period', "{$monthStr} {$year}");

        if (isset($_GET['main'])) {
            $render = 'reports/comission/sections/main';
        } else {
            $render = 'reports/comission/display';
        }

        $this->view->render($render);
    }

    public function showComission($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $sale = $this->model->query('payments')->getSale($id);
        if (empty($sale)) {
            $this->error();
        }

        $month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date('m');
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');

        $start = date('Y-m-d', strtotime("{$year}-{$month}-01"));
        $end = date('Y-m-t', strtotime($start));

        $options = array(
            'period_start' => $start,
            'period_end' => $end,
            'sale' => $id,
            'unlimit' => true,
        );
        $item = $this->model->query('payments')->lists($options);

        $this->view->setData('sale', $sale);
        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Forms/reports');
        $this->view->render('comission');
    }

    public function revenue()
    {
        $this->view->setPage('on', 'revenue');
        $this->view->setPage('title', 'รายงานรายรับ');

        $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');
        $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] : '';
        $term_of_payment = isset($_REQUEST['term_of_payment']) ? $_REQUEST['term_of_payment'] : '';

        $this->view->setData('start', $start);
        $this->view->setData('end', $end);
        $this->view->setData('sale', $sale);
        $this->view->setData('term', $term_of_payment);

        $this->view->setData('periodStr', $this->fn->q('time')->str_event_date($start, $end).' '.date('Y', strtotime($end)));

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
        $this->view->setData('sales', $this->model->query('payments')->sales());
        $this->view->setData('term_of_payment', $this->model->query('orders')->term_of_payment());
        // echo '<pre>';
        // print_r($results);
        // exit;

        if (empty($_GET['main'])) {
            $this->view->render('reports/revenue/display');
        } else {
            $this->view->render('reports/revenue/sections/order-lists');
        }
    }

    public function revenue_total()
    {
        $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');

        $this->view->setData('periodStr', $this->fn->q('time')->str_event_date($start, $end).' '.date('Y', strtotime($end)));

        $results = $this->model->summaryRevenu($start, $end);
        $this->view->setData('results', $results);

        $this->view->render('reports/revenue/sections/order-total');
    }

    public function showDue($id = null)
    {
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->query('sales')->get($id);
        if (empty($item)) {
            $this->error();
        }

        $options = array(
            'sale' => $item['sale_code'],
            'process' => 3,
        );

        $results = $this->model->sale_due($options);
        $this->view->setData('results', $results);
        $this->view->setPage('path', 'Forms/reports');
        $this->view->render('sale_due');
    }

    public function vatsale()
    {
        $this->view->setPage('on', 'vatsale');
        $this->view->setPage('title', 'รายงาน VAT SALE');

        $start = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end   = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');
        $term_of_payment = isset($_REQUEST['term_of_payment']) ? $_REQUEST['term_of_payment'] : '';

        $this->view->setData('term_of_payment', $this->model->query('bills')->term_of_payment());   

        if ($this->format == 'json') {
            $options = array(
                'period_start' => $start,
                'period_end' => $end,
                'term_of_payment' => $term_of_payment,
                'unlimit' => true,
                'dir' => 'ASC',
            );

            $results = $this->model->query('bills')->lists($options);

            $this->view->setData('results', $results);
            $render = 'reports/vat/json/sale';
        } else {
            $render = 'reports/vat/sale';
        }
        $this->view->render($render);
    }

    public function vatbuy()
    {
        $this->view->setPage('on', 'vatbuy');
        $this->view->setPage('title', 'รายงาน VAT BUY');

        $start  = isset($_REQUEST['period_start']) ? $_REQUEST['period_start'] : date('Y-m-d');
        $end    = isset($_REQUEST['period_end']) ? $_REQUEST['period_end'] : date('Y-m-d');
        $credit = isset($_REQUEST['credit']) ? $_REQUEST['credit'] : '';
        $category = isset($_REQUEST['category']) ? $_REQUEST['category'] : '';
        $report = isset($_REQUEST['report']) ? $_REQUEST['report'] : '';

        $this->view->setData('credit', $this->model->query('tax')->credit());
        $this->view->setData('category', $this->model->query('tax')->category());

        if ($this->format == 'json') {
            $options = array(
                'period_start' => $start,
                'period_end' => $end,
                'report' => $report,
                'credit' => $credit,
                'category' => $category,
                'unlimit' => true,
                'dir' => 'ASC',
            );
            $results = $this->model->query('tax')->lists($options);
            $this->view->setData('results', $results);
            $render = 'reports/vat/json/buy';
        } else {
            $render = 'reports/vat/buy';
        }
        $this->view->render($render);
    }

    public function due()
    {
        $this->view->setPage('on', 'due');
        $this->view->setPage('title', 'Debtor Report');

        $month = isset($_REQUEST['month']) ? sprintf('%02d', $_REQUEST['month']) : null;
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;
        $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] : null;

        $this->view->setData('sales', $this->model->query('payments')->sales());

        // $start = date("Y-m-d", strtotime("{$year}-{$month}-01"));
        // $end = date("Y-m-t", strtotime($start));

        if ($this->format != 'json') {
            $render = 'reports/sales/due';
        } else {
            $options = array(
                'process' => 3,
                'month' => $month,
                'year' => $year,
                'sale' => $sale,
            );

            $results = $this->model->sale_due($options);
            $this->view->setData('results', $results);
            $render = 'reports/sales/json/due';
        }
        $this->view->render($render);
    }

    public function project()
    {
        $this->view->setPage('on', 'project');
        $this->view->setPage('title', 'Project Report');

        $project = isset($_REQUEST['project']) ? $_REQUEST['project'] : null;
        $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] : null;

        // $start = date("Y-m-d", strtotime("{$year}-{$month}-01"));
        // $end = date("Y-m-t", strtotime($start));

        if ($this->format != 'json') {
            $this->view->setData('projects', $this->model->query('orders')->projects());
            $this->view->setData('sales', $this->model->query('payments')->sales());
            $render = 'reports/project/display';
        } else {
            $options = array(
                'process' => 3,
                'project' => $project,
                'sale' => $sale,
            );

            $results = $this->model->projects($options);

            // echo '<pre>';
            // print_r($results);
            // exit;
            $this->view->setData('results', $results);

            $render = 'reports/project/list';
        }
        $this->view->render($render);
    }
}
