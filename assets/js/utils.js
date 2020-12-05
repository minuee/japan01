
var Utils = function() {};

window.utils = new Utils();

/**
 * @param name
 * 쿠키 정보 가져오기
 */
Utils.prototype.getCookie = function(name) {
    name = name + '=';
    var cookieData = document.cookie;
    var start = cookieData.indexOf(name);
    var value = '';
    if(start != -1){
        start += name.length;
        var end = cookieData.indexOf(';', start);
        if(end == -1) {
            end = cookieData.length;
        }
        value = cookieData.substring(start, end);
    }
    return value;
};

/**
 *
 * @param name
 * @param value
 * @param expiredays
 * 쿠키 저장
 */
Utils.prototype.setCookie = function(name, value, expiredays) {
    var today = new Date();
    today.setTime(today.getTime() + 1000 * 60 * 60 * parseInt(expiredays) );
    document.cookie = name + '=' + escape(value) + '; path=/; expires=' + today.toGMTString() + '; domain=.'+ utils.options.domain
};

/**
 * Timestamp
 * @returns {number}
 */
Utils.prototype.timestamp = function() {
    return Math.round(+new Date()/1000);
};

/**
 * 공백제거
 * @param value
 */
Utils.prototype.trim = function(value)
{
    return utils.replaceAll(utils.replaceAll(value, ' ', ''), '\n', '');
};

/**
 * 빈값 여부
 * @param value
 * @returns {boolean}
 */
Utils.prototype.empty = function(value)
{
    return (utils.trim(value) == '' || value == null || value == undefined);
};

/**
 * 문자열 치환
 * @param str
 * @param org
 * @param dest
 * @returns {string}
 */
Utils.prototype.replaceAll = function(str, org, dest) {
    return String(str).split(org).join(dest);
};

/**
 * 문자열의 지정된 포맷{key}을 JSON 으로 치환
 * @param string
 * @param params
 * @returns {*}
 */
Utils.prototype.replace = function(string, _params) {
    var params = $.extend({}, _params);
    var matches = [];
    var match;
    var delete_keys = [];
    var regexp = new RegExp("\{.*?\}", "g");
    while ((match = regexp.exec(string)) != null) {
        matches.push(match[0]);
    }
    for(var i in matches) {
        var key = "";
        if (typeof matches[i] == "string") {
            key = matches[i].replace("{", "").replace("}", "");
            if (string.match("{"+ key +"}")) {
                if (params[key] != undefined && String(params[key]) != "") {
                    string = string.replace(new RegExp(matches[i], 'gi'), params[key]);
                    delete_keys.push('{'+ key +'}');
                }
            } else {
                key = matches[i].replace("{", "").replace("?}", '');
                if (string.match("{"+ key +"\\?}")) {
                    if (params[key] != undefined && String(params[key]) != "") {
                        string = string.replace(new RegExp("{"+ key +"\\?}", 'gi'), params[key]);
                    } else {
                        string = string.replace(new RegExp("{"+ key +"\\?}", 'gi'), '');
                    }
                    delete_keys.push('{'+ key +'?}');
                }
            }
        }
    }
    matches = matches.filter(function(data) {
        for (var i in delete_keys) {
            if (data == delete_keys[i]) {
                return false;
            }
        }
        return true;
    });
    if (matches.length > 0) {
        utils.abort("Required parameter missing ["+ matches.join(",") +"]");
    }
    return string;
};

/**
 * @param fs: ****
 * @param s: 11
 * 마스킹 : **11
 */
Utils.prototype.mask = function(fs, s) {
    var string = fs.toString() + s.toString();
    var size = ( fs.length > s.toString().length ) ? fs.length : s.toString().length;
    return string.substr(s.toString().length, size);
};

