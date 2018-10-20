// Utility
if (typeof Object.create !== 'function') {
    Object.create = function(obj) {
        function F() {};
        F.prototype = obj;
        return new F();
    };
}

(function($, window, document, undefined) {

    var TextSlide = {
        init: function(options, elem) {
            var self = this;

            var timer, $elem = $(elem);
            var ul = $elem.find('ul');
            var currentLeft = 0;
            var fullWidth = $elem.parent().width();
            var current = 0;

            $elem.css({
                width: fullWidth,
                overflow: 'hidden'
            })

            ul.css({
                overflow: 'hidden',
                position: 'relative',
                // transition: "left .2s",
                width: $elem.find('ul').width(),
                left: 0
            });

            var currentWidth = 0;
            $.each(ul.find('li'), function() {

                currentWidth += $(this).width() + 1;
            });

            slidesleft();

            function timmer() {

                timer = setTimeout(function() {

                    slidesleft();
                }, 50);
            }

            function slidesleft() {

                if (currentWidth <= fullWidth) return false;

                currentLeft++;
                ul.css({
                    width: currentWidth,
                    left: currentLeft * -1
                });

                if ((currentLeft + fullWidth) === currentWidth) {

                    var li = ul.find('li');

                    var $item = $(li[current]);
                    var item = $item.clone();

                    current++;
                    currentWidth += $item.width();
                    ul.append(item);
                }

                timmer();
            }

            $elem.mouseenter(function() {
                clearTimeout(timer);
            }).mouseleave(function() {
                slidesleft();
            });
        }
    }
    $.fn.textslide = function(options) {
        return this.each(function() {
            var $this = Object.create(TextSlide);
            $this.init(options, this);
            $.data(this, 'textslide', $this);
        });
    };
    // $.fn.textslide.options = {};

    /**/
    /* autosize */
    /**/
    var observe;
    if (window.attachEvent) {
        observe = function(element, event, handler) {
            element.attachEvent('on' + event, handler);
        };
    } else {
        observe = function(element, event, handler) {
            element.addEventListener(event, handler, false);
        };
    }
    var Autosize = {
            init: function(options, text) {
                var self = this;

                $(text).attr('rows', 1).addClass('uiTextareaAutogrow');

                function resize() {
                    text.style.height = 'auto';
                    text.style.height = text.scrollHeight + 'px';
                }

                /* 0-timeout to get the already changed text */
                function delayedResize() {
                    window.setTimeout(resize, 0);
                }

                observe(text, 'change', resize);
                observe(text, 'cut', delayedResize);
                observe(text, 'paste', delayedResize);
                observe(text, 'drop', delayedResize);
                observe(text, 'keydown', delayedResize);

                resize();
            }
        }
        /*$.fn.autosize = function( options ) {
		return this.each(function() {
			var $this = Object.create( Autosize );
			$this.init( options, this );
			$.data( this, 'autosize', $this );
			
		});
	};
	$.fn.autosize.options = {};*/

    /**/
    /* Stars Ratable */
    /**/
    var StarsRatable = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);
            self.options = $.extend({}, $.fn.starsRatable.options, options);

            self.rating = parseInt(self.$elem.val()) || 0;

            self.$box = $('<div/>', {
                class: 'uiStarsRatable'
            });

            self.$elem.before(self.$box);
            $.each(self.options.level, function(i, obj) {
                var $a = $('<a/>', {
                    class: 'icon-star',
                    rating: obj.rating
                });

                if (self.options.showlabel) {
                    Event.setPlugin($a, 'tooltip', {
                        text: obj.text,
                        reload: 0,
                        overflow: {
                            Y: "Above",
                            X: "Left"
                        }
                    });
                }
                self.$box.append($a);
            });

            self.$box.find('a').mouseenter(function() {
                var $item = $(this);
                var rating = $item.attr('rating');

                for (var i = rating; i > 0; i--) {
                    self.$box.find('[rating=' + i + ']').addClass('has-hover');
                };

            }).mouseleave(function() {
                self.$box.find('.has-hover').removeClass('has-hover');

            }).click(function() {
                var $item = $(this);
                self.rating = $item.attr('rating');
                self.active();
            });

            self.active();
        },

        active: function(rating) {
            var self = this;

            self.$box.find('.has-active').removeClass('has-active');
            self.$elem.val(self.rating);
            for (var i = self.rating; i > 0; i--) {
                self.$box.find('[rating=' + i + ']').addClass('has-active');
            };
        }
    };
    $.fn.starsRatable = function(options) {
        return this.each(function() {
            var $this = Object.create(StarsRatable);
            $this.init(options, this);
            $.data(this, 'starsRatable', $this);
        });
    };
    $.fn.starsRatable.options = {
        rating: 0,
        level: {
            1: {
                rating: 1,
                text: 'à¹à¸¢à¹ˆ'
            },
            2: {
                rating: 2,
                text: 'à¸žà¸­à¹ƒà¸Šà¹‰'
            },
            3: {
                rating: 3,
                text: 'à¸”à¸µ'
            },
            4: {
                rating: 4,
                text: 'à¸”à¸µà¸¡à¸²à¸'
            },
            5: {
                rating: 5,
                text: 'à¸¢à¸­à¸”à¹€à¸¢à¸µà¹ˆà¸¢à¸¡'
            }
        },
        showlabel: false
    };

    /**/
    /* phone */
    /**/
    var PhoneNumber = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            var text = self.$elem.val();
            var val = '';
            /*for (var i = 0; i < text.length; i++) {
				
				if( $.inArray(i, [3,6])>=0 ){
					val += "-";
				}
				val += text[i];
			};*/

            self.$elem.attr('maxlength', 12).keydown(function(e) {
                var value = $(this).val();

                console.log(e.keyCode, value.length, $.inArray(value.length, [3, 6]));
                if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) {
                    if ($.inArray(value.length, [3, 7]) >= 0) {
                        $(this).val(value + "-")
                    }
                } else if (e.keyCode == 8) {
                    if ($.inArray(value.length, [5, 9]) >= 0) {
                        $(this).val(value.substr(0, value.length - 1));
                    }
                } else if (e.keyCode == 189 && $.inArray(value.length, [3, 7]) >= 0) {

                } else {
                    e.preventDefault();
                }
            });


        }
    }

    $.fn.phone_number = function(options) {
        return this.each(function() {
            var $this = Object.create(PhoneNumber);
            $this.init(options, this);
            $.data(this, 'starsRatable', $this);
        });
    };

    /**/
    /* Selectbox */
    /**/
    var Selectbox = {
        init: function(options, elem) {

            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.selectbox.options, options);

            // setting
            self.setSlecte();
            self.setElem();

            self.getSlected();

            if (self.options.setitem) {}

            self.active = false;
            self.setMenu();

            if (typeof self.options.onComplete === 'function') {
                self.options.onComplete.apply(self, arguments);
            }

            // Event
            self.initEvent();
        },

        initEvent: function() {
            var self = this;

            self.$btn.not('.disabled').click(function(e) {

                $('body').find('.uiPopover').find('a.btn-toggle.active').removeClass('active');
                if (self.menu.hasClass('open')) {
                    self.close();
                } else {
                    self.$btn.addClass('active');
                    self.display();
                    self.open();
                }

                e.stopPropagation();
            });

            $('a', self.menu).click(function() {
                self.change($(this).parent().index());
            });

            $('html').on('click', function() {

                if (self.active && self.menu.hasClass('open')) {
                    self.$btn.removeClass('active');
                    self.close();
                }

            });
        },

        setElem: function() {
            var self = this;

            self.selectedInput = $('<input>', {
                class: 'hiddenInput',
                type: 'hidden',
                name: self.$elem.attr('name')
            });
            self.selectedText = $('<span>', {
                class: 'btn-text'
            });

            self.original = self.$elem;

            var placeholder = $('<div/>', {
                class: 'uiPopover'
            });

            self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$btn = $('<a>', {
                class: 'btn btn-box btn-toggle'
            }).append(self.selectedText);

            if (!self.options.display) {
                self.$btn.addClass('disabled');
            }

            if (!self.options.icon) {
                self.$btn.append($('<i/>', {
                    class: 'img mls icon-angle-down'
                }));
            }

            self.$elem.append(self.$btn, self.selectedInput);
        },
        setSlecte: function() {
            var self = this;

            self.select = [];
            self.$elem.find('optgroup,option').each(function(i, obj) {

                var $item = $(this),
                    type = "";

                if ($item.context.nodeName.toLowerCase() == 'option') {
                    type = 'default';
                } else if ($item.context.nodeName.toLowerCase() == 'optgroup') {
                    type = 'header';
                }

                if ($item.attr('type')) {
                    type = $item.attr('type');
                }

                if (type) {
                    var data = {
                        type: type,
                        text: $.trim($item.text()),
                        value: $.trim($item.val()),
                        href: ($item.attr('href')) ? $item.attr('href') : '',
                        selected: $item.is(':selected'),
                        image: $.trim($item.attr('image-url')),
                        label: ($item.attr('label')) ? $item.attr('label') : '',
                        icon: ($item.attr('icon')) ? $item.attr('icon') : '',
                        // loadUrl: ( $item.attr('ajaxify') ) ? $item.attr('ajaxify') : '',
                    };

                    if ($item.is(':selected')) {
                        self.selected = data;
                    }

                    self.select.push(data);
                }

            });
        },

        change: function(length) {
            var self = this;
            var $item = self.menu.find('li').eq(length);

            // if( $item.hasClass('selected') ) return false;
            $item.parent().find('.selected').removeClass('selected');
            $item.addClass('selected');

            self.setSlected(length);
            self.getSlected();
        },
        setSlected: function(index) {
            var self = this;
            self.selected = self.select[index];
        },
        getSlected: function() {
            var self = this;

            if (typeof self.options.onSelected === 'function') {
                self.options.onSelected.apply(self, arguments);
            }
            self.selectedText.text(self.selected.text);
            self.selectedInput.val(self.selected.value);
        },

        open: function() {
            var self = this;
            self.active = true;

            self.getOffset();
            self.menu.addClass('open');
        },

        close: function() {
            var self = this;

            self.active = false;
            self.menu.removeClass('open'); // .remove();
        },

        display: function() {
            var self = this;

            $('body').append(self.menu);

            if ($('body').find('.open.uiContextualPositioner').length > 0) {
                $('body').find('.open.uiContextualPositioner').removeClass('open');
            }

            if ($('body').find('.openToggler.uiToggle').length > 0) {
                $('body').find('.openToggler.uiToggle').removeClass('openToggler');
            }
        },

        setMenu: function() {
            var self = this;

            var ul = $('<ul>', {
                class: 'uiMenu'
            });

            /*var $input = 
			ul.append( $('<li class="add"><table><tbody><tr><td><input class="inputtext" type="text"></td><td><button type="text" class="btn">à¹€à¸žà¸´à¹ˆà¸¡</button></td></tr></tbody></table></li>') );*/


            $.each(self.select, function(i, data) {
                ul.append(self._item[data.type || 'default'](data));
            });

            var $boxInput = '';

            if (self.options.add_item_url) {
                var $input = $('<input>', {
                    class: 'inputtext',
                    autocomplete: "off",
                    placeholder: 'à¹€à¸žà¸´à¹ˆà¸¡à¹ƒà¸«à¸¡à¹ˆ...'
                });
                var $btn = $('<a />', {
                    class: 'btn rfloat',
                    'text': 'à¸šà¸±à¸™à¸—à¸¶à¸'
                });
                var $boxInput = $('<div>', {
                    class: 'box-input'
                }).append($input, $btn);
                $input.click(function(e) {
                    e.stopPropagation();
                }).keydown(function(e) {

                    if (e.keyCode == 13) {
                        self.addItem($input);
                        e.preventDefault();
                    }

                });
                $btn.click(function(e) {
                    e.stopPropagation();
                    self.addItem($input);
                });
            }

            self.menu = $('<div>', {
                    class: 'uiContextualPositioner'
                })
                .addClass()
                .append(
                    $('<div>', {
                        class: 'toggleFlyout selectBoxFlyout'
                    }).append($boxInput, ul)
                );

            if (self.options.max_width) {
                self.menu.find('.toggleFlyout').css('width', self.options.max_width);
            }
        },

        addItem: function($input) {
            var self = this;

            var formData = new FormData();
            formData.append(self.options.add_item_name, $input.val());
            formData.append('get_insert_select', true);

            $input.attr('disabled', true);

            $.ajax({
                type: "POST",
                url: self.options.add_item_url,
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
            }).done(function(response) {

                self.selected = response.select;
                var select = [];
                select.push(self.selected);

                $.each(self.select, function(i, obj) {
                    select.push(obj);
                });

                self.select = select;

                self.close();
                self.setMenu();
                self.getSlected();

                self.menu.find('.selected').removeClass('selected');
                self.menu.find('li').first().addClass('selected');

                // console.log( self.select );
            }).fail(function() {

            }).always(function() {
                $input.removeClass('disabled');
            });
        },

        _item: {
            default: function(data) {

                var li = $('<li/>');
                var a = $('<a/>', {
                    class: 'itemAnchor'
                });
                var label = $('<span/>', {
                    class: 'itemLabel',
                    text: data.text
                });

                li.addClass(data.selected ? 'selected' : '').append(a);

                if (data.icon) {
                    li.addClass('has-icon');
                    a.append($('<i/>', {
                        class: 'mrs img icon-' + data.icon
                    }));
                }

                a.append(label);

                if (data.label) {

                    if (data.icon) {
                        label.addClass('fwb');
                    }

                    li.addClass('has-des');
                    a.append($('<div/>', {
                        class: 'itemDes'
                    }).html(data.label));
                }

                return li;
            },

            header: function(data) {
                return $('<li/>', {
                    class: 'header'
                }).html($('<span/>', {
                    class: 'itemLabel'
                }).html(data.label));
            },

            separator: function() {
                return $('<li/>', {
                    class: 'separator'
                });
            },

            user: function(data) {
                return $('<li/>', {
                        class: 'user'
                    })
                    .addClass(data.selected ? 'selected' : '')
                    .html($('<a/>')
                        .addClass('anchor anchor32')
                        .html($('<div/>').addClass('clearfix')
                            .append($('<div/>').addClass('avatar lfloat size32 mrs')
                                .html($('<img/>', {
                                    class: 'img',
                                    src: data.image
                                }))
                            )

                            .append($('<div/>').addClass('content')
                                .append(
                                    $('<div/>', {
                                        class: 'spacer'
                                    }),
                                    $('<div/>', {
                                        class: 'massages clearfix',
                                        text: data.text
                                    })
                                )
                            )
                        )
                    );
            }
        },

        getOffset: function() {
            var self = this;

            if (self.menu.hasClass('uiContextualAbove')) {
                self.menu.removeClass('uiContextualAbove');
            }

            var cssMenu = {
                height: "",
                overflowY: '',
                overflowX: ''
            };

            self.menu.find('.uiMenu').css(cssMenu);

            var outer = $(document).height() < $(window).height() ? $(window) : $(document);

            var offset = self.$elem.offset(),
                position = self.$elem.offset(),
                outerWidth = $(window).width(),
                outerHeight = outer.height();

            position.top += self.$elem.outerHeight();

            var innerWidth = position.left + self.menu.outerWidth();
            if ($('html').hasClass('sidebarMode')) {
                innerWidth += 301;
            }

            if (innerWidth > outerWidth) {
                position.left = position.left - self.menu.outerWidth() + self.$elem.outerWidth();
            }

            var innerHeight = position.top + self.menu.outerHeight();
            if (innerHeight > outerHeight) {


                position.top = position.top - self.menu.outerHeight() - self.$elem.outerHeight();

                if (position.top < 0) {

                    var h = outerHeight - offset.top + self.$elem.outerHeight();
                    // 
                    if (h > offset.top) {
                        position.top = offset.top;
                        position.top += self.$elem.outerHeight();
                        cssMenu.height = outerHeight - position.top - 15;
                    } else {
                        position.top = position.top * -1

                        position.top = $('html').hasClass('hasModal') ? 15 : 65;

                        cssMenu.height = (offset.top - 7) - position.top;

                        self.menu.addClass('uiContextualAbove');
                    }

                    cssMenu.overflowY = 'auto';
                    cssMenu.overflowX = 'hidden';
                    self.menu.find('.uiMenu').css(cssMenu);

                } else {
                    position.top += 2;
                    self.menu.addClass('uiContextualAbove');
                }
            }

            self.menu.css(position);
        }
    };
    $.fn.selectbox = function(options) {
        return this.each(function() {
            var toggle = Object.create(Selectbox);
            toggle.init(options, this);
            $.data(this, 'selectbox', toggle);

        });
    };
    $.fn.selectbox.options = {
        display: true,
        onSelected: function() {},
        onComplete: function() {},
    };


    var Selectbox2 = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);
            self.settings = $.extend({}, $.fn.selectbox2.settings, options);

            // set Data
            self.searchData = [];
            self.searchCurrent = "";
            self.is_loading = false;
            self.is_show = false;
            self.is_focus = false;
            self.is_time = null;
            self.is_keycodes = [37, 38, 39, 40, 13];

            // self.url = self.options

            if (typeof self.settings.onComplete === 'function') {
                self.settings.onComplete.apply(self, arguments);
            }

            // set Elem 
            self.$input = $('<input>', {
                class: 'inputtext select-box-input',
                placeholder: "",
                autocomplete: 'off'
            });
            self.$preview = $('<ul>');

            self.$elem.addClass('select-box').append(
                $('<div>', {
                    class: 'select-box-preview'
                }).html(self.$preview),
                self.$input,
                $('<div>', {
                    class: 'select-box-loader loader-spin-wrap'
                }).html($('<div>', {
                    class: 'loader-spin'
                })));

            self.setMenu();
            self.hide();
            self.is_active = 0;

            $.each(self.settings.options, function(i, obj) {

                if (obj.checked) {
                    self.connect(obj);
                }

            });
            self.active();

            self.events();
        },

        setMenu: function() {
            var self = this;

            var $box = $('<div/>', {
                class: 'uiTypeaheadView selectbox-selectview'
            });
            self.$menu = $('<ul/>', {
                class: 'search has-loading',
                role: "listbox"
            });

            $box.html($('<div/>', {
                class: 'bucketed'
            }).append(self.$menu));
            // bucketed

            var settings = self.$input.offset();
            settings.top += self.$input.outerHeight();

            uiLayer.get(settings, $box);
            self.$layer = self.$menu.parents('.uiLayer');

            // event
            self.$menu.mouseenter(function() {
                self.is_focus = true;
            }).mouseleave(function() {
                self.is_focus = false;
            });

            self.resizeMenu();
            $(window).resize(function() {
                self.resizeMenu();
            });
        },
        resizeMenu: function() {
            var self = this;

            self.$menu.width(self.$input.outerWidth() - 2);
            var settings = self.$input.offset();
            settings.top += self.$input.outerHeight();
            settings.top -= 1;
            // settings.left += 3;

            self.$menu.css({
                overflowY: 'auto',
                overflowX: 'hidden',
                maxHeight: $(window).height() - settings.top
            });

            self.$menu.parents('.uiContextualLayerPositioner').css(settings);
        },
        show: function() {
            var self = this;

            self.resizeMenu();
            self.$layer.removeClass('hidden_elem');
        },
        hide: function() {
            this.$layer.addClass('hidden_elem');
        },
        addOp: function(data) {
            var self = this;

        },
        setItemMenu: function(data) {

            var li = $('<li/>').html(
                $('<a>', {
                    class: 'clearfix'
                }).append(
                    $('<span>', {
                        class: 'text',
                        text: data.text
                    })
                )
            );

            if (data.image_url) {
                li.addClass('picThumb');
                /*$('<div>', {class: 'avatar lfloat mrs'}).html( $('<img>', {src: URL + 'public/images/avatar/error/user2.png'}) )*/
            }

            if (data.activity == 'new') {
                li.addClass('new').find('.text').before(
                    $('<div>', {
                        class: 'box-icon'
                    }).append($('<i>', {
                        class: 'icon-plus'
                    }))
                );
            }

            li.data(data);

            return li;
        },
        events: function() {
            var self = this;

            self.$input.keyup(function(e) {

                if (self.is_keycodes.indexOf(e.which) == -1) {
                    self.search();
                }

            }).keydown(function(e) {

                var keyCode = e.which;

                if (keyCode == 40 || keyCode == 38) {

                    self.changeUpDown(keyCode == 40 ? 'donw' : 'up');
                    e.preventDefault();
                }

                if (keyCode == 13) {

                    self.connect();
                    e.preventDefault();
                    e.stopPropagation();
                }

            }).focus(function() {

            }).blur(function() {

                /*if( !self.focus ){
					self.hide();
					self.focus = false;
				}*/

            }).click(function(e) {
                self.search();
                e.stopPropagation();
            });

            self.$menu.delegate('li', 'mouseenter', function() {

                self.is_active = $(this).index();
                $(this).addClass('selected').siblings().removeClass('selected');

            });

            self.$layer.mouseenter(function() {
                self.focus = true;
            }).mouseleave(function() {
                self.focus = false;
            });

            self.$menu.delegate('li', 'click', function(e) {

                self.connect();
                e.preventDefault();
            });

            self.$preview.delegate('.js-remove-preview', 'click', function(e) {
                var parent = $(this).parent();

                e.stopPropagation();
                var data = parent.data();

                self.updateOp(data, false);
                parent.remove();

                if (self.$preview.find('li').length == 0) {

                    if (self.$elem.hasClass('has-preview')) {
                        self.$elem.removeClass('has-preview');
                    }
                    //  not-multiple

                    if (self.$elem.hasClass('not-multiple')) {
                        self.$elem.removeClass('not-multiple');

                        self.$input.focus();
                        self.search();
                    }
                }

                if (self.settings.multiple) {
                    self.$input.focus();
                    self.search();
                }

            });

            self.$preview.delegate('li', 'click', function(e) {

                if (!self.settings.multiple) {
                    self.search();
                    e.stopPropagation();
                }

            });


            $('html').on('click', function() {

                if (self.open) {
                    self.hide();
                    self.open = false;
                }

                // !self.focus && 

            });

        },
        search: function() {
            var self = this;

            var value = $.trim(self.$input.val());
            var options = [];
            self.open = true;

            if (value == '') {
                options = self.settings.options;
            } else {

                $.each(self.settings.options, function(i, obj) {

                    var text = obj.text || obj.name || '';
                    text = text.toString();
                    if (text.search(value) >= 0) {
                        options.push(obj);
                    } else if (text.toUpperCase().search(value) >= 0) {
                        options.push(obj);
                    } else if (text.toLowerCase().search(value) >= 0) {
                        options.push(obj);
                    }

                });
            }

            if (options.length == 0 && self.settings.insert_url) { //
                self.$menu.empty();
                options.push({
                    text: value,
                    activity: 'new'
                });
            } else {
                self.$menu.empty();
            }

            var c = 0;
            $.each(options, function(i, obj) {

                if (!obj.checked) {
                    self.$menu.append(self.setItemMenu(obj));
                    c++;
                }
            });


            if (c == 0) {
                self.hide();
            } else {
                self.is_active = 0;
                self.active();
                self.show();

            }
        },
        active: function() {
            var self = this;

            self.$menu.find('li').eq(self.is_active).addClass('selected').siblings().removeClass('selected');
        },

        changeUpDown: function(active) {
            var self = this;
            if (!self.$menu) return false;

            var length = self.$menu.find('li').length;
            var index = self.$menu.find('li.selected').index();

            if (active == 'up') index--;
            else index++;

            if (index < 0) index = 0;
            if (index >= length) index = length - 1;

            self.is_active = index;
            self.active();
        },

        connect: function(data) {
            var self = this;

            if (!data) {
                var li = self.$menu.find('li').eq(self.is_active);
                var data = li.data();
            }

            if (data.activity == 'new') {
                self.insertItem(data.text);
                return false;
            }

            self.updateOp(data, true);

            if (!self.settings.multiple) {

                $.each(self.$preview.find('li'), function(i, obj) {

                    self.updateOp($(obj).data(), false);
                });

                self.$preview.empty();
            }
            self.$preview.append(self.setItemPreview(data));
            self.$input.val('');

            self.hide();

            self.$elem.addClass('has-preview'); //  not-multiple

            if (!self.settings.multiple) {
                self.$elem.addClass('not-multiple');
            } else {
                /*self.$input.focus();
				self.search();*/
            }
        },
        updateOp: function(data, checked) {
            var self = this;

            $.each(self.settings.options, function(i, obj) {

                var text = data.text || data.name;
                var value = data.value || data.id;

                var _text = obj.text || obj.name;
                var _value = obj.value || obj.id;

                if (text == _text && value == _value) {
                    self.settings.options[i].checked = checked;
                }

            });
        },
        setItemPreview: function(data) {
            var self = this;
            var li = $('<li>').append(
                $('<span>', {
                    class: 'text',
                    text: data.text || data.name
                }), $('<input>', {
                    type: 'hidden',
                    class: 'hiddenInput',
                    value: data.value || data.id,
                    name: self.settings.name,
                    autocomplete: "off"
                }), $('<button>', {
                    type: 'button',
                    class: 'remove-preview js-remove-preview',
                    title: 'Remove'
                }).html($('<i>', {
                    class: 'icon-remove'
                }))
            );

            li.data(data);

            return li;
        },
        insertItem: function(text) {
            var self = this;

            Dialog.load(self.settings.insert_url, {
                callback: 'selectbox',
                text: self.$input.val()
            }, {
                onSubmit: function(d) {

                    var $form = d.$pop.find('form');
                    Event.inlineSubmit($form).done(function(result) {

                        result.url = "";
                        result.message = "";
                        Event.processForm($form, result);

                        var data = {
                            text: result.text,
                            value: result.value
                        };

                        self.settings.options.push(data);
                        self.connect(data);

                    });
                },
                onClose: function() {

                    self.$input.focus();
                    self.search();
                },
                onOpen: function() {

                    self.$input.val('');
                    self.hide();

                    $(window).keydown(function(e) {

                        if (e.keyCode == 27) {
                            Dialog.close();
                        }

                    });
                },
            });
        }
    };
    $.fn.selectbox2 = function(options) {
        return this.each(function() {
            var toggle = Object.create(Selectbox2);
            toggle.init(options, this);
            $.data(this, 'selectbox', toggle);

        });
    };
    $.fn.selectbox2.settings = {
        name: '',
        options: [],
        onComplete: function() {},
        insert_url: false,
        multiple: false
    };

    /**/
    /* Datepicker */
    /**/
    var Datepicker = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.datepicker.options, options);

            self.options.lang = {
                lang: self.options.lang,
                type: "short"
            }

            if (self.$elem.val()) {
                self.options.selectedDate = new Date(self.$elem.val());
            }

            if (!self.options.selectedDate) {
                self.options.selectedDate = new Date();
            }
            self.options.selectedDate.setHours(0, 0, 0, 0);

            // set date
            self.date = {
                today: new Date(),
                theDate: new Date(self.options.selectedDate),
                selected: self.options.selectedDate,
                startDate: self.options.startDate
            };
            self.date.today.setHours(0, 0, 0, 0);

            if (self.date.startDate) {

                /*
				if( self.date.startDate.getTime()>self.date.selected.getTime() ){
					self.date.startDate = new Date( self.date.selected );
				}*/
                self.date.startDate.setHours(0, 0, 0, 0);
            }

            var lang = Object.create(Datelang);
            lang.init(self.options.lang);
            self.string = lang;

            self.setElem();

            self.getSlected();

            self.active = false;
            self.elemCalendar();
            self.initEvent();
        },

        gYear: function(date) {

            var self = this,
                year = date.getFullYear();


            if (self.options.lang.lang == 'th') {
                year = year + 543;
            }

            if (self.options.style == 'short') {
                year = year.toString().substr(2, 4);
            }

            return year;
        },

        getSlected: function() {
            var self = this;
            // this.date.selected
            //  'normal'
            // 'normal'

            var textDate =
                self.string.day(self.date.selected.getDay(), self.options.style) +
                (self.options.style == 'normal' ? "à¸—à¸µà¹ˆ " : ' ') +
                self.date.selected.getDate() + " " +
                self.string.month(self.date.selected.getMonth(), self.options.style) + " " +
                self.gYear(self.date.selected);

            var date_str = self.date.selected.getDate();
            date_str = date_str < 10 ? "0" + date_str : date_str;

            var month_str = self.date.selected.getMonth() + 1;
            month_str = month_str < 10 ? "0" + month_str : month_str;

            var valDate = self.date.selected.getFullYear() + "-" +
                month_str + "-" +
                date_str;

            self.selectedText.text(textDate);
            self.selectedInput.val(valDate);

            if (typeof self.options.onSelected === 'function') {
                self.options.onSelected.apply(self.elem, arguments);
            }
        },

        setElem: function() {
            var self = this;

            self.selectedInput = $('<input>', {
                class: 'hiddenInput',
                type: 'hidden',
                name: self.$elem.attr('name')
            });

            self.selectedInput.addClass(self.$elem.attr('class'));
            self.selectedText = $('<span>', {
                class: 'btn-text'
            });

            self.original = self.$elem;

            var placeholder = $('<div/>', {
                class: 'uiPopover'
            });

            self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$btn = $('<a>', {
                class: 'btn btn-box btn-toggle'
            }).append(self.selectedText);

            if (!self.options.icon) {
                self.$btn.append($('<i/>', {
                    class: 'img mls icon-angle-down'
                }));
            }

            self.$elem.append(self.$btn, self.selectedInput);
            self.calendar = {};
        },

        initEvent: function() {
            var self = this;

            self.$btn.click(function(e) {

                $('body').find('.uiPopover').find('a.btn-toggle.active').removeClass('active');
                if (self.$calendar.hasClass('open')) {
                    self.close();
                } else {
                    self.$btn.addClass('active');
                    self.display();
                    self.open();
                }

                e.stopPropagation();

            });

            $('html').on('click', function() {

                if (self.active && self.$calendar.hasClass('open')) {
                    self.$btn.removeClass('active');
                    self.close();
                }

            });

            self.$calendar.bind('mousewheel', function(e) {

                if (self.is_loading) return false;

                var offset = e.originalEvent.wheelDelta / 120 > 0 ? -1 : 1;
                var newDate = new Date(self.date.theDate);
                newDate.setMonth(self.date.theDate.getMonth() + offset);
                self.date.theDate = newDate;

                self.updateCalendar();
                e.stopPropagation();

                /*if(e.originalEvent.wheelDelta /120 > 0) {
					console.log('scrolling up !');
				}
				else{
					console.log('scrolling down !');
				}*/
            });
        },

        display: function() {
            var self = this;

            self.updateCalendar();

            $('body').append(self.$calendar);

            if ($('body').find('.open.uiContextualPositioner').length > 0) {
                $('body').find('.open.uiContextualPositioner').removeClass('open');
            }

            if ($('body').find('.openToggler.uiToggle').length > 0) {
                $('body').find('.openToggler.uiToggle').removeClass('openToggler');
            }
        },

        open: function() {
            var self = this;
            self.active = true;

            self.getOffset();
            self.$calendar.addClass('open');
        },

        close: function() {
            var self = this;

            self.active = false;
            self.$calendar.removeClass('open');
        },

        setCalendar: function() {

            var self = this;
            self.is_loading = true;
            var theDate = new Date(self.date.theDate);

            var firstDate = new Date(theDate.getFullYear(), theDate.getMonth(), 1);
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

            prevweekDay = prevweekDay > prevDateLastDay ? 7 - prevweekDay : prevDateLastDay - prevweekDay;

            self.calendar.lists = [];
            for (var y = 0, i = 0; y < 7; y++) {

                var row = [];
                var weekInMonth = false;

                for (var x = 0; x < 7; x++, i++) {
                    var p = ((prevDateLastDate - prevweekDay) + i);

                    var call = {};
                    var n = p - prevDateLastDate;
                    call.date = new Date(theDate);
                    call.date.setHours(0, 0, 0, 0);
                    call.date.setDate(n);

                    // If value is outside of bounds its likely previous and next months
                    if (n >= 1 && n <= lastDay) {
                        weekInMonth = true;

                        if (self.date.today.getTime() == call.date.getTime()) {
                            call.today = true;
                        }

                        if (self.date.selected.getTime() == call.date.getTime()) {
                            call.selected = true;
                        }
                    } else {
                        call.noday = true;
                    }

                    if (self.date.startDate) {
                        if (self.date.startDate.getTime() > call.date.getTime()) {
                            call.empty = true;
                        }
                    }

                    row.push(call);
                }

                if (row.length > 0 && weekInMonth) {
                    self.calendar.lists.push(row);
                }
            }

            self.calendar.header = [];
            for (var x = 0, i = self.options.weekDayStart; x < 7; x++, i++) {
                if (i == 7) i = 0;
                self.calendar.header.push({
                    key: i,
                    text: self.string.day(i)
                });
            };
        },

        elemCalendar: function() {
            var self = this;

            self.$calendar = $('<div>', {
                    class: 'uiContextualPositioner'
                })
                .append($('<div>', {
                    class: 'toggleFlyout calendarGridTableSmall'
                }));

            if (self.options.max_width) {
                self.menu.find('.toggleFlyout').css('width', self.options.max_width);
            }
        },

        updateCalendar: function() {
            var self = this;

            self.setCalendar();

            var year = self.date.theDate.getFullYear();
            if (self.options.lang.lang == 'th') {
                year = year + 543;
            }

            var $title = $('<thead>').html($("<tr>", {
                    class: 'title'
                })
                .append($('<td>', {
                    class: 'prev'
                }).append($('<i/>', {
                    class: 'icon-angle-left'
                })))
                .append($('<td>', {
                    class: 'title',
                    colspan: 5,
                    text: self.string.month(self.date.theDate.getMonth(), 'normal') + " " + year
                }))
                .append($('<td>', {
                    class: 'next'
                }).append($('<i/>', {
                    class: 'icon-angle-right'
                })))
            )

            var $header = $("<tr>", {
                class: 'header'
            });
            $.each(self.calendar.header, function(i, obj) {
                $header.append($('<th>', {
                    text: obj.text
                }));
            });
            $thead = $('<thead/>').html($header);

            var $tbody = $('<tbody>');
            $.each(self.calendar.lists, function(i, row) {
                $tr = $('<tr>');
                $.each(row, function(j, call) {

                    call.cls = "";
                    // call.date/
                    var datestr = call.date.getFullYear() + "-" + (call.date.getMonth() + 1) + "-" + call.date.getDate();

                    if (self.options.start) {

                        if (self.options.start.getTime() == call.date.getTime()) {
                            call.cls += ' select-start';
                        }

                        if (self.options.start.getTime() > call.date.getTime()) {
                            call.overtime = true;
                        }
                    }

                    if (self.options.end) {

                        if (self.options.end.getTime() == call.date.getTime()) {
                            call.cls += ' select-end';
                        }

                        if (self.options.end.getTime() < call.date.getTime()) {
                            call.overtime = true;
                        }
                    }


                    $tr.append(
                        $('<td>', {
                            'data-date': datestr
                        })

                        .addClass(call.empty ? 'empty' : '')
                        .addClass(call.today ? 'today' : '')
                        .addClass(call.selected ? 'selected' : '')
                        .addClass(call.noday ? 'noday' : '')
                        .addClass(call.overtime ? 'overtime' : '')
                        .addClass(call.cls)
                        .addClass(call.date.getDay() == 6 || call.date.getDay() == 0 ? 'weekHoliday' : '')
                        .html($('<span>', {
                            text: call.date.getDate()
                        }))
                    );
                });

                $tbody.append($tr);

            });

            self.$calendar
                .find('.calendarGridTableSmall')
                .html($('<table/>', {
                        class: 'calendarGridTable',
                        cellspacing: 0,
                        cellpadding: 0
                    })
                    .addClass(self.options.format)
                    .append($title, $thead, $tbody)
                );

            self.is_loading = false;


            // event 
            $('td[data-date]', self.$calendar).click(function(e) {

                if ($(this).hasClass('empty') || ($(this).hasClass('noday') && self.$calendar.find('.calendarGridTable').hasClass('range'))) {
                    e.stopPropagation();
                    return false;
                }

                var selected = new Date($(this).attr('data-date'));
                selected.setHours(0, 0, 0, 0);

                if (self.$calendar.find('.calendarGridTable').hasClass('range')) {

                    if (self.$calendar.find('.calendarGridTable').hasClass('start') && self.options.start) {

                        if (self.options.end) {

                            if (selected.getTime() > self.options.end.getTime()) {
                                e.stopPropagation();
                                return false;
                            }
                        }

                        self.options.start = new Date(selected);
                    }

                    if (self.$calendar.find('.calendarGridTable').hasClass('end') && self.options.end) {

                        if (self.options.start) {

                            if (selected.getTime() < self.options.start.getTime()) {
                                e.stopPropagation();
                                return false;
                            }
                        }


                        self.options.end = new Date(selected);
                    }
                }

                self.date.selected = selected

                self.date.theDate = new Date($(this).attr('data-date'));
                self.date.theDate.setHours(0, 0, 0, 0);

                self.getSlected();
            });

            $('td.prev, td.next', self.$calendar).click(function(e) {

                var offset = $(this).hasClass("prev") ? -1 : 1;
                var newDate = new Date(self.date.theDate);
                newDate.setMonth(self.date.theDate.getMonth() + offset);
                self.date.theDate = newDate;

                self.updateCalendar();

                e.stopPropagation();
            });
        },

        getOffset: function() {
            var self = this;

            if (self.$calendar.hasClass('uiContextualAbove')) {
                self.$calendar.removeClass('uiContextualAbove');
            }

            var outer = $(document).height() < $(window).height() ? $(window) : $(document);

            var offset = self.$elem.offset(),
                outerWidth = $(window).width(),
                outerHeight = outer.height();

            var position = offset;

            position.top += self.$elem.outerHeight();

            var innerWidth = position.left + self.$calendar.outerWidth();
            if ($('html').hasClass('sidebarMode')) {
                innerWidth += 301;
            }

            if (innerWidth > outerWidth) {
                position.left = offset.left - self.$calendar.outerWidth() + self.$elem.outerWidth();
            }

            var innerHeight = position.top + self.$calendar.outerHeight();
            if (innerHeight > outerHeight) {
                position.top = offset.top - self.$calendar.outerHeight() - self.$elem.outerHeight();
                self.$calendar.addClass('uiContextualAbove');
            }

            self.$calendar.css(position);
        }
    };
    $.fn.datepicker = function(options) {
        return this.each(function() {
            var $this = Object.create(Datepicker);
            $this.init(options, this);
            $.data(this, 'datepicker', $this);
        });
    };
    $.fn.datepicker.options = {
        lang: 'th',
        selectedDate: null,
        start: null,
        end: null,
        weekDayStart: 1,
        style: 'short',
        format: '',
        onSelected: function() {},
    };

    /**/
    /* ToggleLink */
    /**/
    var ToggleLink = {
        init: function(options, elem) {

            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.toggleLink.options, options);

            self.setElem();

            self.active = false;

            // Event
            self.initEvent();
        },
        setElem: function() {
            var self = this;

            self.$elem.addClass('btn-toggleLink').removeAttr('rel');

            self.$elem = self.$elem.parents('.uitoggleLink');
            self.$btn = self.$elem.find('a.btn-toggleLink');
            self.$menu = self.$elem.find('.uitoggleLinkFlyout');

            self.setOffset();
        },

        setElem: function() {
            var self = this;

            self.$elem.addClass('btn-toggle').removeAttr('rel');

            self.$elem = self.$elem.parents('.uiToggle');
            self.$btn = self.$elem.find('a.btn-toggle');
            self.$menu = self.$elem.find('.uiToggleFlyout');

            self.setOffset();
        },

        initEvent: function() {
            var self = this;

            self.$btn.click(function(e) {

                $('body').find('.uiPopover, .uiToggle').find('a.btn-toggle.active').removeClass('active');
                if (self.$elem.hasClass('openToggler')) {
                    self.close();
                } else {
                    self.$btn.addClass('active');
                    self.display();
                    self.open();
                }

                e.preventDefault();
                e.stopPropagation();

            });

            self.$menu.find('a').click(function() {
                self.selected = $(this).parent().index();
                if (typeof self.options.onSelected === 'function') {
                    self.options.onSelected.apply(self, arguments);
                }
            });

            $('html').on('click', function() {

                if (self.active && self.$elem.hasClass('openToggler')) {
                    self.$btn.removeClass('active');
                    self.close();
                }

            });
        },

        display: function() {
            var self = this;

            if ($('body').find('.open.uiContextualPositioner').length > 0) {
                $('body').find('.open.uiContextualPositioner').removeClass('open');
            }

            if ($('body').find('.openToggler.uiToggle').length > 0) {
                $('body').find('.openToggler.uiToggle').removeClass('openToggler');
            }
        },

        open: function() {
            var self = this;
            self.active = true;

            self.$elem.addClass('openToggler');
            self.getOffset();
        },

        close: function() {
            var self = this;

            self.active = false;
            self.$elem.removeClass('openToggler');
        },

        setOffset: function() {
            var self = this;

            var outer = $(document).height() < $(window).height() ? $(window) : $(document);

            self.$menu.find('.uiMenu').css({
                overflowY: '',
                overflowX: '',
                height: '',
                minHeight: '',
                minWidth: ''
            });

            var offset = self.$elem.offset(),
                outerWidth = $(window).width(),
                outerHeight = outer.height();

            var position = offset;

            position.top += self.$elem.outerHeight();

            var innerWidth = position.left + self.$menu.outerWidth();

            if ($('html').hasClass('sidebarMode')) {
                innerWidth += 301;
            }

            if (innerWidth >= outerWidth || self.options.right) {
                self.$menu.addClass('uiToggleFlyoutRight');
            } else if (self.$menu.hasClass('uiToggleFlyoutRight')) {
                self.$menu.removeClass('uiToggleFlyoutRight');
            }

            var innerHeight = position.top + self.$menu.outerHeight();

            innerHeight += 30;
            if (innerHeight > outerHeight || self.options.above) {
                self.$menu.addClass('uiToggleFlyoutAbove');
            } else if (self.$menu.hasClass('uiToggleFlyoutAbove')) {
                self.$menu.removeClass('uiToggleFlyoutAbove');
            }
        },

        getOffset: function() {
            var self = this;

            self.setOffset();

            var outer = $(document).height() < $(window).height() ? $(window) : $(document);

            var offset = self.$elem.offset(),
                outerWidth = $(window).width(),
                outerHeight = outer.height();

            if (self.$menu.hasClass('fixedMenu')) {
                outerHeight = $(window).height();
                offset.top -= $(window).scrollTop();
            }

            var innerHeight = outerHeight - offset.top;
            if (innerHeight > offset.top) {
                if (self.$menu.hasClass('uiToggleFlyoutAbove')) {
                    self.$menu.removeClass('uiToggleFlyoutAbove');
                }
            }

            innerHeight = offset.top + self.$menu.outerHeight() + self.$elem.outerHeight()
            if (innerHeight > outerHeight && !self.$menu.hasClass('uiToggleFlyoutAbove')) {
                self.$menu.find('.uiMenu').css({
                    overflowY: 'auto',
                    overflowX: 'hidden',
                    height: outerHeight - (offset.top + self.$elem.outerHeight() + 15),
                    minHeight: 180,
                    minWidth: 180
                });
            }
        }
    };
    $.fn.toggleLink = function(options) {
        return this.each(function() {
            var $this = Object.create(ToggleLink);
            $this.init(options, this);
            $.data(this, 'toggleLink', $this);
        });
    };
    $.fn.toggleLink.options = {
        right: false,
        above: false,
        onSelected: function() {},
    };

    /**/
    /* Dropdown */
    /**/
    var Dropdown = {
        init: function(options, elem) {
            var self = this;

            self.$elem = $(elem);
            self.options = $.extend({}, $.fn.dropdown.options, options);

            if (!self.options.select) return false;

            self.is_open = false;
            self._focus = false;

            self.$elem.mouseenter(function() {
                self._focus = true;
            }).mouseleave(function() {
                self._focus = false;
            });

            // event
            self.$elem.click(function(e) {

                if (self.$elem.hasClass('active')) {
                    self.close();
                } else {
                    self.open();
                }

                // e.stopPropagation();
                e.preventDefault();
            });

            $('html').on('click', function() {

                if (self.is_open && self.$elem.hasClass('active') && !self._focus) {
                    self.close();
                }
            });
        },
        setMenu: function() {
            var self = this;

            self.$model = $('<div/>', {
                class: 'uiTypeaheadView'
            });
            self.$menu = $('<ul/>', {
                class: 'uiMenu',
                role: "listbox"
            });

            $.each(self.options.select, function(i, data) {
                self.$menu.append(self._item[data.type || 'default'](data));
            });

            self.$model.html(self.$menu);

            var offset = self.$elem.offset();
            if (self.options.settings.parent) {

                var parentoffset = $(self.options.settings.parent).offset();
                offset.left -= parentoffset.left;
                offset.top += $(self.options.settings.parent).scrollTop();

            }

            var settings = $.extend({}, self.options.settings, offset);
            settings.top += self.$elem.outerHeight();
            settings.$elem = self.$elem;

            uiLayer.get(settings, self.$model);
            self.$model = self.$menu.parents('.uiContextualLayer');
            self.$model.addClass('uiContextualPositioner');
            self.$layer = self.$menu.parents('.uiLayer');
        },
        resizeMenu: function() {
            var self = this;

            var settings = self.$input.offset();
            settings.top += self.$input.outerHeight();
            settings.top -= 1;

            self.$menu.parents('.uiContextualLayerPositioner').css(settings);
        },

        open: function() {
            var self = this;
            self.setMenu();

            Event.plugins(self.$layer);
            self.is_open = true;
            self.$elem.addClass('active');
            self.$model.addClass('open');

            // Event
            self.$model.find('.itemAnchor').click(function(e) {

                if (typeof self.options.onChange === 'function') {
                    self.options.onChange($(this));
                }
            });
        },
        close: function() {
            var self = this;

            self.is_open = false;
            self.$layer.remove();
            self.$elem.removeClass('active');
        },

        _item: {
            default: function(data) {

                var li = $('<li/>');
                var a = $('<a/>', {
                    class: 'itemAnchor'
                });
                var label = $('<span/>', {
                    class: 'itemLabel',
                    text: data.text
                });

                li.addClass(data.selected ? 'selected' : '').append(a);

                if (data.icon) {
                    li.addClass('has-icon');
                    a.append($('<i/>', {
                        class: 'mrs img icon-' + data.icon
                    }));
                }

                a.append(label);

                if (data.label) {

                    if (data.icon) {
                        label.addClass('fwb');
                    }

                    li.addClass('has-des');
                    a.append($('<div/>', {
                        class: 'itemDes'
                    }).html(data.label));
                }

                if (data.href) {
                    a.attr('href', data.href);
                }

                if (data.attr) {
                    a.attr(data.attr);
                }

                if (data.addClass) {
                    a.addClass(data.addClass);
                }

                return li;
            },

            header: function(data) {
                return $('<li/>', {
                    class: 'header'
                }).html($('<span/>', {
                    class: 'itemLabel'
                }).html(data.label));
            },

            separator: function() {
                return $('<li/>', {
                    class: 'separator'
                });
            },

            user: function(data) {
                return $('<li/>', {
                        class: 'user'
                    })
                    .addClass(data.selected ? 'selected' : '')
                    .html($('<a/>')
                        .addClass('anchor anchor32')
                        .html($('<div/>').addClass('clearfix')
                            .append($('<div/>').addClass('avatar lfloat size32 mrs')
                                .html($('<img/>', {
                                    class: 'img',
                                    src: data.image
                                }))
                            )

                            .append($('<div/>').addClass('content')
                                .append(
                                    $('<div/>', {
                                        class: 'spacer'
                                    }),
                                    $('<div/>', {
                                        class: 'massages clearfix',
                                        text: data.text
                                    })
                                )
                            )
                        )
                    );
            }
        },
    }
    $.fn.dropdown = function(options) {
        return this.each(function() {
            var obj = Object.create(Dropdown);
            obj.init(options, this);
            $.data(this, 'dropdown', obj);
        });
    };
    $.fn.dropdown.options = {
        // change: function(){},
        settings: {},
    };


    /**/
    /* ChooseFile */
    /**/
    var ChooseFile = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);
            self.options = options;

            self.$name = self.$elem.find('[data-name]');
            self.defaultName = self.$name.attr('data-name');
            self.$name.removeAttr('data-name');

            self.$remove = self.$elem.find('[data-remove]');
            self.$remove.removeAttr('data-remove');

            self.$file = self.$elem.find('input[type=file]');

            self._event();
            self._change();
        },

        _event: function() {
            var self = this;

            self.$file.change(function(e) {
                self.files = this.files;
                self._change();
            });

            self.$remove.click(function() {

                self.files = null;
                self._change();
            });
        },

        _change: function() {
            var self = this;

            if (!self.files) {
                self.$file.val("");
                self.$name.text(self.defaultName);
                self.$remove.addClass('hidden_elem');

            } else {
                self.$name.text(self.files[0].name);
                self.$remove.removeClass('hidden_elem');
            }
        },
    };
    $.fn.chooseFile = function(options) {
        return this.each(function() {
            var obj = Object.create(ChooseFile);
            obj.init(options, this);
            $.data(this, 'chooseFile', obj);
        });
    };


    var LiveClock = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.liveclock.options, options);

            self.$clock = self.$elem.find('[data-clock-text]');
            self.$date = self.$elem.find('[data-date-text]');
            self.refresh(1);

            if (self.$elem.find('[data-timezone]')) {

                var d = new Date();
                // var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

                // self.$elem.find('[data-timezone]').text( Math.floor( d.getTime() / 1000 ) );
            }

            // self.$date = self.$elem.find('.plugin-date');

        },

        refresh: function(length) {
            var self = this;

            setTimeout(function() {

                var theData = new Date();
                var minute = theData.getMinutes();
                minute = minute < 10 ? "0" + minute : minute;

                var sec = theData.getSeconds();
                sec = sec < 10 ? "0" + sec : sec;

                var hour = theData.getHours();
                // hour = hour < 10 ? "0" + hour : hour;

                var clock = '<span class="hour n' + theData.getHours() + '">' + hour + '</span>:<span class="minute">' + minute + '</span>:' + sec;

                self.$clock.html(clock);

                if (self.$date) {

                    if (self.options.lang == 'th') {

                        self.$date.html(Datelang.day(theData.getDay(), self.options.type, self.options.lang) + "à¸—à¸µà¹ˆ " + theData.getDate() + " " + Datelang.month(theData.getMonth(), self.options.type, self.options.lang));
                    } else {

                        self.$date.html(Datelang.day(theData.getDay(), self.options.type, self.options.lang) + ", " + theData.getDate() + " " + Datelang.month(theData.getMonth(), self.options.type, self.options.lang));
                    }

                }
                // self.$date.html( date );

                if (self.options.refresh) {
                    self.refresh();
                }

            }, length || self.options.refresh);
        }

    }
    $.fn.liveclock = function(options) {
        return this.each(function() {
            var obj = Object.create(LiveClock);
            obj.init(options, this);
            $.data(this, 'liveclock', obj);
        });
    };
    $.fn.liveclock.options = {
        lang: 'th',
        type: 'normal',
        refresh: 1000
    };

    /**/
    /* Clock */
    var Clock = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.clock.options, options);


            self.$clock = self.$elem.find('.plugin-clock');
            self.$date = self.$elem.find('.plugin-date');

            var lang = Object.create(Datelang);
            lang.init(self.options);
            self.string = lang;

            self.refresh(1);
        },

        refresh: function(length) {
            var self = this;

            setTimeout(function() {

                var theData = new Date();
                var minute = theData.getMinutes();
                minute = minute < 10 ? "0" + minute : minute;
                var clock = theData.getHours() + "<span>:</span>" + minute;

                var date = self.string.day(theData.getDay());
                date += 'à¸—à¸µà¹ˆ ' + theData.getDate();
                date += " " + self.string.month(theData.getMonth());
                date += " " + theData.getFullYear();

                self.$clock.html(clock);
                self.$date.html(date);

                if (self.options.refresh) {
                    self.refresh();
                }

            }, length || self.options.refresh);

        }
    };

    $.fn.clock = function(options) {
        return this.each(function() {
            var obj = Object.create(Clock);
            obj.init(options, this);
            $.data(this, 'Clock', obj);
        });
    };
    $.fn.clock.options = {
        lang: 'th',
        type: 'normal',
        refresh: 1000
    };

    /**/
    /* changeForm */
    /**/
    var changeForm = {
        init: function(elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.$btnSubmit = self.$elem.find('.btn.btn-submit');

            self.setDefault();
            self.initEvent();
        },

        setDefault: function() {
            var self = this;

            $.each(self.$elem.find(':input'), function() {

                var type = $(this).attr('type');
                var defaultValue = $(this).val();

                if (type == 'radio') {
                    var name = $(this).attr('name');
                    this.default_value = self.$elem.find('input[name=' + name + ']:checked').val();
                }

                this.defaultValue = defaultValue;

            });
        },

        initEvent: function() {
            var self = this;

            self.$elem.find(':input').change(function() {
                self.update();
            });

            self.$elem.find('input[type=text],input[type=password],input[type=email],textarea').keyup(function() {
                self.update();
            });
        },

        update: function($el) {
            var self = this,
                disabled = false;

            $.each(self.$elem.find(':input'), function() {
                var obj = $(this);
                var default_value = this.defaultValue;
                var currentVal = obj.val();

                if (obj.attr('type') == 'radio') {

                    default_value = this.default_value
                    currentVal = self.$elem.find('input[name=' + obj.attr('name') + ']:checked').val();
                }

                if (default_value != currentVal) {
                    disabled = true;
                    return false;
                }
            });

            // display
            if (self.$btnSubmit.hasClass('disabled') && disabled == true) {
                self.$btnSubmit.removeClass('disabled');
            } else if (!self.$btnSubmit.hasClass('disabled') && disabled == false) {
                self.$btnSubmit.addClass('disabled');
            }
        }
    };
    $.fn.changeForm = function() {
        return this.each(function() {
            var change = Object.create(changeForm);
            change.init(this);
            $.data(this, 'clock', change);
        });
    };

    /**/
    /* save as Picture */
    var save_as_picture = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.$input = self.$elem.find('input[type=file]');
            self.options = $.extend({}, $.fn.save_as_picture.options, options);

            self.form = self.options.form
                // self.$form = $(self.form);

            self.$input.change(function() {
                self.file = this.files[0];
                self.$image = $('<img/>', {
                    class: 'img img-preveiw',
                    alt: ''
                });
                self.setImage();

                $(this).val("");
            });

        },

        setImage: function() {
            var self = this;

            var reader = new FileReader();
            reader.onload = function(e) {

                var image = new Image();
                image.src = e.target.result;
                image.onload = function() {

                    self.$image.attr('src', e.target.result);
                    Event.showMsg({
                        load: true
                    });
                    self.display();
                }
            }
            reader.readAsDataURL(self.file);
        },

        display: function() {
            var self = this;
            /*$('<div/>', {class:'img-preveiw'}).css({
					height:436,
					width:436
				})*/
            Dialog.open({
                form: self.form,
                title: 'à¸›à¸£à¸±à¸šà¸‚à¸™à¸²à¸”à¸£à¸¹à¸›à¸ à¸²à¸ž',
                body: '<div class="img-preveiw"></div>', //self.setCropimage().html(),
                onOpen: function(response) {

                    response.$dialog.find('.img-preveiw').html(self.setCropimage());
                    self.preveiw();

                    response.$dialog.find('form').submit(function(e) {
                        e.preventDefault();
                        var $form = $(this);

                        var formData = new FormData();

                        // set field
                        $.each($form.serializeArray(), function(index, field) {
                            formData.append(field.name, field.value);
                        });

                        formData.append('file1', self.file);

                        Event.inlineSubmit($form, formData).done(function(result) {

                            Event.processForm($form, result);
                            Dialog.close();

                        }).fail(function() {

                        }).always(function() {

                        });
                    });
                },
                button: '<button class="btn btn-blue btn-submit" type="submit" ><span class="btn-text">à¸šà¸±à¸™à¸—à¸¶à¸</span></button><a role="dialog-close" class="btn js-close-dialog btn-white"><span class="btn-text">à¸¢à¸à¹€à¸¥à¸´à¸</span></a>'
            });
        },

        preveiw: function() {

            var self = this;
            if (typeof $.fn['cropper'] !== 'undefined') {
                self.$image.cropper(self.options);
                Event.hideMsg();
            } else {
                Event.getPlugin('cropper').done(function() {
                    self.$image.cropper(self.options);
                    Event.hideMsg();
                }).fail(function() {
                    console.log('Is not connect plugin:');
                    Event.hideMsg();
                });
            }
        },

        setCropimage: function() {
            var self = this;

            var $preveiw = $('<div>', {
                class: 'image-preveiw'
            });
            var $dataX = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[X]"
            });
            var $dataY = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[Y]"
            });
            var $dataHeight = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[height]"
            });
            var $dataWidth = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[width]"
            });
            $('#dataWidth');
            var $dataRotate = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[rotate]"
            });
            var $dataScaleX = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[scaleX]"
            });
            var $dataScaleY = $('<input/>', {
                type: "hidden",
                autocomplete: "off",
                name: "cropimage[scaleY]"
            });

            self.options.crop = function(e) {
                $dataX.val(Math.round(e.x));
                $dataY.val(Math.round(e.y));
                $dataHeight.val(Math.round(e.height));
                $dataWidth.val(Math.round(e.width));
                $dataRotate.val(e.rotate);
                $dataScaleX.val(e.scaleX);
                $dataScaleY.val(e.scaleY);
            }

            return $preveiw.css({
                height: 460,
                width: 460
            }).append(
                $dataX,
                $dataY,
                $dataWidth,
                $dataHeight,
                $dataRotate,
                $dataScaleX,
                $dataScaleY,
                self.$image
            );
        }
    };
    $.fn.save_as_picture = function(options) {
        return this.each(function() {
            var $this = Object.create(save_as_picture);
            $this.init(options, this);
            $.data(this, 'save_as_picture', $this);
        });
    };
    $.fn.save_as_picture.options = {
        aspectRatio: 1,
        autoCropArea: 1,
        // preview: '.img-preveiw',
        strict: true,
        guides: true,
        highlight: false,
        dragCrop: false,
        cropBoxMovable: true,
        cropBoxResizable: false,
        onCallback: function() {},
    };

    /**/
    /* playYoutube */
    var playYoutube = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            if (!options.url) {
                options.URL = self.$elem.attr('data-url');
            }

            options.max_width = self.$elem.width();

            if (!options.URL) return false;

            options.onReady = function() {
                console.log('onReady');
            };

            options.onError = function() {
                console.log('Error');
            };

            uiElem.iframePlayer.youtube.init(options, self.elem);
        }
    }

    $.fn.playYoutube = function(options) {
        return this.each(function() {
            var $this = Object.create(playYoutube);
            $this.init(options, this);
            $.data(this, 'playYoutube', $this);
        });
    };

    /**/
    /* Tooltip */
    var Tooltip = {
        init: function(options, elem) {
            var self = this;
            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.tooltip.options, options);

            if (!self.options.text && self.$elem.attr('title') != '') {
                self.options.text = self.$elem.attr('title');
                self.$elem.removeAttr('title');
            }

            self.is_show = false;
            self.timeout = 0;
            self.Event();
        },

        Event: function() {
            var self = this;
            // Event
            self.$elem.mouseenter(function() {
                self.show();
            }).mouseleave(function() {
                clearTimeout(self.timeout);
                self.hide();
            });

            self.$elem.on('click', function(e) {
                clearTimeout(self.timeout);
                self.hide();
            });
        },

        show: function(length) {
            var self = this;

            if (!self.options.text || self.options.text == "") {
                return false;
            }
            self.timeout = setTimeout(function() {

                self.is_show = true;
                self.get(); // Position

            }, length || self.options.reload);
        },

        hide: function() {
            var self = this;

            if (!self.is_show) return false;

            self.$positioner.remove(); //remove();
            self.is_show = false;
        },

        get: function() {
            var self = this;

            self.$span = $('<span/>').html(self.options.text);
            self.$text = $('<div/>', {
                class: 'tooltipText'
            }).html(self.$span);
            self.$content = $('<div/>', {
                class: 'tooltipContent'
            }).html(self.$text);
            self.$container = $('<div/>', {
                class: 'uiTooltipX'
            }).html(self.$content);

            self.$layer = $('<div/>', {
                class: 'uiContextualLayer'
            }).html(self.$container);
            self.$positioner = $('<div/>', {
                class: 'uiContextualLayerPositioner uiLayer'
            }).html(self.$layer);

            var offset = self.$elem.offset();
            $('body').append(self.$positioner);

            if (self.$span.outerWidth() > (self.$text.outerWidth() + 1)) {

                self.$text.css('width', self.$text.outerWidth()).addClass('tooltipWrap');
            }
            /* else if(self.$text.hasClass('tooltipWrap')){
            	self.$text.removeClass('tooltipWrap');
            }*/
            // 
            offset.top += self.$elem.outerHeight();

            /*if( self.options.pointer ){
            	self.$layer.addClass('uiToggleFlyoutPointer');
            	offset.top += 12;
            }*/

            var overflow = self.options.overflow;

            if (!overflow) {

                overflow = {
                    Y: "Below",
                    X: "Left"
                }

                var $window = $(window);
                var inner = {
                    height: $window.height() - (offset.top + self.$container.outerHeight()),
                    width: $window.width() - (offset.left + self.$container.outerWidth())
                }

                if (inner.height < 0) {
                    overflow.Y = "Above";
                }

                if (inner.width < 0) {
                    overflow.X = "Right";
                }
            }

            if (overflow.X == "Right") {
                self.$layer.css('right', 0);
                offset.left += self.$elem.outerWidth();
            }

            if (overflow.Y == "Above") {
                self.$layer.css('bottom', 0);
                offset.top -= self.$elem.outerHeight();
            }
            self.$layer.addClass("uiContextualLayer" + overflow.Y + overflow.X)
            self.$positioner.css(offset);
        }
    }

    $.fn.tooltip = function(options) {
        return this.each(function() {

            var data = $.data(this);
            if (data.tooltip) {
                data.tooltip.options = $.extend({}, data.tooltip.options, options);
            } else {
                var title = Object.create(Tooltip);
                title.init(options, this);
                $.data(this, 'tooltip', title);
            }

        });
    };

    $.fn.tooltip.options = {
        reload: 800,
        pointer: true,
        text: ""
    };

    /*==================================================
	==================== Checked =====================
	====================================================*/
    var Checked = {
        init: function(options, elem) {
            var self = this;
            self.elem = elem;
            self.$elem = $(self.elem);

            self.options = $.extend({}, $.fn.checkedlists.options, options);
            self.dataSelect = [];

            self.$elem.find('[role=item]').not('.disabled').click(function(e) {
                e.preventDefault();

                self.selected($(this).index());
            });
        },
        selected: function(index) {
            var self = this;

            var item = self.$elem.find('[role=item]').eq(index);
            item.toggleClass('checked', !item.hasClass('checked'));

            if (item.hasClass('checked')) {
                item.find('[type=checkbox]').prop("checked", true);
                self.dataSelect.push({
                    index: index,
                    elem: item
                });

                if (self.options.max) {
                    var length = self.dataSelect.length; // Object.keys(self.dataSelect).length;
                    if (length > self.options.max) {

                        $.each(self.dataSelect, function(i, obj) {
                            if (i == 0) {
                                obj.elem.removeClass('checked').find('[type=checkbox]').prop("checked", false);
                                self.dataSelect.splice(i, 1);
                            }

                        });
                    }
                }
            } else {
                $.each(self.dataSelect, function(i, obj) {
                    if (obj) {
                        if (obj.index == index) {
                            obj.elem.find('[type=checkbox]').prop("checked", false);
                            self.dataSelect.splice(i, 1);
                        }
                    }

                });
            }

            if (typeof self.options.onSelected == 'function') {
                self.options.onSelected(self.dataSelect);
            }
        }
    }

    $.fn.checkedlists = function(options) {
        return this.each(function() {

            var data = $.data(this);
            if (data.checkedlists) {
                data.checkedlists.options = $.extend({}, data.checkedlists.options, options);
            } else {
                var title = Object.create(Checked);
                title.init(options, this);
                $.data(this, 'checkedlists', title);
            }

        });
    };

    $.fn.checkedlists.options = {
        max: 1,
        onSelected: function() {}
    };


    var OpenParent = {
        init: function(elem) {
            var self = this;
            self.$elem = $(elem);
            self.$parent = self.$elem.parent();

            self.$elem.click(function() {

                if (self.$parent.hasClass('active') || self.$parent.find('>.content').css('display') == 'block') {
                    self.$parent.find('>.content').slideUp(200, function() {
                        self.$parent.removeClass('active');
                    });
                } else {
                    self.$parent.find('>.content').slideDown(200, function() {
                        self.$parent.addClass('active');
                    });
                }
            });


        }
    }

    $.fn.openParent = function(options) {
        return this.each(function() {
            var $this = Object.create(OpenParent);
            $this.init(this);
            $.data(this, 'openParent', $this);
        });
    };


    var Editor_tags = {
        init: function(options, elem) {

            var self = this;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.editor_tags.options, options);

            self.$elem.addClass('ui-tags tag-input-wrapper');
            // self.$preveiw = $('<div>', {class: 'tag-preveiw'});
            self.$input = $('<input>', {
                class: 'tag-input',
                placeholder: self.options.placeholder
            });
            self.$elem.append(self.$input);

            // set Data
            self.data = [];
            self.is_loading = false;
            self.is_show = false;
            self.is_focus = false;
            self.is_time = null;
            self.is_keycodes = [37, 38, 39, 40, 13];
            self.url = URL + 'tags/search/';
            self.setMenu();
            self.$menu.mouseenter(function() {
                self.is_focus = true;
            }).mouseleave(function() {
                self.is_focus = false;
            });

            // Event 
            self.$input.keyup(function(e) {

                var $this = $(this);
                var value = $.trim($this.val());

                if (self.is_keycodes.indexOf(e.which) == -1) {

                    if (value == '') {


                        return false;
                    }

                    self.search();
                }

            }).keydown(function(e) {
                var keyCode = e.which,
                    $this = $(this),
                    value = $.trim($this.val());

                console.log();

                if (keyCode == 40 || keyCode == 38) {

                    // self.changeUpDown( keyCode==40 ? 'donw':'up' );
                    e.preventDefault();
                }

                if (keyCode == 13 && value != '') {

                    self.addtag(value);
                    $this.val('');
                    e.preventDefault();
                }

                if (keyCode == 8 && value == '' && self.$elem.find('.tag').length > 0) {

                    self.remove(self.$elem.find('.tag').last().index());
                }
            }).focus(function() {

                self.resizeMenu();
            }).blur(function() {

                var $this = $(this);
                var value = $.trim($this.val());

                if (value != '') {
                    self.addtag(value);
                    $this.val('');
                }

            });

            self.$elem.click(function() {
                self.$input.focus();
            });

            self.$elem.delegate('.js-remove', 'click', function() {

                self.remove($(this).closest('.tag').index());
            });

            $.each(self.options.data, function(i, obj) {
                self.addtag(obj.name);
            });
        },

        setMenu: function() {
            var self = this;

            var $box = $('<div/>', {
                class: 'uiTypeaheadView'
            });
            self.$menu = $('<ul/>', {
                class: 'has-loading ',
                role: "listbox"
            });

            $box.html($('<div/>', {
                class: 'bucketed'
            }).append(self.$menu));
            // bucketed

            var settings = self.$input.offset();
            settings.top += self.$input.outerHeight();
            settings.left -= 1;

            uiLayer.get(settings, $box);
            self.$layer = self.$menu.parents('.uiLayer');
            self.$layer.addClass('hidden_elem');

            $(window).resize(function() {
                self.resizeMenu();
            });
        },
        resizeMenu: function() {
            var self = this;

            var settings = self.$input.offset();
            settings.top += self.$input.outerHeight();
            settings.top -= 1;

            self.$menu.parents('.uiContextualLayerPositioner').css(settings);
        },

        search: function(length) {
            var self = this;

            self.is_time = setTimeout(function() {

                self.q = $.trim(self.$input.val());
                if (self.q == '') return false;

                self.fetch().done(function(data) {

                    // console.log( data );
                });
            }, length || 800);
        },
        fetch: function() {
            var self = this;

            return $.ajax({
                url: self.url,
                data: {
                    q: self.q
                },
                dataType: 'json'
            }).fail(function() {

            }).always(function() {

            });
        },

        buildFrag: function() {

        },

        addtag: function(text) {
            var self = this;

            text = text.toString();

            var has = false;
            $.each(self.data, function(i, val) {

                if (val == text || val.toUpperCase() == text || val.toLowerCase() == text || self.capitalizeFirstLetter(val) == text) {
                    has = true;
                }

            });

            if (!has) {
                self.data.push(text);
                self.$input.before(self.getTag(text));
            }
        },
        capitalizeFirstLetter: function(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        getTag: function(text) {
            var self = this;

            var tag = $('<div>', {
                class: 'tag'
            }).append(
                $('<span>', {
                    class: 'text'
                }).text(text),
                $('<input>', {
                    type: 'hidden',
                    name: self.options.name,
                    autocomplete: 'off',
                    class: "hiddenInput"
                }).val(text),
                $('<button>', {
                    type: 'button',
                    class: 'js-remove'
                }).html($('<i>', {
                    class: 'icon-remove'
                }))
            );

            tag.data('text', text);

            return tag;
        },
        remove: function(length) {
            var self = this;

            var item = self.$elem.find('.tag').eq(length);
            var text = item.data('text');

            var data = [];
            $.each(self.data, function(i, val) {

                if (val == text || val.toUpperCase() == text || val.toLowerCase() == text || self.capitalizeFirstLetter(val) == text) {} else {
                    data.push(val);
                }

            });

            self.data = data;
            item.remove();
        }
    };

    $.fn.editor_tags = function(options) {
        return this.each(function() {
            var $this = Object.create(Editor_tags);
            $this.init(options, this);
            $.data(this, 'editor_tags', $this);
        });
    };
    $.fn.editor_tags.options = {
        placeholder: "",
        name: 'tags[]',
        data: []
    };

    var SelectMany = {
        init: function(options, elem) {

            var self = this;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.editor_tags.options, options);

            self.$ul = $('<ul>', {
                class: ''
            });
            self.$add = $('<a>', {
                class: 'fcg mts fsm',
                text: '+ add'
            });

            self.$elem.addClass('select-many');
            // 
            // self.$input = $('<input>', {class: 'tag-input', placeholder: self.options.placeholder});
            self.$elem.append(self.$ul, self.$add);


            self.$add.click(function() {
                self.new();
            });

            var hasChecked = false;
            $.each(self.options.lists, function(i, obj) {

                if (obj.checked) {
                    hasChecked = true;
                    self.new(obj.id || obj.value);
                }
            });

            if (!hasChecked) {
                self.new();
            }

            self.$ul.delegate('.js-remove', 'click', function() {
                if (self.$ul.find('li').length == 1) {

                    self.$ul.find('li').first().find(":input").val('');
                    return false;
                }
                $(this).closest('li').remove();
            });
        },

        new: function(checked) {
            var self = this;

            self.$ul.append(self.setItem(checked));
        },

        setItem: function(checked) {
            var self = this;
            var li = $('<li>');
            return li.append(self.setSelect(checked), $('<a>', {
                class: 'js-remove'
            }).html($('<i>', {
                class: 'icon-remove'
            })));
        },
        setSelect: function(checked) {

            var self = this;

            var $select = $('<select>', {
                class: self.options.class,
                name: self.options.name,
            });

            $select.append($('<option>', {
                value: '',
                text: '-'
            }));

            $.each(self.options.lists, function(i, obj) {

                var id = obj.id || obj.value;

                $select.append($('<option>', {
                    value: id,
                    text: obj.name || obj.text,
                    selected: checked == id ? true : false
                }));
            });

            return $select;

        }
    };

    $.fn.selectmany = function(options) {
        return this.each(function() {
            var $this = Object.create(SelectMany);
            $this.init(options, this);
            $.data(this, 'selectmany', $this);
        });
    };
    $.fn.selectmany.options = {
        placeholder: "",
        name: '',
        class: ''
    };


    var Upload1 = {
        init: function(options, elem) {
            var self = this;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.upload1.options, options);

            self.$input = self.$elem.find('input[type=file]');
            self.$image = self.$elem.find('.ProfileImageComponent_image');

            self.$input.change(function() {
                self.file = this.files[0];

                self.setImage(self.file);
                $(this).val("");
            });

            self.$elem.find('.js-remove').click(function() {

                self.$image.html('');
                self.$elem.removeClass('has-image');

                if (self.options.autosize) {

                    var arr = {
                        width: self.options.max_width,
                        height: self.options.max_height
                    };
                    self.$elem.find('.ProfileImageComponent').css(arr);

                }
            });

        },

        setImage: function() {
            var self = this;

            var $img = $('<img>', {
                class: 'img',
                alt: ''
            });

            var reader = new FileReader();
            reader.onload = function(e) {

                var image = new Image();
                image.src = e.target.result;
                image.onload = function() {

                    $img.attr('src', e.target.result);

                    var formData = new FormData();

                    // set field
                    $.each(self.options.data, function(name, value) {
                        formData.append(name, value);
                    });
                    formData.append('file1', self.file);

                    var width = this.width,
                        height = this.height;

                    self.$elem.addClass('has-loading');
                    $.ajax({
                            type: "POST",
                            url: self.options.url,
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                        }).done(function(response) {


                            if (response.message) {

                                Event.showMsg({
                                    text: response.message,
                                    auto: true
                                });
                            }


                            if (response.error) {
                                return false;
                            }

                            self.resize(width, height);
                            self.display($img);

                        }).always(function() {
                            // complete

                            self.$elem.removeClass('has-loading');
                        })
                        .fail(function() {
                            // error
                        });
                }
            }
            reader.readAsDataURL(self.file);
        },
        resize: function(width, height) {
            var self = this;

            if (self.options.autosize) {

                var arr = {
                    width: self.options.max_width,
                    height: self.options.max_height
                };

                self.$image.removeClass('hauto').removeClass('wauto');

                if (width > height) {

                    self.$image.addClass('hauto');
                    arr.height = (height * arr.width) / width;
                }

                if (width < height) {

                    self.$image.addClass('wauto');
                    arr.width = (width * arr.height) / height;
                }

                self.$elem.find('.ProfileImageComponent').css(arr);
            }
        },
        display: function($img) {
            var self = this;


            self.$elem.addClass('has-image');
            self.$image.html($img);
        },

        preveiw: function() {

            var self = this;
            if (typeof $.fn['cropper'] !== 'undefined') {
                self.$image.cropper(self.options);
                Event.hideMsg();
            } else {
                Event.getPlugin('cropper').done(function() {
                    self.$image.cropper(self.options);
                    Event.hideMsg();
                }).fail(function() {
                    console.log('Is not connect plugin:');
                    Event.hideMsg();
                });
            }
        }
    };
    $.fn.upload1 = function(options) {
        return this.each(function() {
            var $this = Object.create(Upload1);
            $this.init(options, this);
            $.data(this, 'upload1', $this);
        });
    };
    $.fn.upload1.options = {
        autosize: false,
        max_width: 128,
        max_height: 128
    };

    var Upload = {
        init: function(options, elem) {
            var self = this;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.upload.options, options);

            self.$outInput = self.$elem.find('input[type=file]');

            self.url = URL + 'uploadx/set/';

            if (self.options.preview == 'this') {

                self.$upload = self.setBox();
                self.$listsbox = self.$upload.find('#listsbox');
                self.$main = self.$upload.find('#main');

                self.$elem.html(self.$upload);

                self.$main.height(self.options.main_height || self.$main.parent().height());

                self.$input = self.$upload.find('.js-add');

                self.$input.click(function() {

                    var $input = $('<input type="file" />');
                    $input.attr('accept', self.options.accept);
                    $input.attr('multiple', self.options.multiple);
                    $input.trigger('click');

                    $input.change(function() {
                        self.buildFragFiles(this.files);
                        $(this).val("");
                    });
                });
            }

            self.$outInput.change(function() {
                // var file = this.files[0];

                if (self.options.preview == 'dialog') {
                    self.open();
                }

                self.buildFragFiles(this.files);
                $(this).val("");
            });

        },

        buildFragFiles: function(results) {
            var self = this;

            $.each(results, function(i, file) {

                self.setImage(file);
            });
        },

        open: function() {
            var self = this;

            self.$upload = self.setBox();
            self.$listsbox = self.$upload.find('#listsbox');

            Dialog.open({
                onClose: function() {},
                title: self.options.title,
                width: 750,
                body: self.$upload[0],
                form: '<div class="model-upload-wrapper">'
            });
        },
        setBox: function() {
            var self = this;
            var $wrapper = $('<div>', {
                class: 'upload-wrapper'
            });
            var $header = $('<div>', {
                class: 'upload-wrapper-header'
            });

            /**/
            /* tabs */
            var ul = $('<ul>', {
                class: 'clearfix'
            });
            if (self.options.tabs) {
                ul.append($('<li>').append($('<button>', {
                    class: 'active',
                    type: 'button',
                    text: 'My Images'
                })));
            }


            ul.append($('<li>', {
                class: 'rfloat'
            }).append($('<a>', {
                class: 'mtm mrm btn btn-blue js-add',
                text: 'Upload Images'
            })));

            var $tabs = $('<div>', {
                class: 'tabs clearfix'
            });
            $tabs.append(ul);

            /**/
            /* meta */
            var $meta = $('<div>', {
                class: 'meta clearfix'
            });
            $actions = $('<ul>', {
                class: 'group-actions clearfix hidden_elem'
            });
            $actions.append($('<li>', {
                class: 'delete'
            }).append($('<button>', {
                class: 'btn',
                type: 'button',
                text: 'Delete'
            })));

            $meta.append($actions, self.options.message == '' ? '' : $('<div>', {
                class: 'upload-message fsm pam uiBoxYellow mas'
            }).text(self.options.message));
            $header.append($tabs, $meta);

            var $body = $('<div>', {
                class: 'upload-wrapper-body'
            });

            if (self.options.sidebar) {
                var $sidebar = $('<div>', {
                    class: 'sidebar'
                });
                $sidebar.append(
                    $('<div>', {
                        class: 'sidebar-content'
                    }).append(
                        $('<ul>').append(
                            $('<li>').append($('<a>').text('All Media')), $('<li>').append($('<a>').text('My Media'))
                        )

                    ),
                    $('<div>', {
                        class: 'sidebar-footer'
                    }).append(
                        $('<ul>').append(
                            $('<li>').append($('<a>').append('<i class="icon-plus mrs"></i>', 'Add New Folder'))
                        )

                    )
                );
                $body.append($sidebar);
            }

            var $main = $('<div>', {
                class: 'upload-wrapper-main clearfix has-empty',
                id: 'main'
            });
            $main.append(
                $('<div>', {
                    class: 'breadcrumbs'
                }).html('<span>All Media</span>'),
                $('<div>', {
                    class: 'clearfix',
                    id: 'listsbox'
                }),
                $('<div>', {
                    class: 'empty no-entities-placeholder'
                }).html('<div class="no-entities no-picture"><div class="empty-icon-image"><i class="icon-image"></i></div><a class="js-add">Upload Images</a></div></div>')
            );
            /*<h3>Start Adding Files to "test". It\'s Easy!</h3><div>Drag and drop them here, or click Upload Images.<br>Your images will also appear in your Site Media folder, so theyâ€™re easy to find and use. */
            $body.append($main);

            $wrapper.append($header, $body);
            if (self.options.footer) {

                var $footer = $('<div>', {
                    class: 'upload-wrapper-footer clearfix'
                });
                $footer.append(
                    $('<div>', {
                        class: 'lfloat upload-status hidden_elem'
                    }).append(
                        $('<i>', {
                            class: 'icon-check mrs'
                        }), $('<div>', {
                            class: 'title'
                        }).append(
                            'Uploaded&nbsp;', $('<span>', {
                                class: 'numbers'
                            }).text('(9/9 Files)')
                        ), $('<div>', {
                            class: 'progress-bar medium'
                        }).append(
                            $('<span>', {
                                class: 'progress blue'
                            })
                        ), $('<div>', {
                            class: 'fails'
                        }).append('fails')
                    ),
                    $('<div>', {
                        class: 'rfloat'
                    }).append(
                        $('<a>', {
                            class: 'btn btn-blue'
                        }).text('Done')
                    )
                );

                $wrapper.append($footer);
            }

            return $wrapper;
        },

        setImage: function(file) {
            var self = this;

            var $item = self.editablePhoto();
            file.$elem = $item;
            self.display($item, true);
            self.saveFile(file);
        },

        editablePhoto: function() {
            var self = this;
            return $('<div/>', {
                class: 'uiEditablePhoto has-loading'
            }).css({
                width: self.options.max_width,
                height: self.options.max_height,
                margin: self.options.margin
            }).append(
                $('<div/>', {
                    class: 'photoWrap'
                }).css({
                    width: self.options.max_width,
                    height: self.options.max_height,
                }).append(
                    self.editablePhotoProgress(),
                    self.editablePhotoError(),

                    $('<div/>', {
                        class: 'scaledImageContainer scaledImage'
                    })
                ), (self.options.caption ? self.editablePhotoCaption() : ''),
                self.editablePhotoControls()
            );
        },
        editablePhotoError: function() {
            return $('<div/>').addClass('empty-error')
                .append($('<span/>')
                    .addClass('empty-title')
                    .append($('<span/>'))
                )
                .append($('<div/>')
                    .addClass('empty-message')
                );
        },
        editablePhotoProgress: function() {
            var self = this;

            return $('<div/>').addClass('progress-bar medium')
                .append($('<span/>')
                    .addClass('bar blue')
                    .append($('<span/>'))
                )
                .append($('<div/>')
                    .addClass('text')
                    .append(uiElem.loader())
                );
        },
        editablePhotoCaption: function() {

            return $('<div/>', {
                    class: 'inputs'
                })
                .append($('<div/>')
                    .addClass('captionArea')
                    .append($('<div/>')
                        .addClass('uiTypeahead')
                        .append($('<textarea/>')
                            .addClass('uiTextareaNoResize textInput textCaption')
                            .attr({
                                title: "à¹€à¸‚à¸µà¸¢à¸™à¸„à¸³à¸šà¸£à¸£à¸¢à¸²à¸¢à¸£à¸¹à¸›à¸ à¸²à¸ž...",
                                name: 'caption_text',
                                placeholder: "à¹€à¸‚à¸µà¸¢à¸™à¸„à¸³à¸šà¸£à¸£à¸¢à¸²à¸¢à¸£à¸¹à¸›à¸ à¸²à¸ž"
                            })

                        )

                    )

                );
        },
        editablePhotoControls: function() {

            return $('<div/>').addClass('controls').append(
                /*$('<a/>', {class: 'control'}).html( 
					$('<i/>', {class: 'icon-refresh'})
				)*/
                $('<a/>', {
                    class: 'control remove'
                }).html(
                    $('<i/>', {
                        class: 'icon-remove',
                        title: 'à¸¢à¸à¹€à¸¥à¸´à¸'
                    })
                ),
                $('<a/>', {
                    class: 'control checked'
                }).html(
                    $('<i/>', {
                        class: 'icon-check',
                        title: 'à¹€à¸¥à¸·à¸­à¸'
                    })
                )
            );
        },

        saveFile: function(file) {
            var self = this;

            var $progress = file.$elem.find('.progress-bar');
            var $img = file.$elem.find('.scaledImage');

            var formData = new FormData();
            formData.append('file1', file);

            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    //Upload progress
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            // var percentComplete = evt.loaded / evt.total;
                            var progress = parseInt(((evt.loaded / evt.total) * 100), 10);
                            //Do something with upload progress
                            $progress.find('.bar').width(progress + "%");
                        }
                    }, false);
                    //Download progress
                    /*xhr.addEventListener("progress", function(evt){
				      if (evt.lengthComputable) {
				        var percentComplete = evt.loaded / evt.total;
				        //Do something with download progress
				        console.log(percentComplete);
				      }
				    }, false);*/
                    return xhr;
                },
                url: self.url,
                type: "POST",
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false
            }).done(function(response) {
                // console.log( response );

                if (response.error) {
                    file.$elem.removeClass('has-loading').addClass('has-error');
                    file.$elem.find('.textCaption').attr('disabled', true);
                    file.$elem.find('.empty-message').text(response.error_message);

                    file.$elem.find('.empty-error').css('margin-top', (file.$elem.find('.empty-error').height() / 2) * -1);

                    return false;
                }

                if (response.url) {
                    self.loadImageUrl(file.$elem, response.url, response.photo_id);

                    if (self.options.caption) {
                        file.$elem.addClass('has-caption');
                    }
                }


                // self.displayImage( file.$elem, response, true );

            }).always(function() {
                self.getFiles();
            }).fail(function() {});
        },
        getFiles: function() {
            var self = this;

            /*$.each(self.files, function (i, file) {
				if( file.variable ){
					file.variable = false;
					self.saveFile( file );
					// self.loadFile( file );
					return false;
				}
			});*/
        },
        loadImageUrl: function(item, url, id) {
            var self = this;

            var image = new Image();
            image.onload = function() {
                var img = this;
                var h = item.find('.photoWrap').height() || self.options.max_height;
                var w = item.find('.photoWrap').width() || self.options.max_width;
                var scaled = self.resizeImage({
                    width: w,
                    height: h
                }, {
                    width: img.width,
                    height: img.height
                });

                var fitHeight = scaled.width > scaled.height ? false : true;
                item.find('.scaledImage').html(img);
                $(img).addClass(fitHeight ? "scaledImageFitHeight" : 'scaledImageFitWidth');

                item
                    .removeClass('has-loading')
                    .addClass('has-file')
                    .find('.photoWrap').addClass(fitHeight ? '' : 'fitWidth').css({
                        width: w,
                        height: h,
                        lineHeight: h + "px"
                    }).find('.scaledImage').css({
                        width: scaled.width,
                        height: scaled.height
                    });
            }

            image.onerror = function(e) {

                item.find('.empty-message').text('à¹„à¸¡à¹ˆà¸ªà¸²à¸¡à¸²à¸£à¸–à¸”à¸¹à¸£à¸¹à¸›à¸ à¸²à¸žà¹„à¸”à¹‰à¸«à¸£à¸·à¸­à¹„à¸Ÿà¸¥à¹Œà¸£à¸¹à¸›à¸ à¸²à¸žà¸–à¸¹à¸à¸¥à¸šà¹„à¸›à¹à¸¥à¹‰à¸§');
                item.find('.textCaption').attr('disabled', true);
                item.removeClass('has-loading').addClass('has-error');
            };

            /*image.onloadprogress = function(e) { 
				var progress = e.loaded / e.total;
				// console.log( progress );
			};*/
            image.src = url;
        },
        resizeImage: function(fig, org) {
            if (org.width == org.height) return fig;

            else if (org.width > org.height) {

                return {
                    width: (fig.width).toFixed(),
                    height: ((org.height * fig.height) / org.width).toFixed(),
                    org: org,
                    fitHeight: true
                }

            } else {
                return {
                    width: ((org.width * fig.width) / org.height).toFixed(),
                    height: (fig.height).toFixed(),
                    org: org,
                    fitHeight: false
                }

            }
        },

        display: function(item, up) {
            var self = this;

            if (!up || self.$listsbox.find('.uiEditablePhoto').length == 0) {
                self.$listsbox.append(item);
            } else {
                self.$listsbox.find('.uiEditablePhoto').first().before(item);
            }

            if (self.$upload.find('#main').hasClass('has-empty')) {
                self.$upload.find('#main').removeClass('has-empty');
            }
        },
    };

    $.fn.upload = function(options) {
        return this.each(function() {
            var $this = Object.create(Upload);
            $this.init(options, this);
            $.data(this, 'upload', $this);
        });
    };
    $.fn.upload.options = {
        autosize: false,
        margin: 10,
        max_width: 128,
        max_height: 128,
        title: 'Upload',
        multiple: '',
        accept: 'image/*',
        name: 'file',
        main_height: 350,

        message: '',
        tabs: '',

        caption: false,
        header: true,
        sidebar: false,
        footer: true
    };

    var ImageCover2 = {
        init: function(options, elem) {
            var self = this;
            self.elem = elem;

            self.options = $.extend({}, $.fn.imageCover2.options, options);

            self.url = URL + 'uploadx/image_cover';

            self.initElem();
            self.initEvent();
        },
        initElem: function() {
            var self = this;
            self.$elem = $(self.elem);

            var width = self.$elem.width();
            var height = (self.options.scaledY * width) / self.options.scaledX;
            self.$elem.css({
                width: width,
                height: height
            });

            if (self.options.image_url) {

                self.displayImage();
            }
        },
        initEvent: function() {
            var self = this;
            self.$elem.find('[type=file]').change(function() {
                self.setImage(this.files[0]);
            });
        },

        setImage: function(file) {
            var self = this;

            self.$elem.addClass('has-loading');
            var $progress = self.$elem.find('.progress-bar');
            // var $remove = $('<a/>', {class:"preview-remove"}).html( $('<i/>', {class:'icon-remove'}) );

            $remove.click(function(e) {
                e.preventDefault();
                self.clear();
            });

            var $img = $('<div/>', {
                class: 'image-crop'
            });
            self.$elem.find('.preview').append($img);

            var width = self.$elem.width();

            var reader = new FileReader();
            reader.onload = function(e) {
                var image = new Image();
                image.src = e.target.result;
                $image = $(image).addClass('img img-crop');

                image.onload = function() {

                    var width = self.$elem.width() || self.options.scaledX;

                    // width = this.width;
                    var height = (this.height * width) / this.width;

                    $img.css({
                        width: width,
                        height: height
                    });
                    self.$elem.css({
                        width: width,
                        height: height
                    });


                    setTimeout(function() {
                        self.fetch(file).done(function(results) {

                            self.$elem.addClass('has-file');
                            $img.html($image);

                            self.setControl();

                            // console.log( results );
                        });

                    }, 1);

                    // self.cropperImage( self.$elem.find('.preview') );
                }
            }

            /*reader.onprogress = function(data) {
				if (data.lengthComputable) {                                            
	                var progress = parseInt( ((data.loaded / data.total) * 100), 10 );
	                $progress.find('.bar').width( progress+"%" );
	            }
        	}*/

            reader.readAsDataURL(file);
        },
        fetch: function(file) {
            var self = this;

            var $progress = self.$elem.find('.progress-bar');

            var formData = new FormData();
            formData.append('file1', file);

            $.each(self.options.data_post, function(name, value) {
                formData.append(name, value);
            });

            return $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        //Upload progress
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                // var percentComplete = evt.loaded / evt.total;
                                var progress = parseInt(((evt.loaded / evt.total) * 100), 10);
                                //Do something with upload progress
                                $progress.find('.bar').width(progress + "%");
                            }
                        }, false);
                        return xhr;
                    },
                    url: self.url,
                    type: "POST",
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false
                })
                .always(function() {
                    self.$elem.removeClass('has-loading');
                })
                .fail(function() {

                    self.alert({
                        title: 'à¹€à¸à¸´à¸”à¸‚à¹‰à¸­à¸œà¸´à¸”à¸žà¸¥à¸²à¸”!',
                        text: 'à¹„à¸¡à¹ˆà¸ªà¸²à¸¡à¸²à¸£à¸–à¹€à¸Šà¸·à¹ˆà¸­à¸¡à¸à¸±à¸šà¸¥à¸´à¸‡à¸à¹Œà¹„à¸”à¹‰'
                    });
                });
        },

        alert: function(data) {


            alert(data.text);
        },
        clear: function() {
            var self = this;

            self.$elem.find('[type=file]').val('');
            self.$elem.find('.preview').empty();
            self.$elem.removeClass('has-file');
        },

        displayImage: function() {
            var self = this;

            self.setControl();
            var $img = $('<div/>', {
                class: 'image-crop'
            });
            self.$elem.find('.preview').append($img);

            $image = $('<img>', {
                class: 'img img-crop',
                src: self.options.image_url,
                alt: ''
            });
            $img.html($image);

            self.$elem.addClass('has-file');
        },
        setControl: function() {
            var self = this;

            var $edit = $('<div/>', {
                class: 'image-cover-edit',
                text: 'à¹€à¸›à¸¥à¸µà¹ˆà¸¢à¸™à¸£à¸¹à¸›'
            });
            var $del = $('<div/>', {
                class: 'image-cover-edit',
                text: 'à¸¥à¸š'
            });

            self.$elem.find('.preview').append($edit);

            $edit.click(function() {

                Media.open({
                    title: 'à¹€à¸›à¸¥à¸µà¹ˆà¸¢à¸™à¸£à¸¹à¸›',
                }, {
                    obj_id: self.options.data_post.id,
                    obj_type: self.options.data_post.type,
                    load: URL + 'products/get_images/' + self.options.data_post.id,
                    max: 1,
                    type: 'select',

                });
            });
        }
    };

    $.fn.imageCover2 = function(options) {
        return this.each(function() {
            var $this = Object.create(ImageCover2);
            $this.init(options, this);
            $.data(this, 'imageCover2', $this);
        });
    };
    $.fn.imageCover2.options = {
        scaledX: 640,
        scaledY: 360,
        data_post: {}
    };


    var _update = {
        init: function(options, elem) {
            var self = this;
            self.$elem = $(elem);
            self.options = options;

            if (!self.options.url) {

                return false;
            }

            self.$elem.change(function() {

                var value = self.$elem.val();

                if (self.$elem.attr('type') == 'checkbox') {
                    value = self.$elem.prop('checked') ? 1 : 0;
                }

                Event.showMsg({
                    text: 'à¸šà¸±à¸™à¸—à¸¶à¸à¹à¸¥à¹‰à¸§',
                    load: true,
                    auto: true
                });
                $.post(self.options.url, {
                    value: value
                }, function() {}, 'json');

            });

        },
    };

    $.fn._update = function(options) {
        return this.each(function() {
            var $this = Object.create(_update);
            $this.init(options, this);
            $.data(this, '_update', $this);
        });
    };

    /**/
    /* close date */
    /**/
    var CloseDate = {
        init: function(options, elem) {
            var self = this;

            self.elem = elem;
            self.$elem = $(elem);

            self.options = $.extend({}, $.fn.closedate.options, options);

            self.config();
            self.setElem();

            self.setMenu();
            self.hideMenu();

            // set Calendar
            self.display();

            self.activeIndex = self.options.activeIndex || 0;
            self._activeIndex();
            var data = self.$menu.find('li').eq(self.activeIndex).data();
            self.selectMenu(data);

            self.events();

            if (typeof self.options.onComplete == 'function') {
                self.options.onComplete(self);
            }
        },

        config: function() {
            var self = this;
            self.today = new Date();
            self.today.setHours(0, 0, 0, 0);

            // set date
            self.startDate = new Date(self.options.start || self.today);
            if (self.options.start == null) {
                self.startDate.setDate(1);
            }

            self.endDate = new Date(self.options.end || self.today);

            var lang = Object.create(Datelang);
            lang.init({
                lang: self.options.lang,
                type: self.options.type
            });
            self.string = lang;

            if (self.options.firstDate) {
                self.options.firstDate = new Date(self.options.firstDate);
                self.options.firstYear = self.options.firstDate.getFullYear();
            }
        },
        setElem: function() {
            var self = this;

            self.$startInput = $('<input>', {
                class: 'hiddenInput',
                type: 'hidden',
                name: 'start_date'
            });
            self.$endInput = $('<input>', {
                class: 'hiddenInput',
                type: 'hidden',
                name: 'end_date'
            });
            self.$text = $('<span>', {
                class: 'btn-text'
            });
            self.original = self.$elem;
            var placeholder = $('<div/>', {
                class: 'uiPopover'
            });

            self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$btn = $('<a>', {
                class: 'btn btn-box btn-toggle'
            }).append(self.$text);

            if (!self.options.icon) {
                self.$btn.append($('<i/>', {
                    class: 'img mls icon-angle-down'
                }));
            }
            self.$elem.append(self.$btn, self.$startInput, self.$endInput);

            self.$calendar = $('<div>', {
                class: 'uiContextualPositioner'
            });
            self.$calendar.append($('<div>', {
                class: 'toggleFlyout calendarGridTableSmall'
            }));
            if (self.options.max_width) {
                self.menu.find('.toggleFlyout').css('width', self.options.max_width);
            }

            self.$start = $('<td>', {
                class: 'start'
            });
            self.$end = $('<td>', {
                class: 'end'
            });

            self.$preveiw = $('<span>', {
                class: 'preveiw lfloat'
            });

            var $table = $('<table/>', {
                class: 'calendarCloseDateGridTable'
            }).append(
                $('<tr>').append(self.$start, $('<td>', {
                    class: 'to',
                    text: 'to'
                }), self.$end), $('<tr>').append($('<td>', {
                    colspan: 3,
                    class: 'tar ptm'
                }).append(
                    self.$preveiw, $('<a>', {
                        class: 'btn btn-cancel',
                        text: 'à¸¢à¸à¹€à¸¥à¸´à¸'
                    }), $('<a>', {
                        class: 'btn btn-blue btn-submit',
                        text: 'à¸™à¸³à¹„à¸›à¹ƒà¸Šà¹‰'
                    })
                ))
            );

            self.$calendar.find('.toggleFlyout').append($table);

            // self.$menu = 

            /*self.updateCalendar();
			self.setSlecte();*/
        },
        setMenu: function() {
            var self = this;

            self.$menu = $('<ul/>', {
                class: 'uiContextualMenu',
                role: "listbox"
            });

            var settings = self.$btn.offset();
            settings.top += self.$btn.outerHeight();

            uiLayer.get(settings, self.$menu);
            self.$layer = self.$menu.parents('.uiLayer');


            // event
            self.$menu.mouseenter(function() {
                self.is_focus = true;
            }).mouseleave(function() {
                self.is_focus = false;
            });

            self.resizeMenu();
            $(window).resize(function() {
                self.resizeMenu();
            });

            $.each(self.options.options, function(i, obj) {
                self.$menu.append(self.setItemMenu(obj));
            });

            self.$menu.find('li').mouseenter(function() {
                $(this).addClass('active').siblings().removeClass('active');
            });

            self.$menu.mouseleave(function() {

                self._activeIndex();
            });
        },
        resizeMenu: function() {
            var self = this;

            self.$menu.width(self.$btn.outerWidth() - 2);
            var settings = self.$btn.offset();
            settings.top += self.$btn.outerHeight();
            settings.top -= 1;
            // settings.left += 3;

            self.$menu.css({
                overflowY: 'auto',
                overflowX: 'hidden',
                maxHeight: $(window).height() - settings.top
            });

            self.$menu.parents('.uiContextualLayerPositioner').css(settings);
        },
        setItemMenu: function(data) {
            var li = $('<li/>').html(
                $('<a>', {
                    class: 'clearfix'
                }).append(
                    $('<span>', {
                        class: 'text',
                        text: data.text
                    })
                )
            );

            if (data.image_url) {
                li.addClass('picThumb');
                /*$('<div>', {class: 'avatar lfloat mrs'}).html( $('<img>', {src: URL + 'public/images/avatar/error/user2.png'}) )*/
            }

            if (data.activity == 'new') {
                li.addClass('new').find('.text').before(
                    $('<div>', {
                        class: 'box-icon'
                    }).append($('<i>', {
                        class: 'icon-plus'
                    }))
                );
            }

            li.data(data);

            return li;
        },

        updateCalendar: function(startDate, endDate) {
            var self = this;

            // start
            self.setDataStr();

            var $start = self.setCalendar(startDate || self.startDate);
            $start.addClass('start');
            $start.find('[data-date=' + self.startDateStr + ']').addClass('select-start');
            $start.find('[data-date=' + self.endDateStr + ']').addClass('select-end');

            self.$start.html($start);

            // end
            var $end = self.setCalendar(endDate || self.endDate);
            $end.addClass('end');
            $end.find('[data-date=' + self.startDateStr + ']').addClass('select-start');
            $end.find('[data-date=' + self.endDateStr + ']').addClass('select-end');
            self.$end.html($end);

            self.$preveiw.text(self.setTextCalendar());

            // event 
            $('td[data-date]', $start).click(function(e) {

                e.stopPropagation();

                var selected = new Date($(this).attr('data-date'));
                selected.setHours(0, 0, 0, 0);

                if (selected.getTime() > self.endDate.getTime()) {
                    return false;
                }

                self.startDate = selected;

                self.updateCalendar(selected, $end.data('date'));
            });
            $('td.prev, td.next', $start).click(function(e) {

                var offset = $(this).hasClass("prev") ? -1 : 1;
                var date = new Date($start.data('date'));
                date.setMonth(date.getMonth() + offset);

                self.updateCalendar(date, $end.data('date'));
                e.stopPropagation();
            });
            $('.selectMonth', $start).change(function(e) {

                var date = new Date($start.data('date'));
                date.setMonth($(this).val());

                self.updateCalendar(date, $end.data('date'));
                // e.stopPropagation();
            }).click(function(e) {
                e.stopPropagation();
            });
            $('.selectYear', $start).change(function(e) {

                var date = new Date($start.data('date'));
                date.setYear($(this).val());

                self.updateCalendar(date, $end.data('date'));
            }).click(function(e) {
                e.stopPropagation();
            });

            $('td[data-date]', $end).click(function(e) {

                e.stopPropagation();

                var selected = new Date($(this).attr('data-date'));
                selected.setHours(0, 0, 0, 0);

                if (selected.getTime() < self.startDate.getTime()) {
                    return false;
                }

                self.endDate = selected;
                self.updateCalendar($start.data('date'), selected);
            });
            $('td.prev, td.next', $end).click(function(e) {

                var offset = $(this).hasClass("prev") ? -1 : 1;
                var date = new Date($end.data('date'));
                date.setMonth(date.getMonth() + offset);

                self.updateCalendar($start.data('date'), date);
                e.stopPropagation();
            });

            $('.selectMonth', $end).change(function(e) {

                var date = new Date($end.data('date'));
                date.setMonth($(this).val());

                self.updateCalendar($start.data('date'), date);
                // e.stopPropagation();
            }).click(function(e) {
                e.stopPropagation();
            });

            $('.selectYear', $end).change(function(e) {

                var date = new Date($end.data('date'));
                date.setYear($(this).val());

                self.updateCalendar($start.data('date'), date);
            }).click(function(e) {
                e.stopPropagation();
            });
        },

        setDataStr: function() {
            var self = this;

            self.startDateStr = self.startDate.getFullYear();
            m = self.startDate.getMonth() + 1;
            self.startDateStr += "-" + (m < 10 ? "0" + m : m);
            d = self.startDate.getDate();
            self.startDateStr += "-" + (d < 10 ? "0" + d : d);

            self.endDateStr = self.endDate.getFullYear();
            m = self.endDate.getMonth() + 1;
            self.endDateStr += "-" + (m < 10 ? "0" + m : m);
            d = self.endDate.getDate();
            self.endDateStr += "-" + (d < 10 ? "0" + d : d);
        },
        updateData: function(text) {
            var self = this;

            self.setDataStr();
            if (!text) {
                text = self.setTextCalendar();
            }

            self.$text.text(text);

            self.$startInput.val(self.startDateStr);
            self.$endInput.val(self.endDateStr);

            if (typeof self.options.onChange == 'function') {

                self.options.onChange(self);
            }
        },

        setTextCalendar: function() {
            var self = this;

            var TO = '-'; //self.options.lang == 'th' ? ' à¸–à¸¶à¸‡ ' : ' to ';
            var $text = $('<span>');

            if (self.startDate.getDate() == self.endDate.getDate() && self.startDate.getMonth() == self.endDate.getMonth() && self.startDate.getFullYear() == self.endDate.getFullYear()) {

                $text.append(
                    self.endDate.getDate(), ' ', self.string.month(self.startDate.getMonth(), 'normal', self.options.lang), ' ', self.string.year(self.startDate.getFullYear(), 'normal', self.options.lang)
                );
            } else if (self.startDate.getMonth() == self.endDate.getMonth() && self.startDate.getFullYear() == self.endDate.getFullYear()) {
                $text.append(
                    self.startDate.getDate(), TO, self.endDate.getDate(), ' ', self.string.month(self.startDate.getMonth(), 'normal', self.options.lang), ' ', self.string.year(self.startDate.getFullYear(), 'normal', self.options.lang)
                );
            } else if (self.startDate.getFullYear() == self.endDate.getFullYear()) {
                $text.append(
                    self.startDate.getDate(), ' ', self.string.month(self.startDate.getMonth(), 'normal', self.options.lang)

                    , TO

                    , self.endDate.getDate(), ' ', self.string.month(self.endDate.getMonth(), 'normal', self.options.lang)

                    , ' ', self.string.year(self.startDate.getFullYear(), 'normal', self.options.lang)
                );
            } else {

                $text.append(
                    self.startDate.getDate(), ' ', self.string.month(self.startDate.getMonth(), 'normal', self.options.lang), ' ', self.string.year(self.startDate.getFullYear(), 'normal', self.options.lang)

                    , TO

                    , self.endDate.getDate(), ' ', self.string.month(self.endDate.getMonth(), 'normal', self.options.lang)

                    , ' ', self.string.year(self.endDate.getFullYear(), 'normal', self.options.lang)
                );
            }

            return $text.text();
        },

        setCalendar: function(date) {
            var self = this;

            var theDate = new Date(date);
            var firstDate = new Date(theDate.getFullYear(), theDate.getMonth(), 1);
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

            prevweekDay = prevweekDay > prevDateLastDay ? 7 - prevweekDay : prevDateLastDay - prevweekDay;

            var $tbody = $('<tbody>');
            var lists = [];
            for (var y = 0, i = 0; y < 7; y++) {

                var $tr = $('<tr>');
                var row = [];
                var weekInMonth = false;

                for (var x = 0; x < 7; x++, i++) {
                    var p = ((prevDateLastDate - prevweekDay) + i);

                    var call = {};
                    var n = p - prevDateLastDate;
                    call.date = new Date(theDate);
                    call.date.setHours(0, 0, 0, 0);
                    call.date.setDate(n);

                    call.date_str = call.date.getFullYear();
                    m = call.date.getMonth() + 1;
                    call.date_str += "-" + (m < 10 ? "0" + m : m);

                    d = call.date.getDate();
                    call.date_str += "-" + (d < 10 ? "0" + d : d);

                    $td = $('<td>');


                    if (self.startDate.getTime() > call.date.getTime() || self.endDate.getTime() < call.date.getTime()) {
                        $td.addClass('overtime');
                    }

                    // If value is outside of bounds its likely previous and next months
                    if (n >= 1 && n <= lastDay) {
                        weekInMonth = true;

                        $td.attr('data-date', call.date_str);

                        $td.append($('<span>', {
                            text: n
                        }));

                        if (self.today.getTime() == call.date.getTime()) {
                            $td.addClass('today');
                            call.today = true;
                        }

                        if (theDate.getTime() == call.date.getTime()) {

                            $td.addClass('selected');
                            call.selected = true;
                        }
                    } else {
                        call.noday = true;
                    }

                    /*if( self.date.startDate ){
                          	if( self.date.startDate.getTime()>call.date.getTime() ){
                          		call.empty = true;
                          	}
                          }*/

                    $tr.append($td);
                    row.push(call);
                }

                if (row.length > 0 && weekInMonth) {

                    // console.log( row );
                    $tbody.append($tr);
                    lists.push(row);
                }
            }

            var $selectMonth = $('<select>', {
                class: 'selectMonth'
            });
            for (var i = 0; i < 12; i++) {
                option = $('<option>', {
                    text: self.string.month(i, 'normal'),
                    value: i
                });

                //  && theDate.getFullYear() == 
                if (theDate.getMonth() == i) {
                    option.attr('selected', true);
                }

                $selectMonth.append(option);
            };


            var endYear = self.options.firstYear || self.today.getFullYear() - 5;
            var $selectYear = $('<select>', {
                class: 'selectYear'
            });
            for (var i = self.today.getFullYear(); i >= endYear; i--) {

                option = $('<option>', {
                    text: i,
                    value: i
                });

                if (theDate.getFullYear() == i) {
                    option.attr('selected', true);
                }
                $selectYear.append(option);
            };

            var $title = $('<thead>').html($("<tr>", {
                    class: 'title'
                })
                .append($('<td>', {
                    class: 'prev'
                }).append($('<i/>', {
                    class: 'icon-angle-left'
                })))
                .append($('<td>', {
                    class: 'title',
                    colspan: 5
                }).append($selectMonth, $selectYear))
                .append($('<td>', {
                    class: 'next'
                }).append($('<i/>', {
                    class: 'icon-angle-right'
                })))
            );


            var $header = $("<tr>", {
                class: 'header'
            });

            for (var x = 0, i = self.options.weekDayStart; x < 7; x++, i++) {
                $header.append($('<th>', {
                    text: self.string.day(i)
                }));
                if (i >= 6) {
                    i = -1;
                }
            };
            $thead = $('<thead/>').html($header);

            return $('<table/>', {
                class: 'calendarGridTable range',
                cellspacing: 0,
                cellpadding: 0,
            }).data('date', theDate).append($title, $thead, $tbody);
        },

        display: function() {
            var self = this;


            $('body').append(self.$calendar);
        },

        events: function() {
            var self = this;

            self.$menu.find('li').click(function() {

                var data = $(this).data();

                self.activeIndex = $(this).index();
                self._activeIndex();

                if (data.value == 'custom') {
                    self.hideMenu();
                    self.openCalendar();
                    return false;
                }

                self.selectMenu(data);
            });

            self.$btn.click(function(e) {

                $('body').find('.uiPopover').find('a.btn-toggle.active').removeClass('active');

                self.openMenu();
                self.resizeMenu();

                if (self.$calendar.hasClass('open')) {
                    self.$calendar.removeClass('open');
                }

                e.stopPropagation();
            });

            $(window).resize(function() {
                self.getOffset();
            });

            $('html').on('click', function() {

                // if( self.active ){
                self.hideMenu();
                self.hideCalendar();
                // }
            });

            $('.btn-submit', self.$calendar).click(function(e) {

                self.updateData();
            });
        },

        selectMenu: function(data) {
            var self = this;

            self.endDate = new Date(self.today);
            self.startDate = new Date(self.endDate);

            var minus = 0;
            if (data.value == 'last7days') {
                minus = 7;
            } else if (data.value == 'last14days') {
                minus = 14;
            } else if (data.value == 'last28days') {
                minus = 28;
            } else if (data.value == 'last90days') {
                minus = 90;
            } else if (data.value == 'yesterday') {

                self.endDate.setDate(self.endDate.getDate() - 1);
                self.startDate = new Date(self.endDate);

            } else if (data.value == 'weekly') {
                var first = self.today.getDate() - self.today.getDay();
                first += 1;
                var last = first + 6;

                self.startDate.setDate(first);
                self.endDate.setDate(last);

            } else if (data.value == 'last1week') {

                var first = self.today.getDate() - self.today.getDay();
                first -= 6;
                var last = first + 6;

                self.startDate.setDate(first);
                self.endDate.setDate(last);

            } else if (data.value == 'monthly') {
                self.startDate.setDate(1);
            }

            if (minus > 0) {
                self.startDate.setDate(self.startDate.getDate() - minus);
            }

            self.updateData(data.text);
        },

        _activeIndex: function() {
            var self = this;

            if (self.activeIndex == 'undefined') {
                self.$menu.find('li.active').removeClass('active');
            } else {
                self.$menu.find('li').eq(self.activeIndex).addClass('active').siblings().removeClass('active');
            }
        },

        openMenu: function() {
            var self = this;

            self.$layer.removeClass('hidden_elem');
        },
        hideMenu: function() {
            var self = this;

            self.$layer.addClass('hidden_elem');
        },

        openCalendar: function() {
            var self = this;

            self.updateCalendar();
            self.getOffset();
            // self.$btn.addClass('active');
            self.$calendar.addClass('open');

        },
        hideCalendar: function() {
            var self = this;

            // self.$btn.addClass('active');
            self.$calendar.removeClass('open');
        },

        getOffset: function() {
            var self = this;

            if (self.$calendar.hasClass('uiContextualAbove')) {
                self.$calendar.removeClass('uiContextualAbove');
            }

            var outer = $(document).height() < $(window).height() ? $(window) : $(document);

            var offset = self.$elem.offset(),
                outerWidth = $(window).width(),
                outerHeight = outer.height();

            var position = offset;

            position.top += self.$elem.outerHeight();

            var innerWidth = position.left + self.$calendar.outerWidth();
            if ($('html').hasClass('sidebarMode')) {
                innerWidth += 301;
            }

            if (innerWidth > outerWidth) {
                position.left = offset.left - self.$calendar.outerWidth() + self.$elem.outerWidth();
            }

            var innerHeight = position.top + self.$calendar.outerHeight();
            if (innerHeight > outerHeight) {
                position.top = offset.top - self.$calendar.outerHeight() - self.$elem.outerHeight();
                self.$calendar.addClass('uiContextualAbove');
            }

            self.$calendar.css(position);
        },

    };
    $.fn.closedate = function(options) {
        return this.each(function() {
            var $this = Object.create(CloseDate);
            $this.init(options, this);
            $.data(this, 'closedate', $this);
        });
    };
    $.fn.closedate.options = {
        lang: 'th',
        selectedDate: null,
        firstDate: null,
        start: null,
        end: null,
        weekDayStart: 1,
        type: 'short',
        format: '',
        options: [{
            text: 'Today',
            value: 'daily',
        }, {
            text: 'Yesterday',
            value: 'yesterday',
        }, {
            text: 'This week',
            value: 'weekly',
        }, {
            text: 'Last week',
            value: 'last1week',
        }, {
            text: 'This month',
            value: 'monthly',
        }, {
            text: 'Last 7 days',
            value: 'last7days', // weekly
        }, {
            text: 'Last 14 days',
            value: 'last14days',
        }, {
            text: 'Last 28 days28',
            value: 'last28days',
        }, {
            text: 'Last 90 days',
            value: 'last90days',
        }, {
            text: 'Custom',
            value: 'custom',
        }],
        onSelected: function() {},
    };


    /* input_label */
    var InputLabel = {
        init: function(options, elem) {
            var self = this;

            self.$elem = $(elem);

            self.$elem.find('.js-clone').click(function() {
                var $efirst = self.$elem.find('.control-group').first();

                var $clone = $efirst.clone();
                $clone.find('input, textarea').val('');

                if ($clone.find('select.labelselect').length == 0) {

                    var $wrap = $efirst.find('.labelselect').closest('.wrap');
                    var $select = $wrap.data('select');

                    $clone.find('.wrap').replaceWith($select);
                    $select.find('option').first().prop('selected', true);
                }

                self.$elem.find('[ref=listsbox]').append($clone);

            });

            self.$elem.delegate('select.labelselect', 'change', function() {

                if ($(this).val() == 'custom') {

                    var $wrap = $('<div>', {
                        class: 'wrap'
                    });
                    var $input = $('<input/>', {
                        type: 'text',
                        class: 'inputtext labelselect',
                        name: $(this).attr('name')
                    });
                    $remove = $('<button/>', {
                        type: 'button',
                        class: 'icon-remove js-remove-label'
                    });

                    $wrap.data('select', $(this)).append($input, $remove);

                    $(this).replaceWith($wrap);
                    $input.focus();
                }
            });

            self.$elem.delegate('.js-remove-label', 'click', function() {

                var $wrap = $(this).closest('.wrap');

                var $select = $wrap.data('select');
                $wrap.replaceWith($select.clone());

                $select.find('option').first().prop('selected', true);
            });


            self.$elem.delegate('.js-remove-field', 'click', function() {
                var $field = $(this).closest('.control-group');

                if ($field.parent().find('.control-group').length == 1) {
                    $field.find('input, textarea').val('');
                } else {
                    $field.remove();
                }
            });


            if (options.data) {

                var $efirst = self.$elem.find('.control-group').first();

                var c = 0;
                $.each(options.data, function(i, obj) {
                    c++;
                    var $clone = $efirst.clone();
                    $clone.find('input, textarea').val(obj.value);

                    $clone.find('.labelselect').val(obj.name);

                    var custom = false;
                    $.each($clone.find('.labelselect option'), function() {

                        if ($(this).val() == obj.name) {
                            custom = true;
                        }
                    });


                    if (!custom) {

                        var $select = $clone.find('.labelselect');
                        var $wrap = $('<div>', {
                            class: 'wrap'
                        });
                        var $input = $('<input/>', {
                            type: 'text',
                            class: 'inputtext labelselect',
                            name: $select.attr('name')
                        }).val(obj.name);
                        $remove = $('<button/>', {
                            type: 'button',
                            class: 'icon-remove js-remove-label'
                        });

                        $wrap.data('select', $select).append($input, $remove);

                        $select.replaceWith($wrap);
                        $input.focus();
                    }


                    self.$elem.find('[ref=listsbox]').append($clone);

                });

                if (c > 0) {
                    $efirst.remove();
                }

            }


        },

        fieldset: function(data, options) {

        }
    }

    $.fn.input_label = function(options) {
        return this.each(function() {
            var $this = Object.create(InputLabel);
            $this.init(options, this);
            $.data(this, 'input_label', $this);
        });
    };

    var Timestamp = {
        init: function(options, elem) {
            var self = this;

            self.theDate = options.theDate || $(elem).attr('data-time');
            self.original = elem;
            self.$elem = $('<span>', {
                class: 'timestamp'
            });

            $(elem).replaceWith(self.$elem);
        },

    };
    $.fn.timestamp = function(options) {
        return this.each(function() {
            var $this = Object.create(Timestamp);
            $this.init(options, this);
            $.data(this, 'timestamp', $this);
        });
    };


    var FormEditor = {
        init: function(options, elem) {
            var self = this;

            self.$elem = $(elem);

            // Event 
            self.$elem.find('.js-add-field').click(function() {

                var $field = $(this).closest('.form-field');

                var set = $field.find('.control-group').first().clone();
                set.find(':input').val('');
                set.find('select > option:first').prop('selected', true);

                $(this).before(set);
                self.checkStatus($field);
            });

            self.$elem.find('.labelselect').change(function() {
                var val = $(this).val();

                if (val == 'custom') {

                }
            });

            self.$elem.delegate('.js-input', 'change', function() {

                var box = $(this).closest('.form-field');
                if ($(this).val() == '') {
                    var length = box.find('.control-group').length;

                    if (length > 2) {

                        var box = $(this).closest('.control-group');
                        var index = box.index() + 1;

                        if (index != length) {
                            box.remove();
                        }
                    }
                }

                self.checkStatus(box);
            });

            $.each(self.$elem.find('.form-field'), function() {
                self.checkStatus($(this));
            });
        },

        checkStatus: function(box) {
            var self = this;

            var last = box.find('.js-input').last();
            box.find('.js-add-field').toggleClass('hidden_elem', last.val() == '');
        }

    };
    $.fn.formEditor = function(options) {
        return this.each(function() {
            var $this = Object.create(FormEditor);
            $this.init(options, this);
            $.data(this, 'formEditor', $this);
        });
    };

})(jQuery, window, document);