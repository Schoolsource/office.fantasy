<?php

class Mobile extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        header('location:'.URL.'mobile/customers');
    }

    public function customers($id = null)
    {
        $this->view->setPage('on', 'customers');
        $this->view->setPage('title', 'Customers');

        if (!empty($id)) {
            $item = $this->model->get($id);
            if (empty($item)) {
                $this->error();
            }

            $this->view->setData('category', $this->model->query('categories')->lists());
            $this->view->setData('item', $item);
            $render('customers/cart/display');
        } else {
            $key = isset($_GET['key']) ? $_GET['key'] : null;
            $options = array(
                'sale' => !empty($this->me['sale_code']) ? $this->me['sale_code'] : '',
                'q' => $key,
            );
            $results = $this->model->query('customers')->lists($options);

            $this->view->setData('topbar', array(
                'title' => array(0 => array('text' => '<i class="icon-users"></i> Customers ('.$results['total'].')'),
                ),
            ));

            if ($this->format == 'json') {
                $this->view->setData('results', $results);
                $this->view->render('customers/lists/json');
                exit;
            }
            $render = 'customers/lists/display';
        }
        $this->view->render($render);
    }

    public function orders($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;

        $this->view->setPage('on', 'orders');
        $this->view->setPage('title', 'Orders');

        if (!empty($id)) {
            $item = $this->model->query('orders')->get($id, array('items' => true));
            if (empty($item)) {
                $this->error();
            }

            $this->view->setData('topbar', array(
                'title' => array(0 => array('text' => '<i class="icon-cube"></i> Orders ('.$item['code'].')'),
                ),
                'nav' => array(
                    0 => array(
                                    // 'type' => 'link',
                        'icon' => 'icon-remove',
                                    // 'text' => 'Cancel',
                        'url' => URL.'mobile/orders',
                    ),
                ),
            ));

            $this->view->setData('item', $item);
            $render = 'orders/profile/display';
        } else {
            $options['sale'] = !empty($this->me['sale_code']) ? $this->me['sale_code'] : '';
            $results = $this->model->query('orders')->lists($options);

            $this->view->setData('topbar', array(
                'title' => array(0 => array('text' => '<i class="icon-cube"></i> Orders ('.$results['total'].')'),
                ),
            ));

            if ($this->format == 'json') {
                $this->view->setData('results', $results);
                $this->view->render('orders/lists/json');
                exit;
            }
            $render = 'orders/lists/display';
        }
        $this->view->render($render);
    }

    public function createOrder($id = null, $cate_id = null, $sub_id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $cate_id = isset($_REQUEST['cate_id']) ? $_REQUEST['cate_id'] : $cate_id;
        $sub_id = isset($_REQUEST['sub_id']) ? $_REQUEST['sub_id'] : $sub_id;
        if (empty($id) || empty($this->me)) {
            $this->error();
        }

        $customer = $this->model->query('customers')->get($id);
        if (empty($customer)) {
            $this->error();
        }
        $this->view->setData('customer', $customer);

        $item = $this->model->query('categories')->getFirst(array('show_sub' => true));
        /* if( !empty($cate_id) ){
            $item = $this->model->query('categories')->get($cate_id, array('show_sub'=>true));
        }
        if( empty($item) ) $this->error();

        if( !empty($item['sub_categories']) && empty($sub_id) ){
            $sub_id = $item['sub_categories'][0]['id'];
        }

        if( !empty($sub_id) ){
            $sub_item = $this->model->query('categories')->get($sub_id);
            $this->view->setData('sub_item', $sub_item);
        } */

        $this->view->setData('item', $item);

        $this->view->setData('topbar', array(
            'title' => array(
                0 => array(
                    'text' => '<i class="icon-user"></i> '.$customer['name_store'],
                ),
                1 => array(
                    'text' => $customer['sub_code'],
                ),
            ),
            'nav' => array(
                0 => array(
                                    // 'type' => 'link',
                    'icon' => 'icon-cart-plus',
                    'text' => '(0)',
                ),
            ),
        ));
        $category = $this->model->query('categories')->lists(array('sort' => 'seq', 'dir' => 'ASC', 'not_is_sub' => true, 'show_sub' => true));
        $this->view->setData('category', $category);

        $render = 'cart/lists/display';

        $this->view->render($render);
    }

    public function listsProducts($cate = null)
    {
        $cate = isset($_REQUEST['cate']) ? $_REQUEST['cate'] : $cate;

        $item = $this->model->query('categories')->get($cate);
        if (empty($item)) {
            $this->error();
        }

        $results = $this->model->query('products')->lists(array('category' => $cate));
        $this->view->setData('item', $item);
        $this->view->setData('results', $results);
        $this->view->render('cart/lists/sections/lists');
    }

    public function profileProducts($id = null)
    {
    }

    //SUB MENU
    public function subMenu($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $results = $this->model->query('categories')->listsSubCategories($id);

        $this->view->setData('results', $results);
        $this->view->render('cart/lists/sections/sub-category');
    }
}
