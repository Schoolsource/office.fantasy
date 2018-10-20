var _LightBox = {
	lists: {},
};

var Lightbox = {
	load: function ( url, data, options ) {
		$('<div>').lightbox( 'load', url, data, options );
	},
	open: function ( data ) {
		$('<div>').lightbox( 'open', data );
	},
}

// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var LightBox = {	
		init: function (options, elem) {
			var self = this;

			self.$elem = $(elem);
			self.settings = $.extend( {}, $.fn.lightbox.settings, options );

			self.url = self.$elem.attr( "href" );
			if( !self.url ) return false;

			// Event
			self.$elem
				.removeAttr( "href" )
				.click( function(e){
					e.preventDefault();

					console.log( 'href' );
					self.load();
				});
		},
		load: function () {
			var self = this;
			if( !self.url ) return false;

			console.log( 'load', this.url, this.data  );

			self.fetch().done(function( results ) {
				if( results.error ){

					if( !results.message || results.message=='' ){
						results.message = 'Error!';
					}

					Event.showMsg({text: results.message, load: 1, auto: 1});
					return false;
				}

				self.content = results;

				self.buildFrag();

				setTimeout( function () {
					self.display();
				}, 1 );

				
			});
			
		},
		fetch: function () {
			var self = this;

			return $.ajax({
				url: self.url,
				data: self.getData || {},
				dataType: 'json'
			})
			.always(function() { Event.hideMsg(); })
			.fail(function() { 

				var online = navigator.onLine;
				if( online ){

					/*var msg = 'This "'+self.url+'" could not be loaded..';
					if( self.url='' || !self.url ){
						msg = 'Error! URL';
					}*/
					msg = 'Error! URL';

					Event.showMsg({ text: msg, load: true , auto: true });
				}
				else{
					Event.showMsg({ text: "Unable to connect to internet. Check your Internet connection.", load: true , auto: true });
				}
				// Unable to connect to chat. Check your Internet connection.
			});	
		},
		buildFrag: function () {
			var self = this;

			self.createDialog();

			var effect = self.content.effect || self.settings.effect;
			self.$dialog.addClass( 'effect-' +  effect );

			var bg = self.content.bg || self.settings.bg || 'black'; 
			self.$dialog.addClass( bg );


			if( self.content.width ){
				if( self.content.width=='full'){
					self.content.width = $(window).width() - 80;
				}
				self.$pop.css("width", self.content.width);	
			}

			self.$pop.html( self.setContent( self.content ) );


			// check btn close
			if( self.$pop.find('[role=dialog-close]').length==0 ){
				self.$dialog.append(
					$('<div>', {class: 'dialog-close'}).append(
						  $('<span>', {class: 'dialog-close-tip'}).text('Press esc to close.')
						, $('<span>', {class: 'dialog-close-icon', 'data-dialog': 'close'}).html( $('<i>', {class: 'icon-remove'}) )
					)
				);

				self.is_esc = true;
			}


			Event.plugins( self.$dialog );
			self.Events();
		},
		display: function () {
			var self = this;

			if( !$('html').hasClass('hasModel') ){
				var top = $(window).scrollTop();

				$('#doc').addClass('fixed_elem').css('top', top*-1);
				$('html').addClass('hasModel');
			}

			self.$dialog.addClass('show');
			self.active = true;

			self.resize();
			console.log( _LightBox );
		},
		close: function () {
			var self = this;

			if( $('.model').length == 1){
				var scrollTop = parseInt( $('#doc').css('top'));
				$('#doc').removeClass('fixed_elem').removeAttr('style');

				$(window).scrollTop(scrollTop*-1);
			}

			self.$dialog.removeClass("show");

			delete _LightBox.lists[ _LightBox.current ];
			if( _LightBox.prev ){
				_LightBox.current = _LightBox.prev;
				_LightBox.prev = null;

				_LightBox.lists[ _LightBox.current ].dialog.removeClass('hide').addClass('active');
				
			}
			else{
				_LightBox.current = null;
			}
			
			console.log( _LightBox );
			setTimeout( function(){
				self.$dialog.remove();

				if( $('.model').length == 0 ){
					$('html').removeClass('hasModel');
				}

			}, 300);

			console.log( 'close', self.key );	
		},
		createDialog: function(){
			var self = this;

			self.classDefault = "model model-dialog active"; //hidden_elem
			self.$pop = $('<div/>', {class: 'model-container'});
			self.$dialog = $('<div/>').addClass( self.classDefault ).html( self.$pop ) ;
			self.$doc = $('#doc');
			
			var length = 'key-' + $('.model-dialog').length;

			self.key = length;

			if( _LightBox.current ){
				
				_LightBox.lists[ _LightBox.current ].dialog.removeClass('active').addClass('hide');
				_LightBox.prev = _LightBox.current;
			}

			$('body').append( self.$dialog );
			_LightBox.lists[ length ] = {
				dialog: self.$dialog,
			};

			_LightBox.current = length;
		},
		setContent: function( s ){
			// content
			var $elem = $( s.form || '<div/>' )
				.addClass("model-content")
				.addClass( s.addClass )
				.addClass( s.style ? 'style-'+s.style: '' );

			// Input hidden
			if( s.hiddenInput ){
				$elem.append( this.setHiddenInput( s.hiddenInput ) );
			}

			// Title
			if( s.title ){
				$elem.append( $('<div/>', {class: 'model-title'}).html(s.title) );
			}

			// Summary
			if( s.summary ){
				$elem.append( $('<div/>', {class: 'model-summary'}).html(s.summary) );
			}

			// Body
			if( s.body ){
				$elem.append( $('<div/>', {class: 'model-body'}).html(s.body) );
			}

			// Buttons
			if( s.button || s.bottom_msg ){

				var $buttons = $('<div/>', {class: 'model-buttons clearfix'});

				if ( s.button ){
	                $buttons.append( $('<div/>', {class: 'rfloat mlm'}).html(s.button) );
				}

	            if ( s.bottom_msg ){
	            	$buttons.append( $('<div/>', {class: 'model-buttons-msg'}).html(s.bottom_msg) );
	            }

	            $elem.append($buttons);
			}

			// Footer
			if( s.footer ){
				$elem.append( $('<div/>', {class: 'model-footer'}).html(s.footer) );
			}

			if( typeof this.settings.onSubmit === 'function' ){
				$elem.removeClass('js-submit-form');
			}

			return $elem;
		},
		setHiddenInput: function( data ){
			return $.map( data, function(obj, i){
				return $('<input/>', {
					class: 'hiddenInput',
					type: "hidden",
					autocomplete: "off"
				}).attr( obj )[0];
			});
		},

		resize: function () {
			var self = this;

			var area = $(window).height(), margin = 80;

			if( self.settings.height ){

				var height = self.settings.height;
				var overflow = self.settings.overflowY || 'scroll';
				var $inner = self.$pop.find( self.settings.$height || '.model-body' );

				var outer = 0;
					inner = $inner.outerHeight();

				area -= margin;

				outer += self.$pop.find('.model-title').outerHeight();
				outer += self.$pop.find('.model-summary').outerHeight();
				outer += self.$pop.find('.model-buttons').outerHeight();

				if( height=='auto' && (inner+outer)>area ){
					height = parseInt(area-outer);
				}
				else if( height=='full' ) {
					self.$pop.find('.model-body').css('padding', 0);
					height = parseInt(area-outer);
				}

				$inner.css({
					height: height,
					overflowY: overflow
				});
			}

			// console.log( self.$pop.height(), area-margin );
			if( self.$pop.height() > (area-margin) ){
				$('body').addClass('overflow-page');
			}
			else if($('body').hasClass('overflow-page')){
				$('body').removeClass('overflow-page');
			}
			
			// self.resizeHeight();
			var marginTop = ($(window).height()/2) - (self.$pop.height()/2);

			marginTop = marginTop<25 ? 25:marginTop;
			self.$pop.css( 'margin-top', marginTop);


		},

		Events: function () {
			var self = this;

			$(document).keyup(function(e) {

				console.log( self.is_esc );
			    if(e.which == 27 && self.is_esc) {
			    	self.close();
			    }
			});

			self.$dialog.delegate('[data-dialog=close], [role=dialog-close], [role=close]', 'click', function() {
				self.close();
			});

			$('[role=submit]', self.$model).click(function(e){
				e.preventDefault();

				if( typeof self.settings.onSubmit === 'function' ){
					self.settings.onSubmit(self, self.$pop.find('form')[0] );
				}
			});
		},

	}

	$.fn.lightbox = function( e, url, getData, options ) {
		return this.each(function() {

			var $this = Object.create( LightBox );

			if(	e=='open' ){

				getData = getData||{};
				$this.settings = $.extend( {}, $.fn.lightbox.settings, getData );
				$this.content = url || {};

				$this.buildFrag();

				setTimeout( function () {
					$this.display();
				}, getData.delay || 1 );

			}else if( e=='load' ){
				$this.url = url;
				$this.getData = getData;
				$this.settings = $.extend( {}, $.fn.lightbox.settings, options||{} );

				$this.load();
			}
			else{
				$this.init( e, this );
			}
			
			$.data( this, 'lightbox', $this );
		});
	};
	$.fn.lightbox.settings = {
		effect: 5,
		onOpen: function(){},
		onClose: function(){}
	}
	
})( jQuery, window, document );