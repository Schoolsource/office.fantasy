var __ui = {
	anchorBucketed: function (data, tap) {
		
		var tap = tap || 'div';
		
		var anchor = $('<'+ tap +'>', {class: 'anchor ui-bucketed clearfix'});
		
		var icon = '';
		if( !data.image_url || data.image_url=='' ){

			// icon = 'user';
			if( data.icon ){
				icon = '<div class="initials"><i class="icon-'+data.icon+'"></i></div>';
			}
			else if( data.icon_text ){
				icon = '<div class="initials">'+data.icon_text+'</div>';
			}
		}
		else{
			icon = $('<img>', {
				class: 'img',
				src: data.image_url,
				alt: data.text
			});
		}

		if( icon!='' ){
			var avatar = $('<div>', {class: 'avatar lfloat no-avatar mrm'});
			anchor.append( avatar.html( icon ) );
		}
		else{
			anchor.addClass('no-icon');
		}
		

		var massages = $('<div>', {class: 'massages'});
		if( data.text || data.name ){
			massages.append( $('<div>', {class: 'text fwb u-ellipsis'}).html( data.text || data.name ) );
		}

		if( data.category ){
			massages.append( $('<div>', {class: 'category'}).html( data.category ) );
		}
		
		if( data.subtext ){
			massages.append( $('<div>', {class: 'subtext'}).html( data.subtext ) );
		}

		var content = $('<div>', {class: 'content'});
		content.append(
			  $('<div>', {class: 'spacer'})
			, massages
		);

		anchor.append( content );

        return anchor;
	},
	anchorFile: function ( data ) {
		
		if( data.type=='jpg' ){
			icon = '<div class="initials"><i class="icon-file-image-o"></i></div>';
		}
		else{
			icon = '<div class="initials"><i class="icon-file-text-o"></i></div>';
		}
		
		var anchor = $('<div>', {class: 'anchor clearfix'});
		var avatar = $('<div>', {class: 'avatar lfloat no-avatar mrm'});
		var content = $('<div>', {class: 'content'});
		var meta =  $('<div>', {class: 'subname fsm fcg'});

		if( data.emp ){
			meta.append( 'Added by ',$('<span>', {class: 'mrs'}).text( data.emp.fullname ) );
		}

		if( data.created ){
			var theDate = new Date( data.created );
			meta.append( 'on ', $('<span>', {class: 'mrs'}).text( theDate.getDate() + '/' + (theDate.getMonth()+1) + '/' + theDate.getFullYear() ) );
		}

		avatar.append( icon );

		content.append(
			  $('<div>', {class: 'spacer'})
			, $('<div>', {class: 'massages'}).append(
				  $('<div>', {class: 'fullname u-ellipsis'}).text( data.name )
				, meta
			)
		);
		anchor.append( avatar, content );

        return anchor;
	}
}

