// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var main = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();

			self.options = $.extend( {}, $.fn.main.options, options );

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});

			self.$elem.addClass('on');

			self.Events();


			if( self.$elem.find('[data-load]').length==1 ){

				self.$load = self.$elem.find('[data-load]');

				// set data
				self.data = {
					options: {}
				}
				self.data.url = self.$load.attr('data-load'); self.$load.removeAttr('data-load');

				
				$.each( self.$load.find('select[ref=selector]'), function() {
					self.data.options[ $(this).attr('name') ] = $.trim( $(this).val() );
					// console.log( $(this).attr('name') );
				} );
				

				self.$load.parent().css('position', 'relative');
				self._resize();
				$( window ).resize(function () {
					self._resize();
				});

				$('.navigation-trigger').click(function () {
					self._resize();
				});

				// set elem 
				self.$tableHeader = self.$load.find('.SettingCol-tableHeader');
				self.$tableBody = self.$load.find('.SettingCol-tableBody');

				// event
				
				self.refresh();


				self._Events();
			}
		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);
			self.$elem.attr('id', 'mainContainer')
			self.$elem.find('[role]').each(function () {
				if( $(this).attr('role') ){
					var role = "$" + $(this).attr('role');
					self[role] = $(this);
				}
				
			});
		},
		resize: function () {
			var self = this;

			var outer = $( window );
			var offset = self.$elem.offset();
			var right = 0, leftw = 0;
			var fullw = outer.width() - (offset.left+right);
			var fullh = (outer.height() + outer.scrollTop()) - $('#header-primary').outerHeight();

			if( self.$right ){
				var rightWPercent = self.$right.attr('data-w-percent') || 30;
				var rightw = (fullw*rightWPercent) / 100;

				if( self.$right.attr('data-width') ){
					rightw = parseInt( self.$right.attr('data-width') );
				}

				self.$right.css({
					width: rightw,
					height: fullh,
					position: 'absolute',
					top: 0,
					right: 0
				});

				self.$content.css({
					marginRight: rightw
				});

				right += rightw;
			}

			if( self.$colRigth ){
				var rightWPercent = self.$colRigth.attr('data-w-percent') || 20;
				var rightw = (fullw*rightWPercent) / 100;

				if( self.$colRigth.attr('data-width') ){
					rightw = parseInt( self.$colRigth.attr('data-width') );
				}

				self.$main.css({
					marginRight: rightw,
				});

				self.$colRigth.css({
					position: 'absolute',
					top: 0,
					right: 0,
					bottom: 0,
					width: rightw
				});
			}

			var left = offset.left;

			if( self.$left ){
				var leftw = (fullw*25) / 100;
				if( self.$left.attr('data-width') ){
					leftw = parseInt( self.$left.attr('data-width') );
				}
				else if( self.$left.attr('data-w-percent') ){
					leftw = (fullw*parseInt( self.$left.attr('data-w-percent') )) / 100;
				}

				self.$left.css({
					width: leftw,
					height: fullh,
					position: 'absolute',
					top: 0,
					left: 0
				});

				if( self.$leftContent && self.$leftHeader ){
					self.$leftContent.css({
						height: fullh-self.$leftHeader.outerHeight(),
						overflowY: 'auto'
					});
				}
	
				self.$content.css({
					marginLeft: leftw,
				});

				left+=leftw;
			}

			if( self.$topbar ){
				self.$topbar.css({
					height: self.$topbar.outerHeight(),
					position: 'fixed',
					top: offset.top,
					left: offset.left,
					right: right
				});

			}

			if( self.$topbar ){
				fullh -= self.$topbar.outerHeight();
				self.$elem.css('padding-top', self.$topbar.outerHeight());

				if( self.$left ){
					self.$left.css('top', self.$topbar.outerHeight());
				}

				if( self.$right ){
					self.$right.css('top', self.$topbar.outerHeight());
				}
			}

			if( self.$toolbar ){
				fullh -= self.$toolbar.outerHeight();


				if( self.$colRigth ){

					self.$colRigth.css({
						top: self.$toolbar.outerHeight(),
					});
				}
			}

			if( self.$footer ){

				self.$footer.css({
					position: 'fixed',
					left: offset.left+leftw,
					right: right,
					// backgroundColor: '#f8f8f8',
					// "border-top": "1px soile #efefef"
				});
				fullh -= self.$footer.outerHeight();
			}

			self.$main.css({
				height: fullh,
				overflowY: 'auto'
			});

			if( self.$toolbar && self.$toolbarControls  ){

				self.$toolbarControls.css({
					height: self.$toolbar.outerHeight(),
					position: 'fixed',
					left: offset.left+leftw,
					right: right,
				});
				
			}
		},

		Events: function () {
			var self = this;

			$('.navigation-trigger').click(function () {
				self.resize();
			});

		},

		_resize: function () {
			var self = this;

			var outer = $( window );
			var offset = self.$elem.offset();
			var top = 0, left = offset.left, right = 0, fullw = self.$load.outerWidth();
			var $header = self.$load.find('.SettingCol-header');
			var $main = self.$load.find('.SettingCol-main');

			right += self.$load.parent().outerWidth()-fullw;
			
			if( self.$left ){
				left += self.$left.outerWidth();
			}

			if( $header.length==1 ){

				$header.css({
					position: 'fixed',
					top: 0,
					left: left,
					right: right,
					zIndex: 50
				});
				top += $header.outerHeight();

			}

			if( self.$tableHeader ){
				self.$tableHeader.css({
					position: 'fixed',
					top: top,
					left: left,
					right: right,
					zIndex: 50
				});

				top += self.$tableHeader.outerHeight();
			}

			$main.css({
				paddingTop: top
			});
			
			if( self.$tableBody ){

				var totalW = 0;
				self.$tableBody.find('table tr:first>td').each(function ( i ) {

					var td = $(this);
					var th = self.$tableHeader.find('table th[data-col='+i+']');

					if( td.hasClass('name') ) return;

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


				if( totalW > 0 && self.$tableBody.find('td.name .ellipsis').length){

					totalW += parseInt( self.$tableBody.find('td.name').css('padding-left') );
					totalW += parseInt( self.$tableBody.find('td.name').css('padding-right') );
					self.$tableBody.find('td.name .ellipsis').css('max-width',  fullw-totalW);
				}

			}

		},

		refresh: function ( length ) {
			var self = this;

			if( self.is_loading ) clearTimeout( self.is_loading ); 

			if ( self.$load.hasClass('has-error') ){
				self.$load.removeClass('has-error')
			}

			if ( self.$load.hasClass('has-empty') ){
				self.$load.removeClass('has-empty')
			}

			self.$load.addClass('has-loading');

			self.is_loading = setTimeout(function () {

				self.fetch().done(function( results ) {
					
					self.data = $.extend( {}, self.data, results.settings );
					self.$load.toggleClass( 'has-empty', parseInt(self.data.total)==0 ? true: false );
					
					self.setMore();

					/*if( results.selector ){
						self.setSelector( results.selector );
					}*/

					self.display( results.body );
				});
			}, length || 1);
		},
		fetch: function () {
			var self = this;

			var options = {};
			$.each( self.data.options, function (name, value) {
				if( value ) options[name] = value;
			} );
			self.data.options = options;

			var req = $.param( options );
			if( req ){
				var returnLocation = history.location || document.location,
					href = self.data.url+"?"+req,
					title = "";

				history.pushState('', title, href);
				document.title = title;
			}
			

			if( self.is_search ){
				self.$load.find('.search-input').attr('disabled', true);
			}

			return $.ajax({
				url: self.data.url,
				data: self.data.options,
				dataType: 'json'
			}).always(function () {

				self.$load.removeClass('has-loading');

				if( self.is_search ){
					self.$load.find('.search-input').attr('disabled', false);
					self.$load.find('.search-input').focus();

					self.is_search = false;
				}
				
			}).fail(function() { 
				self.$load.addClass('has-error');
			});

		},
		display: function( item ) {
			var self = this;

			$item = $( item );
			self.$tableBody.html( item );
			Event.plugins( self.$tableBody );

			if( self.$load.hasClass('offline') ){
				self.$load.removeClass('offline');
			}
			
			self._resize();

			// console.log( $item  );
			/*if ( self.options.transition === 'none' || !self.options.transition ) {
				self.$elem.html( self.tweets ); // that's available??
			} else {
				self.$elem[ self.options.transition ]( 500, function() {
					$(this).html( self.tweets )[ self.options.transition ]( 500 );
				});
			}*/
		},
		setMore: function () {
			var self = this;

			var options = self.data.options;
			var total = self.data['total'],
				pager = options['pager'],
				limit = options['limit'];


			if( options.unlimit ) {
				self.$elem.find('#more-link').text( 'Totle: '. PHP.number_format( total ) );
				return false;
			} 

			self.$elem.find('#more-link').empty();
			if( total==0 ){
				self.$elem.find('#more-link').html( $('<span>', {class: 'fcg', text: 'ไม่พบผลลัพธ์'}) );
				return false;
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

			self.$load.find('#more-link').append(
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

		_Events: function () {
			var self = this;

			self.$load.find('.js-refresh').click(function (e) {

				delete self.data.options.time;

				self.refresh( 200 );
				e.preventDefault();
			});


			self.$load.find('#more-link').delegate('a.next, a.prev', 'click', function (e) {
				
				pager = parseInt( self.data.options.pager );
				pager += $(this).hasClass('prev')? -1 : 1;

				self.data.options.pager = pager;
				self.refresh( 1 );
				e.preventDefault();
			});

			self.$load.mouseover(function () {
				self.resize();
			});


			/*$('input#checkboxes', self.$tableBody).change( function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), 'all');
			});*/

			self.$load.find('select[ref=selector]').change(function () {

				
				self.data.options.q = self.$load.find('.search-input').val();
				self.data.options.pager = 1;
				self.data.options[ $(this).attr('name') ] = $(this).val();

				self.refresh( 1 );
			});


			var $searchInput = self.$load.find('.search-input');
			var searchVal = $searchInput.val();
			self.$load.find('.form-search').submit(function (e) {
				e.preventDefault();
				var text = $.trim( $(this).find('.search-input').val() );

				if( text!='' ){
					searchVal = text;
					self._search( text );
				} 
				
			});
			
			$searchInput.keyup(function () {
				var val  = $.trim( $(this).val() );
				if( val=='' && val!=searchVal ){
					searchVal = '';
					self._search( searchVal );
				}				
			});

		},
		_search: function (text) {
			var self = this;

			self.data.options.pager = 1;
			self.data.options[ 'q' ] = text;
			self.is_search = true;
			self.refresh( 500 );
		},
	};

	$.fn.main = function( options ) {
		return this.each(function() {
			var $this = Object.create( main );
			$this.init( options, this );
			$.data( this, 'main', $this );
		});
	};

	$.fn.main.options = {
		url: '',
		sort: 'date'
	};
	
})( jQuery, window, document );