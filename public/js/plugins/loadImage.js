// Utility
if ( typeof Object.create !== 'function' ) {
    Object.create = function( obj ) {
        function F() {};
        F.prototype = obj;
        return new F();
    };
}

(function( $, window, document, undefined ) {

    var LoadImage = {
        init: function (options, elem) {
            var self = this;
            self.$elem = $(elem);

            self.$elem.addClass('has-loading').html('<div class="pic-loading"></div>');
            self.options = options;
            
            self.$scroll = options.$scroll ? $(options.$scroll): $(window);

            if( self.options.width && self.options.height ){
                self.options.height = (self.options.height * self.$elem.outerWidth() )/ self.options.width;
                self.options.width = self.$elem.outerWidth();
            }

            self.options.width = self.options.width || self.$elem.outerWidth();
            self.options.height = self.options.height || self.$elem.outerHeight();

            if( self.options.height=='auto' ){
                self.options.height = '';
            }

            self.scroll();
            self.$scroll.scroll(function() {
                self.scroll();
            });
        },

        scroll: function () {
            var self = this;

            var currTop = self.$scroll.scrollTop() + ( self.$scroll.height() + 400);
            if( self.$elem.hasClass('has-loading') && currTop > self.$elem.offset().top  ){
                self.loadImg();
            }
        },
        loadImg: function () {
            var self = this;

            var img = new Image();
            img.src = self.options.url + '?w=' + self.options.width + '&h=' + self.options.height;
            img.onload = function () {

                self.$elem.html( $(img).attr('alt', self.options.alt || '').css('opacity', 0) );
                $(img).animate({opacity: 1}, 300);
                self.$elem.removeClass('has-loading');
            }
        }

    };

    $.fn.loadImage = function( options ) {
        return this.each(function() {
            var $this = Object.create( LoadImage );
            $this.init( options, this );
            $.data( this, 'loadImage', $this );
        });
    };

})( jQuery, window, document );