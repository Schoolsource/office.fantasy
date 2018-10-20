// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {
	var Settings = {};
	
	var Editor = {
		settings: {},
		edData: {},
		init: function( options, elem ) {
			var self = this;

			self.elem = elem;
			self.$elem = $( elem );
			self.options = $.extend( {}, $.fn.editor.options, options );
			Settings = self.options;
			Editor.settings = self.options;

			self.$textarea = $('<textarea/>', {
            	name: self.$elem.attr('name'), 
            	text: self.$elem.val()
            }).css({
            	height: 0,
            	padding:0,
            	border: 0
            });

			self.$elem.css( 'display', 'none' );

			self.max_width = self.options.width || self.$elem.width();
			self.max_height = self.options.height || 300;
			self.options.text = self.$elem.text();

			var placeholder = $('<div>', {class: 'editor-wrap'});
			self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$loading = $('<div class="pam tac uiBoxGray editor-loader"><div class="loader-spin-wrap" style="display:inline-block"><div class="loader-spin"></div></div></div>');

            self.$elem.append( self.$textarea, self.$loading );

            // return false;
            if (typeof $.fn['tinymce'] == 'undefined') {
            	
				// var host = "http://"+window.location.hostname;
				var url = URL + "public/js/tinymce/";
				
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

			var content_css = self.options.content_css
				? self.options.content_css + "?rand=" + new Date().getTime()
				: false;

			self.$textarea.tinymce({
				language: false, //self.options.language,
				statusbar: false,
        		plugins : "paste",
        		object_resizing : false,
        		paste_preprocess: function(pl, o) {
	                o.content = self.strip_tags( o.content,'<table><tr><td>' );
	            },
			    content_css:content_css,
			    height: self.options.height,
			    setup: function function_name (argument) {
			    	self.Setup(this, self.options);
			    },
			    theme: self.Theme
			});

			self.$loading.hide();
			self.$elem.addClass('on');
		},

		Setup: function(editor, options){
			
			editor.on('init', function(args) {
		        editor = args.target;

		        if( options.text != '' ){

		        	if( options.autosize ){
			        	setTimeout(function () {
			        		Editor.heightAutoResize( editor );
			        		// editor.execCommand("mceFocus");
			        	},100);
		        	}
		        	
		        }
		        
		        editor.on('NodeChange', function(e) {
		            if (e && e.element.nodeName.toLowerCase() == 'img') {
		                // tinyMCE.DOM.setAttribs(e.element, {'width': null, 'height': null});
		            }
		        });

		    });

			editor.on('execCommand',function(args, cmd, ui, val) {
				var node = editor.selection.getNode();

				// resize
				if( options.autosize ){
					Editor.heightAutoResize( editor );
				}
			});

			editor.on('keydown', function(evt) {

			});
			editor.on('keyup', function(evt) {

				if( options.autosize ){
					Editor.heightAutoResize( editor );
				}

				if(evt.shiftKey && (evt.keyCode>=37 && evt.keyCode<=40) ){
					
				}
				
				Editor.insertlink.clearBubble();
			});

			editor.on('mousedown', function(args) {
				Editor.insertlink.clearBubble();
			});

			editor.on('mouseup', function(args) {
				// console.log( 'mouseup' );
				Editor.insertlink.valid(editor);
			});

			editor.on('click', function(evt) {
				// console.log( 'click' );
			});
		},

		Theme: function(editor, target){
			var self = this;

			var dom = tinymce.DOM, editorContainer;
			self.$toolbar = $('<div/>', {class: 'editor-toolbar'});
			var toolbar = ['bold italic | link | numlist bullist | blockquote | image' ];

			$.each(toolbar, function(i, rows){

				$.each(rows.split(" "), function(i, cell){

					if(cell=="|"){
						self.$toolbar.append($('<span/>', {class:'separator'}));
						
					}
					else{

						var btn = $('<button/>', {
								type: 'button',
								class: 'mc-'+cell,
								// "data-command": cell
							}).append( $('<i/>', {class: 'icon-'+cell}) )[0];

						if( cell=="numlist" || cell=="bullist" ){


							btn.command = cell=="numlist"? "InsertOrderedList": "InsertUnorderedList";
						}
						else{
							btn.command = cell;
						}
						
						Editor.edData[cell] = {
							button: btn
						};
						self.$toolbar.append( btn );
					}
					
				});

			});

			var $wrapper = $('<div/>', {class: 'editor-wrapper'});
			// var $htmlify = $('<div/>', {class: 'htmlify'});

			$wrapper.append( self.$toolbar );
	    	
	    	// Generate UI
	    	editorContainer = dom.insertAfter(dom.create('div', {class: 'editor-container'},
	            $wrapper[0]
	        ), target);

	        // Set editor container size to target element size

			dom.setStyle(editorContainer, 'width', self.max_width);

			// Bind events for each button
			
			// Register state change listeners
			// editor.onInit.add(function(args, event) {
			editor.on('init', function(args) {

	            tinymce.each(dom.select('button', editorContainer), function(btn) {

	            	Editor.setButton(editor, btn);
	            	
	            	editor.formatter.formatChanged(btn.command, function(state) {
	            		Editor.buttonChanged(editor, btn, state);
	            	});
	          
	            });

			});

	        // Return editor and iframe containers
	        return {
	            editorContainer: editorContainer,
	            iframeContainer: editorContainer.lastChild,

	            // Calculate iframe height: target height - toolbar height
	            iframeHeight: 300 //target.offsetHeight - editorContainer.firstChild.offsetHeight
	        };
		},

		setButton: function(editor, btn, options){

			var $btn = $(btn);
			var command = btn.command;

			if( command=='image' ){
				
				$btn.click(function(){
					Editor.image_upload.init( editor, options );
				});
			}else if( command=='link' ){
				$btn.addClass('disabled');

				$btn.click(function(){
					Editor.insertlink.click(editor, btn);
				});
			}
			else if(command=='blockquote'){
				
				$btn.click(function(){
					editor.execCommand("FormatBlock", false, "blockquote");
				});
			}

			// 
			$btn.not('.mc-link,.mc-image,.ed-blockquote').click(function(){
				editor.execCommand(command, false, null);
			});
			
		},
		buttonChanged: function(editor, btn, state){
			Editor.edData[btn.command].changed = state;
			$(btn).not('.mc-link,.mc-image').toggleClass('on', state);
		},

		insertlink: {

			click: function ( ed, btn ) {

				var obj = Editor.edData.link;
				
	        	ed.execCommand("mceFocus");
	        	var $btn = $(btn);
	        	var data = this.valid(ed, btn);

				if( data.event == 'insertlink' ){

					var settings = Editor.caretPosition(ed);
					settings.pointer = true;

					var $input = $('<input/>', {class: 'inputtext', type: 'text', placeholder: "วางหรือพิมพ์ลิงก์..."});
					var $dismiss = $('<button/>', {class: 'dismiss icon-remove', type: "button", title: "ลบ"});
					var $bubble = $('<div/>', {class: 'uiBoxBubble'}).html(
						$('<div/>', {class: 'insertlinkbubble clearfix'}).append( $dismiss, $input )
					);

					uiLayer.get(settings, $bubble[0]);
					var $layer = $bubble.parents('.uiLayer');

					$dismiss.click(function(){
						$layer.remove();
					});

					var regexp = new RegExp("^(http|ftp|https):\/\/?");

					$input.focus()
					.keydown(function(e){
						var res = $(this).val();

						if( e.keyCode == 13 && res!=""){
							$layer.remove();

							if( !regexp.test(res) ) res = "http://"+res;
							ed.selection.setContent('<a href="'+res+'">'+data.text+'</a>');
							$btn.addClass('disabled');
							ed.execCommand("mceFocus");
						}
						else if( e.keyCode == 27){
							$layer.remove();
						}
					});

					Editor.edData.link.bubble = $layer;

				}else if( data.event=='unlink' || obj.changed ){
					ed.execCommand('unlink', false, null);
					Editor.insertlink.clearBubble();
					Editor.insertlink.valid( ed );
				}
			},

			changed: function(ed) {
				
				var self = this;
				var wrapEachWith = ed.selection.getNode();
				var url = $(wrapEachWith).attr( 'href' );

				if( url ){
					var settings = Editor.caretPosition(ed);

					var obj = Editor.edData.link;
					var $input = $('<input/>', {class: 'inputtext', type: 'text', placeholder: "วางหรือพิมพ์ลิงก์..."});
					var $chang = $('<a/>', {class: '', text: "เปลี่ยน"});
					var $remove = $('<a/>', {class: '', text: "นำออก"});

					var $bubble = $('<div/>', {class: 'uiBoxBubble'}).html(
						$('<div/>', {class: 'changedlinkbubble clearfix'}).append(
							$( '<a/>',{ target:"_blank", href: url, text: url, title: url } )
							, $('<span/>', {class: 'mhs', text: "-"})
							/*, $chang
							, $('<span/>', {class: 'mhs', text: "|"})*/
							, $remove
						)
					);

					uiLayer.get(settings, $bubble[0]);
					var $layer = $bubble.parents('.uiLayer');

					obj.bubble = $layer;
					$remove.click(function(){
						$layer.remove();
						ed.execCommand('unlink', false, null);
					});

					$chang.click(function(){

						// var text = ed.selection.getNode({format:"a"});
						
						// console.log( ed.selection );

						// ed.selection.setNode( '<a>5555</a>' );
						// var text = 
						// ed.execCommand('', false, null);
						// $layer.remove();
						// ed.execCommand('unlink', false, null);
					});
				}

			},

			setBtn: function( evt ){
				var obj = obj = Editor.edData.link;
				var $btn = $(obj.button);

				if( evt=='unlink' ){
					$btn.addClass('unlink').removeClass('disabled').find('i').removeClass('icon-link').addClass('icon-unlink');
				}
				else if( $btn.hasClass('unlink') ){
					$btn.removeClass('unlink').addClass('disabled').find('i').removeClass('icon-unlink').addClass('icon-link');
				}
			},

			valid: function(ed, target){
				var self = this, obj = Editor.edData.link;
				var text = ed.selection.getContent({format:"strong,text,a,p,img"});
	        	var $btn = target ? $(target): $(Editor.edData['link'].button);
	        	var evt = null;
	        	
				if( obj.changed ){
					Editor.insertlink.changed( ed );					
				}

	        	self.setBtn();

	        	if( text=="" ){
	        		$btn.addClass('disabled');

					if( obj.changed ){
						self.setBtn('unlink');
						evt = 'unlink';
					}
	        	}
	        	else{
	        		placeholder = $('<div/>').html( text );
	        		if( $(placeholder).find('*').length>0 ){
						var tags = {};

						$(text).each(function (i, obj) {
							tags[obj.nodeName.toLowerCase()] = true;
						});

						if( tags.a ){
							evt = 'unlink';
							self.setBtn('unlink');
						}

						if( tags.p ){ evt=null; }else if( !evt ){ evt = 'insertlink' }
					}
					else{

						if( obj.changed ){
							evt = 'unlink';
							self.setBtn('unlink');
						}
						else{
							evt = 'insertlink';
						}
					}

	        	}

	        	if( evt && $btn.hasClass('disabled')){
					$btn.removeClass('disabled');
				}

	        	return { event: evt, text: text };
			},

			clearBubble: function(){
				var obj = Editor.edData.link;

				if( obj.bubble ){

					if( obj.bubble.length /*&& !obj.changed */){
						$(obj.bubble).remove();
					}
				}

				this.setBtn();
			}
		},

		image_upload: {
			init: function( ed ){
				var self = this;
				self.options = Editor.edData['image'];
				self.Editor = ed;

				self.image_upload_url = Editor.settings.image_upload_url;
				// Editor.settings

				self.$input = $('<input/>', { type: 'file',accept: "image/*"});
			    self.$input.trigger('click'); // opening dialog

			    // console.log( self.image_upload_url );

			    self.$input.change(function(){
			    	var file = this.files[0];

			    	var postData = new FormData();
			    	postData.append('file1', file);

			    	Event.showMsg({ load:true });

			    	$.ajax({
						type: "POST",
						url: self.image_upload_url,
						data: postData,
						dataType: 'json',
						processData: false, // Don't process the files
		        		contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		        		cache: false,
		        	}).done(function( result ) {
						
						self.buildFrag( result );
				    	self.display();

					}).always(function() { // complete
						Event.hideMsg();
					}).fail(function( xml ) { // error

						Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
						self.Editor.execCommand("mceFocus");
					});

			    });
			},

			buildFrag: function ( result ) {
				var self = this;

				var cls = '';
				var width = result.width;
				var height = result.height;
				if( width>height || width>516 ){
					cls = ' scaledImageFitWidth';
				}

				if( width > 516 ){
					width = 516;
					height =  parseInt((width*result.height)/result.width);
				}

				self.img = '<photo id="1" /><img data-name="'+result.name+'" class="img'+cls+'" alt="" src="'+result.src+'" width="'+width+'" height="'+height+'"/>';

				console.log( self.img );
			},

			display: function () {
				var self = this;
				self.Editor.execCommand("mceInsertContent", false, self.img);
				
				this.Editor.execCommand("mceInsertContent", false, '<p></p>');
				self.Editor.execCommand("mceFocus");

				if( Settings.autosize ){
					setTimeout(function () {
						Editor.heightAutoResize( self.Editor );
					}, 300);
				}
			}
		},

		caretPosition: function ( ed ) {

			var tinymcePosition = $(ed.getContainer()).offset();
        	var toolbarPosition = $(ed.getContainer()).find(".editor-toolbar").first();
        	var nodePosition = $(ed.selection.getNode()).position();
        	var textareaTop = 0;
			var textareaLeft = 0;

			if (ed.selection.getRng().getClientRects().length > 0) {
				textareaTop = ed.selection.getRng().getClientRects()[0].top + ed.selection.getRng().getClientRects()[0].height;
    			textareaLeft = ed.selection.getRng().getClientRects()[0].left;
			} else {
			    textareaTop = parseInt($(ed.selection.getNode()).css("font-size")) * 1.3 + nodePosition.top;
			    textareaLeft = nodePosition.left;
			}

			var position = $(ed.getContainer()).offset();
			return  {
			    top:  tinymcePosition.top + toolbarPosition.innerHeight() + textareaTop,
			    left: /*tinymcePosition.left + */textareaLeft + position.left
			}
		},

		heightAutoResize: function ( ed ) {

			var $iframe = $(ed.iframeElement),
				min_height = ed.settings.height;

			var $body = $(ed.getDoc().body);
			var current_height = $body.outerHeight();

			if( current_height < min_height ){
				current_height = min_height;
			}

			$iframe.css({
				height: current_height,
				overflowY: 'hidden',
				// transition: "height .07s"
			});

			$body.css({
				overflowY: 'hidden'
			});
		},

		strip_tags: function(str, allowed_tags){
			// var whitelist = "p,ul,ol"; // for more tags use the multiple selector, e.g. "p, img"
			var content = $('<div/>').html( str );

			content.find('>').not("p,ul,ol,blockquote,a").each(function() {

				var outer = this;
				var $outer = $(this);
				var innerPush = {};

				// inner .not("strong,em,u,a,li")
				$outer.find("*").not("em,a,li").each(function() {
			    	var $inner = $(this);
			    	var nodeName = $inner.context.nodeName.toLowerCase(), 
			    		text = $inner.text();

			    	innerPush[nodeName] = true;
			    	$inner.replaceWith( text );
			    });
				
				var nodeName = $outer.context.nodeName.toLowerCase(), 
					text = $outer.text(),
					wrapEachWith = '<p/>';

				// strong
				var strong = ['h1','h2','b'];
				// console.log( strong.indexOf(nodeName) );
				if( strong.indexOf(nodeName) >= 0 ){ //  nodeName innerPush.strong 
					text = $('<strong/>', {text: text});
				}

				// console.log( nodeName, text );

			    var placeholder = $( wrapEachWith ).html( text );
			    if( nodeName=='br' && text=="" ) placeholder = "";
			    if( nodeName=='span' ) placeholder = text;
			    $outer.replaceWith( placeholder );

			});
			
			content.find("span,img").each(function() {
				$(this).replaceWith( $(this).text() );
			});

			content.find("ul>*").not('li').each(function() {
				var nodeName = $(this).context.nodeName.toLowerCase(),
					text = $(this).text();

				var placeholder = $( '<li/>' ).html( text );
			    if( nodeName=='br' && text=="" ) placeholder = "";
			    $(this).replaceWith( placeholder );

				// console.log( nodeName );
				// $(this).replaceWith( $(this).text() );
			});

			content.find("*").not('a').each(function() {
				// var nodeName = $(this).context.nodeName.toLowerCase(),
					// text = $(this).text();

				// var placeholder = $( '<li/>' ).html( text );
			    // if( nodeName=='br' && text=="" ) placeholder = "";
			    $(this).removeAttr("style");

			});

			return content.html();
		}
	};

	$.fn.editor = function( options ) {
		return this.each(function() {
			var editor = Object.create( Editor );
			editor.init( options, this );
			$.data( this, 'editor', editor );
		});
	};

	$.fn.editor.options = {
		content_css: URL +  "public/css/editor.css",
		height: 300,
		language:'th_TH',
		text: "",
		autosize: false,
		onComplete: function(){}
	};

})( jQuery, window, document );
