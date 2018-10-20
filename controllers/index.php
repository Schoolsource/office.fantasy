<?php

class Index extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // header('Location: '. URL.'payments' );
        if( $this->me['type'] == 'sale' ){
            header('Location:'.URL.'mobile');
        }
        else{
           header('Location: '. URL.'payments' ); 
        }
    }

    private function _getUrl() {
        $url = isset( $_GET['url'] ) ? $_GET['url']:null;
        if( empty($url) ) $this->error();
        $url = rtrim($url, '/');
        $this->_url = explode('/', $url);
    }

    public function search($page=null) {
        $this->_getUrl();
        // $this->getPage($page);

        // $this->view->render('search');
        $this->error();
    }
}
