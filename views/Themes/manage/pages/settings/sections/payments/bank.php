<?php

$url = URL .'payments/';


?><div class="pal"><div class="setting-header cleafix">

<div class="rfloat">

	<span class=""><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add_bank"><i class="icon-plus mrs"></i><span><?=$this->lang->translate("Add New")?></span></a></span>

</div>

<div class="setting-title"><i class="icon-credit-card"></i> Bank</div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="status">Acronym</th>
			<th class="name">Bank</th>
			<th class="actions"><?=$this->lang->translate('Action')?></th>
		</tr>

		<?php foreach ($this->data as $key => $item) { ?>
		<tr>
			<td class="status"><?= !empty($item['code']) ? $item['code'] : "-" ?></td>
			<td class="name"><?=$item['name']?></td>

			<td class="actions whitespace">
				<span class=""><a data-plugins="dialog" href="<?=$url?>edit_bank/<?=$item['id'];?>" class="btn btn-orange"><i class="icon-pencil"></i></a></span>
				<span class=""><a data-plugins="dialog" href="<?=$url?>del_bank/<?=$item['id'];?>" class="btn btn-red"><i class="icon-trash"></i></a></span>

			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>