var Calendar = {
	init: function( options ){
		var self = this;

		var defaults = {
			selectedDate: -1,
            startDate: -1,
            endDate: -1
		};

		self.options = $.extend( {}, defaults, options);

		var lang = Object.create( Datelang );
			lang.init( self.options.lang );
			self.string = lang;

		self.render();
	},

	// Render the calendar
	render: function(){
		var self = this;
		var settings = self.options;

		// Get the starting date
        var startDate = settings.startDate;
        if (settings.startDate == -1)
        {
            startDate = new Date();
            startDate.setDate(1);
        }
        startDate.setHours(0, 0, 0, 0);
        var startTime = startDate.getTime();

        // Get the end date
        var endDate = new Date(0);
        if (settings.endDate != -1)
        {
            endDate = new Date(settings.endDate);
            if ((/^\d+$/).test(settings.endDate))
            {
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + settings.endDate);
            }
        }
        endDate.setHours(0, 0, 0, 0);
        var endTime = endDate.getTime();

        // Get the current date to render
        var theDate = settings.theDate;
        theDate = (theDate == -1 || typeof theDate == "undefined") ? startDate : theDate;

        // Get the selected date
        var selectedDate = settings.selectedDate;
        selectedDate = (selectedDate == -1 || typeof selectedDate == "undefined") ? theDate : selectedDate;
        selectedDate.setHours(0, 0, 0, 0);
        var selectedTime = selectedDate.getTime();

        // Calculate the first and last date in month being rendered.
        // Also calculate the weekday to start rendering on
        firstDate = new Date(theDate);
        firstDate.setDate(1);
        var firstTime = firstDate.getTime();
        var lastDate = new Date(firstDate);
        lastDate.setMonth(lastDate.getMonth() + 1);
        lastDate.setDate(0);
        var lastTime = lastDate.getTime();
        var lastDay = lastDate.getDate();

        // Calculate the last day in previous month
        var prevDateLastDay = new Date(firstDate);
        prevDateLastDay.setDate(0);
        prevDateLastDay = prevDateLastDay.getDate();

        var today = new Date(); today.setHours(0, 0, 0, 0);
        var todayTime = today.getTime();

        // save Data
        self.options = $.extend( {}, {
            theDate: theDate,
            // firstDate: firstDate,
            // lastDate: lastDate,
        	startDate: startDate,
        	selectedDate: selectedDate
		}, self.options );

        // header
        self.header = [];
        // var $header = $('<tr class="header">');
        for (var i=0; i<7; i++) {
        	self.header.push({
        		text: self.string.day( i ),
        	});
        }

        // Render the cells as <TD>
        var lists = [];
	    for (var y = 0, i = 0; y < 6; y++){
	        var row = [], show=true;

	        for (var x = 0; x < 7; x++, i++) {
	            var p = ((prevDateLastDay - firstDate.getDay()) + i + 1);
	            var n = p - prevDateLastDay;
	            var sub = "";
	            var active = (x == 0) ? "sun" : ((x == 6) ? "sat" : "day");
	            var activeDate = new Date(theDate); activeDate.setHours(0, 0, 0, 0); activeDate.setDate(n);

	            // If value is outside of bounds its likely previous and next months
	            if (n >= 1 && n <= lastDay){

                    var activeTime = activeDate.getTime();
                    // Test to see if it's today

                    if(todayTime==activeTime){
                    	active +=" today";
                    }

                    if(selectedTime==activeTime){
                    	active +=" selected";
                    }
                    
	            } else {
	      			
	      			active = "noday"; // Prev/Next month dates are non-selectable by default
                    n = (n <= 0) ? p : ((p - lastDay) - prevDateLastDay);

	      			if (y > 0 && x == 0) show = false;
	            }

	            
	            row.push({
	            	text: n,
	            	date: activeDate,
	            	active: active
	            });

	        } // end for col

	        // Create the row
	         if (show && row){
	         	lists.push({
	            	data: row
	            });
	         }

	    } // end for row
	    self.lists = lists;
	 	
	}
}

