<?php

$url = URL .'employees/';


?><div class="pal">

<div class="setting-header cleafix">

<div class="rfloat">

	<span class="gbtn"><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add_skill"><i class="icon-plus mrs"></i><span><?=$this->lang->translate('Add New')?></span></a></span>

</div>

<div class="setting-title"><?=$this->lang->translate('Skill')?></div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name"><?=$this->lang->translate('Name')?></th>

			<th class="actions"><?=$this->lang->translate('Action')?></th>

		</tr>

		<?php foreach ($this->data as $key => $item) { ?>
		<tr>
			<td class="name">
				<h3><?=$item['name']?></h3>
			</td>


			<td class="actions whitespace">
				
				<span class="gbtn"><a data-plugins="dialog" href="<?=$url?>edit_skill/<?=$item['id'];?>" class="btn btn-no-padding"><i class="icon-pencil"></i></a></span>
				<span class='gbtn'><a data-plugins="dialog" href="<?=$url?>del_skill/<?=$item['id'];?>" class="btn btn-no-padding"><i class="icon-trash"></i></a></span>
					
			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>