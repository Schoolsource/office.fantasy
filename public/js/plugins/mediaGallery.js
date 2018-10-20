var MediaGallery = {
	open: function (options) {
		$('<div>').mediaGallery( options ).trigger('click');
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

	var __MediaGallery = {
		init: function ( options, elem ) {
			var self = this;

			// set Elem 
			self.$elem = $(elem);

			// set Data
			self.options = $.extend( {}, $.fn.mediaGallery.options, options );

			// event
			$(window).resize(function () {
				self.resizeModal();
			});

			self.$elem.click(function() {
				// Modal
				self.setModal();
				self.displayModal();
			});
		},

		setModal: function () {
			var self = this;

			self.active = false;
			self.tab = 'my';
			self.selector = {};
			self.checked = [];

			self.$modal = $('<div>', {class: 'model model-gallery active'}).addClass('effect-' + self.options.effect);
			$('body').append( self.$modal );

			// 
			self.$pop = $('<div>', {class: 'model-container'});
			self.$modal.append( self.$pop );

			self.$pop.append( self.setContent( {
				title: 'Photos album',
				toolbar: [ 
					{id:'my', name:'My Images'}, 
					// {id:'social', name:'Social Images'}, 
					// {id:'free', name:'Free Images'} 
				],
				// summary: 'Upload or drag n’ drop images in JPEG, PNG or GIF formats. Each file can be up to 15MB.'
				// summary: 'Add images from your Facebook, Instagram, Google Drive, Google Photos or Flickr accounts at the click of a button.'
			} ) );
		},
		setContent: function ( settings ) { 
			var self = this;

			// content
			var $elem = $( settings.elem || '<div/>' )
				.addClass("model-content")
				.addClass( settings.addClass );


			// Input hidden
			if( settings.hiddenInput ){
				$elem.append( thisettings.setHiddenInput( settings.hiddenInput ) );
			}


			// Title
			if( settings.title ){
				$elem.append( $('<div/>', {class: 'model-title'}).html(settings.title) );
			}

			var $tabs = $('<div/>', {class: 'model-tabs'});
			$elem.append( $tabs );

			// toolbar
			if( settings.toolbar ){

				$toolbar = $('<nav/>', {class: 'model-gallery-toolbar'});
				$.each(settings.toolbar, function( key, obj ) {
					$toolbar.append( $('<div>', { class: 'tab', text: obj.name, 'data-tab-action': obj.id}) );
				});
				$tabs.append( $toolbar );
			}

			var $actions = $('<div>', { class: 'model-tabs-actions'});
			$tabs.append( $actions );
			$actions.append( $('<button>', {type: 'button', class: 'btn btn-blue', role: 'upload'}).append( $('<i>', {class: 'icon-upload mrs'}), $('<span>', {text: 'Upload Images'}) ) );

			// Summary
			if( settings.summary ){
				$elem.append( $('<div/>', {class: 'model-summary clearfix'}).append(
					$('<div/>', {class: 'model-summary-msg lfloat'}).html(settings.summary)
				) );
			}


			var $main = $('<div/>', {class: 'model-main'});
			$elem.append( $main );

			$main.append( $('<div/>', {class: 'model-sidebar'}), $('<div/>', {class: 'model-body'}) );

			// Body
			if( settings.body ){
				$main.find('.model-body').html(settings.body);
			}

			// Buttons
			/*if( settings.button || settings.bottom_msg ){

				var $buttons = $('<div/>', {class: 'model-buttons clearfix'});

				if ( settings.button ){
	                $buttonsettings.append( $('<div/>', {class: 'rfloat mlm'}).html(settings.button) );
				}

	            if ( settings.bottom_msg ){
	            	$buttonsettings.append( $('<div/>', {class: 'model-buttons-msg'}).html(settings.bottom_msg) );
	            }

	            $elem.append($buttons);
			}*/
			var $status = $('<div>', {class: 'upload-status', role: 'status'});
			$status.append(
				  ''
				// , $('<div>', {class: 'ico'}).html('<i class="icon-check"></i>')
				// , $('<div>', {class: 'title'}).text( '' )
				, $('<div>', {class: 'text'}).append(
					'<i class="icon-check mrs"></i>', 'Uploaded (', 1, '/', 1, ' File)'
				)
				, $('<div>', {class: 'progress-bar medium'}).html( $('<span>', {class: 'blue'}) )
			);
			
			var $buttons = $('<div>', {class: 'model-gallery-buttons'});

			$buttons.append( $('<button>', {type: 'button', class: 'btn btn-blue', role: 'done'}).text( self.options.button_done_text ) );

			// Footer
			$elem.append( $('<div/>', {class: 'model-gallery-footer clearfix'}).append(

				  $('<div>', {class: 'model-gallery-status'}).html($status)
				, $('<div>', {class: 'model-gallery-alert'}) // .text( '1 Item Selected' )
				, $buttons

			) );

			/*if( settings.footer ){
				$elem.append( $('<div/>', {class: 'model-footer'}).html(settings.footer) );
			}*/

			// close
			var $actions = $('<div/>', {class: 'model-actions'});
			$elem.append( $actions );
			$actions.append( 
				$('<button/>', {type:'button', class: 'action close', 'role': 'close'}).html('<i class="icon-remove"></i>') )

			return $elem;
		},
		displayModal: function () {
			var self = this;

			if( !$('html').hasClass('hasModel') ){
				var top = $(window).scrollTop();

				$('#doc').addClass('fixed_elem').css('top', top*-1);
				$('html').addClass('hasModel');
			}

			self.$modal.addClass('show');
			self.active = true;

			self.tabsAction(); 

			self.resizeModal();
			self.eventModal();
		},
		closeModel: function () {
			var self = this;

			self.active = false;

			if( $('.model.active').length == 1){
				var scrollTop = parseInt( $('#doc').css('top'));
				$('#doc').removeClass('fixed_elem').removeAttr('style');

				$(window).scrollTop(scrollTop*-1);
			}

			self.$modal.removeClass('active').removeClass('show');
			setTimeout(function () {
				
				self.$modal.remove();
				
				if( $('.model').length == 0 ){
					$('html').removeClass('hasModel');
				}
			}, 500)
		},
		eventModal: function () {
			var self = this;

			self.$modal.delegate('[data-nav-action]', 'click', function(event) {


				if( $(this).hasClass('active') ) return false;
				$(this).addClass('active').siblings().removeClass('active');

				self.action();

			}); 

			self.$modal.delegate('[data-action]', 'click', function(e) {
				e.stopPropagation();

				var action = $(this).data('action'),
					id = $(this).closest('[data-id]').data('id');
				
				if( action=='trash' ){
					self._actionTrash( id );
				}
				
			});
			
			if( self.options.selector ){
				self.$modal.delegate('[data-id]', 'click', function(event) {

					var id = $(this).data('id');
					if( $(this).hasClass('active') ){

						delete self.selector[id];
						$(this).removeClass('active');
					}

					else{
						$(this).addClass('active');
						self.chooseItem( $(this).data() );
					}

					

					
				});
			}

			self.$modal.delegate('[role=upload]', 'click', function() {

				var $input = $('<input/>', { type: 'file', accept: "image/*", name: self.options.upload_file_name, multiple: self.options.upload_multiple });

				$input.trigger('click');


				$input.change(function () {

					self.uploadFile(this.files);
				});
			});

			self.$modal.delegate('[role=status]', 'click', function() {
				self.showStatus();
			});

			self.$modal.delegate('[role=close]', 'click', function() {
				self.closeModel();
			});

			self.$modal.delegate('[role=done]', 'click', function() {
				if( typeof self.options.done === 'function' ){
					self.options.done( self );
				}

				self.closeModel();
			});
			
		},
		resizeModal: function () {
			var self = this;

			if( !self.active || !self.$modal) return false;


			self.$modal.find('.model-main').css({
				height: self.$modal.find('.model-container').outerHeight() - ( self.$modal.find('.model-title').outerHeight() + self.$modal.find('.model-tabs').outerHeight() + self.$modal.find('.model-summary').outerHeight() + self.$modal.find('.model-gallery-footer').outerHeight() )
			});
		},


		tabsAction: function () {
			var self = this;	

			self.$modal.find('[data-tab-action='+ self.tab +']').addClass('active').siblings().removeClass('active');

			if( self.tab=='my' ){
				self.setMyImage();
				/**/
			}
		},
		setMyImage: function () {
			var self = this;

			// sidebar
			var $sidebarWrap = $('<div>', {class: 'model-sidebar-wrap'});
			var $sidebarFooter = $('<div>', {class: 'model-sidebar-footer'});
			self.$modal.find('.model-sidebar').append(
				$sidebarWrap
			);

			var $sidebarNav = $('<ul>', {class: 'model-sidebar-nav'});
			$sidebarWrap.append( $sidebarNav );

			$.get( Event.URL + 'photos/albumsList', self.options.getData, function (res) {

				$.each(res, function(index, obj) {

					$sidebarNav.append( $('<li>', {class: 'model-sidebar-item', 'data-nav-action': obj.id}).html( $('<a>').text(obj.name) ) );
				});

				var $action = self.$modal.find('[data-nav-action]').first();
				$action.addClass('active').siblings().removeClass('active');
				self.action( $action.attr('data-nav-action') );

			}, 'json');

			/*var a = [{ id:'', name: 'All Media'}, {id:'site', name:'Site Media'}, {id:'banner', name:'Banners'}];
			$.each(a, function(index, obj) {
				$sidebarNav.append( $('<li>', {class: 'model-sidebar-item', 'data-nav-action': obj.id}).html( $('<a>').text(obj.name) ) );
			});*/
			
			$sidebarFooter.append( $('<a>', {class: ''}).append(
				  $('<i>', {class: 'icon-plus mrs'})
				, $('<span>').text('Add New Folder')
			) );


			var $droppable = $('<div>', {role: 'droppable', class: 'model-droppable'});

			$droppable.html( $('<div>', { class: 'modal-droppable-text'}).append( 
				  $('<i>', {class: 'icon-picture-o'})
				, $('<h3>', {text: 'Start Adding Files to "Mobile Uploads". It\'s Easy!'}) 
				, $('<p>', {text: 'Drag and drop them here, or click Upload Images.'}) 
				, $('<p>', {text: 'Your images will also appear in your Site Media folder, so they’re easy to find and use. '}).append( $('<a>', {role: 'upload', text: 'Upload Images'}) ) 
			) );


			self.$listsbox = $('<div>', {ref: 'listsbox', class: 'gallery-grid'});
			self.$modal.find('.model-body').append( $droppable, self.$listsbox );

			/*
			https://johnny.github.io/jquery-sortable/js/jquery-sortable.js
			https://codepen.io/salasks/pen/ojzvp
			https://stackoverflow.com/questions/6199890/jquery-droppable-receiving-events-during-drag-over-not-just-on-initial-drag-o

			$droppable.droppable({
				drop: function (event, ui) {
					console.log( 'drop' );
				}
			});*/
		},


		action: function ( id ) {
			var self = this;

			self.$listsbox.empty();
			self.getData = $.extend( {}, {
				limit: 20,
				pager: 1,
				album: id
			}, self.options.getData );


			self.refresh();
		},

		refresh: function (length) {
			var self = this;

			setTimeout(function () {
				self.fetch().done(function( results ) {


					if( results.message ){
						Event.showMsg({ text: results.message });
					}

					if(results.error){

						return false;
					}
					self.getData = $.extend( {}, self.getData, results.options );

					self.$modal.find('.model-body').toggleClass('has-empty', parseInt(results.total) == 0 );

					self.buildFrag(results.lists);

					self.displayImages();

				});
			}, length || 1);
		},

		fetch: function(url, getData){
			var self = this;

			return $.ajax({
				url: Event.URL + 'photos/lists/',
				data: self.getData,
				dataType: 'json'
			})
			.always(function() {
				Event.hideMsg();
			})
			.fail(function() { 
				Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
			});
		},
		buildFrag: function ( results ) {
			self = this;


			$.each( results, function(i, obj) {
				self.$listsbox.append( self.setItemImage(obj)[0] );
			});
			
		},
		setItemImage: function (data) {

			var $div = $('<div>', {class: 'photo-container gallery-grid-item', 'data-id': data.id});
			// var $inner = $('<div>', {class: 'inner'});
			// $div.append( $inner );

			var $thumb = $('<div>', {class: 'image-thumb pic'}).html( $('<img>', {src: data.url + '?h=' + this.options.rowHeight } ) );
			
			var $actions = $('<div>', {class: 'actions'});
			var $checkbox = $('<button>', {class: 'action-checkbox', role: "checkbox"});

			$actions.append(
				''
				// , $('<button>', {type: 'button', class: 'action', 'data-action': 'edit'}).html( $('<i>', {class: 'icon-pencil'}) )
				, $('<button>', {type: 'button', class: 'action', 'data-action': 'trash'}).html( $('<i>', {class: 'icon-trash-o'}) )
				// , $('<button>', {type: 'button', class: 'action', 'data-action': 'zoom'}).html( $('<i>', {class: 'icon-search-plus'}) )
			);


			$checkbox.append( 
				''

				, '<svg width="24px" height="24px" class="JUQOtc orgUxc" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>' 
			);

			$div.append( $thumb, $checkbox, $actions );
			$div.data( data );

			return $div;
		},

		displayImages: function () {
			var self = this,
                ws = [],
                rowNum = 0,
                baseLine = 0,
                rows = [],
                totalWidth = 0;

            self.$items = self.$listsbox.find('.photo-container');
            var limit = self.$items.length,
                photos = self.$items,
                appendBlocks = self.options.appendBlocks();

            self.$listsbox.parent().toggleClass('has-empty', limit == 0 );

			var w = self.$listsbox.width();
            var border = parseInt(self.options.margin, 10);
            var h = parseInt(self.options.rowHeight, 10);


            $.each( self.$items, function() {
				var $elem = $(this);
				var data = $elem.data();

				var wt = parseInt(data.size.width, 10);
	            var ht = parseInt(data.size.height, 10);

	             if (ht !== h) {
	                wt = Math.floor(wt * (h / ht));
	            }

	            $elem.css({
	            	width: wt,
	            });
	            totalWidth += wt;
	            ws.push(wt);
			});


            var perRowWidth = totalWidth / Math.ceil(totalWidth / w);
            // maxRows
            // console.log( 'rows', Math.ceil(totalWidth / w), w );


            // set Rows
            // var tw = 0;
			while (baseLine < limit) {
				var row = {
                        width: 0,
                        photos: []
                    },
                    c = 0,
                    tw = 0;

                while( (tw <= w) && ws[baseLine + c] ){
                	tw += ws[baseLine + c];

                	row.width += ws[baseLine + c];
                	row.photos.push({
                        width: ws[baseLine + c],
                        photo: photos[baseLine + c]
                    });

                	c++;
                }

                /*while ((tw + ws[baseLine + c] / 2 <= perRowWidth * (rows.length + 1)) && (baseLine + c < limit)) {
                    tw += ws[baseLine + c];

                    row.width += ws[baseLine + c];
                    row.photos.push({
                        width: ws[baseLine + c],
                        photo: photos[baseLine + c]
                    });
                    c++;
                }*/

                baseLine += c;
                rows.push(row);
			}

            for (var i = 0; i < rows.length; i++) {
                var row = rows[i],
                    lastRow = false;
                // console.log( 'row', row );

                rowNum = i + 1;
                if (self.options.maxRows && rowNum > self.options.maxRows) {
                    break;
                }

                if (i === rows.length - 1) {
                    lastRow = true;
                }

                tw = -1 * border;
                var availableRowWidth = w;

                // Ratio of actual width of row to total width of images to be used.
                var r = availableRowWidth / row.width, //Math.min(w / row.width, self.options.maxScale),
                    c = row.photos.length;

                if( c==1 && lastRow  ){
                	r = 1;
                }

                // new height is not original height * ratio
                var ht = Math.min(Math.floor(h * r), parseInt(self.options.maxRowHeight,10));
                	r = ht / self.options.rowHeight;


                var imagesHtml = '';
                for (var j = 0; j < row.photos.length; j++) {
                	var photo = row.photos[j].photo;
                	var data = $(photo).data();

                	// Calculate new width based on ratio

                	// var fw = row.photos[j].width > w ? w : 
                    var wt = Math.floor(row.photos[j].width * r);
                    wt -= border;
                    
                    $(photo).css( {
                		width: wt,
                		height: ht,
                		marginTop: border,
                		marginLeft: border
                	} );
                	
                	$(photo).find('.pic').loadImage({
                		url: data.url,
                		width: wt,
                		height: ht,
                	});
                   
                }
            }
		},
		renderPhoto: function(image, obj, isLast) {
            var data = {},
                d;
            d = $.extend({}, image, {
                src: obj.src,
                displayWidth: obj.width,
                displayHeight: obj.height,
                marginRight: isLast ? 0 : this.options.margin
            });
            if (this.options.dataObject) {
                data[this.options.dataObject] = d;
            } else {
                data = d;
            }
            return this.options.template(data);
		},

		uploadFile: function ( files ) {
			var self = this;

			for (var i = 0; i < files.length; i++) {
				var file = files[i];

				self._uploadFile( file );
			}
		},

		_uploadFile: function ( file ) {
			var self = this;

			var formData = new FormData();
			formData.append(self.options.upload_file_name, file);

			formData.append('album', self.getData.album );

			$.each(self.options.getData, function(index, el) {
				formData.append(index, el );
			});

			self.showStatus();

			$.ajax({
				url: Event.URL + 'photos/upload',
				type: 'POST',
				dataType: 'json',
				data: formData,
				processData: false,
				contentType: false,
			    progress: function(e) {
			    	//make sure we can compute the length
			        if(e.lengthComputable) {
			            //calculate the percentage loaded
			            var pct = (e.loaded / e.total) * 100;

			            //log percentage loaded
			            console.log(pct);
			        }
			        //this usually happens when Content-Length isn't set
			        else {
			            console.warn('Content Length not reported!');
			        }
			    }
			})
			.done(function(res) {

				console.log( res );
				var first = self.$listsbox.find('.photo-container').first();
				var item = self.setItemImage(res.item)[0];

				if( first.length==1 ){
					first.before( item );
				}
				else{
					self.$listsbox.append( item );
				}

				self.displayImages();
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});
			
		},

		showStatus: function () {
			var self = this;

			/*$uploadsList = $('<ul class="uploads-list"></ul>');


			Lightbox.open({
				title: 'Upload status',
				button: '<button type="button" class="btn btn-blue" role="close">Done</button>',
				body: $uploadsList[0],
				width: 500
			});*/
		},

		_actionTrash: function (id) {
			var self = this;


			Lightbox.load( Event.URL + 'photos/remove', {id: id, callback: 1}, {
				onSubmit: function (e, form) {
					var $form = $(form);

					Event.inlineSubmit( $form ).done(function( result ) {
						result.url = '';

						Event.processForm($form, result);
						self.$listsbox.find('[data-id='+ id +']').remove();


						self.displayImages();
					});

					
				},
			} );
		},


		chooseItem: function (data) {
			var self = this;

			var length = Object.keys( self.selector ).length;

			var n = 0;
			$.each(self.selector, function(index, obj) {
				n ++;

				if( n>=self.options.selector_limit ){
					self.$listsbox.find('[data-id='+ obj.id +']').removeClass('active');
					delete self.selector[index];
				}
			});

			self.selector[ data.id ] = data;

			self.checked = [];
			$.each(self.selector, function(index, obj) {
				self.checked.push(obj);
			});
		}
	};

	$.fn.mediaGallery = function( options ) {
		return this.each(function() {
			var $this = Object.create( __MediaGallery );
			$this.init( options, this );
			$.data( this, 'mediaGallery', $this );
		});
	};

	$.fn.mediaGallery.options = {
		type: 'preview',

		effect: 1,

		appendBlocks : function(){ return []; },
        rowHeight: 150,
        maxRowHeight: 350,
        handleResize: false,
        margin: 10,
        imageSelector: 'image-thumb',
        imageContainer: 'photo-container',

        template: function(data) {
            return '<div class="photo-container" style="height:' + data.displayHeight + 'px;margin-right:' + data.marginRight + 'px;">' +
                '<img class="image-thumb" src="' + data.src + '" style="width:' + data.displayWidth + 'px;height:' + data.displayHeight + 'px;" >' +
                '</div>';
        },


        upload_file_name: 'file1',
        upload_multiple: true,

        selector: false,
        selector_limit: 1,

        button_done_text: 'Done',

        getData: {}
	};
	
	
})( jQuery, window, document );