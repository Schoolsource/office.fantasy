window.onbeforeunload = function (event) {
    /*var message = 'Important: Please click on \'Save\' button to leave this page.';

    if (typeof event == 'undefined') {
        event = window.event;
    }
    if (event) {
        event.returnValue = message;
    }

    return message;*/
};

var Dialog = {
	load: function (url, getData, options, callback) {
		Event.showMsg({ load:true });
		this.fetch( url, getData ).done(function( results ) {

			if( results.error ){

				if( !results.message || results.message=='' ){
					results.message = 'Error!';
				}

				Event.showMsg({text: results.message, load: 1, auto: 1});
				return false;
			}
			Dialog.open( $.extend( {}, options, results, callback) );
		});
	},
	fetch: function(url, getData){

		return $.ajax({
			url: url,
			data: getData,
			dataType: 'json'
		})
		.always(function() {
			Event.hideMsg();
		})
		.fail(function() { 

			// self.close();
			Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
		});
	},
	open: function (options, callback) {
		var self = this;

		// set options
		self.settings = $.extend( {}, $.fn.dialog.options, options );
		
		if ( typeof options.onClose !== 'function' ) {
			self.close( 1 );
		}
		else{
			self.$old = $('body').find('.model.active.model-dialog');
			self.$old.removeClass('active');
		}	

		if ( typeof callback === 'function' ) {
			self.settings.onOpen = callback;
		}

		self.createDialog();
		// self.$model.data('dialog', self);

		self.buildFrag();

		self.resize();
		$(window).resize(function(){
			self.resize();
		});

		if( self.settings.effect ){
			setTimeout(function(){ self.display(); }, 150);
		}
		else{
			self.display();
		}
	},
	buildFrag: function(){
		var self = this;

		// insert Content
		self.$pop.html( self.setContent( self.settings ) );

		if( self.settings.effect ){
			self.$dialog.addClass( 'effect-' +  self.settings.effect );
		}

		if( self.settings.width ){
			if( self.settings.width=='full'){
				self.settings.width = $(window).width() - 80;
			}
			self.$pop.css("width", self.settings.width);	
		}

		// check btn close
		if( self.$pop.find('[role=dialog-close]').length==0 ){
			self.$close = $('<a/>', { href:"#" }).addClass('model-close').html( $('<i/>', {class: 'icon-remove'}) );
			self.$pop.append( self.$close );
		}
		else{
			self.$close = self.$pop.find('[role=dialog-close]');
			self.$close.removeAttr('role');
		}

		self.$dialog.addClass( self.settings.bg || 'black' );
	},
	resize: function(){
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
	// display
	display: function(){
		var self = this;

		Event.plugins( self.$dialog );
		if( !$( 'html' ).hasClass('hasModel') ){
			setTimeout(function () {
				$( 'html' ).addClass('hasModel');
			},200);
			// 
			self.$doc.addClass('fixed_elem').css('top', $(window).scrollTop()*-1 );
			
		}
		$(window).scrollTop( 0 );
		
		// show
		self.$dialog.addClass("show").addClass('active');

		// set data

		if( typeof self.settings.onOpen === 'function' ){
			self.settings.onOpen( self );
		}

		if( typeof self.settings.onForm === 'function' ){
			self.settings.onForm( self.$pop.find('.model-content')[0] );
		}

		var is_focus = false;
		self.$dialog.find('.model-content').mouseover(function () {
			is_focus = true;
		}).mouseenter(function () {
			is_focus = true;
		}).mouseleave(function () {
			is_focus = false;
		});


		if( self.settings.is_close_bg ){
			self.$dialog.click(function () {
				if( !is_focus ){
					self.close();
				}	
			});
		}

		// save 
		$.data( self.$dialog[0], self );

		// 
		self.resize();
		self.Event();
	},

	Event: function(){
		var self = this;

		self.$close.click(function(e){
			e.preventDefault();
			self.close();
		});

		$('[role=submit]', self.$model).click(function(e){
			e.preventDefault();
			
			if( typeof self.settings.onSubmit === 'function' ){
				self.settings.onSubmit( self, $.data( self.$pop.find('.model-content')[0]) );
			}
		});

		
		if( self.$pop.find(':input[autofocus]').first().length==1 ){
			self.$pop.find(':input[autofocus]').first().focus();
		}
		else if( self.$pop.find(':input[autoselect]').first().length==1 ){
			self.$pop.find(':input[autoselect]').first().select();
		}
	},

	close: function( length ){
		var self = this;

		var $dialog = $('body').find('.model.active.model-dialog');

		var scroll = parseInt( $("#doc").css("top"));
			scroll= scroll<0 ? scroll*-1:scroll;

		$dialog.removeClass("show");
		
		setTimeout( function(){
			$dialog.remove();

			if( $('body').find('.model').not('.hidden_elem').length == 0 ){
				$('html').removeClass('hasModel');

				$("#doc").removeClass('fixed_elem').css('top', "");
				$(window).scrollTop( scroll );

				if($('body').hasClass('overflow-page')){
					$('body').removeClass('overflow-page');
				}
			}

		}, length || 300);

		if( !self.settings ) return false;

		if( self.$old ){
			var data = self.$old.data();
			if( data ){
				if( typeof data.resize === 'function' ){
					data.resize();
				}
			}			
			
		}

		if( typeof self.settings.onClose === 'function' ){
			self.settings.onClose();
			if( self.$old ){
				self.$old.addClass('active');
			}
		}
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

		return $elem;
	},

	createDialog: function(){
		var self = this;

		self.classDefault = "model model-dialog"; //hidden_elem
		self.$pop = $('<div/>', {class: 'model-container'});
		self.$dialog = $('<div/>').addClass( self.classDefault ).html( self.$pop ) ;
		self.$doc = $('#doc');
		
		$('body').append( self.$dialog );
	}
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
	$.fn.dialog = function( options ) {
		return this.each(function() {

			var $elem = $(this);
			var url = $elem.attr( "href" );
			if( !url ) return false;

			$elem
				.removeAttr( "href" )
				.click( function(e){
					e.preventDefault();
					Dialog.load( url, {}, options );
				});

			// $.data( this, 'dialog', e );
		});
	};

	$.fn.dialog.options = {
		effect: 5,
		onOpen: function(){},
		onClose: function(){}
	};

})( jQuery, window, document );