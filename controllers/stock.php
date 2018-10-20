<?php

class Stock extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->error();
    }

    public function balance()
    {
        // print_r($this->model->find()); die;

        if ($this->format == 'json') {
            // $arr = $this->model->find();
            $options = [];
            $results = $this->model->find($options);
            $this->view->setData('results', $results);

            $this->view->render('stock/balance/lists/json');

        /*header('Content-Type: application/json');
            echo json_encode($arr);*/
        } else {
            // $category = $this->model->query('products')->query('categories')->lists();
            $category = $this->model->query('categories')->listByName();
            $this->view->setData('categoryLists', $category);

            $this->view->render('stock/balance/lists/display');
        }
    }
}
