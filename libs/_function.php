<?php

class _function {

    function __construct() {
        $this->lang = new Langs();
    }

    private $_q = array();
    public function q( $query ){

        if(array_key_exists($query, $this->_q)==false){
            require_once "Fn/{$query}_fn.php";
            $_fn = $query . '_Fn';
            $this->_q[$query] = new $_fn;
        }

        return $this->_q[$query];
    }

    public function text($fn=null, $text=null){

        if(!empty($fn))
        return $this->q('text')->{$fn}($text);

        else
        return $this->q('text')->_config();        
    }

    // connect: user.fn
    public function listbox_user($result){
        return $this->q('user')->_config('listbox', $result);
    }

    // connect group.fn
    public function listbox_group($result){
        return $this->q('group')->_config('listbox', $result);
    }
    public function listbox_groupUser($result){
        return $this->q('group')->_config('listbox_user', $result);
    }

    // fun: default 
	public function stringify($data){
        return htmlentities(json_encode($data));
    }

	public function avatar($fileImage, $figSize=48, $pathfile = null, $alt = null) {
        $figSize_h = $figSize - 1;
        $img = "";
        if ($fileImage) {

            if ($pathfile) {
                $file = ROOT . DS . 'public' . DS . str_replace('/', DS, $pathfile) . DS . $fileImage;
                $src = URL . "public/" . $pathfile . '/' . $fileImage;
            } else {
                $file = ROOT . DS . 'public' . DS . 'images' . DS . 'avatar' . DS . $fileImage;
                $src = URL . "public/images/avatar/" . $fileImage;
            }
            //echo $file;
            if (file_exists($file)) {
                $editPic = '';

                $size = getimagesize($file);
                $width = $size[0];
                $height = $size[1];

                if ($width > $height) {
                    $editPic = ($width * $figSize) / $height;

                    if ($editPic > $figSize_h) {
                        $editPic -= $figSize;
                        $editPic /= 2;
                        $editPic = '-' . $editPic;
                    } else {
                        $editPic = ($figSize - $editPic) / 2;
                    }
                }

                $cls = ($width < $height) ? 'scaledImageFitWidth img' : 'img';
                $st = ($editPic != '') ? ' style="left:' . floor($editPic) . 'px"' : '';

                $alt = ($alt) ? ' alt="' . $alt . '" ' : '';

                $img .= '<img class="' . $cls . '" src="' . $src . '"' . $alt . $st . '>';
            }
        } else {
            $pic = (80 * $figSize) / 80;
            $editPic = ($figSize - $pic) / 2;
            $img .= '<img class=" img" src="' . URL . 'public/images/avatar/error/error.png" style="left:' . floor($editPic) . 'px">';
        }

        return $img;
    }

    public function addClass($class=null){

        $str = "";
        if(!empty($class)){
            if(is_array($class)){
                foreach ($class as $value) {
                    $str .= !empty($str)? " ":"";
                    $str .= $value;
                }
            }else{
                $str = $class;
            }

            $str = ' class="'.$str.'"';
        }

        return $str;
    }

    public function hiddenInput($data=array()){
        $html = "";
        foreach ($data as $key => $value) {

            $class = array();
            $class[] = "hiddenInput";
            if(!empty($value['addClass'])){
                $class[] = $value['addClass'];
            }
            $class = self::addClass($class);

            $name = "";
            if(!empty($value['name'])){
                $name = ' name="'.$value['name'].'"';
            }

            $val = "";
            if(!empty($value['value'])){
                $val = ' value="'.$value['value'].'"';
            }

            $id = "";
            if(!empty($value['id'])){
                $id = ' id="'.$value['id'].'"';
            }

            $html.='<input'.$class.' type="hidden" autocomplete="off"'.$name.$val.$id.'>';
        }

        return $html;
    }

    public function set_hiddenInput(&$hiddenInput, $data, $key=null){
        
        foreach ($data as $name=>$value) {
            if(is_array($value)){
                self::set_hiddenInput( $hiddenInput, $value, $name);
            }
            else{
                if( $key ){
                    $hiddenInput[] = array( "name"=> $key.'['.$name.']', "value"=>$value );
                }
                else{
                    $hiddenInput[] = array( "name"=>$name, "value"=>$value );
                }
            }
        }     
    }

