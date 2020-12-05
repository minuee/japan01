
$.fn.extend({
    "onlynumber": function()
    {
        $(this).css({'IME-MODE': 'disabled'});
        $(this).on("keypress", utils.onlynumber);
        $(this).on("keyup", utils.onlynumber);
        $(this).on("blur", utils.onlynumber);
    },
    "required": function(message, callback)
    {
        $(this).focus();
        if (!utils.empty(message)) {
            alert(message);
        }
        if (callback) {
            callback($(this));
        }
        return false;
    },
    "defaultValue": function()
    {
        var element = $(this).get(0);
        var value = undefined;
        if (['checkbox', 'radio'].exist(element.type)) {
            if (element.defaultChecked == true) {
                value = element.value;
            }
        } else if (['select-one', 'select-multiple'].exist(element.type)) {
            for (var j = 0; j < element.options.length; j++) {
                if (!utils.empty(element.options[j].value)) {
                    if (element.options[j].defaultSelected == true) {
                        value = element.options[j].value;
                        break;
                    }
                } else {
                    value = '';
                }
            }
        } else {
            value = element.defaultValue;
        }
        return value;
    } ,
    "isFormChanged": function()
    {
        var elements = this[0].elements;
        var is = false;
        for (var i in elements) {
            if (['checkbox', 'radio'].exist(elements[i].type)) {
                if (elements[i].checked !== elements[i].defaultChecked) {
                    is = true;
                    break;
                }
            } else if (['select-one', 'select-multiple'].exist(elements[i].type)) {
                for (var j = 0; j < elements[i].options.length; j++) {
                    if (!utils.empty(elements[i].options[j].value)) {
                        if (elements[i].options[j].selected !== elements[i].options[j].defaultSelected) {
                            is = true;
                            break;
                        }
                    }
                }
                if (is) {
                    break;
                }
            } else {
                if (elements[i].value) {
                    if (elements[i].value != elements[i].defaultValue) {
                        is = true;
                        break;
                    }
                }
            }
        }
        return is;
    },
    "layer": function(mode, callback)
    {
        var $layer = $(this).closest('.layer-area').css({"z-index": 1000});
        if (mode == 'open') {
            var maskWidth = $(window).width();
            var maskHeight = $(window).height();
            $('body').removeClass('no-scroll').addClass('no-scroll');
            $('#dim-layer').css({"width": maskWidth, "height": maskHeight}).show();
            $layer.show();
        }
        if (mode == 'close') {
            $layer.hide();
            if ($('.layer-area:visible').length == 0) {
                $('body').removeClass("no-scroll");
                $('#dim-layer').hide();
            }
        }
        if (callback) {
            callback($layer);
        }
        return $layer;
    },
    "getValues": function(attribute) {
        var result = [];
        $(this).each(function() {
            var value = $(this).val();
            if (attribute) {
                value = $(this).attr(attribute);
            }
            result.push(value);
        });
        return result;
    },
    "getCursorPosition": function() {
        var el = $(this).get(0);
        var pos = 0;
        if ("selectionStart" in el) {
            pos = el.selectionStart;
        } else if("selection" in document) {
            el.focus();
            var sel = document.selection.createRange()
            var selLength = document.selection.createRange().text.length;
            sel.moveStart("character", -el.value.length);
            pos = sel.text.length - selLength;
        }
        return pos;
    },
    "appendIcons": function(is_admin, is_teacher, is_notice, is_spam, is_forbidden, is_block, is_block_history, is_hidden) {
        if (is_forbidden == 1) {
            var $icon = $(ui.templates.icons.forbidden);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_spam == 1) {
            var $icon = $(ui.templates.icons.spam);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_hidden == 1) {
            var $icon = $(ui.templates.icons.hidden);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_block_history == 1) {
            var $icon = $(ui.templates.icons.block_history);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_block == 1) {
            var $icon = $(ui.templates.icons.block);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_teacher == 1) {
            var $icon = $(ui.templates.icons.teacher);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_notice == 1) {
            var $icon = $(ui.templates.icons.notice);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        if (is_admin == 1 && is_notice == 0) {
            var $icon = $(ui.templates.icons.admin);
            $icon.insertAfter($(this).find('.clickLayerMenu'));
        }
        return $(this);
    },
    "frontIcons": function(is_teacher, is_notice) {
        if (is_teacher == 1) {
            var $icon = $(ui.templates.icons.teacher);
            $icon.insertAfter($(this).find('strong'));
        }
        if (is_notice == 1) {
            var $icon = $(ui.templates.icons.notice);
            $icon.insertAfter($(this).find('strong'));
        }
        return $(this);
    },
    "timer": function(current, target, end_callback) {
        var $this = $(this);
        var remaining_time = (new Date(target).getTime() / 1000) - (new Date(current).getTime() / 1000);
        if ($(this).data('timer')) {
            clearInterval($(this).data('timer'));
        }
        if (remaining_time > 0) {
            var timer = setInterval(function() {
                remaining_time--;
                if (remaining_time < 0) {
                    clearInterval(timer);
                    $this.hide();
                    if (end_callback) {
                        end_callback();
                    }
                } else {
                    if ($this.is(':hidden')) {
                        $this.show();
                    }
                    var hours = Math.floor(remaining_time / 3600);
                    var minutes = Math.floor((remaining_time - (hours * 3600)) / 60);
                    var seconds = remaining_time - (hours * 3600) - (minutes * 60);
                    var format = ((hours < 10) ? utils.mask('00', hours) : hours) +':'+ utils.mask('00', minutes) +':'+ utils.mask('00', seconds);
                    $this.html(format);
                }
            }, 1000);
            $(this).data('timer', timer);
        }
        return $(this);
    },
    "clearTimer": function() {
        if ($(this).data('timer')) {
            clearInterval($(this).data('timer'));
        }
    },
    "resizeImage": function(max_width, max_height) {
        var $this = $(this);
        function size() {
            var width   = $this.prop('naturalWidth');
            var height  = $this.prop('naturalHeight');
            var ratio = 1;
            if (width > max_width) {
                ratio = max_width / width;
            }
            if (height > max_height) {
                ratio = (max_height / height) < ratio ? max_height / height : ratio;
            }
            $this.css({"width": width * ratio, "height": height * ratio, "display": 'block', "margin-left": 'auto', "margin-right": 'auto'});
        }
        if ($(this).data('loaded')) {
            size();
        } else {
            $(this).one('load', function() {
                $(this).data('loaded', true);
                size();
            });
        }
    }
});