<?php

class Table extends uiElement{

    public function __construct()
    {
        parent::__construct();
    }

    public function set( $options=array())
    {
        $this->current = 'table';
        $this->_data = array();

        $this->numrow = -1;
        $this->numcell = -1;
        return $this;
    }

    public function attr($keys='', $val='') {
        if( is_array($keys) ){
            foreach ($keys as $key => $value) {
                $this->_attr($key,$value);
            }
        }
        else{
            $this->_attr($keys, $val);
        }

        return $this;
    }

    private function _attr($key, $val)
    {
        switch ($this->current) {
            case 'table':
                $this->setAttr($key, $val, $this->_data);
                break;
            
            case 'row':
                $this->setAttr($key, $val, $this->_data['row'][$this->numrow]);
                break;


            case 'cell':
                $this->setAttr($key, $val, $this->_data['row'][$this->numrow]['cell'][$this->numcell]);
                break;
        }
    }

    public function addClass($name='')
    {
        $this->attr('class', $name);
        return $this;
    }

    public function row()
    {
        $this->current = 'row';
        $this->numrow++;
        $this->_data['row'][$this->numrow] = array();

        return $this;
    }

    public function cell()
    {
        $this->current = 'cell';
        $this->numcell++;
        $this->_data['row'][$this->numrow]['cell'][$this->numcell] = array();

        return $this;
    }

    public function header($ret=true)
    {
        if( $this->current==='cell' ){
            $this->_data['row'][$this->numrow]['cell'][$this->numcell]['header'] = $ret;
        }
        elseif( $this->current==='table' && $ret){

            $numrow = $this->numrow<=0 ? 0:$this->numrow;
            $this->_data['header']['row'][$numrow] = $ret;

        }
        return $this;
    }

    public function text($str='')
    {
        if( $this->current==='cell' ){
            $this->_data['row'][$this->numrow]['cell'][$this->numcell]['text'] = $str;
        }

        return $this;
    }

    public function display()
    {
        // print_r($this->_data);

        $atts = !empty($this->_data['attr']) ? $this->getAttr( $this->_data['attr'] ): '';
        $table = "<table{$atts}>";

        $t = 'body';
        if( !empty($this->_data['row']) ){
            foreach ($this->_data['row'] as $key => $row) {

                if( !empty( $this->_data['header']['row'][$key] ) && $t!='head' ){
                    $table .= '<thead>';        
                }
                elseif( $t!='body' ){
                    $table .= '<tbody>';
                }

                $cell_str = '';
                foreach ($row['cell'] as $i => $cell) {

                    $tag = !empty($cell['header'])? 'th':'td';
                    $atts = !empty($cell['attr']) ? $this->getAttr( $cell['attr'] ): '';
                    $cell_str .=  "<$tag{$atts}>";

                    if( !empty($cell['header']) ){
                        $cell_str .= '<span class="hdr_text">'.$cell['text'].'</span>';
                    }
                    else{
                        $cell_str .= $cell['text'];
                    }
                    
                    $cell_str .= "</$tag>";
                }

                $atts = !empty($row['attr']) ? $this->getAttr( $row['attr'] ): '';
                $table .= "<tr{$atts}>{$cell_str}</tr>";

                if( !empty($this->_data['header'][$key] ) && $t!='head' ){
                    $table .= '</thead>';
                    $t = 'head';
                }
                elseif( $t!='body' ){
                    $table .= '</tbody>';
                    $t = 'body';
                }

            } // end rows
        }
        else{

        }
        

        $table .= '</tbody></table>';

        return $table;

    }

}