    // stepList
    public static function stepList($lists = array(), $select=null, $show_number = true, $style_line = false) {

        $str = "";
        $i = 0;
        foreach ($lists as $key => $val) {
            $i++;
            $selected = $val['name'] == $select ? ' uiStepSelected' : '';
            $str .= '<li data-id="'.$key.'" class="uiStep' . $selected . '">' .
                    '<div class="part back"><span class="arrowBorder"></span><span class="arrow"></span></div>' .
                    '<div class="part middle"><div class="content">' .
                    (!empty($val['link']) ? '<a href="' . $val['link'] . '" class="title">' : '<span class="title">') .
                    ($show_number ? '<span class="fwb">' . $i . '.</span> ' : '' ) . $val['text'] .
                    (!empty($val['link']) ? '</a>' : '</span>') .
                    '</div></div>' .
                    '<div class="part point"><span class="arrowBorder"></span><span class="arrow"></span></div>' .
                    '</li>';
        }

        return '<div class="uiStepList' . ($style_line ? ' uiStepListSingleLine' : '') . '"><ol>' . $str . '</ol></div>';
    }

    public function getURL($options, $set=null, $val=null){

        $get = "";
        foreach ($options as $key => $value) {

            if($key==='url') continue;

            $value = $set==$key? $val: $value;

            if(isset($value)){
                $get.=empty($get)? "?":"&";
                $get.="$key=$value";
            }
        }

        return URL.$options['url']."/{$get}";
    }

    public function actionPager($options=null) {
        # pager
        $pager = array(
            'length'=>$options['pager']
        );

        $pager['prev'] = $pager['length']-1;
        $pager['prev_url'] = $this->getURL($options['geturl'], "pager", ($pager['length']-1));
        $pager['prev_btn'] = $pager['prev']<1
            ? '<a class="phs btn disabled" href="#"><i class="icon-chevron-left"></i></a>'
            : '<a class="phs btn" href="'.$pager['prev_url'].'"><i class="icon-chevron-left"></i></a>';

        $pager['next'] = $pager['length']+1;
        $pager['next_url'] = $this->getURL($options['geturl'], "pager", ($pager['length']+1));
        $pager['next_btn'] = '<a class="phs btn" href="'.$pager['next_url'].'"><i class="icon-chevron-right"></i></a>';

        $limit = $options['limit'];
        $start_limit = ($limit*$pager['length'])-$limit+1;
        $end_limit = $limit*$pager['length'];

        if($end_limit>=$options['count']){
            $end_limit = $options['count'];
            $pager['next_btn'] = '<a class="phs btn disabled" href="#"><i class="icon-chevron-right"></i></a>';
        }

        if( !empty($options['is_disabled']) ){
            $pager['prev_btn'] = '<a class="phs btn disabled" href="#"><i class="icon-chevron-left"></i></a>';
            $pager['next_btn'] = '<a class="phs btn disabled" href="#"><i class="icon-chevron-right"></i></a>';
        }

        return $options['count']!=0
            ? '<li class="r group-btn">'.
                '<span class="mhs fcg">'.$start_limit.'-'.$end_limit.' จาก '.$options['count'].'</span>'.
                $pager['prev_btn']. $pager['next_btn'].
              '</li>'
            : "";
    }
	
    // PHP 5.3-
    function birthday($birthday){ 
        $age = strtotime($birthday);
    
        if($age === false){ 
            return false; 
        } 
        
        list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
        
        $now = strtotime("now"); 
        
        list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
        
        $age = $y2 - $y1; 
        
        if((int)($m2.$d2) < (int)($m1.$d1)) 
            $age -= 1; 
            
        return $age; 
    }

    // PHP 5.3+
    /*function birthday($birthday) {
        $age = date_create($birthday)->diff(date_create('today'))->y;
        
        return $age;
    }*/

    public function spinner(){
        
        $circle = '';
        for ($i=1; $i <= 12; $i++) { 
            $circle.='<div class="sk-child sk-circle'.$i.'"></div>';
        }
        return '<div class="sk-circle">'. $circle.'</div>';
    }

    public function imageCoverBox($url, $size=851, $theSize=array(851, 315)){
        
        $width = $size;
        $height = round( ($theSize[1]*$size) /$theSize[0], 2 );
        return '<div class="avatar-cover" style="width:'.$width .'px;height:'.$height.'px"><img src="'.$url.'" /></div>';
    }
    public function imageBox($url, $size=640, $theSize=array(640, 360)){
        $width = $size;
        $height = round( ($theSize[1]*$size) /$theSize[0], 2 );
        return '<div class="avatar-cover" style="width:'.$width .'px;height:'.$height.'px"><img src="'.$url.'" /></div>';
    }


