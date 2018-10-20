// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {
	
	var Render = {
		init: function( options, elem ){
			var self = this;

			self.elem = elem;
			self.$elem = $( elem );

			self.options = $.extend( {}, $.fn.calendar.options, options );

			self.config();
			self.setElem();
			self.refresh();

			if ( typeof self.options.onComplete === 'function' ) {
				self.options.onComplete.apply( self, arguments );
			}
		},

		config:function () {
			var self = this;

			self.options.lang = {
				lang: self.options.lang,
				type: (self.options.summary==true)? "short":"normal"
			}

			if( !self.options.selectedDate ){
				self.options.selectedDate = new Date();
			}
			self.options.selectedDate.setHours(0, 0, 0, 0);

			self.options.theDate = new Date(self.options.selectedDate);
			self.options.theDate.setHours(0, 0, 0, 0);

			// set date
			self.date = {
				today: new Date(),
				theDate: self.options.theDate,
				selected: self.options.selectedDate
			};
			self.date.today.setHours(0, 0, 0, 0);

			var lang = Object.create( Datelang );
			lang.init( self.options.lang );
			self.string = lang;
		},
		setElem: function(){
			var self = this;

            var original = self.$elem, placeholder = $('<div class="calendarGridRoot"></div>');
            self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$wrapper = $('<div class="calendarWrapper"></div>');
            self.$root = $('<div class="calendarMonthRoot"></div>');

            self.$wrapper.append( self.$root );
            self.$elem.append( self.$wrapper );

            $( window ).resize(function () {
            	self.resize();
            });

            $('.navigation-trigger').click(function () {
            	self.resize();
            });          
		},
		refresh: function () {
			var self = this;

			self.update();
			self.display();
			self.initEvent();
			self.resize();

			if( self.options.url ){
				self.load();
			}
		},
		update: function() {
			var self = this;

			var settings = self.calculation( self.date.theDate );
			self.$calendar = self.Theme[ self.options.theme ]( settings, self.date, self.string );

			var $add = '';

			if( self.options.add_url ){

				$add = $('<a/>',{class: 'rfloat btn btn-blue mrm', href: self.options.add_url}).append( 
					$('<i/>', {class:'icon-plus mrs'}), self.options.add_label || 'เพิ่ม' );

				$add.dialog();
			}

			//
			self.$calendar.find('.calendarBoxHeaderRoot').addClass('clearfix').append( 
				
				  $('<h2/>',{class: 'title lfloat mlm'}).text( self.string.month( self.date.theDate.getMonth(), "normal" ) + " " + (self.date.theDate.getFullYear()+543) )

				, $add

				, $( '<div>', {class: 'rfloat group-btn mrm'} ).append(
					  $('<a/>',{class: 'btn prevnext prev'}).html( $('<i/>', {class:'icon-chevron-left'}) )
					, $('<a/>',{class: 'btn today'}).text( 'วันนี้' )
				
					, $('<a/>',{class: 'btn prevnext next'}).html( $('<i/>', {class:'icon-chevron-right'}) )
				)

				
		    );
		},
		display:  function() {
			this.$root.html( this.$calendar );
		},
		initEvent: function () {
			var self = this;

			self.$calendar.find(".prevnext").click(function(e){
				e.preventDefault();

				var offset = $(this).hasClass("prev") ? -1 : 1;
				var newDate = new Date( self.date.theDate );

				newDate.setMonth( self.date.theDate.getMonth() + offset);
				
				self.date.theDate = newDate;
                self.refresh();

			});

			self.$calendar.find(".today").click(function(e){
				e.preventDefault();

				today = new Date();
				today.setHours(0,0,0,0);

				if( self.date.theDate.getTime()==today.getTime() ){
					return;
				}

				self.date.theDate = today;
				self.date.selected = new Date();
                self.refresh();

			});
		},
		resize: function () {
			var self = this;
			if( self.options.theme=='month' && self.$calendar.length==1 ){
				var fullWidth = self.$elem.outerWidth();
				var length = self.$calendar.find('th.calendarGridCell').length;

				var width = fullWidth/length;

				self.$calendar.find('.calendarGridItem,.calendarGridDayHeader').css('width', width );

				self.resizeTab();	
			}
		},
		calculation: function( date ){
			var self = this;
			var data = {};
			var theDate = date || self.date.today;

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
	        if( prevweekDay>prevDateLastDay ){
	        	prevweekDay = 7-prevweekDay;
	        }
	        else{
	        	prevweekDay = prevDateLastDay-prevweekDay;
	        }

			data.lists = [];
			for (var y = 0, i = 0; y < 7; y++){

				var row = [];
				var weekInMonth = false;

				for (var x = 0; x < 7; x++, i++) {
					var p = ((prevDateLastDate - prevweekDay) + i);

					var call = {};
					var n = p - prevDateLastDate;
					call.date = new Date( theDate ); 
					call.date.setHours(0, 0, 0, 0); 
					call.date.setDate( n );

					// If value is outside of bounds its likely previous and next months
	            	if (n >= 1 && n <= lastDay){
	            		weekInMonth = true;

	            		if( self.date.today.getTime()==call.date.getTime()){
	                    	call.today = true;
	                    }

	                    if( self.date.selected.getTime()==call.date.getTime() ){
	                    	call.selected = true;
	                    }
	            	}
	            	else{
	            		call.noday = true;
	            	}

					row.push(call);
				}

				if( row.length>0 && weekInMonth ) data.lists.push(row);
			}

			data.header = [];
			for (var x=0,i=self.options.weekDayStart; x<7; x++, i++) {
				if( i==7 ) i=0;
				data.header.push({
	        		key: i,
	        		text: self.string.day( i, 'normal' )
	        	});
			};

			self.date.first = firstDate;
			self.date.end = lastDate;
			self.date.start_date = data.lists[0][0].date;
			self.date.end_date = data.lists[ data.lists.length-1 ][6].date;

			return data;
		},
		Theme: {
			month: function ( settings, options, string ) {
				var $tbody = $('<tbody/>');
				// header
				var $header = $("<tr/>");

				$.each( settings.header, function(i, obj){

					$text = $('<div>').addClass('calendarGridDayHeader').html( $('<h2>').addClass('pas fsm fwn fcg').text( obj.text ) ); // 

					$header.append( $('<th>').append( $text ) );
				});
				$tbody.append( $header );

				// lists days
				$.each( settings.lists, function(y, row){
					$tr = $('<tr>');
					$.each(row, function(x, call){

						$fade = $('<div class="calendarFade"></div>');

						/*var monthStr = call.date.getMonth()+1;
						monthStr = monthStr<10? '0'+monthStr:monthStr;
						var dateStr = call.date.getDate();
						dateStr = dateStr<10? '0'+dateStr:dateStr;*/

						// options.end = call.date.getFullYear()+"-"+monthStr+"-"+dateStr;

						if( x==0 && y==0 ){
							// self.date.start = self.date.end;
						}
						
						$td = $('<td>')
							.append( $('<div>', { 
									class: 'calendarGridItem', 
									'data-date': Render.getDateToStr( call.date ),
									/*'data-month': monthStr,
									'data-year': call.date.getFullYear(),*/
									// 'data-row': 0,
									// 'data-col': 0
								})
								.append( $('<a>', {class: 'calendarItemDay calendarItemDayLink'})
									.html( $('<span>', {class: 'mrs calendarItemDayNumber'})
										.text( call.date.getDate() ) 
									)
								)
							);

						if( call.noday ){
							$td .addClass('calendarGridCellEmpty')
								.find('.calendarItemDayNumber').addClass('fsm fwn fcg');

							if( call.date.getDate()==1 || (y==0&&x==0)){
								$td .find('.calendarItemDayNumber')
									.text( call.date.getDate() +' '+ string.month( call.date.getMonth(),"normal" ) );
							}
						} //self.
						else{
							$td.find('.calendarItemDayNumber').addClass('fwb fcb');
						}

						$td .addClass( call.today?'today':"" )
							.addClass( call.date.getDay()==6 || call.date.getDay()==0?'weekHoliday':'' );

						$tr.append( $td );
					});

					$tbody.append( $tr );
				});

				// ser addClass
				$tbody.find('tr').addClass('calendarGridRow');
				$tbody.find('th,td').addClass('calendarGridCell calendarGridCellLeftBorder');
				$tbody.find('th').addClass('calendarGridCellSubtitle');
				$tbody.find('tr').find("td:first,th:first").removeClass('calendarGridCellLeftBorder');
				
				$tbody.find('td.today').removeClass('today').addClass('calendarGridToday').parent().addClass('calendarGridWeek');

				var $table = $('<table/>', {
					class: 'calendarGridTable',
					cellspacing:0,
					cellpadding:0

				});
				$table.html( $tbody );

				var $calendar = $('<div/>')
					.addClass('calendarBox calendarGrid')
					.append( 
						  $('<div/>',{class: 'calendarBoxHeaderRoot'})
						, $('<div/>',{class: 'calendarBoxContentRoot'}).append( 
						  	$table, 
						  	$('<div/>',{class: 'calendarBoxDataEvent'}) 
						)
					);

				return $calendar;
			}
		},

		load: function () {
			var self = this;

			// console.log( self.date.start_date, self.date.end_date );
			setTimeout(function () {
				

				self.fetch().done(function( results ) {
 
					self.dataEvt = results;
					self.buildFrag();
					// console.log( results );
				});
			}, 1);
			
		},
		fetch: function(url, getData){
			var self = this;

			return $.ajax({
				url: self.options.url,
				data: { 
					start_date: self.getDateToStr(self.date.start_date), 
					end_date: self.getDateToStr(self.date.end_date)
				},
				dataType: 'json'
			})
			.always(function() {
				// Event.hideMsg();
			})
			.fail(function() { 

				// self.close();
				// Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
			});
		},
		getDateToStr: function (theDate) {
			
			month = theDate.getMonth()+1;
			month = month < 10 ? "0"+month:month;

			date = theDate.getDate();
			date = date < 10 ? "0"+date:date;

			return theDate.getFullYear() + "-" + month + "-" + date;
		},

		buildFrag: function  ( results ) {
			var self = this;
			
			data = [];

			$.each( self.dataEvt, function (i, obj) {

				self.calTab(obj);
			} );

		},

		calTab: function ( obj ) {
			var self = this; 

			start = new Date( obj.start_date );
			start.setHours(0,0,0,0);

			end = new Date( obj.end_date );
			end.setHours(0,0,0,0);

			// 
			dStart = '';
			row = -1;
			first = true;
			col = 0;
			
			for (var j = 1; j < 60; j++) {

				var date = new Date( start );
				elem = self.$calendar.find('.calendarGridItem[data-date='+ self.getDateToStr(date) +']');

				// tdCol = elem.parent().index();

				if( !dStart && elem.length==1 ){
					dStart = date;
				}

				if( elem.length==1 ){

					col++;
					var data = elem.data('lists') || [];

					if( row<0 ){
						row = Object.keys(data).length;

						if( row>0 && first ){
							for (var k = 0; k < 10; k++) {
								if( !data[k] ){
									row = k;
									break;
								}
								
							};
						}
					}
					
					// console.log( obj.text, row, col );

					data[row || 0] = obj;
					first = false;

					// update
					elem.data('lists', data);


					dEnd = new Date( elem.closest('tr').find('.calendarGridItem').last().data('date') );
					dEnd.setHours(0,0,0,0);

					if( start.getTime()==dEnd.getTime() && end.getTime()!=dEnd.getTime() ){
						obj.next = true;
						break;	
					} 
				}
				else{
					obj.prev = true;
				}
				
				if( start.getTime() == end.getTime() ){
					break;
				}
				start.setDate( start.getDate() + 1 );
			};
		
			obj.row = row;
			obj.col = col;

			// console.log( obj.text,  dStart );
			obj.dStart = new Date( dStart );
			obj.dStart.setHours(0,0,0,0);
			obj.td = self.$calendar.find('.calendarGridItem[data-date='+ self.getDateToStr(obj.dStart) +']');
			obj.tr = obj.td.closest('tr');

			if( obj.td.length==0 ) return;

			tdCol = obj.td.parent().index();
			if( obj.col>(7-tdCol) ){
				obj.next = true;
				obj.col = 7-tdCol;
			}

			self.setTab( obj );

			if( obj.next ){
				obj.next = false;
				obj.prev = true;

				obj.start_date = obj.dStart.setDate( obj.dStart.getDate()+obj.col );
				self.calTab( obj );
			}
		},

		setTab: function ( data ) {
			var self = this;

			var title = $('<span/>').text( data.text );
			var icon = '';
			var tab = $('<div>');

			if( data.url ){
				tab = $('<a>', {href: data.url}); //.text( title );

				if( data.plugin == 'dialog' ){
					tab.dialog();
				}
			}

			if( data.icon ){
				icon = $('<i>', {class: 'mrs icon-' +  data.icon});
			}

			var $bg = $('<div>', {class: 'bg'});

			if( data.color_code ){
				$bg.css('background-color', "#"+data.color_code)
			}

			tab.attr({
				class: 'tab col-'+data.col,
				'data-id': data.id
			})
			.addClass( data.prev ? 'prev':'' )
			.addClass( data.next ? 'next':'' )
			.append(
				  $bg 
				, $('<div>', {class: 'text ellipsis'}).append( icon, title )
			)

			self.$calendar.find('.calendarBoxDataEvent').append( tab );

			// Event 
			tab.mouseenter(function () {
				$('.tab[data-id='+ $(this).data('id') +']').addClass('hover');
			}).mouseleave(function () {
				$('.tab[data-id='+ $(this).data('id') +']').removeClass('hover');
			});

			tab.data( data );
			self.resizeTab();
		},
		resizeTab: function () {
			var self = this;

			$.each(self.$calendar.find('.calendarBoxDataEvent>.tab'), function (i, obj) {
				
				data = $( obj ).data();

				// res
				w = data.td.parent().outerWidth();
				h = data.td.parent().height();

				data.pos = data.td.parent().position();

				data.pos.left += 1;
				data.pos.top += (20 * (data.row+1)) + (data.row) + 12;

				// set height
				orverH = (data.row+1)*21 + 30;
				if( orverH > h ){
					data.tr.find('.calendarGridItem').height( orverH );
				}

				// set width
				data.width = 0;
				var date = new Date( data.dStart );
				for (var d = 0; d < data.col; d++) {
					td = self.$calendar.find('.calendarGridItem[data-date='+ self.getDateToStr(date) +']');
					data.width+=td.parent().outerWidth();
					date.setDate( date.getDate()+1 );
				};

				$( obj ).css( data.pos ).width( data.width );
			});
		}
	}

	$.fn.calendar = function( options ) {
		return this.each(function() {
			var calendar = Object.create( Render );
			calendar.init( options, this );
			$.data( this, 'calendar', calendar );
		});
	};
	
	$.fn.calendar.options = {
		// string
		lang: "th",
		theme: 'month', 
		summary: true,

		$header: null,

		// date
		weekDayStart: 1,
		theDate: null,
		selectedDate: null,

		// resize
		resize: true,
		bordertop: 1,
		borderleft: 1,

		onComplete: function () {}
	};
	
})( jQuery, window, document );