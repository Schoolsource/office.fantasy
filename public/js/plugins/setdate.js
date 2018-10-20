// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var setDate = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $( elem );
			
			self.options = $.extend({}, $.fn.setdate.options, options);

			self.config();
			self.setElem();

			// 
			self.setCalendarStart();
			self.setCalendarEnd();

			if( self.options.allday != 'disabled' ){
				self.$checkAllday.change(function (e) {
					self.changeAllday();
				});
				self.changeAllday();
			}

			if( self.options.time != 'disabled' ){
				self.$checkEndTime.change(function (e) {
					self.changeEndTime();
				});
				self.changeEndTime();
			}
		},

		config: function () {
			var self = this;

			var today = new Date();
			self.startDate = new Date( self.options.startDate || today );
			self.endDate = new Date( self.options.endDate|| today );
		},

		setElem: function () {
			var self = this;

			self.$startDate = $('<div>', {name: 'start-date-wrap'});
			self.$startTime = self.setElemTime( self.options.name[2] || 'start_time', self.startDate );

			self.$endDate = $('<div>', {name: 'end-date-wrap'});
			self.$endTime = self.setElemTime( self.options.name[3] || 'end_time', self.endDate );


			self.$checkAllday = $('<input>', {type: 'checkbox', name: self.options.name[4] || 'allday'}).prop('checked', self.options.allday );
			self.$checkEndTime = $('<input>', {type: 'checkbox', name: self.options.name[5] || 'has_end'}).prop('checked', self.options.endtime );

			var tableStartDate = $('<table>').append( $('<tr>').append(
				  $('<td>').html( $('<span>', {class: 'mhs', text: self.options.str[0] }) )
				, $('<td>', {class: 'date-calendar'}).append( 
					  self.$startDate
					, $('<i>', {class: 'icon-calendar-o mrs'})
				)
			) );

			var tableEndDate = $('<table>').append( $('<tr>').append(
				  $('<td>').html( $('<span>', {class: 'mhs', text: self.options.str[1] }) )
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

					( self.options.time !== 'disabled' 
						? $('<td>', {class: 'start time'}).html( self.$startTime )
						: ""
					)
				  	
				  	, $('<td>', {class: 'td-divider'}) 
				  	, $('<td>', {class: 'start action'}).append( 

				  		( self.options.allday !== 'disabled' 
				  			? $( '<label>', {class: 'checkbox mlm'} ).append( 
					  			self.$checkAllday,
					  			$('<span>', {class: '', text: self.options.str[2]}) 
					  		) : ''
				  		)
				  		,
				  		( self.options.check_endtime 
				  			? $( '<label>', {class: 'checkbox mlm'} ).append( 
					  			self.$checkEndTime,
					  			$('<span>', {class: '', text: self.options.str[3]}) 
					  		) : ''
				  		)
				  	) 
				)

				, ( self.options.time !== 'disabled'  
					? $('<tr>', {class: 'tr-endDate'}).append(  $('<td>', {colspan: 3, class: 'pas'}) )
					: ''
				)

				// end date
				, $('<tr>', {class: 'tr-endDate'}).append( 
				  	$('<td>', {colspan: 3, class: 'end date'}).append( tableEndDate )
				)

				, ( self.options.time !== 'disabled' 
					? $('<tr>', {class: 'tr-endDate'}).append( $('<td>', {colspan: 3, class: 'td-divider'}) )
					: ''
				)

				, ( self.options.time !== 'disabled' 
					? 	$('<tr>', {class: 'tr-endDate'}).append( 
					  		  $('<td>', { class: 'end time'}).html( self.$endTime )
					  		, $('<td>', {colspan: 2, class: 'start action'}) 
						)
					: ''
				)

			);

			self.$elem.html( self.$table );	

			if( self.options.allday != 'disabled' ){
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

		setElemTime: function ( name, date ) {

			$hours =  $('<select>', {name: name + '_hour'});
			for (var h = 0; h < 24; h++) {

				hours = h;
				hours = hours < 10 ? "0"+hours:hours;
				$hours.append( $('<option>', {value: hours, text: hours}) );
				
			};

			$minute = $('<select>', {name: name + '_minute'});
			for (var i = 0; i < 60; i++) {
				
				if( i%5==0 ){
					minute = i < 10 ? "0"+i:i;
					$minute.append( $('<option>', {value: minute, text: minute}) );
				}	
			}

			if( !date ){
				date  = new Date();
			}

			hour = date.getHours();
			hour = hour < 10 ? "0"+hour:hour;
			$hours.val( hour );

			minute = date.getMinutes();
			minute = parseInt( minute / 5 ) * 5;
			minute = minute < 10 ? "0"+minute:minute;
			$minute.val( minute );

			$select = $('<div>').append( $hours, $minute );

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
				lang: self.options.lang,
				onChange: function( date ){

					var startDate = new Date( date );

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
				lang: self.options.lang,
				onChange: function( date ){

					var theDate = new Date( date );
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

		changeEndTime: function () {
			var self = this;

			var checked = self.$checkEndTime.is(':checked');
			self.$table.find('.tr-endDate').toggleClass('hidden_elem', !checked);	
		}
	};

	$.fn.setdate = function( options ) {
		return this.each(function() {
			var $this = Object.create( setDate );
			$this.init( options, this );
			$.data( this, 'setdate', $this );
		});
	};

	$.fn.setdate.options = {
		startDate: new Date(),
		endDate: new Date(),
		allday: true,
		endtime: true,
		name: ['start_date', 'end_date', 'start_time', 'end_time', 'allday', 'has_end'],
		lang: 'en',
		str: ['Start', 'End', 'All day', 'End Time']
	};
	
})( jQuery, window, document );