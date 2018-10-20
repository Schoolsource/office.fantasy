<div id="mainContainer" class="report-main clearfix" data-plugins="main">
    <div role="content">
        <div role="main" class="pal">
            <div class="uiBoxWhite pas pam">
                <div class="clearfix">
                    <div class="lfloat">
                        <h3 class="fwb"><i class="icon-free-code-camp"></i> Project Report</h3>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="lfloat" style="margin-left: 5mm;">
                        <ul>
                            <li class="js-control mtm">
                                <label for="project" class="label">Project</label>
                                <select class="inputtext" name="project" style="display:inline;">
                                    <option value="" selected="1">- All -</option>
                                    <?php
                                    foreach ($this->projects as $project) {
                                        $sel = '';
                                        // if( $i == date("n") ){
                                        // 	$sel = ' selected="1"';
                                        // }
                                        echo '<option'.$sel.' value="'.$project['project_id'].'">'.$project['project_name'].'</option>';
                                    }
                                    ?>
                                </select>

                                <label for="sale" class="label">Sale</label>
                                <select class="inputtext" name="sale" style="display:inline;">
                                    <option value="" selected="1">- All -</option>
                                    <?php
                                    foreach ($this->sales as $sale) {
                                        $sel = '';
                                        // if( $i == date("n") ){
                                        // 	$sel = ' selected="1"';
                                        // }
                                        echo '<option'.$sel.' value="'.$sale['code'].'">'.$sale['name'].'</option>';
                                    }
                                    ?>
                                </select>

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
        loadMain: function(project, sale){
            $("#table-lists").html( '<div class="tac"><div class="loader-spin-wrap" style="display:inline-block;"><div class="loader-spin"></div></div></div>' );
            $.get(Event.URL + 'reports/project', {project:project, sale:sale, main:1}, function(res){
                $('#table-lists').html( res );
                Event.plugins( $('#table-lists') );
            });
        }
    });

    $('.js-control').loadMain();

    $('.js-control').change(function(){
        var project = $(this).find('[name=project]').val();
        var sale = $(this).find('[name=sale]').val();

        $(this).loadMain(project, sale);
    });
</script>