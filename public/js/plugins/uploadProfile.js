// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var UploadProfile = {

		init: function( options, elem ){
			var self = this;

			self.elem = elem
			self.$elem = $(elem);
			self.options = $.extend( {}, $.fn.uploadProfile.options, options );

			self.initEvent();

			if( self.options.remove ){
				self.$elem.addClass('is-remove');
			}
		},
		config: function () {
			var self = this;

			self.$doc = $('#doc');
			if( self.$elem.attr('href') ){
				self.url = self.$elem.attr('href');
				self.$elem.removeAttr('href');
			}
			
			self.files = [];
			self.count = 0;
			
		},
		initEvent: function () {
			var self = this;

			if( !self.options.setdata_url ){

				self.$input = $('<input/>', { type: 'file', accept: "image/*", name: self.options.file_name });
				self.$elem.find('.ProfileImageComponent_overlay').append( self.$input );

				self.$input.change(function () {

					if( $(this).val()=='' ){
						self.cancel();
						return false;	
					} 

					self.file = this.files[0];
					self.$elem.addClass('is-loading');
					self.loadImageUrl();
				});
			}
			else{

				self.$elem.find('.ProfileImageComponent_overlay').click(function () {
					self.change();
				});
			}

			self.$elem.find('.ProfileImageComponent_remove').click(function () {

				if( self.options.remove_url ){

					var data = {callback: true};

					if( self.curID ) {
						data.id = self.curID;
					}

					Dialog.load(self.options.remove_url, data, {
						onClose: function () {
		
						},
						onSubmit: function ( d ) {

							var $form = d.$pop.find('form');

							Event.inlineSubmit( $form ).done(function( result ) {

								result.url = '';

								Event.processForm($form, result);

								if( result.error ){

									return false;
								}

								self.cancel();
							});
						}
					});
				}
				else{

					if( self.$input ){
						self.$input.val('');
						self.file = '';
					}
					self.cancel();
				}
			});

		},

		cancel: function () {
			var self = this;

			self.$elem
				.removeClass('has-file')
				.addClass('has-empty');
		},
		change: function () {
			var self = this;

			var $input = $('<input/>', { type: 'file', accept: "image/*"}); //, multiple:''
			$input.trigger('click');

			$input.change(function(){

				self.$elem.addClass('is-loading');
				self.refresh( this.files[0] );
			});
		},

		loadImageUrl: function () {
			var self = this;	

			var $img = self.$elem.find('.ProfileImageComponent_image');

			var reader = new FileReader();
			reader.onload = function (e) {
				var image = new Image();
				image.src = e.target.result;

				$image = $(image).addClass('img img-crop');

				image.onload = function() {
					
					var scaledW = this.width;
					var scaledH = this.height;

					self.$elem.removeClass('is-loading').removeClass('has-empty').addClass('has-file');
					$img.html( $image );

				}
			}

			reader.onprogress = function(data) {
				/*                                   
                var progress = parseInt( ((data.loaded / data.total) * 100), 10 );
                $progress.find('.bar').width( progress+"%" );
	            */
        	}

			reader.readAsDataURL( self.file );
		},

		refresh: function ( file, length ) {
			var self = this;

			setTimeout(function () {
				self.fetch(file).done(function( results ) {

					if( results.message ){
						Event.showMsg({ text: results.message, load: true , auto: true });
					}

					if( results.error ){

						return false;
					}

					if( self.$elem.hasClass('has-empty') ){
						self.$elem.removeClass('has-empty');
					}

					self.$elem.addClass('has-file');

					self.curID = results.id;
					self.display( results );
				});

			}, length || 1);
		},

		fetch: function ( file ) {

			var self = this, 
				formData = new FormData();


			$.each( self.options.data, function (name, value) {
				formData.append(name, value);
			} );

			formData.append('file1', file);
			
			return $.ajax({
				type: "POST",
				url: self.options.url,
				data: formData,
				dataType: 'json',
				processData: false, // Don't process the files
        		contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			}).always(function() { 
				self.$elem.removeClass('is-loading');
			})
			.fail(function(  ) { 
				self.$elem.removeClass('is-loading');
				Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
			});
		},

		display: function ( results ) {
			var self = this;

			if( results.url ){
				self.$elem.find('.ProfileImageComponent_image').html( $('<img>', {src: results[self.options.show]}) ); 
			}

			self.updateData( results.id );
		},

		updateData: function (value) {
			var self = this;

			if( !self.options.setdata_url ) return false;

			$.post( self.options.setdata_url , {value: value}, function () {
				
			}, 'json');
		}
	};

	$.fn.uploadProfile = function( options ) {
		return this.each(function() {
			var upload = Object.create( UploadProfile );
			upload.init( options, this );
			$.data( this, 'uploadProfile', upload );
		});
	};

	$.fn.uploadProfile.options = {
		title: "",
		activitie: 'upload',
		caption: true,
		show:'url',
		scaledX: 128,
		scaledY: 128,
		file_name: 'file1'
	};

})( jQuery, window, document );