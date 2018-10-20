// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Slideshow = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(elem);
			
			self.setup();

			if( self.limit <= 1 ){

				self.inx = 0;
				self.display( 1 );
				return false;
			} 

			self.options = $.extend( {}, $.fn.slideshow.options, options );
			
			
			self.refresh( 1 );
			self.is_first = true;

			self.control();
		},

		setup: function(){
			var self = this;
			
			self.limit = self.$elem.find('#slide-image>li').length;

			self.inx = -1;
			self.$ol = self.$elem.find('#slide-image');
			self.$ol.find('li').each(function () {
				$(this).css({opacity:0, display: 'none'});
			});


		},
		refresh: function( length ){
			var self = this;
			
			self.timeout = setTimeout(function () {
				
				self.buildFrag();
				self.display();
				
				if ( self.options.refresh ) {
					self.refresh();
				}
				
            }, length || self.options.refresh );
		},
		
		buildFrag:function(){
			var self = this;

			self.inx = self.inx>=(self.limit-1) ||  self.inx < 0
				? 0
				: self.inx+1;
		},
		
		display: function( length ){
			var self = this;

			self.effect['fade'](self, length);
			self.is_first = false;

			// dotnav
			var ol = self.$elem.find('.dotnav ul');
			ol.find('.current').removeClass('current');
			$(ol.find('li')[self.inx]).find('a').addClass('current');
		},

		effect: {
			fade: function ( then, length ) {
				var self = then;

				
				var active = self.$ol.find( 'li.on' );
				if( active.length ){
					active.animate({opacity:0}, length || 300, function()
					{
						active.hide().removeClass('on');
					});
				}

				var current = $(self.$ol.find( 'li' )[self.inx]);
				if( self.is_first ){
					current.css({opacity:1, display:'block'}).addClass('on');
				}
				else{
					current.show().animate({opacity:1}, length || 300).addClass('on');
				}
			}
		},

		control: function(){
			var self = this;

			$('.dotnav-item', self.$elem).click(function(e){
				e.preventDefault();

				if( $(this).hasClass('current') ){
					return false;
				}

				clearTimeout( self.timeout );
				self.inx = $(this).parent().index();

				self.display();
				self.refresh();
			});

			$('.prev, .next', self.$elem).click(function(e){
				e.preventDefault();

				clearTimeout( self.timeout );

				if( $(this).hasClass('prev') ){
					self.inx --;

					if(self.inx < 0){
						self.inx = self.limit;
						self.inx --;
					}
				}
				else{
					self.inx ++;

					if(self.inx > (self.limit-1)){
						self.inx = 0;
					}
				}

				self.display();
				self.refresh();
			});
		}
	};

	$.fn.slideshow = function( options ) {
		return this.each(function() {
			var $this = Object.create( Slideshow );
			$this.init( options, this );
			$.data( this, 'slideshow', $this );
		});
	};

	$.fn.slideshow.options = {
		speed: 500,
		// wrapEachWith: '<div></div>',
		auto: true,
		refresh: 13000,
		random: true,
	};
	
})( jQuery, window, document );