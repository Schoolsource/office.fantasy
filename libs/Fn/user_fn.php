<?php

class User_Fn extends _function
{
	/* 
		options = {
			display: The item, List all(Default)
		}
	*/
	private $_options = null;

	private $_currentItem = null;
	private $_outer = true;
	private $_data = null;

	public function _config($current, $result){
		$this->_outer = false;
		$this->_currentItem = $current;
		$this->_data[$this->_currentItem]['result'] = $result;

		return $this;
	}

	public function display($display){
		$this->_data[$this->_currentItem]['display'] = $display;
		return $this;
	}

	public function _sample(){

		return "sample: fun User";
	}

	// return: type html
	public function html(){
		return $this->{$this->_currentItem}( $this->_data[$this->_currentItem]['result'] );
	}

	// public
	public function listbox($result=null){
		$item = "";
		if(!$result){
			$btnEmpty = "";
			$item .='<li class="emptyAccount"><div class="textEmpty">ไม่มีสมาชิก!</div>'.$btnEmpty.'</li>';
		}else{

		    foreach ($result as $key => $value) {

		        $control = '<div class="actions">'.
		            '<a class="action_checked js-checkmark"><i class="icon-tumblr-checkmark lfloat"></i></a>'.
		        '</div>';

		        
		        if(isset($value['display'])){
		        	$disabled = $value['display']=="disabled"? " disabled":"";
		        }else{
		        	$disabled = "disabled";
		        }


		        $item .= '<li class="uiListItem uid_'.$value['user_id'].$disabled.'" data-user-id="'.$value['user_id'].'" data-group-id="'.$value['group_id'].'">'.

		            '<div class="casingTop"></div><div class="casingRight"></div><div class="casingBottom"></div><div class="casingLeft"></div>'.

		            '<div class="clearfix">'.
		            	'<div class="avatar lfloat">'._function::avatar($value['avatar'], 80).'</div>'.
		                '<div class="content"><div class="spacer"></div>'.
		                    '<div class="messages">'.
		                    	// '<div class="header">'.
		                            '<div class="fullname">'.$value['fullname'].'</div>'.
		                        // '</div>'.
		                        '<div class="fwn fcg">'.$value['username'].'</div>'.
		                        // '<div class="fwn fcg">กลุ่ม'.$value['group_name'].'</div>'.
		                   	'</div>'.
		                        
		                '</div>'.
		            '</div>'.
		            
		            '<div class="noit-status" title="สถานะ: ปิดการใช้งาน"><i class="img icon-lock"></i><span class="text">สถานะ: ปิดการใช้งาน</span></div>'.
		            
		            $control.

		        '</li>';
		    } // loop for result

		}// if result

		if($this->_outer){
			return ($this->_data[$this->_currentItem]['display'] == "item")
				? $item
				: '<ul class="uiListAccounts">'.$item.'</ul>';
		}

		else
		return '<ul class="uiListAccounts">'.$item.'</ul>';
		
	}

}