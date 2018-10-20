<?php

class Settings extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->my();
    }

    public function company($tap = 'basic')
    {
        $this->view->setPage('title', 'Setting '.ucfirst($tap));

        $this->view->setPage('on', 'settings');
        $this->view->setData('section', 'company');
        $this->view->setData('tap', 'display');
        $this->view->setData('_tap', $tap);

        // if( empty($this->permit['company']['view']) ) $this->error();
        // print_r($this->permit); die;

        if ($tap != 'basic') {
            $this->error();
        }

        if (!empty($_POST) && $this->format == 'json') {
            foreach ($_POST as $key => $value) {
                $this->model->query('system')->set($key, $value);
            }

            $arr['url'] = 'refresh';
            $arr['message'] = 'บันทึกเรียบร้อย';

            echo json_encode($arr);
        } else {
            $this->view->render('settings/display');
        }
    }

    public function my($tap = 'basic')
    {
        $this->view->setPage('title', 'Setting '.ucfirst($tap));

        $this->view->setPage('on', 'settings');
        $this->view->setData('section', 'my');
        $this->view->setData('tap', 'display');
        $this->view->setData('_tap', $tap);

        if ($tap == 'basic') {
            $this->view
            ->js(VIEW.'Themes/'.$this->view->getPage('theme').'/assets/js/bootstrap-colorpicker.min.js', true)
            ->css(VIEW.'Themes/'.$this->view->getPage('theme').'/assets/css/bootstrap-colorpicker.min.css', true);

            $this->view->setData('prefixName', $this->model->query('system')->prefixName());
        }

        $this->view->render('settings/display');
    }

    public function payments($tap = 'bank')
    {
        $this->view->setPage('title', 'Settings '.ucfirst($tap));

        $this->view->setPage('on', 'settings');
        $this->view->setData('section', 'payments');
        $this->view->setData('tap', $tap);

        if ($tap == 'bank') {
            $data = $this->model->query('payments')->bank();
        } elseif ($tap == 'type') {
            $data = $this->model->query('payments')->type();
        } elseif ($tap == 'account') {
            $data = $this->model->query('payments')->account();
        } else {
            $this->error();
        }

        $this->view->setData('data', $data);
        $this->view->render('settings/display');
    }

    public function suppliers($tap = 'type')
    {
        $this->view->setPage('title', 'Settings '.ucfirst($tap));

        $this->view->setPage('on', 'settings');
        $this->view->setData('section', 'suppliers');
        $this->view->setData('tap', $tap);

        if ($tap == 'type') {
            $data = $this->model->query('suppliers')->type();
        } elseif ($tap == 'category') {
            $data = $this->model->query('tax')->category();
        } else {
            $this->error();
        }

        $this->view->setData('data', $data);
        $this->view->render('settings/display');
    }

    public function accounts($tap = 'admins')
    {
    
        $this->view->setPage('title', 'Settings '.ucfirst($tap));

        $this->view->setPage('on', 'settings');
        $this->view->setData('section', 'accounts');
        $this->view->setData('tap', $tap);
        $render = 'settings/display';

        if ($tap == 'admins') {
            $data = array();
            if ($this->format == 'json') {
                $this->view->results = $this->model->query('admins')->lists();
               
                $render = 'settings/sections/accounts/admins/json';
            }
        } else {
            $this->error();
        }

        $this->view->setData('data', $data);
        $this->view->render($render);
    }

    public function export($tap = 'categories')
    {
        $this->view->setPage('title', 'Settings '.ucfirst($tap));

        $this->view->setPage('on', 'settings');
        $this->view->setData('section', 'export');
        $this->view->setData('tap', $tap);
        $render = 'settings/display';

        if ($tap == 'categories') {
            $data = $this->model->query('export')->category();
        }

        $this->view->setData('data', $data);
        $this->view->render($render);
    }

    public function customer($tap = 'project')
    {
        $results = $this->model->query('customers')->project->find();

        $this->view->setData('dataList', $results['items']);
        $this->view->setData('section', 'customer');
        $this->view->setData('tap', $tap);
        $this->view->render('settings/display');
    }
}
