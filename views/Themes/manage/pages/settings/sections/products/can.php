<?php

$url = URL .'products/';


?><div class="pal"><div class="setting-header cleafix">

<div class="rfloat">

	<span class=""><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add_can"><i class="icon-plus mrs"></i><span><?=$this->lang->translate('Add New')?></span></a></span>

</div>

<div class="setting-title"><?=$this->lang->translate('Category Can')?></div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name"><?=$this->lang->translate('Category')?></th>
			<th class="actions"><?=$this->lang->translate('Action')?></th>

		</tr>

		<?php foreach ($this->data as $key => $item) { 
			$code = !empty($item['code']) ? '('.$item['code'].')' : '';
		?>
		<tr>
			<td class="name"><?=$item['name']?> <?=$code?></td>

			<td class="actions whitespace">
				
				<span class=""><a data-plugins="dialog" href="<?=$url?>edit_can/<?=$item['id'];?>" class="btn btn-orange"><i class="icon-pencil"></i></a></span>
				<span class=""><a data-plugins="dialog" href="<?=$url?>del_can/<?=$item['id'];?>" class="btn btn-red"><i class="icon-trash"></i></a></span>
					
			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>