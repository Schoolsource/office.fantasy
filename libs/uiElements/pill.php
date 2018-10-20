<?php

class pill extends uiElement{

	private $_currentItem = null;
	private $_data = null;

	public function _init(){
		$this->_currentItem = "parent";
		$this->attr('class', "uiListPill");
		return $this;	
	}

	public function attr($name, $value=null){
		$this->_data[$this->_currentItem] = $this->attribute( $name, $value, $this->_data[$this->_currentItem] );
		return $this;
	}

	public function addClass($name){
		$this->attr('class', $name);
		return $this;
	}

	public function item( $name=null ){
		$name = !$name
			? count( $this->_currentItem )
			: $name;

		$this->_currentItem = $name;
		$this->_data[$this->_currentItem]['key'] = $name;
		return $this;
	}

	public function text($text){
		$this->_data[$this->_currentItem]['text'] = $text;
		return $this;
	}

	public function link($url){
		$this->attr('href', $url);
		$this->_data[$this->_currentItem]['url'] = $url;
		return $this;
	}

	public function selected($key=null){

		if($key){
			if($key==$this->_currentItem){
				$this->attr('class', 'selected');
				$this->_data[$this->_currentItem]['selected'] = true;
			}
		}
		else{
			$this->attr('class', 'selected');
			$this->_data[$this->_currentItem]['selected'] = true;
		}
		
		return $this;
	}

	public function fetch($dataType="html"){
		$item = "";
		foreach ($this->_data as $key => $value) {

			if($key=='parent'){
				$parent = $value;
				continue;
			}

			$text = isset($value['text'])
				? $value['text']
				: $value['key'];

			$item.='<li>'.
				'<a'.$this->getAttr( isset($value['attr'])?$value['attr']:"" ).'>'.$text.'</a>'.
			'</li>';
		}
		
		// $parent
		$listbox = '<ul'.$this->getAttr( isset($parent['attr'])?$parent['attr']:"" ).'>'.$item.'</ul>';

		return $listbox;
	}

	public function html(){
		echo $this->fetch("html");
	}
}