var Datelang = {
	init: function( options ){
		var self = this;

		self.type = options.type || "short";
		self.lang = options.lang || "en";
	},

	display: function( theDate ){

		var fullYear = self.lang=='th'
			? theDate.getFullYear()-543
			: theDate.getFullYear();

		return this.day( theDate.getDay() ) +" "+ theDate.getDate() + " " + this.month( theDate.getMonth() ) +" "+ fullYear;
	},

	fulldate: function( theDate, type, lang, displayYear ){

		lang = lang||this.lang||'th';

		var _DS = [', ', ', '];
		if( lang=='th' ){ 
			var _DS = ['ที่ ', ' '];
		}


		var year = '';
		if( displayYear ){
			year = _DS[1]+this.year( theDate.getFullYear(), type, lang );
		}

		return this.day( theDate.getDay(), type, lang ) + _DS[0] +
			theDate.getDate() + " " + this.month( theDate.getMonth(), type, lang) + year;
	},

	day: function( numbar, type, lang ){
		return this._day[type||this.type||'short'][lang||this.lang||'th'][numbar];
	},

	month: function( numbar, type, lang ){
		return this._month[type||this.type||'short'][lang||this.lang||'th'][numbar];
	},

	year: function( numbar, type, lang ){

		lang = lang||this.lang||'th';

		if( lang=='th' ){
			numbar +=543;
		}

		type = type||this.type||'short';
		if( type=='short' ){
			numbar = numbar.toString().substr(2, 2);
		}

		return numbar;
	},

	_day: {
		normal: {
			en: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
			th: ["วันอาทิตย์", "วันจันทร์", "วันอังคาร", "วันพุธ", "วันพฤหัสบดี", "วันศุกร์", "วันเสาร์"]
		},
		short: {
			en: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
			th: ["อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."]
		}
		
	},

	_month: {
		normal: {
			en: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],

			th: ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]
		},
		short: {
			en: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],

			th: ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."]
		}
		
	}
};
var uiLayer = {
	set: function (options, elem) {
		var self = this;

		self.$container = $(elem);
		self.$layer = $('<div/>', {class: 'uiContextualLayer'}).html(self.$container);
        self.$positioner = $('<div/>', {class: 'uiContextualLayerPositioner uiLayer'}).html(self.$layer);

        $( options.parent || 'body' ).append( self.$positioner );
	},

	get: function( options, elem ){
		var self = this;

		self.$content = $(elem);
		self.$elem = $('<div/>', {class: 'uiContextualLayerPositioner uiLayer'});
		self.$layer = $('<div/>', {class: 'uiContextualLayer'}).html( self.$content );
		self.$parent = options.$parent || $(window);
		 
		self.options = options;

		self.is_open = false;
		if( typeof self.options.is_auto_position === 'undefined' ){
			self.options.is_auto_position = true;
		}
		self.$elem.html( self.$layer );

		self.initEvent();

		self.is_open = true;
		self.$body = $( options.parent || 'body' );

		self.$body.append( self.$elem );

		self.config();

		// search position
		if( self.options.is_auto_position ){
			self.searchPosition();
			$( window ).resize( function(){
				
				self.config();
				self.searchPosition();
				self.resize();
			} );
		}

		self.resize();
	},

	config: function(){
		var self = this;

		self.top = self.options.top;
		self.left = self.options.left;

		if( self.options.pointer ){
			self.$layer.addClass('uiToggleFlyoutPointer');

			self.top += 12;
			self.left -= 22;
		}
	},

	initEvent: function(){
		var self = this;
	},

	resize: function(){
		var self = this;

		if( self.parent ){
		}

		self.$elem.css({
			top: self.top,
			left: self.left
		});
	},
	searchPosition: function( ){
		var self = this;

		// set Width
		var maxWidth = self.$parent.width();
		var needWidth = ( self.left + self.$layer.outerWidth() );

		if( self.options.axisX=='left' || self.options.axisX=='right' ){

			if( self.options.axisX=='right' ){
				self.$layer.addClass('uiToggleFlyoutRight');

				if( self.options.$elem ){
					self.left += self.options.$elem.outerWidth();
				}
			}
		}
		else{
			if( needWidth > maxWidth ){
				// overflow X
				self.$layer.addClass('uiToggleFlyoutRight');

				if( self.options.$elem ){
					self.left += self.options.$elem.outerWidth();
				}
				
				if(self.options.pointer){
					self.left += 44;
				}
			}else if(self.$layer.hasClass('uiToggleFlyoutRight')){
				self.$layer.removeClass('uiToggleFlyoutRight');


			}
		}
		
		// set Height
		var maxHeight = self.$parent.height();
		var needHeight = ( self.top + self.$content.height() );

		if( needHeight > maxHeight ){
			// overflow Y
			self.$layer.addClass('uiToggleFlyoutAbove');

			if(self.options.pointer){
				self.top -= 24;
			}
			else if(self.options.$elem){
				self.top -= self.options.$elem.outerHeight();
			}
		}
		else if(self.$layer.hasClass('uiToggleFlyoutAbove')){
			self.$layer.removeClass('uiToggleFlyoutAbove')
		}
		
	}
};

