// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {
	
	var Editor2 = {
		init: function( options, elem ) {
			var self = this;

			self.$elem = $( elem );
			self.options = $.extend( {}, $.fn.editor2.options, options );

			console.log( self.options );

			self.$textarea = $('<textarea/>', {
            	name: self.$elem.attr('name'), 
            	text: self.$elem.val()
            }).css({
            	height: 0,
            	padding:0,
            	border: 0
            });


            var placeholder = $('<div>', {class: 'editor-wrap'});
			self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

			/*

			// self.$elem.css( 'display', 'none' );

			self.max_width = self.options.width || self.$elem.width();
			self.max_height = self.options.height || 300;
			self.options.text = self.$elem.text();

			

            self.$loading = $('<div class="pam tac uiBoxGray editor-loader"><div class="loader-spin-wrap" style="display:inline-block"><div class="loader-spin"></div></div></div>');

            */
            self.$elem.append( self.$textarea );

            // return false;
            if (typeof $.fn['tinymce'] == 'undefined') {
            	
				// var host = "http://"+window.location.hostname;
				var url = Event.URL + "public/js/tinymce/";
				
				self.getScript(url+"tinymce.min.js" ).done(function () {
					self.getScript(url+"jquery.tinymce.min.js" ).done(function () {
						self.initElem();
					});
				});				
			}
			else{
				self.initElem();
			}
		},

		getScript: function (url) {
			return $.getScript( url );
		},

		initElem: function () {
			var self = this;

			var url_font = 'https://fonts.googleapis.com/css?family=Prompt:300';
			var content_style = ".mce-content-body{font-family:'Prompt',sans-serif;}";
			if( self.options.font ){
				url_font = 'https://fonts.googleapis.com/css?family=' + self.options.font.name;
				content_style = '.mce-content-body{font-family:'+ self.options.font.specify +'}';
			}
			

			self.$textarea.tinymce({
				// selector: 'textarea',
				height: self.options.height,
				menubar: false,
				block_formats: 'Paragraph=p;Header 1=h1;Header 2=h2;Header 3=h3',
				plugins: [
					'advlist autolink lists link image charmap  anchor textcolor',
					'visualblocks code fullscreen',
					'media table paste code',
					'img'
				],
				toolbar: 'formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote | link img media | forecolor backcolor table | removeformat | code', // undo redo |  
				content_css: [
					url_font,
					Event.URL + 'public/css/codepen.min.css'
				],
				content_style: content_style,

				relative_urls: false,
				statusbar: false,
				language: 'th_TH',
				object_resizing : false,
				getData: self.options.getData
			});
		},
	};

	$.fn.editor2 = function( options ) {
		return this.each(function() {
			var editor = Object.create( Editor2 );
			editor.init( options, this );
			$.data( this, 'editor2', editor );
		});
	};

	$.fn.editor2.options = {
		content_css: Event.URL +  "public/css/editor.css",
		height: 300,
		language:'th_TH',
		text: "",
		autosize: false,
		onComplete: function(){},

		getData: {}
	};

})( jQuery, window, document );
