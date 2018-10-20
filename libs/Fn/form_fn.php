<?php

class form_Fn extends _function{

	public function address( $data=array(), $options=null ) {

		if( $options===null )  $options = $data;

		$options = array_merge( array(
			'field_name' => 'address',
			'field_first_name' => '',
			'field_last_name' => '',
			'fields' => array()
		), $options );

		$name = !empty($options['field_name'])? $options['field_name']:'address';

		if( !empty($options['field_first_name']) ){
			$name = $options['field_first_name'].$name;
		}

		if( !empty($options['field_last_name']) ){
			$name .= $options['field_last_name'];
		}

		$city = array(
            'id' => 'address_city', 
            'name' => $name.'[city]', 
            'label' => 'จังหวัด',
            'value' => !empty($data['city']) ? $data['city']:''
        );
		if( !empty($options['city'])){
            if( is_array($options['city']) ){
            	$city['type'] = 'select';
            	$city['options'] = $options['city'];
            }
        }
        
        $fields = array( 0=> 
            array( 0 => 
                array(
                    'id' => 'address_number', 
                    'name' => $name.'[number]', 
                    'label' => 'บ้านเลขที่',
                    'value' => !empty($data['number']) ? $data['number']:''
                ),
                array(
                    'id' => 'address_mu', 
                    'name' => $name.'[mu]', 
                    'label' => 'หมู่ที่',
                    'value' => !empty($data['mu']) ? $data['mu']:''
                ),
                array(
                    'id' => 'address_village', 
                    'name' => $name.'[village]', 
                    'label' => 'หมู่บ้าน',
                    'value' => !empty($data['village']) ? $data['village']:''
                ),
                array(
                    'id' => 'address_alley', 
                    'name' => $name.'[alley]', 
                    'label' => 'ซอย',
                    'value' => !empty($data['alley']) ? $data['alley']:''
                ),
            ),

            array( 0 => 
                array(
                    'id' => 'address_street', 
                    'name' => $name.'[street]', 
                    'label' => 'ถนน',
                    'value' => !empty($data['street']) ? $data['street']:''
                ),
                array(
                    'id' => 'address_district', 
                    'name' => $name.'[district]', 
                    'label' => 'แขวง/ตำบล',
                    'value' => !empty($data['district']) ? $data['district']:''
                ),
                array(
                    'id' => 'address_amphur', 
                    'name' => $name.'[amphur]', 
                    'label' => 'เขต/อำเภอ',
                    'value' => !empty($data['amphur']) ? $data['amphur']:''
                ),
            ),

            array( 0 => 
                $city, 
                array(
                    'id' => 'address_zip', 
                    'name' => $name.'[zip]', 
                    'label' => 'รหัสไปรษณีย์',
                    'value' => !empty($data['zip']) ? $data['zip']:''
                ),
            ),
        );

        return '<div class="table-address-wrap">'.  $this->_address( $fields ) .'</div>';
	}
	private function _address($data) {
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
                    
                    $option = '<option value="">-</option>';
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

	public function fullname( $data=array(), $options=null ) {
	
		$options = array_merge( array(
			'field_first_name' => '',
			'field_last_name' => '',
			'fields' => array(),
			'prefix_name' => array()
		), $options);


		$fields = array_merge( array( 
			'prefix_name' => array('id'=>'prefix_name','label'=> $this->lang->translate('Prefix Name'),'type'=>'select', 'options'=>$this->_prefixName($options['prefix_name']), 'addClass'=>'input-prefix'), 
			'first_name'  => array('id'=>'first_name','label'=> $this->lang->translate('First Name') ), 
			'last_name'  => array('id'=>'last_name','label'=> $this->lang->translate('Last Name') ), 
			'nickname'  => array('id'=>'nickname','label'=> $this->lang->translate('Nickname') ,'addClass' => 'input-nickname')
		), $options['fields'] );

		$_fields = array();
		foreach ($fields as $key => $field) {

			if( isset($field['disabled']) || empty($field['id']) ) continue;

			if( !empty($options['field_first_name']) ){
				$field['id'] = $options['field_first_name'].$field['id'];
			}

			if( !empty($options['field_last_name']) ){
				$field['id'] .= $options['field_last_name'];
			}

			if( !empty($data[ $key ]) ){
				$field['value'] = $data[ $key ];
			}

			$_fields[] = $field;
		}

		return '<div class="u-table-wrap u-table-fullname">'. $this->uTableCell( $_fields) .'</div>';
	}
	private function _prefixName( $options=array() ) {
		
        $a['Mr.'] = array('id'=>'Mr.', 'name'=> $this->lang->translate('Mr.') );
        $a['Mrs.'] = array('id'=>'Mrs.', 'name'=> $this->lang->translate('Mrs.') );
        $a['Ms.'] = array('id'=>'Ms.', 'name'=> $this->lang->translate('Ms.') );

        return array_merge($a, $options);
	}

	public function birthday( $data=null, $options=array() ) {

		$options = array_merge( array(
			'field_first_name' => 'birthday',
			'field_last_name' => '',
			'end_year' => 18
		), $options);

		if( $data==null ) $data = $options;

		$days[] = array('id'=>'00', 'name'=> '--'.$this->lang->translate('Date').'--' );
		for ($i=1; $i <= 31; $i++) { 
			$d = $i < 10 ? "0{$i}":$i;
		    $days[] = array('id'=>$d, 'name'=> $i);
		}

		$fields[] = array( 
		    'id' => $options['field_first_name'] . '_date',
		    'name' => $options['field_first_name'] . '[date]', 
		    'label' => $this->lang->translate('Day'),
		    'type' => 'select',
		    'options' => $days,
		    'value' => !empty($data['birthday']) ? date('j', strtotime($data['birthday']) ):''
		);

		$months[] = array('id'=>'00', 'name'=> '--'.$this->lang->translate('Month').'--' );
		for ($i=1; $i <= 12; $i++) { 
			$m = $i < 10 ? "0{$i}":$i;
		    $months[] = array('id'=>$m, 'name'=> $this->q('time')->month( $i, 0, $this->lang->getCode() ));
		}
		$fields[] = array( 
		    'id' =>  $options['field_first_name'] . '_month',
		    'name' => $options['field_first_name'].'[month]', 
		    'label' => $this->lang->translate('Month'),
		    'type' => 'select',
		    'options' => $months,
		    'value' => !empty($data['birthday']) ? date('n', strtotime($data['birthday']) ):''
		);

		$years[] = array('id'=>'0000', 'name'=> '--'.$this->lang->translate('Year').'--');
		$y = date('Y') - $options['end_year'];
		$i = 1;
		do {
		    $years[] = array('id'=>$y, 'name'=>$y );
		    $y--;  $i++;
		} while ($i <= 70);

		$fields[] = array( 
		    'id' =>  $options['field_first_name'] . '_year', 
		    'name' => $options['field_first_name'].'[year]', 
		    'label' => $this->lang->translate('Year'),
		    'type' => 'select',
		    'options' => $years,
		    'value' => !empty($data['birthday']) ? date('Y', strtotime($data['birthday']) ):''
		);

		return '<div class="u-table-wrap u-table-birthday">' . $this->uTableCell($fields) .'</div>';
	}

	public function contacts($type, $data=array(), $options=array()) {
		
		$options = array_merge( array(
			'field_first_name' => '',
			'field_last_name' => '',
			'field_label' => '',
			'has_add' => true,
		), $options);

		$field_name = !empty($options['field_first_name'])? $options['field_first_name']: $type;

		if( !empty($options['field_last_name']) ){
			$field_name .= $options['field_last_name'];
		}

		$fieldset = '';
		foreach ($data as $key => $value) {
			$fieldset .= $this->_contacts( $type, $options, $field_name, $value['name'], $value['value'] );
		}

		if( empty($data) ){
			$fieldset .= $this->_contacts( $type, $options, $field_name );
		}

		return '<fieldset id="'. str_replace(array('[',']'), array('_', ''), $field_name).'_fieldset" class="form-field clearfix form-field-'.$type.'" data-plugins="formcontacts">'.
		    $fieldset.
		'</fieldset>';
	}
	public function _contact_label_email() {
		$labels = array();
		$labels[] = array('text'=> $this->lang->translate('Personal Email') );
		$labels[] = array('text'=> $this->lang->translate('Work Email') );
		$labels[] = array('text'=> $this->lang->translate('Other Email') );

		return $labels;
	}
	public function _contact_label_phone($value='') {
		$labels = array();
		$labels[] = array('text'=> $this->lang->translate('Mobile Phone') );
		$labels[] = array('text'=> $this->lang->translate('Work Phone') );
		$labels[] = array('text'=> $this->lang->translate('Home Phone') );
		$labels[] = array('text'=> $this->lang->translate('Other phone') );
		return $labels;
	}
	public function _contact_label_social($value='') {
		$labels = array();
		$labels = array();
		$labels[] = array('text'=> $this->lang->translate('Line ID') );
		$labels[] = array('text'=> $this->lang->translate('Facebook') );
		$labels[] = array('text'=> $this->lang->translate('Other') );
		return $labels;
	}
	public function _contacts($type, $options=array(), $name='', $label='', $value='' ) {
		
		$labelselect = '';
		foreach ($this->{"_contact_label_{$type}"}() as $val) {
			$active = $label == $val['text'] ? ' selected="1"':'';
        	$labelselect .='<option'.$active.' value="'.$val['text'].'">'.$val['text'].'</option>';
    	}

    	if( empty($name) ) $name = 'contacts['.$type.']';

    	$actions = !empty($options['has_add']) 
    		? '<div class="controls-actions">'.
    			'<a class="btn-add js-add-field"><i class="icon-plus"></i><span>เพิ่ม</span></a>'.
    			'<a class="btn-add js-remove-field remove"><i class="icon-remove"></i><span>ลบ</span></a>'.
    		  '</div>'
    		:'';

		return '<fieldset class="control-group">'.
	        '<label class="control-label">'.
	            '<select name="'.$name.'[name][]" class="labelselect">'.$labelselect.'</select>'.
	        '</label>'.
	        '<div class="controls">'.
	            '<input class="inputtext js-input" autocomplete="off" type="text" name="'.$name.'[value][]" value="'.$value.'" />'.
	            '<div class="notification"></div>'.
	            $actions.
	        '</div>'.
	    '</fieldset>';
	}


}