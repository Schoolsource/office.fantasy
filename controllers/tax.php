<?php

class Tax extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->setPage('on', 'tax');
        $this->view->setPage('title', 'VAT BUY');
        $categories = $this->model->category();

        if ($this->format == 'json') {
            $results = $this->model->lists();
            $this->view->setData('categories', $this->formatCategory($categories));
            $this->view->setData('results', $results);
            $render = 'tax/lists/json';
        } else {
            $this->view->setData('credit', $this->model->credit());
            $this->view->setData('category', $categories);

            $render = 'tax/lists/display';
        }

        $this->view->render($render);
    }

    public function add()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setData('credit', $this->model->credit());
        $this->view->setData('category', $this->model->category());
        $this->view->setData('supplier', $this->model->query('suppliers')->lists(array('unlimit' => true, 'sort' => 'code', 'dir' => 'ASC')));
        $this->view->setPage('path', 'Themes/manage/forms/vat/buy');
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
        $this->view->setData('credit', $this->model->credit());
        $this->view->setData('category', $this->model->category());
        $this->view->setData('supplier', $this->model->query('suppliers')->lists(array('unlimit' => true, 'sort' => 'code', 'dir' => 'ASC')));
        $this->view->setPage('path', 'Themes/manage/forms/vat/buy');
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
            $form->post('tax_date')
                    ->post('tax_credit')->val('is_empty')
                    ->post('tax_category_id')
                    ->post('tax_slipt')->val('is_empty')
                    ->post('tax_desc')->val('is_empty')
                    ->post('tax_sup_id')->val('is_empty')
                    ->post('tax_total')->val('is_empty')
                    ->post('tax_vat');
            $form->submit();
            $postData = $form->fetch();

            $postData['tax_is_report'] = !empty($_POST['tax_is_report']) ? 1 : 0;

            $supplier = $this->model->query('suppliers')->get($postData['tax_sup_id']);
            $postData['tax_sup_code'] = $supplier['code'];
            $postData['tax_sup_name'] = $supplier['name'];

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->update($id, $postData);
                } else {
                    $postData['tax_user_id'] = $this->me['id'];
                    $this->model->insert($postData);
                }

                $arr['message'] = 'Saved !';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
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
                $arr['message'] = 'Deleted !';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'Error : This can not delete by SYSTEM';
            }

            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/vat/buy');
            $this->view->render('del');
        }
    }

    public function add_category()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $this->view->setPage('path', 'Themes/manage/forms/vat/category');
        $this->view->render('add');
    }

    public function edit_category($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->category($id);
        if (empty($item)) {
            $this->error();
        }

        $this->view->setData('item', $item);
        $this->view->setPage('path', 'Themes/manage/forms/vat/category');
        $this->view->render('add');
    }

    public function save_category()
    {
        if (empty($_POST)) {
            $this->error();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (!empty($id)) {
            $item = $this->model->category($id);
            if (empty($item)) {
                $this->error();
            }
        }

        try {
            $form = new Form();
            $form->post('category_name')->val('is_empty');
            $form->submit();
            $postData = $form->fetch();

            $has_name = true;
            if (!empty($item)) {
                if ($item['name'] == $postData['category_name']) {
                    $has_name = false;
                }
            }
            if ($this->model->is_category($postData['category_name']) && $has_name) {
                $arr['error']['category_name'] = 'This name have already !';
            }

            if (empty($arr['error'])) {
                if (!empty($id)) {
                    $this->model->updateCategory($id, $postData);
                } else {
                    $this->model->insertCategory($postData);
                }

                $arr['message'] = 'Saved !';
                $arr['url'] = 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }
        echo json_encode($arr);
    }

    public function del_category($id = null)
    {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (empty($id) || empty($this->me) || $this->format != 'json') {
            $this->error();
        }

        $item = $this->model->category($id);
        if (empty($item)) {
            $this->error();
        }

        if (!empty($_POST)) {
            if (!empty($item['permit']['del'])) {
                $this->model->deleteCategory($id);
                $arr['message'] = 'Deleted !';
                $arr['url'] = 'refresh';
            } else {
                $arr['message'] = 'Error : This can not delete by SYSTEM';
            }
            echo json_encode($arr);
        } else {
            $this->view->setData('item', $item);
            $this->view->setPage('path', 'Themes/manage/forms/vat/category');
            $this->view->render('del');
        }
    }

    public function plate()
    {
        if (empty($this->me) || $this->format != 'json') {
            $this->error();
        }
        $this->view->setPage('path', 'Themes/manage/forms/vat/buy');
        $this->view->render('print');
    }

    private function formatCategory($categories)
    {
        $cat = [];
        foreach ($categories as $category) {
            $cat[$category['id']] = $category['name'];
        }

        return $cat;
    }
}