Utils.prototype.comma = function(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

/**
 * 날짜 포멧
 * @param format
 * @param dateString
 * @returns {void|XML|string|*}
 */
Utils.prototype.dateFormat = function(format, date) {
    var d;
    if (date == undefined || date == "" || date == null) {
        d = new Date();
    } else {
        if (typeof date == "string") {
            d = utils.stringToDate(date);
        } else {
            d = date;
        }
    }
    return format.replace(/(yyyy|yy|MM|dd|E|hh|mm|ss|a\/p)/gi, function($1) {
        switch ($1) {
            case "yyyy": return d.getFullYear(); break;
            case "yy": return utils.mask("00", d.getFullYear() % 1000); break;
            case "MM": return utils.mask("00", d.getMonth() + 1); break;
            case "dd": return utils.mask("00", d.getDate()); break;
            case "HH": return utils.mask("00", d.getHours()); break;
            case "mm": return utils.mask("00", d.getMinutes()); break;
            case "ss": return utils.mask("00", d.getSeconds()); break;
            case "a/p": return d.getHours() < 12 ? "오전" : "오후"; break;
            default: return $1;
        }
    });
};

/**
 * @param dateString
 * @returns {Date}
 */
Utils.prototype.stringToDate = function(dateString) {
    dateString = dateString.replace(/-/g, '/');
    dateString = dateString.replace('T', ' ');
    return new Date(dateString.replace(/(\+[0-9]{2})(\:)([0-9]{2}$)/, ' UTC\$1\$3'));
};

/**
 * 휴대폰 번호 유효성 검사
 * @param phoneNumber
 * @returns {boolean}
 */
Utils.prototype.isMobilePhoneNumber = function(phoneNumber) {
    return /^(01[016789]{1})([0-9]{3,4})([0-9]{4})$/.test(phoneNumber);
};

/**
 * HTML 제거
 * @param html
 * @returns {string|*}
 */
Utils.prototype.stripTags = function(html) {
    html = html.replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, '');
    html = utils.replaceAll(html, '&nbsp;', '');
    return html;
};

Utils.prototype.xss = function(str) {
    str = str.replace(/</g, '&#60;');
    str = str.replace(/>/g, '&#62;');
    return str;
};

