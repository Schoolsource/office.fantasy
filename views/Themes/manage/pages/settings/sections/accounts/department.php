<?php

$url = URL .'employees/';


?><div class="pal">

<div class="setting-header cleafix">

<div class="rfloat">

	<span class="gbtn"><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add_department"><i class="icon-plus mrs"></i><span><?=$this->lang->translate('Add New')?></span></a></span>

</div>

<div class="setting-title"><?=$this->lang->translate('Department')?></div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name"><?=$this->lang->translate('Name')?></th>

			<?php foreach ($this->access as $key => $value) {
				echo '<th class="status">'.$value['name'].'</th>';
			}?>

			<th class="actions"><?=$this->lang->translate('Action')?></th>

		</tr>

		<?php foreach ($this->data as $key => $item) { ?>
		<tr>
			<td class="name">
				<h3><?=$item['name']?></h3>
				<?php if( !empty($item['notes']) ){ ?>
				<div class="fsm fcg"><?=$item['notes']?></div>
				<?php } ?>
			</td>
			
			<?php foreach ($this->access as $key => $value) {
				$item['access'] = !empty($item['access']) ? $item['access']: array();

			echo '<td class="status"><label class="checkbox"><input type="checkbox" name="'.$value['id'].'"'.( in_array($value['id'], $item['access']) ?' checked="1"' :'').'></label></td>';
			}?>

			<td class="actions">
				
				<div class="group-btn whitespace"><a data-plugins="dialog" href="<?=$url?>edit_department/<?=$item['id'];?>" class="btn"><i class="icon-pencil"></i></a><!-- <a data-plugins="dialog" href="<?=$url?>edit_permit/<?=$item['id']?>?type=department" class="btn"><i class="icon-check-square-o"></i></a> --><a data-plugins="dialog" href="<?=$url?>del_department/<?=$item['id'];?>" class="btn"><i class="icon-trash"></i></a>
				</div>
					
			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>