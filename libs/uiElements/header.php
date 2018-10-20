<?php

class Header extends uiElement{

	private $_actions = null;
	private $_title = "";
	private $_currentData = null;
	private $_data = null;

	public function _config($text, $return=false, $option=null){

		$this->_currentData = "header";
		$this->addClass('uiHeader');

		if( $option ){
			foreach ($option as $key => $value) {
				$this->_currentData = $key;

				foreach ($value as $event => $name) {
					$this->{$event}($name);
				}
			}
		}

		$this->title( $text );

		if($return)
			return $this->html();
		else
			return $this;
	}

	public function addClass($name){
		$this->attr('class', $name);
		return $this;
	}

	public function attr($name, $value=null){
		$this->_data[$this->_currentData] = $this->attribute( $name, $value, $this->_data[$this->_currentData] );
		return $this;
	}

	public function title($text=null){
		$this->_title = $text;
		return $this;
	}

	public function action($item){
		$this->_actions[] = $item;
		return $this;
	}

	public function html(){

		// action 
		$action_str = !empty($this->_actions)
			? '<div class="rfloat">'. $this->actions(). '</div>'
			: "";

		$str = '<div'.$this->getAttr( isset($this->_data['header']['attr'])?$this->_data['header']['attr']:"" ).'><div class="clearfix">'.

			$action_str.

			'<h2 class="lfloat uiHeaderTitle">'.$this->_title.'</h2>'.

		'</div></div>';

		return $str;
	}

	// uiToolbarContent
	public function actions(){
		$item = "";
		foreach ($this->_actions as $text) {
			$item.='<li class="uiHeaderAction">'.$text.'</li>';
		}

		return '<ul class="uiHeaderActions">'.$item.'</ul>';
	}
}