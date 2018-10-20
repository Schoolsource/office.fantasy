// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var LoginPin = {
		init: function ( options, elem ) {
			var self = this;

			self.$elem = $(elem);
			self.options = options;

			$encrypt = self.$elem.find('.pin-encrypt');
			self.length = 4;

			self.pin = '';

			self.$elem.find('[data-pin]').click(function () {
				
				var pin = $(this).attr('data-pin');

				if( pin=='c' ){ self.clear(); return false; }
				if( pin=='s' ){ self.submit(); return false; }

				if( self.pin.length >= self.length ) return false;

				self.pin += pin;
				self.checkPIN();
			});

			$(window).keydown(function (e) {

				if( !isNaN(e.key) ){

					self.pin += parseInt( e.key );
					self.checkPIN();
				}
		    });
		},
		checkPIN: function () {
			var self = this;

			$.each( $encrypt.find('span'), function (i) {

				$(this).toggleClass('active', self.pin.length>i);
			} );


			if( self.pin.length==self.length ){
				self.submit();
			}
		},
		clear: function () {
			var self = this;

			self.$elem.find(':input[name=pin]').val('');
			self.pin = '';
			self.checkPIN();
		},
		submit: function () {
			var self = this;

			self.$elem.find('[data-pin]').prop('disabled', 1).addClass('disabled');
			self.$elem.addClass('has-loading');
			if( $encrypt.hasClass('error') ){
				$encrypt.removeClass('error');
			}

			self.$elem.find(':input[name=pin]').val( self.pin );
			Event.inlineSubmit( self.$elem ).done(function( result ) {

				if( result.error )  {
					$encrypt.addClass('error');
					self.clear();
					return false;
				}
				
				window.location = result.url; 
			}).always(function() {
				self.$elem.find('[data-pin]').prop('disabled', false).removeClass('disabled');
				self.$elem.removeClass('has-loading');
				
			}).fail(function() {
				$encrypt.addClass('error');
				self.clear();
			});
			
		}
	}
	$.fn.loginPin = function( options ) {
		return this.each(function() {
			var $this = Object.create( LoginPin );
			$this.init( options, this );
			$.data( this, 'loginPin', $this );
		});
	};

})( jQuery, window, document );


