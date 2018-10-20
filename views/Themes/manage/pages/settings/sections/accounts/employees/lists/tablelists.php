<?php

$tr = "";
$tr_total = "";
$url = URL .'employees/';
if( !empty($this->results['lists']) ){ 

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) { 


        // $item = $item;
        $cls = $i%2 ? 'even' : "odd";
        // set Name

        $image = '';
        if( !empty($item['image_url']) ){
            $image = '<div class="avatar lfloat mrm"><img class="img" src="'.$item['image_url'].'" alt="'.$item['fullname'].'"></div>';
        }
        else{
            $image = '<div class="avatar lfloat no-avatar mrm"><div class="initials"><i class="icon-user"></i></div></div>';
        }

        $subtext = '';
        $express = '';
        if( !empty($item['phone_number']) ){
            $subtext .= !empty($subtext) ? ', ':'';
            $subtext.='<i class="icon-phone mrs"></i>'. $item['phone_number'];

            $express .= '<li><i class="icon-phone mrs"></i><a href="tel:'.$item['phone_number'].'">'.$item['phone_number'].'</a></li>';
        }

        if( !empty($item['email']) ){
            $subtext .= !empty($subtext) ? ', ':'';
            $subtext.='<i class="icon-envelope-o mrs"></i>'. $item['email'];

             $express .= '<li><i class="icon-envelope mrs"></i><a href="mailto:'.$item['email'].'" title="'.$item['email'].'">'.$item['email'].'</a></li>';
        }

        if( !empty($item['line_id']) ){
            $subtext .= !empty($subtext) ? ', ':'';
            $subtext .= '<a target="_blank" href="http://line.me/ti/p/~'.$item['line_id'].'"><i class="mls icon-external-link"></i> '.$item['line_id'].'</a>';

            $express .= '<li>Line ID: <a href="line:'.$item['line_id'].'">'.$item['line_id'].'</a></li>';
        }

        $disabled = '';
        if( $item['id'] == $this->me['id'] ){
            $disabled = ' disabled';
        }

        $subtext = '';
        if( !empty($item['dep_name']) ){
            $subtext .= !empty($subtext) ? ', ':'';
            $subtext.= 'Department: '.$item['dep_name'];
        }
        if( !empty($item['pos_name']) ){
            $subtext .= !empty($subtext) ? ', ':'';
            $subtext.= 'Position: '.$item['pos_name'];
        }

        $dropdown = array();
        $dropdown[] = array(
            'text' => 'Change Password',
            'href' => $url.'password/'.$item['id'],
            'attr' => array('data-plugins'=>'dialog'),
            'icon' => 'key'
            );

        if( $item['display']=='enabled' ){
            $dropdown[] = array(
                'text' => 'Disable',
                'href' => $url.'display/'.$item['id'].'/disabled',
                'attr' => array('data-plugins'=>'dialog'),
                'icon' => 'lock'
            );
        }

        if( $item['display']=='disabled' ){
            $dropdown[] = array(
                'text' => 'Enable',
                'href' => $url.'display/'.$item['id'].'/enabled',
                'attr' => array('data-plugins'=>'dialog'),
                'icon' => 'unlock'
            );
        }

        // if( !empty($item['permit']['del']) ){
        //     $dropdown[] = array(
        //         'text' => 'Delete',
        //         'href' => $url.'del/'.$item['id'],
        //         'attr' => array('data-plugins'=>'dialog'),
        //         'icon' => 'remove'
        //     );
        // }

        if( $this->me['id'] != $item['id'] ){

            $dropdown[] = array(
                'text' => 'Delete',
                'href' => $url.'del/'.$item['id'],
                'attr' => array('data-plugins'=>'dialog'),
                'icon' => 'remove'
            );

            /*$dropdown[] = array(
                'text' => 'Permission',
                'href' => $url.'edit_permit/'.$item['id'].'?type=employees',
                'attr' => array('data-plugins'=>'dialog'),
                'icon' => 'check-square-o'
            );

            $dropdown[] = array(
                'text' => 'Skill',
                'href' => $url.'set_skill/'.$item['id'],
                'attr' => array('data-plugins'=>'dialog'),
                'icon' => 'info-circle'
            );*/
        }

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'">'.

            // '<td class="check-box"><label class="checkbox"><input id="toggle_checkbox" type="checkbox" value="'.$item['id'].'"></label></td>'.

            // '<td class="bookmark"><a class="ui-bookmark js-bookmark'.( $item['bookmark']==1 ? ' is-bookmark':'' ).'" data-value="" data-id="'.$item['id'].'" stringify="'.URL.'customers/bookmark/'.$item['id']. (!empty($this->hasMasterHost) ? '?company='.$this->company['id']:'') .'"><i class="icon-star yes"></i><i class="icon-star-o no"></i></a></td>'.

            '<td class="name">'.

                '<div class="anchor clearfix">'.
                    $image.
                    
                    '<div class="content"><div class="spacer"></div><div class="massages">'.

                        '<div class="fullname"><a class="fwb">'. $item['fullname'].'</a> <span class="fwn">@'. $item['username'].'</span></div>'.

                        '<div class="subname fsm fcg meta">'.$subtext.'</div>'.

                        '<div class="fsm fcg whitespace">Last update: '.$this->fn->q('time')->live( $item['updated'] ).'</div>'.
                    '</div>'.
                '</div></div>'.

            '</td>'.

            '<td class="express"><ul class="fsm">'.$express.'</ul></td>'.
            '<td class="actions">'.

                '<div class="group-btn whitespace mts">';
                if( !empty($this->permit['employees']['edit']) ){
                    $tr .= '<a data-plugins="dialog" href="'.$url.'edit/'.$item['id'].'" class="btn"><i class="icon-pencil"></i></a>'.
                    '<a data-plugins="dropdown" class="btn" data-options="'.$this->fn->stringify( array(
                        'select' => $dropdown,
                        'settings' =>array(
                            'axisX'=> 'right',
                            'parent'=>'.setting-main'
                        ) 
                    ) ).'"><i class="icon-ellipsis-v"></i></a>';
                }
                    // '<a data-plugins="dialog" href="'.$url.'change_password/'.$item['id'].'" class="btn"><i class="icon-key"></i></a>'.
                    // '<a data-plugins="dialog" href="'.$url.'del/'.$item['id'].'" class="btn '.$disabled.'"><i class="icon-trash"></i></a>'.
                $tr .= '</div>'.
            '</td>'.
              
        '</tr>';
        
    }
}

$table = '<table class="settings-table"><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';