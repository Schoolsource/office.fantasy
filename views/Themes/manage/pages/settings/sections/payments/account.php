<?php

$url = URL .'payments/';


?><div class="pal"><div class="setting-header cleafix">

<div class="rfloat">

	<span class=""><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add_account"><i class="icon-plus mrs"></i><span><?=$this->lang->translate("Add New")?></span></a></span>

</div>

<div class="setting-title"><i class="icon-credit-card"></i> Bank account</div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name">Account number</th>
			<th class="actions"><?=$this->lang->translate('Action')?></th>
		</tr>

		<?php foreach ($this->data as $key => $item) { ?>
		<tr>
			<td class="name">
				<h3><?=$item['number']?> (<?=$item['name']?>)</h3>
				<div class="fsm fcg">Bank: <?=$item['bank_name']?></div>
				<div class="fsm fcg">branch: <?=$item['branch']?></div>
			</td>

			<td class="actions whitespace">
				<span class=""><a data-plugins="dialog" href="<?=$url?>edit_account/<?=$item['id'];?>" class="btn btn-orange"><i class="icon-pencil"></i></a></span>
				<span class=""><a data-plugins="dialog" href="<?=$url?>del_account/<?=$item['id'];?>" class="btn btn-red"><i class="icon-trash"></i></a></span>

			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>
