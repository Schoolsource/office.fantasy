<div id="mainContainer" class="profile clearfix" data-plugins="main">

	<div data-plugins="ManageCategories">
		<div role="main" class="pal">

			<div style="max-width: 750px">

				<div class="mbm clearfix">
					<div class="lfloat">
						<h2><i class="icon-database mrs"></i>Categories</h2>
						<span style="color:blue;" class="fwb mts">* Click on the name of the category and drag up or down as you like.</span>
					</div>
					<div class="rfloat">
						<a href="<?=URL; ?>categories/add" class="btn btn-blue" data-plugins="dialog"><i class="icon-plus"></i> Add</a>
					</div>
				</div>
				<!-- <div class="uiBoxYellow pam mbm">กดลากเพื่อจัดลำดับ ประเภทโปรแกรมทัวร์</div> -->

				<ul class="listsdata-table-lists">
					<li class="head">
						<div class="ID"><label class="label">Order</label></div>
						<div class="name"><label class="label">Categories</label></div>
						<div class="num"><label class="label">Product Number</label></div>
						<div class="date"><label class="label">Website</label></div>
						<div class="date"><label class="label">Salon</label></div>
						<div class="date"><label class="label">Status</label></div>
						<div class="actions"><label class="label">Actions</label></div>
					</li>
				</ul>
				<ul class="listsdata-table-lists" rel="listsbox">
					<?php
                    $seq = 0;
                    foreach ($this->results['lists'] as $key => $value) {
                        ++$seq; ?>
						<li class="list seq-item" data-id="<?=$value['id']; ?>">
							<div class="ID fwb"><span class="seq"><?=$seq; ?></span></div>
							<div class="name"><span class="fwb"><a class="fwb"><?=$value['name_th']; ?> (<?=$value['name_en']; ?>)</span></a></div>
							<div class="num tac">
								<?=!empty($value['product_count']) ? $value['product_count'] : '-'; ?>
							</div>
							<div class="date">
							    <?php echo '<label class="checkbox"><input'.(!empty($value['is_show']) ? ' checked="1"' : ' ').' type="checkbox" name="is_show" data-plugins="_update" data-options="'.$this->fn->stringify(array(
                        'url' => URL.'categories/_update/'.$value['id'].'/is_show',
                )).'" /></label>'; ?>
							</div>
							<div class="date">
							    <?php echo '<label class="checkbox"><input'.(!empty($value['is_mobile']) ? ' checked="1"' : ' ').' type="checkbox" name="is_mobile" data-plugins="_update" data-options="'.$this->fn->stringify(array(
                        'url' => URL.'categories/_update/'.$value['id'].'/is_mobile',
                )).'" /></label>'; ?>
							</div>
							<div class="date">
								<?=$value['status'] == 'A' ? 'Active' : 'Inactive'; ?>
							</div>
							<div class="actions tac">
								<span class="gbtn">
									<a href="<?=URL; ?>categories/edit/<?=$value['id']; ?>" class="btn btn-no-padding btn-orange" data-plugins="dialog"><i class="icon-pencil"></i></a>
								</span>
								<span class="gbtn">
									<a href="<?=URL; ?>categories/del/<?=$value['id']; ?>" class="btn btn-no-padding btn-red" data-plugins="dialog"><i class="icon-trash"></i></a>
								</span>
							</div>
						</li>
						<?php
                    }?>

					</ul>
				</div>

			</div>


		<!-- <div role="footer">
			<div class="pal clearfix" style="max-width: 750px">
				<div class="rfloat"><a class="btn btn-blue">Save</a></div>
			</div>
		</div> -->

	</div>
</div>
