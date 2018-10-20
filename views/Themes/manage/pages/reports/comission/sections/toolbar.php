<div role="toolbar" class="mal">
	<div class="uiBoxWhite pam js-control">
		<ul class="ui-list ui-list-horizontal clearfix" ref="actions">
			<li class="ui-item"><h2><i class="icon-signal"></i> Commission</h2></li>
			<li class="js-control mtm">
				<label for="month" class="label">Month</label>
				<select class="inputtext" name="month" style="display:inline;">
					<?php
                    for ($i = 1; $i <= 12; ++$i) {
                        $sel = '';
                        if ($i == date('n')) {
                            $sel = ' selected="1"';
                        }
                        echo '<option'.$sel.' value="'.$i.'">'.$this->fn->q('time')->month($i, true).'</option>';
                    }
                    ?>
				</select>
				<label for="year" class="label mlm">Year</label>
				<select class="inputtext" name="year" style="display:inline;">
					<?php
                    $year = date('Y');
                    for ($i = 0; $i < 5; ++$i) {
                        $_year = $year - $i;

                        $sel = '';
                        if ($_year == $year) {
                            $sel = ' selected="1"';
                        }

                        echo '<option'.$sel.' value="'.$_year.'">'.$_year.'</option>';
                    }
                    ?>
				</select>
			</li>
		</ul>
	</div>
</div>
