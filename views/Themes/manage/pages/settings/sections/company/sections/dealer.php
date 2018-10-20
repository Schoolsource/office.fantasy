<?php
$url = URL .'dealer/';
?>
<section class="setting-section">
	<table class="settings-table"><tbody>
		<tr>
			<th class="name">Name</th>
			<?php foreach ($this->paytype['lists'] as $key => $value) {
				echo '<th class="status">'.$value['name'].'</th>';
			}
			?>
			<th class="actions">Actions</th>

		</tr>

		<?php foreach ($this->data['lists'] as $key => $item) { ?>
		<tr>
			<td class="name"><?=$item['name']?></td>

			<?php 
			foreach ($this->paytype['lists'] as $key => $value) {

				$sel = '';

				foreach ($item['paytype'] as $val) {
					
					if( $value['id'] == $val['id'] ) {
						$sel = ' checked="1"';
						break;
					}
				}

				echo '<td class="status"><label class="checkbox"><input'.$sel.' disabled class="disabled" type="checkbox" name=""></label></td>';
			}
			?>

			<td class="actions whitespace">
				
				<span class="gbtn"><a data-plugins="dialog" href="<?=$url?>edit/<?=$item['id'];?>" class="btn btn-no-padding"><i class="icon-pencil"></i></a></span>
				<span class='gbtn'><a data-plugins="dialog" href="<?=$url?>del/<?=$item['id'];?>" class="btn btn-no-padding"><i class="icon-trash"></i></a></span>

			</td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
<div class="clearfix">
	<div class="rfloat">
	<span class="gbtn"><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add"><i class="icon-plus mrs"></i><span>Add New</span></a>
	</span>
	</div>
</div>