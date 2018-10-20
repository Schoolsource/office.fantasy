<?php

/* fn.pageNav
	option: {
	        selected: "",
	        addClass: "",
	        icon: "",
	        text: "",
	        notify_count: "",
	        attr: ""
	        parent = {
	            addClass: ""
	        },

	        // sub 
	        children = {
	            selected: "",
	            addClass: "",
	            icon: "",
	            text: "",
	            notify_count: ""
	        }
	    }

*/

class Navigations extends uiElement {
	
	private $_currentItem = "master";
	private $_children = null;
    private $_data = array();
    private $_type = "";
    private $_master = array();
    private $_active = null;

	// Type
	public function type($type){
		$this->addClass("uiNav-".$type);
		$this->attr('role',"navigation");
		$this->_type = $type;
		return $this;
	}

	public function active($i){
		$this->_active = $i;
		return $this;
	}

	public function hasClass($class){

        $_currentClass = isset($this->_data[$this->_currentItem]['attr']['class'])
            ? $this->_data[$this->_currentItem]['attr']['class']
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

	public function attr($name, $value=null){

		if(is_string($name)){
			if($value){
				if(isset($this->_data[$this->_currentItem]['attr'][$name])&&$name=="class")
				{
		        	$this->_data[$this->_currentItem]['attr'][$name].=($this->hasClass($name))
		            	? ""
		                : " ".$value;
		        }
		        else{
		        	$this->_data[$this->_currentItem]['attr'][$name] = $value;
		        }

		        return $this;
		    }
		    else{
		    	if( isset($this->_data[$this->_currentItem]['attr'][$name]) )
	        		return $this->_data[$this->_currentItem]['attr'][$name];
		    }
		}elseif(is_array($name)){
            $this->_data[$this->_currentItem]['attr'] = $name;
            return $this;
		}
	}

	public function parent(){
		$this->_children = $this->_currentItem;
		$this->_currentItem = "parent"; // = true;
		return $this;
	}

	public function addClass($class){
        $this->attr('class', $class);
        return $this;
    }

    public function append($text){
    	$this->_data[$this->_currentItem]['append'] = $text;
    	return $this;
    }

	public function item($name){

		if( !empty($this->_data) && $name){

			if( !empty($this->_data["master"])){
				$this->_master = $this->_data['master'];
				unset($this->_data["master"]);
			}

			if( !empty($this->_data["parent"])){
				$this->_data[$this->_children]["parent"] = $this->_data["parent"];
				unset($this->_data["parent"]);
			}

		}

		$this->_data[$name] = array();
        $this->_currentItem = $name;
		return $this;
	}

	public function text($text){
		$this->_data[$this->_currentItem]['text'] = $text;
		return $this;
	}

	public function icon($icon=null){
		$this->_data[$this->_currentItem]['icon'] = $icon;
		return $this;
	}


	public function link($link=null){
		$this->attr('href', $link);
		return $this;
	}
	// $this->attr('href', $link);

	public function notify($val){
		if($val>0){
			$this->attr('class', 'hasCount');
		}
		$this->_data[$this->_currentItem]['notify'] = $val;
		return $this;
	}

	public function reset(){

		if( !empty($this->_data["parent"])){
			$this->_data[$this->_children]["parent"] = $this->_data["parent"];
			unset($this->_data["parent"]);
		}

		$this->_currentItem="master";
		$this->_children=null;
	}

	public function html($fieldName = false) {
		
		
		$this->reset();

		// print_r($this->_data);
		$list = "";
		foreach ($this->_data as $key => $value) {

			$icon = !empty($value['icon'])? $value['icon']:"";
            $text = !empty($value['text'])? '<span class="navText">'.$value['text'].'</span>':"";
            $notify = '<span class="countWrapper"><span class="countValue">'.(!empty($value['notify'])?$value['notify']:0).'</span></span>';

            $append = !empty($value['append'])? $value['append']: "";

            

            if( $key == $this->_active){
           	
            	if( isset($value['parent']['attr']['class']) ){
            		$value['parent']['attr']['class'] .= " active";
            	}
            	else{
            		$value['parent']['attr']['class'] = "active";
            	}
            }
            
            $parentAttr=(isset($value['parent']['attr']))
           		? $this->getAttr($value['parent']['attr'])
           		: "";
           	

        	$attr=(isset($value['attr']))
            	? $this->getAttr($value['attr'])
           		: "";
            
			$list.='<li'.$parentAttr.'><a'.$attr.'>'.
				$notify.$icon.$text.'</a>'.$append.'</li>';
		}
		
		$attr=(isset($this->_master['attr']))
            	? $this->getAttr($this->_master['attr'])
           		: "";

	    $this->reboot();

		return '<ul'.$attr.'>'.$list.'</ul>';
	}

	private function reboot(){
		$this->_currentItem = "master";
		$this->_children = null;
	   	$this->_data = array();
	    $this->_type = "";
	    $this->_master = array();
	}

}

?>