var Event = {
	URL: window.location.origin + '/',
	mobilecheck: function () {
		var check = false;
		(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera); return check;	
	},
	getCaret: function (el) {
		if (el.selectionStart) {
	        return el.selectionStart;
	    } else if (document.selection) {
	        el.focus();
	        var r = document.selection.createRange();
	        if (r == null) {
	            return 0;
	        }
	        var re = el.createTextRange(), rc = re.duplicate();
	        re.moveToBookmark(r.getBookmark());
	        rc.setEndPoint('EndToStart', re);
	        return rc.text.length;
	    }
	    return 0;
	},
	inlineSubmit: function( $form, formData, dataType ){

		var self = this;
		var dataType = dataType || 'json';

		var btnSubmit = $form.find('.btn.btn-submit');
		if( btnSubmit.hasClass('btn-error') ) btnSubmit.removeClass('btn-error');

		if( !formData ){
			var formData = new FormData();

			// set field
			$.each(self.formData($form), function (index, field) {
				formData.append(field.name, field.value);
	        });

	        // set file
	        $.each( $form.find('input[type=file]'), function (index, field) {

	        	var files = $(this)[0].files;

	        	if( files.length>0 ){
	        		formData.append(field.name, this.files[0]);
	        	}
	        });
		}

		if( $form.hasClass("loading") ) return false;
		btnSubmit.addClass('disabled');
		self.showMsg({ load: true });
		$form.find(':input').not('.disabled').attr('disabled', true);
		//  +'?__a=' + Math.random(),
		return $.ajax({
				type: "POST",
				url: $form.attr('action'),
				data: formData,
				dataType: dataType,
				processData: false, // Don't process the files
        		contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			}).always(function() {
				// complete
				self.hideMsg();
				btnSubmit.removeClass('disabled');
				$form.find(':input').not('.disabled').removeAttr('disabled', false);
			})
			.fail(function(  ) {
				// error
				btnSubmit.removeClass('disabled');
				self.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });

			});
	},
	formData: function (form) {
        return form.serializeArray();
    },
	processForm: function( $form, result  ){
		var self = this;

		if( !result ) {
			self.showMsg({ text: "Error: 101", load: true , auto: true });
			return false;
		}
		if( result.form_reset ){
			$form.trigger('reset');
		}

		var $btnSubmit = $form.find('.btn-submit');
		if( $btnSubmit.hasClass('btn-error') ) $btnSubmit.removeClass('btn-error');

		if( $form.find('.control-group').hasClass('has-error') )
			$form.find('.control-group').removeClass('has-error');

		$form.find('.notification').empty();

		if(!result||result.error){

			$.each(result.error, function(field, msg){
				var $field = $form.find('#'+field+"_fieldset"),
					$noity = $field.find('.notification');

				$field.addClass('has-error');
				$noity.html( msg );
			});

			if( result.message ){

				if( typeof result.message === 'string' ) {
					Event.showMsg({ text: result.message, load: true , auto: true });
				}
				else{
					Event.showMsg( result.message );
				}
			}

			self.emptyForm( $form );

			$btnSubmit.addClass('btn-error');
			return false;
		}

		if( result.callback ){
			var callback = result.callback.split(",");
			$.each(callback, function (i, fun) {
				__Callback[fun](result);
			});
		}

		if( result.onDialog==true ){ }
		else{ Dialog.close(); }

		if( result.link ){
			self.showMsg({ link: result.link, text: result.message, bg: 'yellow', sleep: result.link.sleep });
			return false;
		}

		if( result.url=="refresh" ){
			result.url = window.location.href;
		}

		if( result.message ){
			if( typeof result.message === 'string' ) {
				Event.showMsg({ text: result.message, load: true , auto: true });
			}
			else{
				Event.showMsg( result.message );
			}

			if( result.url ){

				setTimeout(function(){
					window.location = result.url;
				}, 2000);
			}
		}
		else if( result.url ){ window.location = result.url; }
	},
	emptyForm: function( $form ){

		var $fieldset = $form.find( 'fieldset.has-error' );
		var $btnSubmit = $form.find('.btn-submit');

		$fieldset.find(':input').blur(function(){

			if( $(this).val()!=""){
				$(this).parents( '.has-error' ).removeClass('has-error').find( '.notification' ).empty();
				if( $btnSubmit.hasClass('btn-error') ) $btnSubmit.removeClass('btn-error');
			}

		});

		$fieldset.find('select, [type=radio], [type=textbox]').change(function(){
			if( $(this).val()!=""){
				$(this).parents( '.has-error' ).removeClass('has-error').find( '.notification' ).empty();
				if( $btnSubmit.hasClass('btn-error') ) $btnSubmit.removeClass('btn-error');
			}
		});
	},
	showMsg: function( options ) {
		var self = this;

		var set = options || {};

		if( $('#alert-messages').length==0 ){

			var $dismiss = $('<span/>', {class: 'btn-icon icon-remove dismiss'});

			var el = $('<div/>', {class:"alert-messages", id:"alert-messages"})
				.html( $('<div/>', {class:"message"})
					.html(
						$('<div/>', {class:"message-inside"}).append(
							$('<div/>', {class:"message-text"}),
							$dismiss
						)
					)
				);

			$('body').append(el);
		}
		else{
			var el = $('#alert-messages');
			el.removeAttr('class').addClass('alert-messages');
			var $dismiss = $('#alert-messages').find('.dismiss');
		}

		// reset
		if( set.load ){
			el.addClass("load");
			el.find('.message-text').html("Loading...");
		}
		else el.removeClass("load");

		if( set.dismiss==false ){
			$dismiss.addClass('hidden_elem');
		}
		else if($dismiss.hasClass('hidden_elem')){
			$dismiss.removeClass('hidden_elem');
		}

		if( set.bg ){
			el.addClass( set.bg );
		}
		else{
			if( el.hasClass('yellow') ) el.removeClass('yellow');
		}

		if( set.text ){
			el.find('.message-text').html(set.text);
		}

		if( set.align ){
			el.addClass(set.align);
		}

		if( set.link ){
			el.find('.message-text').append( $('<a/>')
				.attr({
					href: set.link.url
				})
				.html( set.link.text )
			);
		}

		if ( set.auto || el.hasClass('auto') ) {
			setTimeout(function(){ self.hideMsg(); }, 3000);
		};

		if( set.sleep ){
			setTimeout(function(){ self.hideMsg(); }, set.sleep);
		}

		// event
		$dismiss.click(function(e){
			e.preventDefault();
			self.hideMsg(300);
		});

		el.stop(true,true).fadeIn(300);
	},

	hideMsg: function ( length ){

		$('#alert-messages').stop(true,true).fadeOut( length || 0, function () {

			$(this).remove();
		});
	},

	getPlugin: function ( name, url ) {
		var plugin_url = Event.URL  + 'public/js/plugins/';
		return $.getScript( url || plugin_url+name+".js" );
	},
	setPlugin: function ( $el, plugin, options, url ) {

		var self = this;
		if (typeof $.fn[plugin] !== 'undefined') {
			$el[plugin]( options );
		}
		else{
			self.getPlugin( plugin, url ).done(function () {
				$el[plugin]( options );
			}).fail(function () {
				console.log( 'Is not connect plugin:'+ plugin );
			});
		}
	},
	plugins: function ( $el ){
		var self = this;
		$elem = $el || $('html');

		$.each( $elem.find('[data-plugins]'), function(){

			var $this = $(this);

			var plugin = $this.attr('data-plugins'),
				options = {};

			$this.removeAttr('data-plugins');

			if( $this.attr('data-options') ){
				options = $.parseJSON( $this.attr('data-options') );

				$this.removeAttr('data-options');
			}

			// console.log(plugin);
			self.setPlugin( $this, plugin, options );
		});
	},

	scroll: function () {
		var self = this,
			currentHeight = $(window).height();

		$('.elevator-wrapper').toggleClass('visible', $(window).scrollTop()>=100 ? true:false);
		$('.elevator-wrapper').toggleClass('show', $(window).scrollTop()>=150 ? true:false);
	},


	log: function( options ) {
		var self = this;

		var set = options || {};

		if( $('#alert-messages-log').length==0 ){

			var el = $('<div/>', {class:"alert-messages-log", id:"alert-messages-log"});
			$('body').append(el);
		}
		else{
			var el = $('#alert-messages-log');
		}

		$item = $('<div/>', {class:"message"}).css('display','none');

		if( options.loading || options.loader){
			$item.addClass('loader').append( '<div class="loader-spin-wrap mrm"><div class="loader-spin"></div></div>', '<div class="loader-spin-text">loading...</div>' );
		}else if( options.text ){
			$item.text( options.text );
		}

		el.append( $item );
		$item.slideToggle( 200 );


		if( options.auto ){
			
			self.logHide( $item );			
			return false;
		}

		return $item;
	},

	logHide: function ( $el, length, callback ) {
		var self = this, $msg = $('#alert-messages-log');;

		if( !$el ){
			$el = $msg.find( '.message' ).first();
		}

		if( !$el ){
			return false;
		}

		var t = setTimeout(function () {
			$el.animate( {
				left: '-=' + 240
			}, 250, function () {
				
				$el.hide(100);
				setTimeout(function () {
					$el.remove();

					if( typeof callback === 'function' ){
						callback( 1 );
					}

					if( $msg.find( '.message' ).length==0 ){
						$msg.remove();
					}
				}, 200);
			});
		}, length || 5000);

		$el.mouseenter(function () {
			clearTimeout( t );
		}).mouseleave(function () {
			self.logHide( $el, 2400 );
		});
	}
};
var PHP = {
	number_format: function (number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
	    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	    var n = !isFinite(+number) ? 0 : +number,
	        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	        s = '',
	        toFixedFix = function (n, prec) {
	            var k = Math.pow(10, prec);
	            return '' + Math.round(n * k) / k;
	        };

	    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	    if (s[0].length > 3) {
	        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	    }
	    if ((s[1] || '').length < prec) {
	        s[1] = s[1] || '';
	        s[1] += new Array(prec - s[1].length + 1).join('0');
	    }

	    return s.join(dec);
	},

	dateJStoPHP: function ( date ) {
		m = date.getMonth()+1;
		m = m < 10 ? '0'+m:m;

		d = date.getDate();
		d = d < 10 ? '0'+d:d;
		return date.getFullYear() + '-' + m + '-' + d;	
	},

	dateJStoShortDate: function( date ){
		var d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;

		return [day, month, year].join('/');
	}
}

var __Callback = {
}

$(function () {

	Event.plugins();

	/**/
	/* Set Form */
	/**/ 
	$('body').delegate('form.js-submit-form','submit',function(e){
		var $form = $(this);
		e.preventDefault();
		Event.inlineSubmit( $form ).done(function( result ) {
			Event.processForm($form, result);
		});
	});

	Event.scroll();
	$(window).scroll(function () {
		Event.scroll();
	});
	$('.elevator-wrapper').click(function () {
		$('body').animate({scrollTop:0}, 300);
	});


	$('#primary-menu-toggle').click(function () {

		$('body').toggleClass('has-menu', $('body').hasClass('has-menu') ? false:true);
	});
});
