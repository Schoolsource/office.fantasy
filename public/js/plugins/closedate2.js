// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {


	var CloseDate2 = {
		init: function (options, elem) {
			var self = this;

			// self.elem = elem;
			self.$elem = $(elem);

			self.options = $.extend( {}, $.fn.closedate2.options, options );

			self.options.options = [];
			$.each(self.$elem.find('option'), function(index, el) {
				self.options.options.push( { value: $(this).val(), text: $(this).text() } );
			});

			self.config();
			self.setElem();
			
			self.setMenu();
			self.hideMenu();

			// set Calendar
			self.display();

			
			if( self.options.options.length > 1 ){
				self.activeIndex = self.options.activeIndex || 0;
				self._activeIndex();
				var data = self.$menu.find('li').eq( self.activeIndex ).data();
				self.selectMenu( data );

				self.events();
			}
			else{

				if( self.options.options[0].value=='custom' ){

					var date = new Date();
					var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
					var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

					self.startDate = new Date( firstDay );
					self.endDate = new Date( date );
					

					self.$elem.click(function(e) {

						if( !self.$calendar.hasClass('open') ){
							self.openCalendar();
							e.stopPropagation();
						}
					});


					var calendar_focus = false;
					$('[data-action=done]', self.$calendar).click(function(e){
						self.updateData();
						self.hideCalendar();
						
						
					});

					$('[data-action=cancel]', self.$calendar).click(function(e){
						self.hideCalendar();
					});

					self.$calendar.mouseenter(function(
						) {
						
						calendar_focus = true;

					}).mouseleave(function() {
						calendar_focus = false;
					});;

					$(document).on('click', function(event)
					{
						if(!calendar_focus && self.$calendar.hasClass('open') ){
							self.hideCalendar();
						}
					});

					
					self.updateData();
				}
			}

			if( typeof self.options.onComplete == 'function' ){
				self.options.onComplete( self );
			}
		},

		config: function () {
			var self = this;
			self.today = new Date();
			self.today.setHours(0, 0, 0, 0);

			// set date
			self.startDate = new Date( self.options.start || self.today );
			if( self.options.start==null ){
				self.startDate.setDate( 1 );
			}
			
			self.endDate = new Date( self.options.end || self.today );

			var lang = Object.create( Datelang );
			lang.init( {
				lang: self.options.lang,
				type: self.options.type
			} );
			self.string = lang;

			if( self.options.firstDate ){
				self.options.firstDate = new Date( self.options.firstDate );
				self.options.firstYear = self.options.firstDate.getFullYear();
			}
		},
		setElem: function () {
			var self = this;

			self.$startInput = $('<input>', { class: 'hiddenInput', type: 'hidden', name: 'start_date' });
			self.$endInput = $('<input>', { class: 'hiddenInput', type: 'hidden', name: 'end_date' });
			self.$text = $('<span>', {class: 'btn-text'});
			self.original = self.$elem;
			var placeholder = $('<div/>', {class: 'uiPopover'});

			self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$btn = $('<a>', {class: 'btn btn-box btn-toggle'}).append( self.$text );

			if( !self.options.icon ){
				self.$btn.append( $('<i/>', {class: 'img mls icon-angle-down'}) );
			}
			self.$elem.append( self.$btn, self.$startInput, self.$endInput);

			self.$calendar = $('<div>', {class: 'uiContextualPositioner'});
			self.$calendar.append( $('<div>', {class: 'toggleFlyout calendarGridTableSmall'}) );	
			if( self.options.max_width ){
				self.menu.find('.toggleFlyout').css('width', self.options.max_width);
			}

			self.$start = $('<td>', {class: 'start'});
			self.$end = $('<td>', {class: 'end'});

			self.$preveiw = $('<span>', {class: 'preveiw lfloat'});

			var $table = $('<table/>', {class: 'calendarCloseDateGridTable'}).append( 
				  $('<tr>').append( self.$start, $('<td>', {class: 'to', text: 'to'}), self.$end )
				, $('<tr>').append( $('<td>', {colspan: 3, class: 'tar ptm'}).append(
					  self.$preveiw
					, $('<a>', {class: 'btn btn-cancel', 'data-action':'cancel', text: 'Cancel'})
					, $('<a>', {class: 'btn btn-blue btn-submit', 'data-action':'done' , text: 'Done'})
				) )
			);

			self.$calendar.find('.toggleFlyout').append( $table );

			// self.$menu = 

			/*self.updateCalendar();
			self.setSlecte();*/
		},
		setMenu: function () {
			var self = this;

			if( self.options.options.length==1 ) return;

			self.$menu = $('<ul/>', {class: 'uiContextualMenu', role: "listbox"});

			var settings = self.$btn.offset();
			settings.top += self.$btn.outerHeight();

			uiLayer.get(settings, self.$menu);
			self.$layer = self.$menu.parents('.uiLayer');


			// event
			self.$menu.mouseenter(function () {
				self.is_focus = true;
			}).mouseleave(function () {
				self.is_focus = false;
			});

			self.resizeMenu();
			$( window ).resize(function () {
				self.resizeMenu();
			});

			$.each( self.options.options, function (i, obj) {
				self.$menu.append( self.setItemMenu( obj ) );				
			});

			self.$menu.find('li').mouseenter(function () {
				$(this).addClass('active').siblings().removeClass('active');
			});

			self.$menu.mouseleave(function () {
				
				self._activeIndex();
			});
		},
		resizeMenu: function() {
			var self = this;

			self.$menu.width( self.$btn.outerWidth()-2 );
			var settings = self.$btn.offset();
			settings.top += self.$btn.outerHeight();
			settings.top -= 1;
			// settings.left += 3;

			self.$menu.css({
				overflowY: 'auto',
				overflowX: 'hidden',
				maxHeight: $( window ).height()-settings.top
			});

			self.$menu.parents('.uiContextualLayerPositioner').css( settings );
		},
		setItemMenu: function(data) {
			
			var li = $('<li/>');
			if( data.divider ){
				li.addClass('divider');
			}
			else{
				li.html( $('<a>', {class: 'clearfix'}).append(
					$('<span>', {class: 'text', text: data.text })
				) );
			}
			

			if( data.image_url ){
				li.addClass('picThumb');
				/*$('<div>', {class: 'avatar lfloat mrs'}).html( $('<img>', {src: URL + 'public/images/avatar/error/user2.png'}) )*/
			}

			if( data.activity=='new' ){
				li.addClass('new').find('.text').before( 
					$('<div>', {class: 'box-icon'}).append( $('<i>', {class: 'icon-plus'}) )
				);
			}

			li.data( data );

			return li;
		},

		updateCalendar: function ( startDate, endDate ) {
			var self = this;

			// start
			self.setDataStr();

			var $start = self.setCalendar( startDate || self.startDate );
			$start.addClass('start');
			$start.find('[data-date='+self.startDateStr+']').addClass('select-start');
			$start.find('[data-date='+self.endDateStr+']').addClass('select-end');

			self.$start.html( $start );

			// end
			var $end = self.setCalendar( endDate || self.endDate );
			$end.addClass('end');
			$end.find('[data-date='+self.startDateStr+']').addClass('select-start');
			$end.find('[data-date='+self.endDateStr+']').addClass('select-end');
			self.$end.html( $end );

			self.$preveiw.text( self.setTextCalendar() );

			// event 
			$('td[data-date]', $start).click(function(e){
				
				e.stopPropagation();

				var selected = new Date( $(this).attr('data-date') );
				selected.setHours(0, 0, 0, 0);

				if( selected.getTime() > self.endDate.getTime() ){
					return false;
				}

				self.startDate = selected;

				self.updateCalendar(selected, $end.data('date'));
			});
			$('td.prev, td.next', $start).click(function(e){

				var offset = $(this).hasClass("prev") ? -1 : 1;
				var date = new Date( $start.data('date') );
				date.setMonth( date.getMonth() + offset);

				self.updateCalendar( date, $end.data('date') );
				e.stopPropagation();
			});
			$('.selectMonth', $start).change(function(e){
				
				var date = new Date( $start.data('date') );
				date.setMonth( $(this).val() );

				self.updateCalendar( date, $end.data('date') );
				// e.stopPropagation();
			}).click(function (e) {
				e.stopPropagation();
			});
			$('.selectYear', $start).change(function(e){
				
				var date = new Date( $start.data('date') );
				date.setYear( $(this).val() );

				self.updateCalendar( date, $end.data('date') );
			}).click(function (e) {
				e.stopPropagation();
			});

			$('td[data-date]', $end).click(function(e){
				
				e.stopPropagation();

				var selected = new Date( $(this).attr('data-date') );
				selected.setHours(0, 0, 0, 0);

				if( selected.getTime() < self.startDate.getTime() ){
					return false;
				}

				self.endDate = selected;
				self.updateCalendar( $start.data('date'), selected );
			});
			$('td.prev, td.next', $end).click(function(e){

				var offset = $(this).hasClass("prev") ? -1 : 1;
				var date = new Date( $end.data('date') );
				date.setMonth( date.getMonth() + offset);

				self.updateCalendar( $start.data('date'), date );
				e.stopPropagation();
			});

			$('.selectMonth', $end).change(function(e){
				
				var date = new Date( $end.data('date') );
				date.setMonth( $(this).val() );

				self.updateCalendar( $start.data('date'), date );
				// e.stopPropagation();
			}).click(function (e) {
				e.stopPropagation();
			});

			$('.selectYear', $end).change(function(e){
				
				var date = new Date( $end.data('date') );
				date.setYear( $(this).val() );

				self.updateCalendar( $start.data('date'), date );
			}).click(function (e) {
				e.stopPropagation();
			});
		},

		setDataStr: function () {
			var self = this;

			self.startDateStr = self.startDate.getFullYear();
			m = self.startDate.getMonth()+1; 
			self.startDateStr += "-" + (m < 10 ? "0"+m:m);
			d = self.startDate.getDate(); 
			self.startDateStr += "-" + (d < 10 ? "0"+d:d);

			self.endDateStr = self.endDate.getFullYear();
			m = self.endDate.getMonth()+1; 
			self.endDateStr += "-" + (m < 10 ? "0"+m:m);
			d = self.endDate.getDate(); 
			self.endDateStr += "-" + (d < 10 ? "0"+d:d);
		},
		updateData: function( text ){
			var self = this;

			self.setDataStr();
			if( !text ){
				text = self.setTextCalendar();
			}
			
			self.$text.text( text );

			self.$startInput.val( self.startDateStr );
			self.$endInput.val( self.endDateStr );

			if( typeof self.options.onChange == 'function' ){

				self.options.onChange( self );
			}
		},

		setTextCalendar: function() {
			var self = this;

			var TO = '-'; //self.options.lang == 'th' ? ' à¸–à¸¶à¸‡ ' : ' to ';
			var $text = $('<span>');

			if( self.startDate.getDate()==self.endDate.getDate() && self.startDate.getMonth()==self.endDate.getMonth() && self.startDate.getFullYear()==self.endDate.getFullYear() ){

				$text.append( 
					  self.endDate.getDate()
					, ' '
					, self.string.month( self.startDate.getMonth(), 'normal', self.options.lang ) 
					, ' '
					, self.string.year( self.startDate.getFullYear(), 'normal', self.options.lang ) 
				);
			}
			else if( self.startDate.getMonth()==self.endDate.getMonth() && self.startDate.getFullYear()==self.endDate.getFullYear() ){
				$text.append( 
					  self.startDate.getDate()
					, TO
					, self.endDate.getDate()
					, ' '
					, self.string.month( self.startDate.getMonth(), 'normal', self.options.lang ) 
					, ' '
					, self.string.year( self.startDate.getFullYear(), 'normal', self.options.lang ) 
				);
			}
			else if( self.startDate.getFullYear()==self.endDate.getFullYear() ){
				$text.append( 
					  self.startDate.getDate()
					, ' '
					, self.string.month( self.startDate.getMonth(), 'normal', self.options.lang ) 
					
					, TO

					, self.endDate.getDate()
					, ' '
					, self.string.month( self.endDate.getMonth(), 'normal', self.options.lang ) 

					, ' '
					, self.string.year( self.startDate.getFullYear(), 'normal', self.options.lang ) 
				);
			}
			else{

				$text.append( 
					  self.startDate.getDate()
					, ' '
					, self.string.month( self.startDate.getMonth(), 'normal', self.options.lang ) 
					, ' '
					, self.string.year( self.startDate.getFullYear(), 'normal', self.options.lang ) 
					
					, TO

					, self.endDate.getDate()
					, ' '
					, self.string.month( self.endDate.getMonth(), 'normal', self.options.lang ) 
					
					, ' '
					, self.string.year( self.endDate.getFullYear(), 'normal', self.options.lang ) 
				);
			}

			return $text.text();
		},

		setCalendar: function( date ){
			var self = this;

 			var theDate = new Date( date );
			var firstDate = new Date( theDate.getFullYear(), theDate.getMonth(), 1);
			firstDate = new Date(theDate);
	        firstDate.setDate(1);
	        var firstTime = firstDate.getTime();
			var lastDate = new Date(firstDate);
	        lastDate.setMonth(lastDate.getMonth() + 1);
	        lastDate.setDate(0);
	        var lastTime = lastDate.getTime();
	        var lastDay = lastDate.getDate();

			// Calculate the last day in previous month
	        var prevDateLast = new Date(firstDate);
	        prevDateLast.setDate(0);
	        var prevDateLastDay = prevDateLast.getDay();
	        var prevDateLastDate = prevDateLast.getDate();

	        var prevweekDay = self.options.weekDayStart;
	
			prevweekDay = prevweekDay>prevDateLastDay
				? 7-prevweekDay
				: prevDateLastDay-prevweekDay;

			var $tbody = $('<tbody>');
			var lists = [];
			for (var y = 0, i = 0; y < 7; y++){

				var $tr = $('<tr>');
				var row = [];
				var weekInMonth = false;

				for (var x = 0; x < 7; x++, i++) {
					var p = ((prevDateLastDate - prevweekDay ) + i);

					var call = {};
					var n = p - prevDateLastDate;
					call.date = new Date( theDate ); 
					call.date.setHours(0, 0, 0, 0); 
					call.date.setDate( n );

					call.date_str = call.date.getFullYear();
					m = call.date.getMonth()+1; 
					call.date_str += "-" + (m < 10 ? "0"+m:m);

					d = call.date.getDate(); 
					call.date_str += "-" + (d < 10 ? "0"+d:d);

					$td = $('<td>');


					if( self.startDate.getTime() > call.date.getTime() || self.endDate.getTime() < call.date.getTime() ){
						$td.addClass('overtime');
					}

					// If value is outside of bounds its likely previous and next months
	            	if (n >= 1 && n <= lastDay){
	            		weekInMonth = true;

	            		$td.attr('data-date', call.date_str );

	            		$td.append( $('<span>', {text: n}) );

	            		if( self.today.getTime()==call.date.getTime()){
	            			$td.addClass('today');
	                    	call.today = true;
	                    }

	                    if( theDate.getTime()==call.date.getTime() ){

	                    	$td.addClass('selected');
	                    	call.selected = true;
	                    }
	            	}
	            	else{
	            		call.noday = true;
	            	}

	            	/*if( self.date.startDate ){
                    	if( self.date.startDate.getTime()>call.date.getTime() ){
                    		call.empty = true;
                    	}
                    }*/

	            	$tr.append( $td );                    
					row.push(call);
				}

				if( row.length>0 && weekInMonth ){

					// console.log( row );
					$tbody.append( $tr ) ;
					lists.push(row);
				}
			}

			var $selectMonth = $('<select>', {class: 'selectMonth'});
			for (var i = 0; i < 12; i++) {
				option = $('<option>', {text: self.string.month( i, 'normal' ), value: i});

				//  && theDate.getFullYear() == 
				if( theDate.getMonth() == i ){
					option.attr('selected', true);
				}
				
				$selectMonth.append( option );
			};


			var endYear = self.options.firstYear || self.today.getFullYear()-5;
			var $selectYear = $('<select>', {class: 'selectYear'});
			for (var i = self.today.getFullYear(); i >= endYear; i--) {

				option = $('<option>', {text: i, value: i });
				
				if( theDate.getFullYear() == i ){
					option.attr('selected', true);
				}
				$selectYear.append( option );
			};

			var $title = $('<thead>').html( $("<tr>", {class: 'title'})
				.append( $('<td>', {class: 'prev'}).append( $('<i/>', {class:'icon-angle-left'}) ) )
				.append( $('<td>', {class: 'title', colspan: 5 }).append( $selectMonth, $selectYear ) )
				.append( $('<td>', {class: 'next'}).append( $('<i/>', {class:'icon-angle-right'}) ) )
			);


			var $header = $("<tr>", {class: 'header'});

			for (var x=0,i=self.options.weekDayStart; x<7; x++, i++) {
				$header.append( $('<th>', {text: self.string.day( i )}) );
				if( i>=6 ){ i = -1; }
			};
			$thead = $('<thead/>').html( $header );

			return $('<table/>', { 
				class: 'calendarGridTable range', 
				cellspacing: 0, 
				cellpadding: 0,
			}).data( 'date', theDate ).append( $title, $thead, $tbody );
		},

		display: function () {
			var self = this;

			$('body').append( self.$calendar );
		},

		events: function () {
			var self = this;

			self.$menu.find('li').click(function() {
				
				var data = $(this).data();

				self.activeIndex = $(this).index();
				self._activeIndex();

				if( data.value=='custom' ){
					self.hideMenu();
					self.openCalendar();
					return false;
				}
				
				self.selectMenu( data );
			});

			self.$btn.click(function(e){
				
				$('body').find('.uiPopover').find('a.btn-toggle.active').removeClass('active');

				self.openMenu();
				self.resizeMenu();

				if( self.$calendar.hasClass('open') ){
					self.$calendar.removeClass('open');
				}
				
				e.stopPropagation();
			});

			$(window).resize(function(){
				self.getOffset();
			});

			$('html').on('click', function() {
				
				// if( self.active ){
				self.hideMenu();
				self.hideCalendar();
				// }
			});

			$('.btn-submit', self.$calendar).click(function(e){
				self.updateData();
			});
		},

		selectMenu: function (data) {
			var self = this;

			self.endDate = new Date( self.today );
			self.startDate = new Date( self.endDate );

			var date_str = '', minus = 0;

			if( data.value=='last7days' ){
				minus = 7;
			}else if( data.value=='last14days' ){
				minus = 14;
			}else if( data.value=='last28days' ){
				minus = 28;
			}else if( data.value=='last90days' ){
				minus = 90;
			}else if( data.value=='yesterday' ){

				self.endDate.setDate( self.endDate.getDate()-1 );
				self.startDate = new Date( self.endDate );

			}else if( data.value=='daily' ){

				date_str = ' | ' + self.today.getDate() + ' ' + self.string.month( self.today.getMonth() ) + ' ' + self.today.getFullYear();
				self.startDate.setDate( self.today.getDate() );
				self.endDate.setDate( self.today.getDate() );

			}else if( data.value=='weekly' ){
				var first = self.today.getDate() - self.today.getDay();
				first +=1;
				var last = first + 6;

				self.startDate.setDate( first );
				self.endDate.setDate( last );

				date_str = ' | ' 
					+ self.startDate.getDate() 
					+ ' - ' 
					+ self.endDate.getDate() 
					+ ' ' 
					+ self.string.month( self.startDate.getMonth() ) 
					+ ' ' 
					+ self.endDate.getFullYear();

			}else if( data.value=='last1week' ){

				var first = self.today.getDate() - self.today.getDay();
				first -=6;
				var last = first + 6;

				self.startDate.setDate( first );
				self.endDate.setDate( last );

			}else if( data.value=='monthly' ){
				self.startDate.setDate( 1 );

				var date = new Date( self.startDate );
				date.setMonth( date.getMonth()+ 1 );
				date.setDate( 0 );

				date_str = ' | ' 
					+ 1 
					+ ' - ' 
					+ date.getDate()
					+ ' ' 
					+ self.string.month( self.startDate.getMonth() ) 
					+ ' ' 
					+ self.endDate.getFullYear();
			}
			else if( data.value=='latest' ){
				
				self.startDate = new Date('1989-01-01');
				self.endDate.setDate( self.today.getDate() );
			}
			
			if( minus>0 ){
				self.startDate.setDate( self.startDate.getDate()-minus );
			}

			self.updateData( data.text + date_str );
		},

		_activeIndex: function() {
			var self = this;
			if( self.options.options.length <= 1 ) return;

			if( self.activeIndex=='undefined' ){
				self.$menu.find('li.active').removeClass('active');
			}
			else{
				self.$menu.find('li').eq( self.activeIndex ).addClass('active').siblings().removeClass('active');
			}
		},

		openMenu: function () {
			var self = this;

			if( self.$layer ){
				self.$layer.removeClass('hidden_elem');
			}
		},
		hideMenu: function () {
			var self = this;

			if( self.$layer ){
				self.$layer.addClass('hidden_elem');
			}
			
		},

		openCalendar: function () {
			var self = this;

			self.updateCalendar();
			self.getOffset();
			// self.$btn.addClass('active');
			self.$calendar.addClass('open');

		},
		hideCalendar: function () {
			var self = this;
			
			// self.$btn.addClass('active');
			self.$calendar.removeClass('open');		
		},
		
		getOffset: function(){
			var self = this;
			
			if( self.$calendar.hasClass('uiContextualAbove') ){
				self.$calendar.removeClass('uiContextualAbove');
			}
			
			var outer = $(document).height()<$(window).height()?$(window):$(document);

			var offset = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();

			var position = offset;
			
			position.top += self.$elem.outerHeight();
			
			var innerWidth = position.left+self.$calendar.outerWidth();
			if( $('html').hasClass('sidebarMode') ){
				innerWidth+= 301;
			}

			if( innerWidth>outerWidth ){
				position.left = offset.left-self.$calendar.outerWidth()+self.$elem.outerWidth();
			}
			
			var innerHeight = position.top+self.$calendar.outerHeight();
			if( innerHeight>outerHeight ){
				position.top = offset.top-self.$calendar.outerHeight()-self.$elem.outerHeight();
				self.$calendar.addClass('uiContextualAbove'); 
			}

			self.$calendar.css( position );
		},		
	};
	$.fn.closedate2 = function( options ) {
		return this.each(function() {
			var $this = Object.create( CloseDate2 );
			$this.init( options,this );
			$.data( this, 'closedate2', $this );
		});
	};
	$.fn.closedate2.options = {
		lang: $('html').attr('lang') || 'en',
		selectedDate: null,
		firstDate: null,
		start: null,
		end: null,
		weekDayStart: 1,
		type: 'short',
		format : '',
		options: [
			{
				text: 'Today',
				value: 'daily',
			},
			{
				text: 'Yesterday',
				value: 'yesterday',
			},
			{
				text: 'This week',
				value: 'weekly',
			},
			{
				text: 'Last week',
				value: 'last1week',
			},
			{
				text: 'This month',
				value: 'monthly', 
			},
			{
				text: 'Last 7 days',
				value: 'last7days', // weekly
			},
			{
				text: 'Last 14 days',
				value: 'last14days',
			},
			{
				text: 'Last 28 days28',
				value: 'last28days',
			},
			{
				text: 'Last 90 days',
				value: 'last90days',
			},
			{
				text: 'Custom',
				value: 'custom',
			}
		],
		onSelected: function () { },
	};

})( jQuery, window, document );