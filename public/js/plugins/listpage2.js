/* v.2.2 */

// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var List = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();
			self.settings = $.extend( {}, $.fn.listpage2.options, options );

			self.data = {
				total: 0,
				options: self.settings.options || {},
				url: self.settings.url
			};

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});
			self.$elem.addClass('on');

			self.ids = {};
			self.Events();
		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);
			self.$elem.find('[ref]').each(function () {
				if( $(this).attr('ref') ){
					var ref = "$" + $(this).attr('ref');
					self[ref] = $(this);
				}
				
			});

			$.each(self.$elem.find('[plugin=dropdown]'), function() {
				var options = $.parseJSON( $(this).attr('data-options') );
				options.onClick = function (el) {
					self.action($(el).attr('ajaxify'));
				}

				$(this).dropdown(options);
			});
			
		},
		resize: function () {
			var self = this;

			if( $('#doc').hasClass('fixed_elem') ) return;
			
			var outer = $( window ); // $( window );
			var offset = self.$elem.offset();
			var right = 0;
			var fullw = outer.width()- (offset.left+right);
			var fullh = outer.height(); // + outer.scrollTop();

			var top = 0;

			if( $('body').hasClass('hasTopbar') ){
				top = $('#header-primary').height();
			}

			var headerH = 0;
			if( self.$header ){
				headerH = self.$header.outerHeight();

				self.$header.css({
					top: top,
					left: offset.left,
					width: fullw - 16,
					// right: right,
					position: 'fixed'
				});
			}

			self.$elem.find('.listpage2-table-overlay-warp').css({
				left: offset.left,
			});

			self.$table.css({
				paddingTop: headerH,
				// width: fullw,
				// height: fullh - (offset.top),
				// overflow:'hidden'
			}); 

			self.$tabletitle.css({
				position: 'fixed',
				left: offset.left,
				right: right ,
				zIndex: 20
			});
			
			self.$tablelists.css({
				marginTop: self.$tabletitle.outerHeight(),
				/*height: fullh - (offset.top+headerH+self.$tabletitle.outerHeight()),
				overflowY: 'auto'*/
			});

			var totalW = 0;
			if(self.$tablelists.find('table tr:first>td').hasClass('empty')){ return false; }

				self.$tablelists.find('table tr:first>td').each(function ( i ) {

				var td = $(this);
				var th = self.$tabletitle.find('table th[data-col='+i+']');

				if( td.hasClass('name') ){
					return
				}

				var outerW = td.outerWidth()
				var width = td.width();

				if( th.width() > width){
					outerW = th.outerWidth();
					width = th.width();
				}
				
				totalW+=outerW;
				td.width( width );
				th.width( width );
			});


			if( totalW > 0 && self.$tablelists.find('td.name .ellipsis').length){

				totalW += parseInt( self.$tablelists.find('td.name').css('padding-left') );
				totalW += parseInt( self.$tablelists.find('td.name').css('padding-right') );
				self.$tablelists.find('td.name .ellipsis').css('max-width',  fullw-totalW);
			}

			if( self.$table.hasClass('has-empty') ){
				self.$tabletitle.css( 'padding-right', 0 );

				self.$table.find('.listpage2-table-empty').css( 'padding-top', self.$tabletitle.outerHeight()+headerH );
			}
			else{
				self.$tabletitle.css( 'right', self.$tabletitle.outerWidth() - self.$tablelists.find('table').outerWidth() );
			}
		},

		Events: function () {
			var self = this;

			self.$elem.mouseover(function () {
				self.resize();
			});

			$('input#checkboxes', self.$tabletitle).change( function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), 'all');
			});

			self.$elem.delegate('input#toggle_checkbox', 'change', function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), $(this).parents('tr') );
			});

			$('.navigation-trigger').click( function (e) {
				self.resize();
			});

			self.$elem.find('.js-refresh').click(function (e) {

				self.refresh( 200 );
				e.preventDefault();
			});


			self.$elem.find('[ref=form]').click(function (e) {

				var url = $(this).data('url');
				var $form = $('<form>', {'target': '_blank', 'action': url, 'method': 'post'});

				$.each(self.data.options, function(name, val) {
					$form.append( $('<input>', {type: 'hiden', name: name, value: val}) );
				});
				

				$('body').append( $form );

				$form.submit();
				$form.remove();


				e.preventDefault();
			});


			self.$elem.find('#more-link').delegate('a.next, a.prev', 'click', function (e) {
				
				pager = parseInt( self.data.options.pager );
				pager += $(this).hasClass('prev')? -1 : 1;

				self.data.options.pager = pager;
				self.refresh( 1 );
				e.preventDefault();
			});

			self.$elem.delegate('[ajaxify]', 'click', function (e) {
				e.preventDefault();

				self.action($(this).attr('ajaxify'));
			});

			$('a.link-sort', self.$tabletitle).click( function (e) {

				var val = $(this).attr('data-sort') || 'asc';

				val = val=='asc' ? 'desc' : 'asc';
				$(this).attr('data-sort', val);

				// set data
				self.data.options.dir = val;
				self.data.options.sort = $(this).attr('data-sort-val');
				self.refresh( 1 );

				// set elem
				self.$tabletitle.find( '.sorttable.asc' ).removeClass('asc');
				self.$tabletitle.find( '.sorttable.desc' ).removeClass('desc');
				$(this).parent().addClass( val );

				e.preventDefault();
			});

			if( self.$elem.find('select[ref=selector]').length>0 ){
				$.each( self.$elem.find('select[ref=selector]'), function() {
					
					if( $(this).attr('name') ){
						self.data.options[ $(this).attr('name') ] = $(this).val();
					}
				} );
			}

			var $closedate = self.$elem.find('[ref=closedate]');
			var $date = self.$elem.find(':input[ref=date]');
			if( $closedate.length==1 ){

				var closedateOptions = [], activeIndex = 0;
				$.each( $closedate.find('option'), function (i) {

					if( $(this).prop('selected') ){
						activeIndex = i;
					}

					var attr = $(this).attr('divider');
					if( typeof attr !== typeof undefined && attr !== false ){
						closedateOptions.push({
							divider: true
						});
					}
					else{
						closedateOptions.push({
							value: $(this).attr('value')? $(this).attr('value'): $(this).text(), 
							text: $(this).text()
						});
					}
					
				} );

				if( closedateOptions.length > 0 ){
					$closedate.closedate({
						onComplete: function ( that ) {
							// console.log( 'Complete', that, this );	
						},
						onChange: function ( that ) {

							var data = that.$menu.find('li.active').data();
							if(data.value == ''){
								delete self.data.options.period_start;
								delete self.data.options.period_end;
							}
							else{
								self.data.options.period_start = that.startDateStr;
								self.data.options.period_end = that.endDateStr;
							}

							self.refresh( 1 );
						},

						options: closedateOptions,
						activeIndex: activeIndex
					});
				}
				else self.refresh( 1 );
			}
			else if( $date.length==1 ){
				$date.datepicker({
					onSelected: function ( d ) {
						self.data.options.date = PHP.dateJStoPHP( d );
						self.data.options.pager = 1;
						self.refresh( 1 );
					},

					onComplete: function ( d ) {

						self.data.options.date = PHP.dateJStoPHP( d );
						self.refresh( 1 );
					}
				});
			}
			else{
				self.refresh( 1 );
			}

			/**/
			/* search */
			self.$elem.find('.search-input').keydown(function(e){
				var text = $.trim( $(this).val() );

				if(e.keyCode == 13 && text!='') {
					self.search( text );
				}
			}).keyup(function(e){
				var text = $.trim( $(this).val() );

				if( text=='' && text!= self.data.options.q ){
					self.search( text );
				}
			});
			self.$elem.find('.form-search').submit(function (e) {
				var text = $.trim( $(this).find('.search-input').val() );

				if( text!='' ){
					self.search( text );
				}
				
				e.preventDefault();
			});

			
			if( self.$header ){
				self.$header.find('select[ref=selector]').change(function () {

					self.data.options.pager = 1;
					self.data.options[ $(this).attr('name') ] = $(this).val();

					self.refresh( 1 );
				});
				
			}

			self.$elem.find('select[role=choose]').change(function () {

				if( $(this).data('options') ){
					$.each($.parseJSON( $(this).attr('data-options') ), function(i, obj) {
						self.chooseSelect( obj );	
					});
				}
				else{						
					self.chooseSelect( $(this).data() );						
				}
			});
		},
		chooseSelect: function (data, callback) {
			var self = this;

			var $el = self.$elem.find('select[name='+data.id+']'), _get = {};

			$.each( self.$elem.find('select[role=choose][data-id='+data.id+']'), function(index, el) {
				_get[$(this).attr('name')] = $(this).val();
			});



			$.get( data.url, _get, function (res) {
							
				$el.empty();

				var count = 0;
				var options = $.map( res, function (obj) {

					count+= parseInt( obj.count );
					return $('<option>', {
						value: obj.id,
						text: obj.name+ ( obj.count ? ' ('+obj.count+')':'' )
					})[0];
				});

				$el.append( $('<option>', {
					value: '',
					text: 'All' // (' + count + ')
				}), options);

				/*self.data.options.pager = 1;
				self.data.options[ $el.attr('name') ] = $el.val();
				self.refresh( 300 );*/

			},'json');
		},

		search: function (text) {
			var self = this;

			self.data.options.pager = 1;
			self.data.options[ 'q' ] = text;
			self.is_search = true;
			self.refresh( 500 );
		},
		setSelectedDate: function () {
			var self = this;
		},

		selection: function (checked, item) {
			var self = this;

			if( item == 'all' ){
				$.each(self.$tablelists.find('tr'), function (i, obj) {
					var item = $(this);

					if(checked==true && !item.hasClass('has-checked')){
						self.selectItem(item);
					}
					else if(checked==false && item.hasClass('has-checked')){
						self.cancelItem(item);
					}
				});
			}
			else{
				if(checked){
					self.selectItem(item);
				}
				else{
					self.cancelItem(item);
				}
			}
		},
		selectItem: function (el) {
			var self = this;
			var toggle_checkbox = el.find('input#toggle_checkbox');
			var id = el.attr('data-id');
			toggle_checkbox.prop('checked', true);
			el.addClass('has-checked');

			self.ids[ parseInt(id) ] = el;
			self.active();
		},
		cancelItem: function (el) {
			var self = this;

			var toggle_checkbox = el.find('input#toggle_checkbox');
			var id = el.attr('data-id');
			toggle_checkbox.prop('checked', false);
			el.removeClass('has-checked');

			delete self.ids[ parseInt(id) ];
			self.active();
		},
		active: function () {
			var self = this;

			var length = Object.keys(self.ids).length;
			if( length > 0){
				self.$actions.addClass('hidden_elem');
				self.$selection.removeClass('hidden_elem').find('.count-value').text( length );
			}
			else{

				self.$selection.addClass('hidden_elem').find('.count-value').text("");
				self.$tabletitle.find('input#checkboxes').prop('checked', false);
				self.$actions.removeClass('hidden_elem');
			}

			self.resize();
		},
		refresh: function ( length ) {
			var self = this;

			if( self.is_loading ) clearTimeout( self.is_loading ); 
			self.$elem.addClass('has-loading');

			if( !self.data.url ) return false;
			self.is_loading = setTimeout(function () {
				self.fetch().done(function( results ) {

					if( self.$tablelists.parent().hasClass('has-error') ){
						self.$tablelists.parent().removeClass('has-error');
					}

					if( results.settings.total === false ){
						self.$tablelists.parent().addClass('has-error');
						Event.showMsg({text: 'Data Error' });
						return false;
					}
					
					Event.hideMsg();

					self.data = $.extend( {}, self.data, results.settings );
					self.$tablelists.parent().toggleClass( 'has-empty', parseInt(self.data.total)==0 ? true: false );
					
					self.setMore();

					if( results.selector ){
						self.setSelector( results.selector );
					}
					// self.buildFrag();
					// console.log( self.data.options.sql );
					self.display( results.body );
				});
			}, length || 1);
		},
		fetch: function() {
			var self = this;

			// console.log( self.data.options );
			// var qLoad = setTimeout( function () {
			
			// set url
			/*var returnLocation = history.location || document.location,
				href = self.data.url+"?"+$.param(self.data.options),
				title = "";*/
			// history.pushState('', title, href);
			// document.title = title;

			if( self.is_search ) self.$elem.find('.search-input').attr('disabled', true);

			return $.ajax({
				url: self.data.url,
				data: self.data.options,
				dataType: 'json'
			}).always(function () {

				// console.log( self.data.options );
				self.$elem.removeClass('has-loading');

				if( self.is_search ){
					self.$elem.find('.search-input').attr('disabled', false);
					self.$elem.find('.search-input').focus();

					self.is_search = false;
				}
				
			}).fail(function() { 
				self.$elem.addClass('has-error');
			});
		},
		display: function( item ) {
			var self = this;

			$item = $( item );
			self.$tablelists.html( item );
			self.resize();

			Event.plugins( self.$tablelists );

			if( self.$elem.hasClass('offline') ){
				self.$elem.removeClass('offline');
			}	
		},

		setMore: function () {
			var self = this;

			var options = self.data.options;
			var total = self.data['total'],
				pager = options['pager'],
				limit = options['limit'];

			self.$elem.find('#more-link').empty();
			if( total==0 ){
				self.$elem.find('#more-link').addClass('hidden_elem');
				return false;
			} 

			if( self.$elem.find('#more-link').hasClass('hidden_elem') ){
				self.$elem.find('#more-link').removeClass('hidden_elem');
			}
			
			var length = parseInt(total/limit); // floor( );

			if(total%limit){
				length++;
			}

			var first = (limit*pager)-limit+1,
				last = limit*pager;

			if( last>total ){
				last = total;
			}

			self.$elem.find('#more-link').append(
				  $('<span>', {class: 'mhs'}).append(
					  first
					, '-'
					, last
					, $('<span>', {class: 'mhs', text: 'จาก'})
					, PHP.number_format(total)
				)

				, pager > 1 
					? $('<a>', {class: 'prev'}).html( $('<i>', {class: 'icon-angle-left'}) )
					: $('<span>', {class: 'prev disabled fcg'}).html( $('<i>', {class: 'icon-angle-left'}) )

				, pager==length
					? $('<span>', {class: 'next disabled fcg'}).html( $('<i>', {class: 'icon-angle-right'}) )
					: $('<a>', {class: 'next'}).html( $('<i>', {class: 'icon-angle-right'}) )
			);
		},
		setSelector: function ( results ) {
			var self = this;

			$.each(results, function (key, lists) {
				
				$item = self.$elem.find('select[ref=selector][name='+key+']');
				
				if( $item ){
					$item.empty();

					$.each(lists, function (i, val) {
						$item.append( $('<option>', {
								'text': val.text + ( val.total>0? ' ('+val.total+')':'' ) ,
								'value': i,
								'selected': val.current
							})
						);
					});
					
				}
			});
		},


		action: function ( url ) {
			var self = this;

			if( Object.keys(self.ids).length==0 ) return false;
			
			var ids = [];
			for (var i in self.ids) {
				var obj = self.ids[i];

				ids.push( i );
			}

			Dialog.load( url, {
				ids: ids,
				callback: 1
			}, {
				onSubmit: function ($d) {
					
					var $form = $d.$pop.find('form');

					if( $form.hasClass('js-print') ){
						self.print( $form );

						self.selection(false, 'all');
						Dialog.close();
					}
					else{
						Event.inlineSubmit( $form ).done(function( result ) {
							Event.processForm($form, result);
						});
					}
					
				},
				onOpen: function () {
					
				},
				onClose: function () {
					// self.selection(false, 'all');
				}
			});

		},

		print: function ($form) {
	    	var self = this;

	    	var mm_px = 3.779528;

            var h = $(window).height();
            var w = 210*mm_px;

            var params = 'status=0,title=0,scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width='+w+',height='+h+',left=0,top=0';

            var mapForm = $form[0];

            map = window.open( '', '_print', params );

            if (map) { 
            	mapForm.submit();
            	Dialog.close()
            }
            else{
              alert('You must allow popups for this map to work.');
            }	
	    },
	};

	$.fn.listpage2 = function( options ) {
		return this.each(function() {
			var $this = Object.create( List );
			$this.init( options, this );
			$.data( this, 'listpage2', $this );
		});
	};

	$.fn.listpage2.options = {
		options: {
			pager: 1
		},
		/*onOpen: function() {},
		onClose: function() {}*/
	};
	
})( jQuery, window, document );