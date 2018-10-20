// Utility
if ( typeof Object.create !== 'function' ) {
  Object.create = function( obj ) {
    function F() {};
    F.prototype = obj;
    return new F();
  };
}

(function( $, window, document, undefined ) {

	var SetSticker = {
	    init: function( options, elem ) {
	        
	      var self = this;
	      
	      	self.elem = elem;
	      	self.$elem = $( elem );
	     	
	      	self.data = $.extend( {}, $.fn.setsticker.data, options );
	      	self.setElem();
	      	self.Events();

	      	console.log( 'setSticker' );
	    },
	    setElem: function () {
	    	var self = this;

	    	self.nQueue=0;
	    	self.queue = [];

	    	self.$btnSubmit = self.$elem.find('.btn-submit');
	    	self.t = '';
	    },
	    Events: function () {
	    	var self = this;
	    	

	    	self.$elem.find('[data-actions=hide]').click(function(e) {
	    		
	    		var $box =  $(this).closest('[data-id]');
	    		var is_hide = !$box.hasClass('has-hide');

	    		
	    		$box.toggleClass('has-hide', is_hide).find(':input').toggleClass('disabled', is_hide).prop('disabled', is_hide);
	    	
	    	});

	    	self.$elem.find(':input').change(function() {
	    		var $box =  $(this).closest('[data-id]');

	    		$box.addClass('is-save');
	    		setTimeout( function () {
	    			
	    			self.queue.push({
	    				ative: false,
	    				id: $box.data('id'),
	    				$elem: $box
	    			});

	    			self.save();
	    		}, 1);
	    	});
	    },

	    save: function () {
	    	var self = this;


	    	if( self.has_loading ) return false;
	    	self.has_loading = true;
	    	self.$btnSubmit.addClass('disabled').prop('disabled', true);

	    	var dataPost = self.queue[self.nQueue];
	    	console.log( 'loading..', dataPost );

	    	self.t = setTimeout( function () {

	    		dataPost.$elem.removeClass('is-save');
	    		self.has_loading = false;

	    		self.nQueue++;
	    		if( self.queue[self.nQueue] ){
	    			self.save();
	    		}
	    		else{
	    			self.$btnSubmit.removeClass('disabled').prop('disabled', false);
	    		}
	    	}, 3000);
	    	// $.get('d', )
	    },

	    countval: function() {
	    	var self = this;

	    	var $drawer = self.$elem.find('.js-drawer');
	    	$drawer.toggleClass('is-visible', self.data.total>0);

	    	var max = self.data.options.limit*self.data.options.pager;
	    	var min =  (max - self.data.options.limit)+1;
			if( max >= self.data.total ) max = self.data.total;

	    	// self.$listsbox.find('li').not('.is-hide').length
	    	$drawer.find('.js-selected-countval').text( min +' - ' + max );
	    	$drawer.find('.js-total-countval').text( self.data.total );

	    	$uipager = self.$elem.find('.ui-pager');
	    	$uipager.empty();

	    	if( self.data.total > 0){
		    	var pages_length = self.data.total / self.data.options.limit;
		    	var min = 1;
		    	for (var i = 1; i <= parseInt(pages_length)+1; i++) {
		    		
		    		max = self.data.options.limit*i;

		    		if( max >= self.data.total ){
		    			max = self.data.total;
		    		}
		    		a = $('<a>', {'data-page': i}).text( min + '-' + max );

		    		if( i==self.data.options.pager ){
		    			a.addClass('active');
		    		}

		    		$uipager.append( a );
		    		min = max+1;
		    	}
	    	}
	    },

	    changeDate: function () {
	    	var self = this;
	    	
	    	$.each( self.$elem.find('[name=end_year] option'), function () {

	    		if( $(this).val()!='' &&  parseInt( $(this).val() ) < self.currentYear ){
	    			$(this).prop('disabled', true);
	    		}
	    		else{
	    			$(this).prop('disabled', false);
	    		}
					
	    	} );

	    	if( self.currentYear=='' ){
	    		self.$elem.find('[name=end_year] option').first().prop('disabled', true);
	    	}
	    	else{
	    		self.$elem.find('[name=end_year]').val( self.currentYear );
	    	}
	    }
	}
	$.fn.setsticker = function( options ) {
		return this.each(function() {
		  	var $this = Object.create( SetSticker );
		  	$this.init( options, this );
		  	$.data( this, 'setsticker', $this );
		});
	};
	$.fn.setsticker.data = {};
})( jQuery, window, document );
