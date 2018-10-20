<?php

$url = URL .'customer/';


?><div class="pal"><div class="setting-header cleafix">

<div class="rfloat">

	<span class="gbtn"><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>setProject"><i class="icon-plus mrs"></i><span>Add New Project</span></a></span>

</div>

<div class="setting-title"><i class="icon-address-card-o mrs"></i><span>Customer Project</span></div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name">Project Name</th>
			<th class="status">Enabled</th>
			<th class="actions">Action</th>

		</tr>

		<?php foreach ($this->data as $key => $item) { ?>
		<tr>
			<td class="name">
				<h3><?=$item['name']?></h3>
				<?php if( !empty($item['dep_name']) ){ ?>
				<div class="fsm fcg">Department: <?=$item['dep_name']?></div>
				<?php } ?>
			</td>

			<td class="actions"><?php

			$dropdown = array();

			$dropdown[] = array(
                'text' => $this->lang->translate('Permission'),
                'href' => $url.'edit_permit/'.$item['id'].'?type=position',
                'attr' => array('data-plugins'=>'dialog'),
            );

			$dropdown[] = array(
                'text' => $this->lang->translate('Delete'),
                'href' => $url.'del_position/'.$item['id'],
                'attr' => array('data-plugins'=>'dialog'),
            );

            echo '<div class="whitespace group-btn">'.

            	'<span class="gbtn"><a data-plugins="dialog" href="'.$url.'edit_position/'.$item['id'].'" class="btn"><i class="icon-pencil"></i></a></span>'.

            	'<span class="gbtn"><a data-plugins="dropdown" class="btn" data-options="'.$this->fn->stringify( array(
                        'select' => $dropdown,
                        'settings' =>array(
                            'axisX'=> 'right',
                            'parent'=>'.setting-main'
                        ) 
                    ) ).'"><i class="icon-ellipsis-v"></i></a></span>'.

            '</div>';

			?></td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>