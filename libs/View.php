<?php

class View {

    // protected $_data = array();
    public $page = array(
        'format' => 'default',
        'title' => '',
        'on' => '',

        'theme' => 'default',
        'theme_options' => array(
            'has_topbar' => true,
            'has_footer' => true
        ),

        'head' => null,
        'elem' => array(),
        'data' => array(),

        'navigation' => array(),
    );

    function __construct() {

        $this->fn = new _function();
        $this->ui = new uiElement();
        
        $this->lang = new Langs();
        $this->elem('html')->attr('lang', $this->lang->getCode() );
    }

    public function setData($key, $value) {
        $this->page['data'][$key] = $value;
    }
    public function setPage($key, $value, $value2=null) {

        if( !empty($this->page[ $key ]) ){
            if( is_array($this->page[ $key ]) && is_array($value) ){
                $this->page[ $key ] = array_merge($this->page[ $key ], $value);
            }elseif( $value2!=null ){
                $this->page[ $key ][ $value ] = $value2;
            }
            else{
                $this->page[ $key ] = $value;
            }
        }
        else{
            $this->page[ $key ] = $value;
        }
    }
    public function getPage($key) {
        return isset( $this->page[ $key ] ) ? $this->page[ $key ]:'';
    }
    public function setPageOptions($key, $value=true){
        $this->pageOptions[$key] = $value;
    }

    public function render($name, $data=array(), $include=false){
        if( is_array($data) && !empty($data) ){
            foreach ($data as $key => $value) {
                $this->setData($key, $value);
            }
        }

        if ( $include==true || $this->format==="json" ) {
            if( !empty($this->page['data']) ){
                foreach ($this->page['data'] as $key => $value) {
                    $this->{$key} = $value;
                }
            }

            $path = $this->getPage('path');
            if( !empty($path)  ){
                require 'views/'. $path .'/' . $name . '.php';
            }
            else{
                require 'views/Themes/'. $this->getPage('theme') .'/pages/' . $name . '.php';
            }

        } else {
            $theme = new Theme();
            $theme->page = $this->page;
            $theme->init( $this->getPage('theme'), $name );
        }
    }

    /**/
    /* Elem : */
    /**/
    // protected $_elem = array();
    private $_currentElem = null;
    // elem
    public function elem($elem=null){
        $this->_currentElem = $elem;
        $this->page['elem'][] = $elem;
        return $this;
    }
    public function addClass($class){
        $this->attr('class', $class);

        return $this;
    }
    public function hasClass($class){

        $_currentClass = isset($this->page['elem'][$this->_currentElem]['attr']['class'])
            ? $this->page['elem'][$this->_currentElem]['attr']['class']
            : null;

        if( $_currentClass )
        {
            if(in_array($class, explode(" ", $_currentClass)))
                return true;
            else
                return false;
        }
        else return false;
    }

    // attributes
    public function attr($attr=null, $value=null){

        if($attr){
            if(is_string($attr)){

                if( $value ){

                    if( isset($this->page['elem'][$this->_currentElem]['attr'][$attr]) ){

                        $this->page['elem'][$this->_currentElem]['attr'][$attr] .= ( $this->hasClass($attr) )
                            ? ""
                            : " ".$value;
                    }
                    else{
                        $this->page['elem'][$this->_currentElem]['attr'][$attr] = $value;
                    }

                    return $this;
                }
                else{


                    if( isset($this->page['elem'][$this->_currentElem]['attr'][$attr]) )
                    return $this->page['elem'][$this->_currentElem]['attr'][$attr];

                }

            }
            elseif(is_array($attr)){
                $this->page['elem'][$this->_currentElem]['attr'] = $attr;
                return $this;
            }
        }else{
            if( isset($this->page['elem'][$this->_currentElem]['attr']) )
            return $this->page['elem'][$this->_currentElem]['attr'];
        }
    }

    /**/
    /* Head : */
    /**/
    private $_head = null;
    public function css($link, $host=false){
        $this->page['head']['css'][] = array('name'=>$link, 'host'=>$host);
        return $this;
    }
    public function js($src, $host=false){
        $this->page['head']['js'][] = array('name'=>$src, 'host'=>$host);
        return $this;
    }
    public function style($cls, $opr='', $val='', $_t='.') {
        if( is_numeric($val) ){
            $val .='px';
        }

        if( $opr=='' ){
            $this->page['head']['style'][] = $cls;
        }
        else{
            $this->page['head']['style'][] = $_t.$cls.'{'. $opr .':'. $val .'}';
        }

        
        return $this;
    }
    public function head( $name ){

        $elem = "";
        if(isset($this->page['head'][$name]) && $name!='style'){

            
            for ($i=count($this->page['head'][$name])-1; $i>=0; $i--) {

                if( $name=='css' ){
                    $href = !$this->page['head'][$name][$i]['host']? CSS . $this->page['head'][$name][$i]['name'] . '.css': $this->page['head'][$name][$i]['name'];
                    $elem .='<link rel="stylesheet" type="text/css" href="'.$href.'" />';
                }

                if( $name=='js' ){
                    $src = !$this->page['head'][$name][$i]['host']? JS . $this->page['head'][$name][$i]['name'] . '.js': $this->page['head'][$name][$i]['name'];
                    $elem .='<script type="text/javascript" src="'.$src.'"></script>';
                }

                
            }

        }

        if(isset($this->page['head']['style']) && $name=='style' ){
            
            $elem.='<style type="text/css">';
            for ($i=0; $i <count($this->page['head']['style']) ; $i++) { 
                $elem.=$this->page['head']['style'][$i];
            }
            $elem.='</style>';
        }

        return $elem;
    }

}