Utils.prototype.link = function(text) {
    // var regexp = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/gi;
    var regexp = /(?:(?:(https?|ftp|telnet):\/\/|[\s\t\r\n\[\]\`\<\>\"\'])((?:[\w$\-_\.+!*\'\(\),]|%[0-9a-f][0-9a-f])*\:(?:[\w$\-_\.+!*\'\(\),;\?&=]|%[0-9a-f][0-9a-f])+\@)?(?:((?:(?:[a-z0-9\-가-힣]+\.)+[a-z0-9\-]{2,})|(?:[\d]{1,3}\.){3}[\d]{1,3})|localhost)(?:\:([0-9]+))?((?:\/(?:[\w$\-_\.+!*\'\(\),;:@&=ㄱ-ㅎㅏ-ㅣ가-힣]|%[0-9a-f][0-9a-f])+)*)(?:\/([^\s\/\?\.:<>|#]*(?:\.[^\s\/\?:<>|#]+)*))?(\/?[\?;](?:[a-z0-9\-]+(?:=[^\s:&<>]*)?\&)*[a-z0-9\-]+(?:=[^\s:&<>]*)?)?(#[\w\-]+)?)/gmi;
    text = text.replace(regexp, function(url) {
        return '<a href="'+ url +'" target="_blank">'+ url +'</a>';
    });
    return text;
};

/**
 * Input 숫자만 + (소수점) 입력
 * @param e
 * @returns {boolean}
 */
Utils.prototype.onlynumber = function(e)
{
    var code = e.which ? e.which : e.keyCode;
    var allow_codes = [8, 27, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
    var decimal_codes = [110, 190];
    var is_decimal = JSON.parse(e.target.getAttribute('decimal')) || false;
    var size = e.target.getAttribute('maxlength') || 0;
    var max = e.target.getAttribute('max') || 0;
    // 소수 사용 시
    if (is_decimal == true) {
        allow_codes = allow_codes.concat(decimal_codes);
        e.target.value = e.target.value.replace(/[^0-9-.]/g, '');
        if (e.target.value.length > 0) {
            if (e.target.value.split('.').length > 2) {
                var arr = e.target.value.split('.');
                var s = arr[0]; delete arr[0];
                e.target.value = [s, arr.join('')].join('.');
                return false;
            }
            if (e.target.value.charAt(0) == '.') {
                e.target.value = utils.replaceAll(e.target.value, '.', '');
                return false;
            }
            if (e.target.value.charAt(e.target.value.length - 1) == '.' && e.target.value.length == size) {
                e.target.value = utils.replaceAll(e.target.value, '.', '');
                return false;
            }
            if (e.type == 'blur') {
                if (e.target.value.charAt(e.target.value.length - 1) == '.') {
                    e.target.value = utils.replaceAll(e.target.value, '.', '');
                    return false;
                }
            }
        }
    } else {
        if (e.target.value.length > 0) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = (isNaN(parseInt(e.target.value))) ? '' : e.target.value;
            if (max > 0) {
                if (parseInt(e.target.value) >= max) {
                    e.target.value = max;
                    return false;
                }
            }
        }
    }
    if (!allow_codes.exist(code)) {
        return false;
    }
};

/**
 * Ajax (로딩 구현)
 * @param options
 * @returns {*}
 */
Utils.prototype.ajax = function(options)
{
    utils.loading.open();
    try {
        var params = $.extend({}, options.data);
        return $.ajax({
            "url": utils.replace(options.url, params),
            "type": options.type,
            "cache" : (options.cache) ? true : false,
            "sync": (options.sync) ? options.sync : true,
            "contentType": (options.contentType) ? options.contentType : 'application/json',
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "dataType": (options.dataType) ? options.dataType : 'json',
            "data": (options.type.toLowerCase() == 'get') ? JSON.parse(JSON.stringify(params)) : JSON.stringify(params),
            "success": options.success,
            "error": function(e) {
                if (options.error) {
                    options.error(e);
                } else {
                    if (e.status == 401) {
                        alert("권한이 없거나 로그아웃 되었습니다.\n로그인 후 다시 시도해 주세요.");
                        location.reload();
                    } else {
                        alert("관리자에게 문의하세요. ("+ e.status +" : "+ e.statusText +")");
                    }
                }
            },
            "complete": function() {
                utils.loading.close();
            }
        });
    } catch (e) {
        utils.loading.close();
    }
};

/**
 * 로딩
 * @type {{open: Utils.loading.'open', close: Utils.loading.'close'}}
 */
Utils.prototype.loading = {
    'open' : function() {
        $('#loading').css({"z-index": 2000}).show();
    },
    'close': function() {
        $('#loading').hide();
    }
};

Utils.prototype.player = function(url, width, height)
{
    var html =
        "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='100%' height='100%' id='player1' name='player1'>"+
        "   <param name='movie' value='/common/mediaplayer-5.9/player.swf'>"+
        "   <param name='allowfullscreen' value='true'>"+
        "   <param name='allowscriptaccess' value='always'>"+
        "   <param name='flashvars' value='autostart=true&type=rtmp&streamer="+ url +"'>"+
        "   <embed id='player1'"+
        "          name='player1'"+
        "          src='/common/mediaplayer-5.9/player.swf'"+
        "          width='100%'"+
        "          height='100%'"+
        "          allowscriptaccess='always'"+
        "          allowfullscreen='true'"+
        "          wmode='Opaque'"+
        "          controls='none'"+
        "          flashvars='autostart=true&type=rtmp&streamer="+ url +"'"+
        "   />"+
        "</object>";
    return $(html);
};

Utils.prototype.video = function(url, width, height)
{
    var html =
        "<video width='"+ width +"' height='"+ height +"'>" +
        "   <source src='"+ url +"'/>" +
        "</video>";
    return $(html);
};

/**
 * Window 팝업
 * @param url
 * @param width
 * @param height
 * @param top
 * @param left
 */
Utils.prototype.popup = function(url, width, height, top, left)
{
    var props = 'width={width}, height={height}, top={top}, left={left}, resizable=no, status=no, menubar=no';
    if (top == undefined) {
        top = (screen.availHeight/ 2) - (height / 2);
    }
    if (left == undefined) {
        left = (screen.availWidth / 2) - (width / 2);
    }
    if (window.screenLeft != 0) {
        left += window.screenLeft;
    }
    window.open(url, url, utils.replace(props, {'width': width, 'height': height, 'top': top, 'left': left}));
};

/**
 * @param message
 * 프로세스 중단
 */
Utils.prototype.abort = function(message) {
    throw new Error(message);
};


/****************************************************************
 * Prototype: Array
 ***************************************************************/
/**
 * @param value
 * @returns {*} index
 * 배열 값의 존재 여부
 */
Array.prototype.exist = function (value) {
    for (var i in this) {
        if (this[i] == value) {
            return i;
        }
    }
    return undefined;
};