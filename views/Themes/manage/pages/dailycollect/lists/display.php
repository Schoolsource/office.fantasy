<div id="mainContainer" class="report-main clearfix" data-plugins="main">
    <div role="content">
        <div role="main" class="pal">
            <div class="uiBoxWhite pas pam">
                <div class="clearfix">
                    <div class="lfloat">
                        <h3 class="fwb"><i class="icon-ship"></i>Daily collect</h3>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="lfloat" style="margin-left: 5mm;">
                        <ul>
                            <li class="js-control mtm">
                                <label for="month" class="label">Month</label>
                                <select class="inputtext" name="month" style="display:inline;">
                                    <option value="" selected="1">- All -</option>
                                    <?php
                                    for ($i = 1; $i <= 12; ++$i) {
                                        $sel = '';
                                        // if( $i == date("n") ){
                                        //  $sel = ' selected="1"';
                                        // }
                                        echo '<option'.$sel.' value="'.$i.'">'.$this->fn->q('time')->month($i, true, 'en').'</option>';
                                    }
                                    ?>
                                </select>
                                <label for="year" class="label mlm">Year</label>
                                <select class="inputtext" name="year" style="display:inline;">
                                    <option value="" selected="1">- All -</option>
                                    <?php
                                    $year = date('Y');
                                    for ($i = 0; $i < 5; ++$i) {
                                        $_year = $year - $i;

                                        $sel = '';
                                        // if( $_year == $year ){
                                        //  $sel = ' selected="1"';
                                        // }

                                        echo '<option'.$sel.' value="'.$_year.'">'.$_year.'</option>';
                                    }
                                    ?>
                                </select>

                                <label class="label">Select sale</label>
                                <select class="inputtext" name="sale" style="display:inline;">
                                    <option value="">-</option>
                                    <?php
                                    foreach ($this->sales as $key => $value) {
                                        echo '<option value="'.$value['code'].'">'.$value['name'].'</option>';
                                    }
                                    ?>
								</select>
								<label class="label">Bill On.</label>
								<select class="inputtext" name="sale" style="display:inline;">
                                    <option value="">-</option>
                                    <?php
                                    foreach ($this->sales as $key => $value) {
                                        echo '<option value="'.$value['code'].'">'.$value['name'].'</option>';
                                    }
                                    ?>
                            
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="uiBoxWhite pas pam" style="margin-top: 2mm;">
                <div id="table-lists"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $.fn.extend(
    {
        loadMain: function(month, year, sale){
            $("#table-lists").html( '<div class="tac"><div class="loader-spin-wrap" style="display:inline-block;"><div class="loader-spin"></div></div></div>' );
            $.get(Event.URL + 'collectdaily', {month:month, year:year, sale:sale, main:1}, function(res){
                $('#table-lists').html( res );
                Event.plugins( $('#table-lists') );
            });
        }
    });

    $('.js-control').loadMain();

    $('.js-control').change(function(){
        var month = $(this).find('[name=month]').val();
        var year = $(this).find('[name=year]').val();
        var sale = $(this).find('[name=sale]').val();

        $(this).loadMain(month, year, sale);
    });
</script>