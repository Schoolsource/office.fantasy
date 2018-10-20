<?php

// uiToggle
class toggle extends uiElement {

	private $_title = null;
	private $_menu = array();
	private $_currentItem = 0;
	private $_position = null;
	private $_pointer = false;
	
	function title($title=null){
		$this->_title = $title;
		return $this;
	}

	function option($name=""){
		$this->_currentItem = count($this->_menu);
		$this->_menu[$this->_currentItem]['text'] = $name;
		$this->attr('class', "itemAnchor");
		return $this;
	}

	function link($link, $target=null){
		// $this->attr('class', $name);
		$this->_menu[$this->_currentItem]['url'] = $link;
		return $this;
	}

	function attr($name, $value=null){


        if( is_array($name) ){
            foreach ($name as $key => $value) {
                $this->_menu[$this->_currentItem] = $this->attribute( $key, $value, $this->_menu[$this->_currentItem] );
            }
        }
        else{
            $this->_menu[$this->_currentItem] = $this->attribute( $name, $value, $this->_menu[$this->_currentItem] );
        }

        return $this;
	}

	function divider(){
		$this->_menu[$this->_currentItem]['divider'] = true;
		return $this;
	}

	function value($value=null){
		$this->_menu[$this->_currentItem]['value'] = $value;
		return $this;
	}
	
	function icon($name=null){
		$this->_menu[$this->_currentItem]['icon'] = $name;
		return $this;
	}
	
	function selected(){
		$this->_menu[$this->_currentItem]['selected'] = true;
		return $this;
	}

	function addClass($name){

		$this->attr('class', $name);
		return $this;
	}
	
	// above below aboveRight belowRight 
	function position($position="left"){
		$this->_position = $position;
		return $this;
	}
	function pointer($pointer=true){
		$this->_pointer = $pointer;
		return $this;
	}
	private $_flyoutClass = "";
	function flyoutClass($name){
		$this->_flyoutClass = ' '.$name;
		return $this;
	}
	
	private function _scanMenu(){
		
		$title = "";
		foreach($this->_menu as $key=>$value){
		
			$checked = isset($value['selected'])? true:false;

			$title = ( $key==0 || $checked )
				? $value['text']
				: $title;			
			
			$this->_menu[$key] = array(
				'text'=> trim($value['text']),
                'value'=> isset($value['value']) ? $value['value']: $key,
                'url'=> isset($value['url']) ? $value['url']: "#",
                'selected'=> isset($value['selected'])? $value['selected']: null,
                'divider'=> isset($value['divider'])? $value['divider']: null,
                'attr'=>isset($value['attr'])? $value['attr']: null,
                //description: thisData.description,
                //imageSrc: thisData.imagesrc //keep it lowercase for HTML5 data-attributes
			);	
		}
		
		$this->_title = empty( $this->_title )
			? $title
			: $this->_title;
		
		return $this;
	}
	function getJson(){
		$this->_scanMenu();
		$json = array(
			'title'=>$this->_title,
			'menu'=>$this->_menu
		);
		$this->reset();

		return htmlentities(json_encode($json));
	}
	function getPluginJquey(){
		$this->_scanMenu();
		
		$options = array(
			'title' => $this->_title,
			'pointer' => $this->_pointer,
			'position' => $this->_position,
			'menu' => $this->_menu
		);

		$options = htmlentities(json_encode($options));

		$this->reset();
		return '<a data-plugins="toggle" data-options="'.$options.'"></a>';
	}
	
	function html(){
		$this->_scanMenu();
		
		// menu
		$menuItem = "";
		$title = "";
		foreach($this->_menu as $key=>$value){
			
			$checked = isset($value['selected'])? true:false;
			
			$title = ( $key==0 || $checked )
				? $value['text']
				: $title;

			$icon = isset( $value['icon'] )
				? '<i class="itemIcon"></i>'
				: "";
				
			$url = isset( $value['url'] )
				? $value['url']
				: "#";

			$attr = isset( $value['attr'] )
				? $this->getAttr($value['attr'])
				: "";

			$selected = $checked?" selected":'';
				
			$menuItem .= '<li class="menuItem'.$selected.'"><a href="'.$url.'"'.$attr.'><span class="itemLabel">'.$value['text'].'</span></a></li>';

			if(!empty($value['divider'])){
				$menuItem .= '<li class="menuItemDivider" role="separator"></li>';
			}
	
		}
		
		$position = isset( $this->_position )
			? " uiToggleFlyout".ucwords($this->_position)
			: "";
			
		$pointer = $this->_pointer
			? " uiToggleFlyoutPointer"
			: "";

		$menu = '<div class="selectorMenuWrapper uiToggleFlyout'.$pointer.$position.$this->_flyoutClass.'"><ul role="menu" class="selectorMenu uiMenu">'.$menuItem.'</ul></div><button class="hideToggler" type="button"></button>';

		$title = !empty( $this->_title )
			? $this->_title
			: $title;

		if(is_array($title)){

			$title['class'] = isset($title['class'])
				? ' class="'.$title['class'].'"'
				: "";

			$title['icon'] = isset($title['icon'])
				? '<i class="'.$title['icon'].'"></i>'
				: "";

			$title['text'] = isset($title['text'])
				? '<span class="btn-text">'.$title['text'].'</span>'
				: "";

			$title['ricon'] = isset($title['ricon'])
				? '<i class="mls '.$title['ricon'].'"></i>'
				: "";

			$title = '<a'.$title['class'].' data-plugins="toggle" aria-expanded="false" href="#">'.$title['icon'].$title['text'].$title['ricon'].'</a>';

		}
		else{
			$title = '<a class="btn" data-plugins="toggle" aria-expanded="false" href="#"><span class="btn-text">'.$title.'</span></a>';
		}
		
		$this->reset();
			
		return '<div class="uiToggle">'.$title.$menu.'</div>';
	}
	
	function reset(){
		$this->_title = "";
		$this->_menu = array();
		$this->_currentItem = 0;
		$this->_position = null;
		$this->_pointer = false;
	}
	
}

?>