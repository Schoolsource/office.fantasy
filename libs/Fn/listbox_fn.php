<?php

class Listbox_Fn extends _function
{

	public function ul_anchor($data, $options=array(), $item_options=array()) {

		$options = array_merge( array(
			'addClass' => 'ui-lists'
		), $options);

		$cls = !empty($options['addClass']) ? ' class="'.$options['addClass'].'"':'';

		$li = '';
		foreach ($data as $key => $value) {
			$li .= $this->li_anchor( $value, $item_options );
		}

		return '<ul'.$cls.'">'.$li.'</ul>';
	}

	public function li_anchor($data, $options=array() ){

		$options = array_merge(array(
			'icon' => '',
			'addClass' => 'ui-item',
			'size' => ''
		), $options);

		$anchorCls = '';
		// is_array('ui-bucketed',explode(' ', $options['addClass'])) || 
		// 
		if( !empty($options['size']) ){
			$anchorCls = ' anchor'.$options['size'];
		}

		$cls = !empty($options['addClass']) ? ' class="'.$options['addClass'].'"':'';

		$li = '<li'.$cls.'><div class="anchor'.$anchorCls.' clearfix">'.
	        
	        '<div class="avatar lfloat no-avatar mrm"><div class="initials"><i class="icon-user"></i></div></div>'.
	        
	        '<div class="content"><div class="spacer"></div><div class="massages">'.

	            (!empty($data['text'])?'<div class="text">'.$data['text'].'</div>':'').
	            (!empty($data['subtext'])?'<div class="subtext">'.$data['subtext'].'</div>':'').
	            (!empty($data['category'])?'<div class="category">'.$data['category'].'</div>':'').
	            (!empty($data['meta'])?'<div class="meta">'.$data['meta'].'</div>':'').
	            
	        '</div></div>'.
	    '</div></li>';

	    return $li;
	}
}