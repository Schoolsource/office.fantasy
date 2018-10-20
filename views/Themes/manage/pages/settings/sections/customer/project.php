<?php

$url = URL .'customers/';


?><div class="pal"><div class="setting-header cleafix">

<div class="rfloat">
	<span class="gbtn"><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>insertProject"><i class="icon-plus mrs"></i><span>Add New Project</span></a></span>
</div>

<div class="setting-title"><i class="icon-address-card-o mrs"></i><span>Customer Project</span></div>
</div>

<section class="setting-section">
	<table class="settings-table admin"><tbody>
		<tr>
			<th class="name">Project Name</th>
			<th class="status">Total Order</th>
			<th class="status">Enabled</th>
			<th class="actions">Action</th>

		</tr>

		<?php foreach ($this->dataList as $key => $item) { ?>
		<tr>
			<td class="name">
				<h3><?=$item['project_name']?></h3>
			</td>
			<td class="status"><?=$item['totalOrder']==0? '-': '<a href="'.URL.'payments?project='.$item['project_id'].'" target="_blank">'.number_format($item['totalOrder']).'</a>'?></td>
			<td class="status">
				<label class="checkbox"><input type="checkbox" data-id="<?=$item['project_id']?>" name="project_enabled" <?=!empty( $item['project_enabled'] )? ' checked':''?> /></label>
			</td>
			<td class="actions"><?php

            echo '<div class="whitespace group-btn">'.

            	'<span class="gbtn"><a data-plugins="dialog" href="'.$url.'updateProject/'.$item['project_id'].'" class="btn btn-no-padding btn-orange"><i class="icon-pencil"></i></a></span>'.
            	'<span class="gbtn"><a data-plugins="dialog" href="'.$url.'deleteProject/'.$item['project_id'].'" class="btn btn-no-padding btn-red "><i class="icon-trash"></i></a></span>'.

            '</div>';

			?></td>

		</tr>
		<?php } ?>
	</tbody></table>
</section>
</div>

<script type="text/javascript">
	
	$('[name=project_enabled]').change(function() {

		$.post( Event.URL+ 'customers/enabledProject', {
			id: $(this).data('id'), 
			val: $(this).prop('checked') ? 1:0
		}, function(data, textStatus, xhr) {
			
			if( data.message ){

				Event.showMsg({text: data.message, load: 1, auto: 1});
			}
		}, 'json');
	});
</script>