<?php

class Step extends uiElement{

	private $_data = array();
	private $keys = -1;

    public function __construct()
    {
        parent::__construct();

        $this->reset();
    }

    private function setupDefault()
    {
    	$this->addClass('uiStepList clearfix');
    }

    public function reset()
    {
    	$this->current = 'default';
    	$this->_data = array();
    	$this->keys = -1;

    	$this->setupDefault();
    }

    public function style($style='')
    {
    	switch ($style) {
    		case 'singleLine':
    			$this->addClass('uiStepListSingleLine');
    			break;
    	}

    	return $this;
    }

    public function item( $name = '' )
    {
    	$this->current = 'item';
    	$this->keys++;

    	$this->addClass('uiStep'); //
    	if( $this->keys == 0){
    		$this->addClass('uiStepFirst');
    	}

    	if( !empty($name) ){
    		$this->_data['items'][$this->keys]['key'] = $name;
    	}
    	return $this;
    }

    public function text($str='')
    {
    	$this->_data['items'][$this->keys]['text'] = $str;
    	return $this;
    }

    public function selected()
    {
    	$this->addClass('uiStepSelected');
    	return $this;
    }

    public function link( $url )
    {
    	$this->current = 'link';
    	$this->_data['items'][$this->keys]['link'] = array();

    	$this->attr('href',$url);
    	return $this;
    }

    public function fetch()
    {
    	// $this->addClass('uiStepLast');

    	$partback = '<div class="part back"><span class="arrowBorder"></span><span class="arrow"></span></div>';
    	$partpoint = '<div class="part point"><span class="arrowBorder"></span><span class="arrow"></span></div>';

    	// print_r($this->_data);

    	$items = '';
        foreach ($this->_data['items'] as $key => $value) {

        	$tag = 'div';
        	$tag_atts = "";
        	$attr = !empty($value['attr']) ? $value['attr']: null;
        	if( !empty($value['link']) ){

	    		if( !$this->hasClass( 'uiStepSelected', $value['attr']['class'] ) ){
					$tag = 'a';

					$tag_atts = $this->getAttr( $value['link']['attr'] );
	    		}

        	}

        	if( count($this->_data['items'])==($key+1) ){ 
        		$this->setAttr('class', 'uiStepLast', $value);
        	}
        	
        	$atts = $this->getAttr( $value['attr'] );
        	$items .= "<li{$atts}>{$partback}".

        		'<'.$tag.$tag_atts.' class="part middle"><div class="content">'.
					'<span class="title">'.$value['text'].'</span>'.
				'</div></'.$tag.'>'.

        	"{$partpoint}</li>";
        }

        $atts = !empty($this->_data['attr']) ? $this->getAttr( $this->_data['attr'] ): '';
    	return "<div{$atts}><ol>{$items}</ol></div>";
    }

    public function attr($key='', $val='')
    {
        if( is_array($key) ){
            foreach ($key as $keys => $value) {
                $this->_attr($keys,$value);
            }
        }
        else{
            $this->_attr($key,$val);
        }

        return $this;
    }

    private function _attr($key, $val)
    {
        switch ($this->current) {
            case 'default':
                $this->setAttr($key, $val, $this->_data);
                break;
            
            case 'item':
                $this->setAttr($key, $val, $this->_data['items'][$this->keys]);
                break;

            case 'link':
                $this->setAttr($key, $val, $this->_data['items'][$this->keys]['link']);
                break;
        }
    }

    public function addClass($name='')
    {
        $this->attr('class', $name);
        return $this;
    }

}