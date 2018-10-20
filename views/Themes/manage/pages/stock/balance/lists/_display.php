<div id="mainContainer" class="clearfix" data-plugins="main">

	<div role="toolbar" class="phl ptl pbm">
		<h1><i class="icon-balance-scale"></i><span class="mls">Stock Balance</span></h1>
	</div>

	<div role="content" class="clearfix">
		<div role="main">
			
			<div class="phl pbl">
				<div class="table-data__control clearfix mbs" ref="control">
					<div class="lfloat"><label>Close date:</label><select class="inputtext" plugin="closedate2">
						<option value=""> -- All --</option>
						<option value="custom">Custom</option>
					</select></div>
					<div class="lfloat mhl fc7"> | </div>
					<div class="lfloat"><label>Product Category:</label><select name="category" class="inputtext">
						<option value=""> -- All --</option>
						<?php foreach ($this->categoryLists as $key => $value) {
						echo '<option value="'.$value['id'].'">'.$value['name_en'].'</option>';
					} ?></select></div>
					<div class="lfloat"><label class="checkbox" style="line-height: 20px;font-weight: bold;"><input type="checkbox" name="pds_has_vat" data-action="check"><span>VAT Only</span></label></div>
					<div class="rfloat"><input type="text" name="q" class="inputtext"></div>
				</div>

				<table class="table-data__table">
					<thead>
						<tr>
							<th class="td-no">#</th>
							<!-- <th class="td-code">Product Code</th> -->
							<th class="td-name">Product Name</th>

							<th class="td-status">Vat</th>
							<th class="td-price">Receive</th>
							<th class="td-price">Adjust</th>

							<th class="td-price">Input</th>
							<th class="td-price">Output</th>
							<th class="td-price">Balance</th>
						</tr>
					</thead>
					<tbody ref="listsbox"></tbody>
					<tbody class="alert">
						<tr><td colspan="8" class="td-empty">No results found.</td></tr>
						<tr><td colspan="8" class="td-loading">loading...</td></tr>
					</tbody>
				</table>

				<div class="mts">
					<div ref="total"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	
	.table-data__table{
		width: 100%;
	}

	.table-data__table th, .table-data__table td{
		padding: 4px 12px;
		border: 1px solid #ddd
	}



	.table-data__table th{
		white-space: nowrap;
		background-color: #eee
	}
	.table-data__table td{
		background-color: #fff
	}

	.table-data__table .td-loading, .table-data__table .td-empty{
		padding: 60px;
		text-align: center;
		display: none;
	}
	.table-data__table.has-loading .td-loading, .table-data__table.has-empty .td-empty{
		display: table-cell;
	}
	.table-data__table.has-loading [ref=listsbox], .table-data__table.has-empty [ref=listsbox]{
		display: none;
	}
	.table-data__table .td-no{
		width: 20px;
		text-align: center;
	}
	.table-data__table .td-price{
		width: 80px;
		text-align: right;
		white-space: nowrap;
	}
	.table-data__table .td-status{
		width: 30px;
		text-align: center;
		white-space: nowrap;
	}
	.table-data__table .td-code{
		width: 30px;
		text-align: center;
	}
	.table-data__table .balance{
		background-color: #eee;
	}

	.table-data__control label{
		display: inline-block;
		line-height: 29px;
		vertical-align: top;
		border-bottom: 1px solid #d9d9d9;
	}
	.table-data__control label.checkbox{
		border-bottom: none;
		margin: 0
	}
	.table-data__control > div{
		display: inline-block;
		line-height: 30px;
		vertical-align: top;

	}
	.table-data__control > div + div{
		margin-left: 14px;
	}
	.table-data__control .inputtext,.table-data__control .uiPopover .btn-box{
		display: inline-block;
		vertical-align: top;
		border-width: 0 0 1px;
		line-height: 30px;
		box-shadow: none;
		background-color: transparent;
		font-weight: bold;
		/*padding: 0*/
	}
</style>


<script src="/public/js/plugins/closedate2.js"></script>
<script type="text/javascript">
	
	var __options = {
			unlimit: 1
		},
		$listsbox = $('[ref=listsbox]');

	function setOptions() {
		$.each($('[ref=control] :input'), function(index, el) {

			console.log( $(this).attr('name') );

			if( $(this).attr('name') && $(this).val()!='' ){
				__options[ $(this).attr('name') ] = $(this).val();
			}
		});
	}


	function refresh() {
		
		setTimeout( function () {

			setOptions();

			$listsbox.parent().addClass('has-loading').removeClass('has-empty');
			
			$.ajax({
				url: '/stock/balance/',
				type: 'GET',
				dataType: 'json',
				data: __options,
			})
			.done(function( res ) {

				if( parseInt(res.total) == 0 ){
					$listsbox.parent().addClass('has-empty');
				}
				
				$('[ref=total]').text( 'results: '+ PHP.number_format(res.total)  +' found.' );

				var $item = setItem( res.items );
				$listsbox.html( $item );
			})
			.fail(function() {

				$listsbox.parent().addClass('has-error');
				console.log("error");
			})
			.always(function() {
				$listsbox.parent().removeClass('has-loading');
				console.log("complete");
			});
			
		}, 1);
	}

	function setItem( data ) {
		
		var l = data || {};

		var n = 0;
		return $.map(l, function(obj) {

			n++;
			if( !obj.receive ) obj.receive = 0;
			if( !obj.adjust ) obj.adjust = 0;
			if( !obj.output ) obj.output = 0;

			var input = parseInt(obj.receive) + parseInt(obj.adjust);
			var balance = parseInt( input ) - parseInt(obj.output);

			var txt = $('<div>', {class: 'fsm fcg'});

			txt.append( obj.category_name_en, ' (', obj.category_name, ')' );

			/*if(  ){
				txt.append( ' - Vat' );
			}*/

			return $tr = $('<tr>').append(
				  $('<td>', {class: 'td-no'}).text( PHP.number_format(n) )
				// , $('<td>', {class: 'td-code'}).html( obj.pds_code )
				, $('<td>', {class: 'td-name'}).append(
					obj.name
					, txt
				)
				, $('<td>', {class: 'td-status'}).html( parseInt(obj.vat) ? '<span style="color:#792dff">✔</span>':'<span style="color:#FF5722">✖</span>' )
				, $('<td>', {class: 'td-price'}).text( PHP.number_format( obj.receive ) )
				, $('<td>', {class: 'td-price'}).text( PHP.number_format( obj.adjust ) )

				, $('<td>', {class: 'td-price'}).text( PHP.number_format( input ) )

				, $('<td>', {class: 'td-price'}).text( PHP.number_format( obj.output ) )
				, $('<td>', {class: 'td-price balance'}).text( PHP.number_format( balance ) )
			)[0];
		});
	}


	$('[ref=control] select:input').change(function() {
		refresh();
	});

	var curentVal = '';
	$('[ref=control] input[name=q]').keypress(function(e) {

		if( curentVal != $(this).val() && e.which == 13){
			refresh();
			curentVal = $(this).val();
		}
	}).keyup(function() {
		if( curentVal != $(this).val() && $(this).val()==''){
			__options.q = '';
			refresh();
		}
	});
	

	$('[plugin=closedate2]').closedate2({
		onChange: function () {
			refresh();
		}
	});
	 
	// 
</script>