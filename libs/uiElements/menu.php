<?

class Menu{

	private $_menu = array();
	private $_currentItem = 0;
	private $_selected = null;
	
	function item($name=""){
		$this->_currentItem = count($this->_menu);
		$this->_menu[$this->_currentItem]['text'] = $name;
		return $this;
	}
	
	function value($value=null){
		$this->_menu[$this->_currentItem]['value'] = $value;
		return $this;
	}

	function divider(){
		$this->_menu[$this->_currentItem]['divider'] = true;
		return $this;
	}

	function link($url=null){
		$this->_menu[$this->_currentItem]['url'] = $url;
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
	
	private function _scanMenu(){
	
		foreach($this->_menu as $key=>$value){
			
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
		return $this;
	}
	
	function _getMenuHtml(){
		$this->_scanMenu();
		
		$menuItem = ""; $title = "";
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
				
			$menuItem .= '<li class="menuItem"><a class="itemAnchor" href="'.$url.'"><span class="itemLabel">'.$value['text'].'</span></a></li>';

			if(!empty($value['divider'])){
				$menuItem .= '<li class="menuItemDivider" role="separator"></li>';
			}
		}
		
		$this->_selected = !empty( $title )
			? $this->_selected
			: $this->_selected;
			
		return $menuItem;
	}
	
	function html(){
		
		return '<ul role="menu" class="uiMenu">'. $this->_getMenuHtml().'</ul>';
	}
	
}
?>