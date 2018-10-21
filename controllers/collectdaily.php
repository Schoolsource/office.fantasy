<?php

class Collectdaily extends Controller{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $this->view->setPage('title', 'Daily collect');
        $this->view->setPage('on', 'dailycollect');
        $this->view->setData('getURL', 'dailycollect');
      
        $month = isset($_REQUEST['month']) ? sprintf('%02d', $_REQUEST['month']) : null;
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;
        $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] : null;

        $this->view->setData('sales', $this->model->query('payments')->sales());

        // $start = date("Y-m-d", strtotime("{$year}-{$month}-01"));
        // $end = date("Y-m-t", strtotime($start));

        if ($this->format != 'json') {
            $render = 'dailycollect/lists/display';
        } else {
            $options = array(
                'process' => 3,
                'month' => $month,
                'year' => $year,
                'sale' => $sale,
            );
            
            $results = $this->model->collect_daily($options);
            $this->view->setData('results', $results);
            $render = 'dailycollect/lists/json';
           
        }
        
        $this->view->render($render);
    
    }
    // add list of collection daily
    public function save($data=""){
       if(empty($_POST)){
           $this->error();
       }
      try {
        $query = $this->model->save($_POST['input']);
      }catch (Exception $e) {
        $arr['error'] = $e->getMessage();
    
        }
        $arr['message'] = 'Saved !';
        $arr['url'] = 'refresh';
        echo json_encode($arr);
    }


}