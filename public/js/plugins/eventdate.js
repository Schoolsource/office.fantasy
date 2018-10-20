// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var EventDate = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $( elem );
			
			self.options = $.extend({}, $.fn.eventdate.options, options);

			self.setElem();
			self.config();

			// 
			self.setCalendarStart();
			self.setCalendarEnd();

			self.$checkAllday.change(function (e) {

				self.changeAllday();
			});
			self.changeAllday();
		},

		config: function () {
			var self = this;

			var today = new Date();
			self.startDate = new Date( self.options.startDate || today );
			self.endDate = new Date( self.options.endDate|| today );
			
			self.options.startDate  = new Date( self.options.startDate );
			self.options.endDate  = new Date( self.options.endDate );

			if( !self.options.allday ){
				self.$checkAllday.attr('checked', false);

				// start
				hours = self.options.startDate.getHours();
				hours = hours < 10 ? "0"+hours:hours;

				minute = self.options.startDate.getMinutes();
				minute = minute < 30 ? "00":"30";

				self.$table.find('.start.time select').val( hours+":"+minute );

				// end
				hours = self.options.endDate.getHours();
				hours = hours < 10 ? "0"+hours:hours;

				minute = self.options.endDate.getMinutes();
				minute = minute < 30 ? "00":'30';

				self.$table.find('.end.time select').val( hours+":"+minute );
				
			}
		},

		setElem: function () {
			var self = this;

			self.$startDate = $('<div>', {name: 'start-date-wrap'});
			self.$startTime = self.settime( self.options.name[2] || 'start_time');

			self.$endDate = $('<div>', {name: 'end-date-wrap'});
			self.$endTime = self.settime( self.options.name[3] || 'end_time');

			self.$checkAllday = $('<input>', {type: 'checkbox', name: self.options.name[4] || 'allday', checked: 'checked'});

			var tableStartDate = $('<table>').append( $('<tr>').append(
				  $('<td>').html( $('<span>', {class: 'mhs', text: 'เริ่ม: '}) )
				, $('<td>', {class: 'date-calendar'}).append( 
					  self.$startDate
					, $('<i>', {class: 'icon-calendar-o mrs'})
				)
			) );

			var tableEndDate = $('<table>').append( $('<tr>').append(
				  $('<td>').html( $('<span>', {class: 'mhs', text: 'สิ้นสุด: '}) )
				, $('<td>', {class: 'date-calendar'}).append( self.$endDate, $('<i>', {class: 'icon-calendar-o mrs'}) )
			) );

			self.$table = $('<table>', {class: 'when-date-table'}).append(
				  $('<tr>').append( 
				  	  $('<td>', {colspan: 3, class: 'start date'}).append( tableStartDate )
				)
				, $('<tr>').append( 
				  	 $('<td>', {colspan: 3, class: 'td-divider'})
				)

				// start time
				, $('<tr>').append( 
				  	  $('<td>', {class: 'start time'}).html( self.$startTime )
				  	, $('<td>', {class: 'td-divider'}) 
				  	, $('<td>', {class: 'start action'}).html( 
				  		$( '<label>', {class: 'checkbox'} ).append( 
				  			self.$checkAllday,
				  			$('<span>', {class: 'mls', text: 'ทั้งวัน'}) 
				  		)
				  	) 
				)
				, $('<tr>').append( 
				  	 $('<td>', {colspan: 3, class: 'pas'})
				)

				// end date
				, $('<tr>').append( 
				  	$('<td>', {colspan: 3, class: 'end date'}).append( tableEndDate )
				)
				, $('<tr>').append( 
				  	 $('<td>', {colspan: 3, class: 'td-divider'})
				)
				, $('<tr>').append( 
				  	$('<td>', { class: 'end time'}).html( self.$endTime )
				  	, $('<td>', {colspan: 2, class: 'start action'}) 
				)

			);

			self.$elem.html( self.$table );	
		},

		settime: function ( name, hour, minute ) {

			$select =  $('<select>', {name: name});
			for (var h = 0; h < 24; h++) {

				for (var i = 0; i < 2; i++) {

					hours = h;
					hours = hours < 10 ? "0"+hours:hours;

					_minute = i==1 ? "30" : "00";

					$select.append( $('<option>', {value: hours + ":" + _minute, text: hours + ":" + _minute}) );
				};
				
			};

			var $today = new Date();
			hour = !hour ? $today.getHours() : hour;
			hour = hour < 10 ? "0"+hour:hour;
			
			minute = !hour ? $today.getMinutes() : minute;
			minute =  minute < 30 ? "00":"30";

			$select.val( hour + ":" + minute );

			return $select;
		},

		setCalendarStart: function () {
			var self = this;

			$startDate = $('<input/>',{ type:"text", name: self.options.name[0] || "start_date" });
			self.$startDate.html( $startDate );

			$startDate.datepicker({
				style: 'normal',
				// format: 'range start',
				selectedDate: self.startDate,
				start: self.startDate,
				end: self.endDate,
				onChange: function( e ){

					var startDate = new Date( e );

					var distance = self.endDate.getTime() - self.startDate.getTime();
					var start_distance = startDate.getTime() - self.startDate.getTime();

					self.startDate = startDate;

					if( distance==0 ){
						self.endDate.setTime( self.startDate.getTime() );
					}
					else{
						self.endDate.setTime( self.endDate.getTime() + start_distance );
					}

					if( start_distance!=0 ){
						self.setCalendarEnd();
					}				
				}
			});
		},

		setCalendarEnd: function () {
			var self = this;

			$endDate = $('<input/>',{ type:"text", name: self.options.name[1] || "end_date" });
			self.$endDate.html( $endDate );

			$endDate.datepicker({
				style: 'normal',
				// format: 'range end',
				selectedDate: self.endDate,
				start: self.startDate,
				end: self.endDate,
				onChange: function( e ){

					// var theDate = new Date( e.date.selected );
					var theDate = new Date( e );
					var distance = theDate.getTime() - self.endDate.getTime();

					self.endDate = theDate;

					if( distance!=0 && self.endDate.getTime() < self.startDate.getTime() ){
						self.startDate.setTime( self.startDate.getTime() + distance );
						self.setCalendarStart();
					}

				}
			});
		},

		changeAllday: function () {
			var self = this;

			var checked = self.$checkAllday.is(':checked');
			self.$table.find('.time select').toggleClass('disabled', checked).prop('disabled', checked);
			
		},
	};

	$.fn.eventdate = function( options ) {
		return this.each(function() {
			var $this = Object.create( EventDate );
			$this.init( options, this );
			$.data( this, 'eventdate', $this );
		});
	};

	$.fn.eventdate.options = {
		startDate: new Date(),
		endDate: new Date(),
		allday: true,
		name: ['start_date', 'end_date', 'start_time', 'end_time', 'allday']
	};
	
})( jQuery, window, document );