    public function manage_nav($lists=array(), $active=null){

        $li = '';
        foreach ($lists as $key => $value) {
            
            $selected = $active==$value['key'] ? ' class="active"':'';

            $icon = '';
            if( !empty($value['icon']) ){
                $icon = '<i class="icon-'.$value['icon'].'"></i>';
            }

            $target ='';
            if( isset($value['target']) ){
                $target = ' target="'.$value['target'].'"';
            }

            $li.='<li'.$selected.'><a href="'.$value['link'].'"'.$target.'>'.$icon.$value['text'].'</a></li>';
        }

        return '<ul class="navigation-list">'.$li.'</ul>';

    }

    /* USER-AGENTS
    ================================================== */
    function check_user_agent ( $type = NULL ) {
            $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
            if ( $type == 'bot' ) {
                    // matches popular bots
                    if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
                            return true;
                            // watchmouse|pingdom\.com are "uptime services"
                    }
            } else if ( $type == 'browser' ) {
                    // matches core browser types
                    if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
                            return true;
                    }
            } else if ( $type == 'mobile' ) {
                    // matches popular mobile devices that have small screens and/or touch inputs
                    // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
                    // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
                    if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
                            // these are the most common
                            return true;
                    } else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
                            // these are less common, and might not be worth checking
                            return true;
                    }
            }
            return false;
    }

    public function uTableCell($arr) {
        $str = '';
        foreach ($arr as $value) {

            $type = isset($value['type']) ? $value['type'] : 'text';
            $name = isset($value['name']) ? $value['name'] : $value['id'];
            $id = isset($value['id']) ? $value['id'] : $name;
            $id = str_replace('[', '_', $id);
            $id = str_replace(']', '', $id);
            $cls = isset($value['addClass']) ? ' '.$value['addClass'] : '';

            $attr = '';
            if( isset($value['attr']) ){
                foreach ($value['attr'] as $n => $val) {
                    // $attr .= !empty($attr) ? ' ':'';
                    $attr .= " {$n}=\"{$val}\"";
                }
            }

            if($type=='select'){

                $option = '';
                if( strpos( $value['id'], "prefix_name" ) ){
                    $option = '<option value="">-</option>';
                }

                $val = isset($value['value']) ? $value['value'] : '';
                foreach ($value['options'] as $data) {
                    $active = $val==$data['id'] ? ' selected="1"':'';
                    $option .= '<option'.$active.' value="'.$data['id'].'">'.$data['name'].'</option>';
                }
                $input = '<select id="'.$id.'" name="'.$name.'" class="inputtext'.$cls.'"'.$attr.'>'.$option.'</select>';
            }
            else{

                $val = isset($value['value']) ? ' value="'.$value['value'].'"':'';
                $input = '<input id="'.$id.'" autocomplete="off" placeholder="'.$value['label'].'" class="inputtext'.$cls.'" type="text" name="'.$name.'"'.$attr.''.$val.'>';
            }

            $str .= '<div class="u-table-cell">'.$input.'</div>';
        }

        return '<div class="u-table"><div class="u-table-row">'.$str.'</div></div>';
    }

    public function uTableAddress($data) {
        $str = '';
        foreach ($data as $rows) {

            $str .= '<table cellspacing="0" class="table-address"><tr>';
            // cell
            foreach ($rows as $cell => $value) {
                
                $type = isset($value['type']) ? $value['type'] : 'text';
                $id = isset($value['id']) ? $value['id'] : '';
                $name = isset($value['name']) ? $value['name'] : $id;
                $label = isset($value['label']) ? $value['label'] : '';
                

                if($type=='select'){
                    
                    $option = '';
                    $val = isset($value['value']) ? $value['value'] : '';
                    foreach ($value['options'] as $data) {

                        $active = $val==$data['id'] ? ' selected="1"':'';
                        
                        $option .= '<option'.$active.' value="'.$data['id'].'">'.$data['name'].'</option>';
                    }

                    $input = '<select class="inputtext" id="'.$id.'" name="'.$name.'">'.$option.'</select>';
                }
                else{

                    $val = isset($value['value']) ? ' value="'.$value['value'].'"':'';
                    $input = '<input id="'.$id.'" autocomplete="off" class="inputtext" type="text" name="'.$name.'"'.$val.'>';
                }

                $str .= '<td class="label"><label for="'.$id.'">'.$label.'</label></td>';
                $str .= '<td class="data">'.$input.'</div>';

            }

            $str .= '</tr></table>';
        }

        return $str;
    }
}