<?php require_once 'init.php'; ?>

<!-- <pre> -->
    <?php //print_r($this->results);?>
<!-- </pre> -->

<div id="mainContainer" class="clearfix listpage2-container" data-plugins="main">

    <div role="content">
        <div role="main">

<div class="listpage2 has-loading offline listpage2-mg" data-plugins="listpage2" data-options="<?= $this->fn->stringify(array(
        'url' => $this->getURL,
    )); ?>">

    <!-- header -->
    <?php require 'header.php'; ?>

    <!-- table -->
    <div ref="table" class="listpage2-table table-mg">
        <div ref="tabletitle"><?php require 'tabletitle.php'; echo $tabletitle; ?></div>
        <div ref="tablelists"></div>

        <!-- <div class="listpage2-table-overlay"></div> -->
        <div class="listpage2-table-empty">
            <div class="empty-icon"><i class="icon-cube"></i></div>
            <div class="empty-title">Data not found.</div>
        </div>

    </div>

    <div class="listpage2-table-overlay-warp">
        <div class="listpage2-table-overlay"></div>
        <div class="listpage2-alert">
            <div class="listpage2-loading">
                <div class="listpage2-loading-icon loader-spin-wrap"><div class="loader-spin"></div></div>
                <div class="listpage2-loading-text">Loading...</div>
            </div>
        </div>
    </div>
</div>

        </div>
        <!-- end: main -->
    </div>
    <!-- end: content -->
</div>
<!-- end: container -->

<style type="text/css">
    .td-seq{
        width: 20px;
        text-align: center;
    }
    .td-price{
        border-left: 1px solid #eee
    }
    .td-input{ background-color: #c9c9c9!important; }
    .td-output{ background-color: #e0e0e0!important; }
    .td-balance{ background-color: #cccccc!important; }
    .td-minus{ background-color: #ff8484 !important;}
</style>

<script>
    $(function(){
        $(document).on('change', 'input[name=start_date], input[name=end_date], #category, input[name=pds_has_vat]', function(){
			console.log('start_date: '+ $('input[name=start_date]').val());
		});
    });

</script>