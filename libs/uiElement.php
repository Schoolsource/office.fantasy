<?php


class uiElement{
	
	private $_currentItem = null;
    private $_obj = array();

    function __construct() {}

    private function query($obj, $options=array() ){

        if(array_key_exists($obj, $this->_obj)==false){
            require_once "uiElements/{$obj}.php";
            $this->_obj[$obj] = new $obj( $options );
        }

        return $this->_obj[$obj];
    }

    public function header($title=null){
        $return = ( $title ) ? true:false;
        return $this->query("header")->_config($title,$return);
    }

    public function headerPage( $title ){
        $return = ( $title ) ? true:false;

        $option = array( 'header' => array('addClass'=>'uiHeaderPage') );
        return $this->query("header")->_config($title,$return,$option);
    }

    public function nav( $type=null ){
       return $this->query("navigations")->type($type);         
    }

    public function toggle(){
       return $this->query("toggle");    
    }

    public function menu(){
       return $this->query("menu");
    }

    public function pill(){
        return $this->query("pill")->_init();
    }

    // 
    public function boxUser( $me, $options=array() ){
        $subname = '';

        $size = '';
        if( !empty($options['size']) ) {
            $size = ' anchor'.$options['size'];
        }

        $title = $me['name'];
        if( !empty($options['title']) ){
            if( !empty($me[ $options['title'] ]) ){
                $title = $me[ $options['title'] ];
            }
            else{
                $title = $options['title'];
            }
        }

        $subtitle = "";
        if( !empty($options['subtitle']) ){
            if( !empty($me[ $options['subtitle'] ]) ){
                $subtitle = '<span class="fsm">'.$me[ $options['subtitle'] ].'</span>';
            }
        }elseif( !empty($options['sub']) ){
            $subtitle = $options['sub'];
        }

        /*( !empty($field) )
                        ? '<div class="subname">'.$me[$field].'</div>'
                        : '<div class="subname">'.$field.'</div>'
                    ).*/

        // $field="", $url="", $size=""



        return '<div class="anchor clearfix'.$size.'">'.

            '<div class="avatar lfloat mrs"><img class="img" src="'.$me['profile_image_url'].'" alt="'.$me['name'].'"></div>'.

            '<div class="content"><div class="spacer"></div>'.
                '<div class="massages">'.
                    '<div class="fullname">'.$title.'</div>'. $subtitle.
                '</div>'.
            '</div>'.

        '</div>';
    }

    public function chooseFile($name='file1', $options=array()){


        $accept = "";
        if( isset($options['accept']) ){
             $accept = ' accept="'.$options['accept'].'"';     
        }

        $subtext = "";
        if( isset($options['subtext']) ){
             $subtext = '<div class="mts fcg">'.$options['subtext'].'</div>';  
        }

        return '<div class="uiAttach" data-plugins="chooseFile"><div class="uiInlineBlock_root uiInlineBlock_middle"><a class="btn btn-choosefile"><span class="btn-text">เลือกไฟล์</span><span class="uiChooseFile"><input type="file" id="'.$name.'" name="'.$name.'"'.$accept.'/></span></a></div><div class="uiInlineBlock_root uiInlineBlock_middle pls choose-file-name"><label for="file1" data-name="ไม่ได้เลือกไฟล์"></label></div><a data-remove="1" class="uiInlineBlock_root uiInlineBlock_middle mls hidden_elem"><i title="ลบออก" class="icon-remove"></i></a>'.$subtext.'</div>';
    }

    public function glossaryTip($text, $title='',$options='')
    {
        return '<span class="glossaryTip glossaryTipQ glossaryTipFixedWidth">'.
            '<span class="point"><sup>?</sup></span>'.
            '<span class="tip right">'.
                (!empty($title) ? '<span class="tipTitle">'.$title.'</span>': "").
                '<span class="tipBody">'.$text.'</span>'.
                '<span class="tipArrow"></span>'.
            '</span>'.

        '</span>';
    }

    public function hasClass($name, $class_atr){
        
        $class_arr = explode(" ", $class_atr);
        $return = false;
        foreach ($class_arr as $key => $value) {
            if( $name === $value ){
                $return = true;
                break;
            }
        }
        return $return;
    }

    public function attribute($name, $value=null, &$currentItem){

        if(is_string($name)){
            if($value!==""){
                if(isset($currentItem['attr'][$name])&&$name=="class")
                {
                    $currentItem['attr'][$name].=($this->hasClass($value, $currentItem['attr'][$name]))
                        ? ""
                        : " {$value}";
                }
                else{
                    $currentItem['attr'][$name] = $value;
                }

                if($currentItem){
                    return $currentItem;
                }
                else{
                    return $this;
                }
               
            }
            else{
                if( isset($currentItem['attr'][$name]) )
                    return $currentItem['attr'][$name];
            }
        }elseif(is_array($name)){
            $currentItem['attr'] = $name;
            return $this;
        }
    }

    public function setAttr($name, $value='', &$currentItem){

        if( !empty($name) && ($value!='' || $value===0) ){
            
            if( isset($currentItem['attr'][$name]) ){ // &&$name=="class"

                $currentItem['attr'][$name].=($this->hasClass($value, $currentItem['attr'][$name]))
                        ? ""
                        : " {$value}";

            }
            else{
                $currentItem['attr'][$name] = $value;
            }
        }
    }

    public function getAttr( $attr = null ){
            
            if(!$attr) return "";

            $attributes = "";
            foreach ($attr as $key => $value) {
                $attributes .=" {$key}=\"{$value}\"";
            }
            return $attributes;
    }
	
    public function __call($name, $arguments) {

        if( file_exists( WWW_LIBS."uiElements/{$name}.php" ) ){
            return $this->query($name, $arguments);
        }        
        // throw new Exception("$name does not exist inside of: " . __CLASS__);
         
    }
}