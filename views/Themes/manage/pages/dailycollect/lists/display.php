

<div id="mainContainer" class="report-main clearfix container" data-plugins="main">
    <div role="content">
        <div role="main" class="pal">
            <div class="uiBoxWhite pas pam">
                <div class="clearfix">
                    <div class="lfloat" style="display:flex; padding-bottom:1rem;">
                        <h3 class="fwb"><i class="icon-ship"></i>Daily collect</h3>
                        <h4 style="margin:.2rem 0 0 .5rem;"><?=$this->fn->q('time')->full(strtotime(date("Y-m-d")),true,true,false)?> </h4>
                    </div>
                </div>
                <div class="clearfix">
                  
                </div>
                <div class="clearfix">
                    <ul class="lfloat" ref="control">
                    <li class="mt">
                        <form class="js-submit-form form-insert" action="collectdaily/save" method="post">
                            <input class="inputtext search-input" style="border-radius: .25rem" type="text" id="search-query" placeholder="<?="Enter billing id"?>" name="input" autocomplete="off">
            

                        </form>
                    </li>
                </ul>
                </div>
            </div>
            <div class="uiBoxWhite pas pam" style="margin-top: 2mm;">
                <div id="table-lists"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
    })();

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
    $("#table-lists").delegate('.js-auto-submit', 'keypress',function(e){
            delay(function(){
                
                }, 1000 );
    })
    $('.form-search').submit(function(e){
        e.preventDefault();
        data = $(this).find('input[name="js-billing"]').val()
        $.ajax({url: "collectdaily/save",data:{'input':data},method:'POST', success: function(res){
           
        }}).fail(function(err){
            
        });
    })
    $('.js-control').loadMain();
    
    $('.js-control').change(function(){
        var month = $(this).find('[name=month]').val();
        var year = $(this).find('[name=year]').val();
        var sale = $(this).find('[name=sale]').val();

        $(this).loadMain(month, year, sale);
    });
</script>