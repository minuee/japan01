//----------------------
//숫자만 입력
//ex $("input.number").digits();
//----------------------
$.fn.digits = function(){
    $(this).keyup(function(event) {
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
            return value.replace(/\D/g, "");
        });
    });
};


//----------------------
//영어소문자, 숫자만 입력
//ex $("input.alphanum").alphaDigits();
//----------------------
$.fn.alphaDigits = function(){
    $(this).keyup(function(event) {
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
            return value.replace(/[~!@\#$%^&*\()|\-{}\[\]=+,.;:'\/\\]|[\ㄱ-ㅎㅏ-ㅣ가-힣]/gi, "").toLowerCase();
        });
    });
};

//----------------------
// , 붙여서 return
//----------------------
function commify(value){
    return value.toString().replace(/\D/g, "")
        .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}

//----------------------
// array -> json 으로 변환
//----------------------
function arrToJson(data) {
    var json = {};
    $.each(data, function(idx, ele){
        json[ele.name] = ele.value;
    });

    return json;
}

//----------------------
// checkbox 전체선택/해제
//----------------------
function checkAll(target_field, click_field) {
    var $target_obj = $('input[name="' + target_field + '"]');
    var $click_obj = $('input[name="' + click_field + '"]');

    $target_obj.prop('checked', $click_obj.eq(0).is(':checked'));
}

//----------------------
// ajax function
// data는 is_file = false인 경우는 seialize된 데이터 (json)
// is_file = true인 경우는 FormData로 보내줄것
//----------------------
function sendAjax(url, data, callback, error_callback, async, method, data_type, is_file)
{

    $("button, .btn").prop("disabled",true);
    if(typeof is_file == 'undefined') is_file = false;
    var process_data = true;
    var content_type = 'application/x-www-form-urlencoded; charset=UTF-8';
    if(is_file){
        process_data = false;
        content_type = false;
        method = method=='GET' ? 'POST' : method; // file upload는 get 방식 불가
    }

    $.ajax({
        type: ((typeof method != 'undefined') ? method : 'POST'),
        url: url,
        data: data,
        async: (typeof async != 'undefined') ? async : false,
        processData: process_data,
        contentType: content_type,
        dataType: (typeof data_type != 'undefined') ? data_type : 'json',
        success : function (data, status, req) {
            if(typeof callback === "function") {
                callback(data);
            }
            $("button, .btn").prop("disabled", false);
        }
        ,error : function(req, status, err) {
        if(typeof error_callback === "function") {
            error_callback(JSON.parse(req.responseText), req.status);
        }
        $("button, .btn").prop("disabled", false);
        }
    });
}


//----------------------
//json 을 get 방식 query 로
//----------------------
function jsonToQueryString(json) {
    return '?' +
        Object.keys(json).map(function(key) {
            return encodeURIComponent(key) + '=' + encodeURIComponent(json[key]);
        }).join('&');
}

//----------------------
//popup open
//----------------------
popupWins = new Array();
function popupOpen(url, name, width, height, xpos, ypos){
    try{
        name =  name || '_blank';
        xpos = xpos || (screen.availWidth-width)/2;
        ypos = ypos || (screen.availHeight-height)/2;

        if ( typeof( popupWins[name] ) != "object" ){
            popupWins[name] = window.open(url, name, 'width='+width+', height='+height+', left='+xpos+', top='+ypos+', menubar`o, status=no, toolbar=no, scrollbars=no, resizable=yes');
        } else {
            if (!popupWins[name].closed){
                popupWins[name].location.href = url;
            } else {
                popupWins[name] = window.open(url, name, 'width='+width+', height='+height+', left='+xpos+', top='+ypos+', menubar=no, status=no, toolbar=no, scrollbars=no, resizable=yes');
            }
        }

    }catch(e){

    }
}

var stack_center = {"dir1": "down", "dir2": "right", "firstpos1": ($(window).height()/2 -150), "firstpos2": ($(window).width()/2 - 150)};
/*----------------------
* notify 창 (type : error, success, info, warning)
* parameter center 추가 -> true 일때만 창이 센터에서 표시
* parameter hide 조건 값에 null 추가
* ex1) notifyAlert('info', '알림', '내용을 입력하세요');
* ex2) notifyAlert('info', '알림', '내용을 입력하세요', null, null, true);
//----------------------*/
function notifyAlert(type, title, text, delay, hide, center){
    title = title || "알림";
    delay = delay || 3000;
    hide = (typeof(hide) == "undefined" || hide == null) ? true : hide;
    center = (center == true)? stack_center : "";

    new PNotify({
        title:title,
        text:text,
        type:type,
        hide: hide,
        delay:delay,
        stack: center
    });
}


// ----------------------
// 검색 레이어 팝업
// ----------------------
// * url : 레이어 팝업 url
// * select_type : radio : 1개만 선택 가능, checkbox : multi 선택 가능, checkbox-callback:
// * pk_name : 선택한 데이터의 pk hidden input 의 name
// * label_name : 선택한 데이터의 name 데이터 input의 name
// * add_param_type : input, attr, attr_url
//      - input : add_param json데이터의 id 값을 hidden의 id로 찾아 해당 값을 ajax에 get 파라메터로 전달
//      - attr : add_param json데이터의 id 값을 data attribute의 key로 찾아 해당 값을 ajax에 get 파라메터로 전달
//      - attr_url : add_param json데이터의 id 값을 data attribute의 key로 찾아 해당 값을 url에 /로 연결하여 전달
// * add_param : 추가로 전달 하는 데이터 설정
// * width : 레이어 팝업 넓이
// * max_height : overflow가 scroll, hidden 일때만 사용됨
// * overflow가: overflow css 값
// * callback : 선택 이 후 호출되는 callback ㅎ마수
/* ex)
$('#product_search_btn').setSearchLayer({
    'url' : '/billing/common/layer/product',
    'select_type' : 'radio',
    'selected_area_id' : 'product_area',
    'pk_name' : 'product_idx',
    'label_name' : 'product_name',
    'add_param_type' : 'input'
    'add_param' : [{'id' : 'teacher_id', 'required':true}],
});
 */
// ----------------------
(function($){
    var modal_html = '<div class="modal fade" id="pop_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">empty</div></div></div>';

    $.fn.setSearchLayer = function(options){
        var settings = $.extend({
            url : "",
            select_type : "radio", // radio, checkbox, checkbox-callback
            selected_area_id : "selected_area", // #selected_area > radio, checebox에서사용
            pk_name : "idx",
            label_name : "label",
            add_param_type : 'input',  // 추가로 넘겨주는 값  ex) [{id:'service_id', 'name':'서비스아이디', required':true}
            add_param : [],  // 추가로 넘겨주는 값  ex) [{id:'service_id', 'name':'서비스아이디', required':true}
            width : "620",
            max_height : "400", // overflow가 scroll / hidden 일 때만 쓰임
            overflow : "scroll",
            backdrop : true,

            callback : function(settings, data){
                var selected_area = $('#'+settings.selected_area_id);

                if(settings.type == 'radio') {
                    var idx = data.idx;
                    var label = data.label;

                    if(selected_area.find('[name="'+settings.pk_name+'"]').length >0){
                        selected_area.find('[name="'+settings.pk_name+'"]').remove();
                        selected_area.find('[name="'+settings.label_name+'"]').remove();
                    }

                    var html = '';
                    html += '<input type="hidden" name="'+settings.pk_name+'" value="'+idx+'"/>';
                    html += '<input type="text" class="form-control" readonly="readonly" name="'+settings.label_name+'" value="'+label+'"/>';
                    selected_area.append(html);
                } else if(settings.type == 'checkbox') {
                    var _data_length = data.length;
                    for(var i=0; i<_data_length; i++){
                        var _data = data[i];
                        var idx = _data.idx;
                        var label = _data.label;
                        if(selected_area.find('[name="'+settings.pk_name+'"][value="'+idx+'"]').length < 1){
                            var html = '';
                            html += '<input type="hidden" name="'+settings.pk_name+'" value="'+idx+'"/>';
                            html += '<input type="text" class="form-control" readonly="readonly" name="'+settings.label_name+'" value="'+label+'"/>';
                            selected_area.append(html);
                        }
                    } // end for
                }
            } // end callback
        }, options);

        this.css('cursor','pointer');

        if(settings.add_param.constructor !== Array) {
            settings.add_param = [settings.add_param];
        }

        $(document).off("click", this.selector);
        $(document).on("click", this.selector ,function() {

            var event_obj = $(this);

            for (var i = settings.add_param.length - 1; i >= 0; i--) {
                var _param = settings.add_param[i];

                if(_param.required) {
                    var _param_value = '';
                    if(settings.add_param_type == 'input' && $("#"+_param.id).length >0)
                        _param_value = $("#"+_param.id).val();
                    else if(settings.add_param_type == 'attr_param' || settings.add_param_type == 'attr_url')
                        _param_value = event_obj.attr('data-'+_param.id);

                    if(_param_value == '') {
                        alert(_param.name + '을 먼저 선택 하셔야 야 합니다.');
                        return false;
                    }
                }
            }

            event_obj.after(modal_html);
            var pop_modal = $("#pop_modal");

            pop_modal.modal({
                show: 'false',
                backdrop: settings.backdrop
            }).on('hidden.bs.modal', function(){
                $(this).remove();
            });

            var _callback = function(d){
                pop_modal.find(".modal-content").html(d).end()
                    .find(".modal-dialog").css("width",settings.width+"px").end();

                if(settings.overflow == 'scroll' || settings.overflow == 'hidden') {
                    pop_modal.find(".modal-content .box-body").css({
                        "overflow-y" : settings.overflow
                        , "max-height" : settings.max_height+"px"
                    });
                }

                if(settings.select_type == 'radio'){ // 1개 선택
                    pop_modal.find(".modal-content").on("click", ".pop-select-btn", function(event) {
                        event.preventDefault();
                        var data = $(this).data();
                        settings.callback(settings, data);
                        pop_modal.modal("toggle");
                        if(event_obj.is('input:text')) event_obj.trigger("change");
                        return false;
                    });
                } else if(settings.select_type == 'checkbox') { // 복수개 선택
                    pop_modal.find(".modal-content").on("click", "#return-check-btn", function(event) {
                        event.preventDefault();
                        var data = [];

                        pop_modal.find('input:checkbox[name="check_'+settings.pk_name+'[]"]:checked').each(function() {
                            data.push($(this).data());
                        });

                        settings.callback(settings, data);
                        pop_modal.modal("toggle");
                        if(event_obj.is('input:text')) event_obj.trigger("change");
                        return false;
                    });
                } else if(settings.select_type == 'checkbox-callback') { // 복수개 선택 콜백
                    pop_modal.find(".modal-content").on("click", "#return-check-btn", function(event) {
                        event.preventDefault();
                        var data = [];

                        pop_modal.find('input:checkbox[name="check_'+settings.pk_name+'[]"]:checked').each(function() {
                            data.push($(this).data());
                        });

                        settings.callback(settings, data);
                        // pop_modal.modal("toggle");
                    });
                }

                pop_modal.find(".modal-content").on("click", "#close_modal_btn", function(event) {
                    event.preventDefault();
                    pop_modal.modal("toggle");
                    return false;
                });
            };

            var _url = settings.url;
            var _data = {};
            for (var i = 0; i < settings.add_param.length; i++) {
                var _param = settings.add_param[i];
                var _param_value = null;
                if(settings.add_param_type == 'input'){
                    _param_value = $("#"+_param.id).val();
                    if(_param_value) _data[_param.id] = _param_value;
                } else if(settings.add_param_type == 'attr_param') {
                    _param_value = event_obj.attr('data-'+_param.id);
                    if(_param_value) _data[_param.id] = _param_value;
                } else if(settings.add_param_type == 'attr_url') {
                    _param_value = event_obj.attr('data-'+_param.id);
                    _url += '/' + _param_value
                }
            }

            sendAjax(_url, _data, _callback, function(req, status, err){
                if( status === 401 ){  //권한 없음 || 미로그인
                    notifyAlert('error', '알림', '권한이 없습니다.');
                    pop_modal.modal("toggle");
                    return false;
                }
            }, false, 'GET', 'html');
        });

        return this;
    }
}(jQuery));



// ----------------------
// Layer popup
// * 모달 내  닫기 버튼이 있는 경우 id를 close_modal_btn로 줄것
// ex)
// $('#regist_btn').setLayer({
//     "url" : "/billing/service/form",
//     "add_param" : [
//         {'id' : 'service_type', 'required' : true, 'name':'서비스 아이디'}
//     ]
// });
// ----------------------
(function($){
    var modal_html = '<div class="modal fade" id="pop_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><center>...</center></div></div></div>';

    $.fn.setLayer = function(options){
        var settings = $.extend({
            url : "",
            // add_param_type : add_param을 가져올 유형
            // 1) input : ID가 name 파라메터인 input을 찾아 해당 value를 ajax get의 파라메터로 으로 함께 넘겨준다.
            // 2) attr_param : 클릭된 버튼의 data-ID 어트리뷰트의 value를 ajax get 의 파라메터로 함께 넘겨준다.
            // 3) attr_url : 클릭된 버튼의 data-ID 어트리뷰트의 value를 ajax get의 url로 함께 넘겨준다. url/param1_value/param2_value -> 순서는 add_param 순서이다.
            add_param_type : 'input',  // 추가로 넘겨주는 값  ex) [{id:'service_id', 'name':'서비스아이디', required':true}
            add_param : [],  // 추가로 넘겨주는 값  ex) [{id:'service_id', 'name':'서비스아이디', required':true}
            width : "620",
            max_height : "400", // scroll/hidden 일 때만 쓰임
            overflow : "auto",
            backdrop : true
        }, options);
        this.css('cursor','pointer');

        if(settings.add_param.constructor !== Array) {
            settings.add_param = [settings.add_param];
        }
//        event.preventDefault();
        var event_btn = $(this);

        for (var i = settings.add_param.length - 1; i >= 0; i--) {
            var _param = settings.add_param[i];
            if(_param.required){
                var _param_value = '';
                if(settings.add_param_type == 'input' && $("#"+_param.id).length >0)
                    _param_value = $("#"+_param.id).val();
                else if(settings.add_param_type == 'attr_param' || settings.add_param_type == 'attr_url')
                    _param_value = event_btn.attr('data-'+_param.id);

                if(_param_value == ''){
                    alert(_param.name + '을 먼저 선택 하셔야 야 합니다.');
                    return false;
                }
            }
        }

        $('body').append(modal_html);
        var pop_modal = $("#pop_modal");

        pop_modal.modal({
            show: 'false',
            backdrop: settings.backdrop
        }).on('hidden.bs.modal', function(){
            $(this).remove();
        });

        var callback = function(d){

            if(d=='fail') {
                notifyAlert('error', '알림', '표시할 내역이 없습니다.');
                pop_modal.modal("toggle");
            } else if(d.substr(0, 3) == 'ERR') {
                notifyAlert('error', '에러', d.substr(4));
                pop_modal.modal("toggle");
            } else {
                pop_modal.find(".modal-content").html(d).end()
                    .find(".modal-dialog").css("width",settings.width+"px").end();
                if(settings.overflow == 'scroll' || settings.overflow == 'hidden') {
                    pop_modal.find(".modal-content .box-body").css({
                        "overflow-y" : settings.overflow
                        , "max-height" : settings.max_height+"px"
                    });
                }

                pop_modal.find(".modal-content").on("click", "#close_modal_btn", function(event){
                    pop_modal.modal("toggle");
                    event.preventDefault();
                    return false;
                });
            }

        };

        var _url = settings.url;
        var _data = {};
        for (var i = 0; i < settings.add_param.length; i++) {
            var _param = settings.add_param[i];
            var _param_value = null;
            if(settings.add_param_type == 'input'){
                _param_value = $("#"+_param.id).val();
                if(_param_value) _data[_param.id] = _param_value;
            } else if(settings.add_param_type == 'attr_param') {
                _param_value = event_btn.attr('data-'+_param.id);
                if(_param_value) _data[_param.id] = _param_value;
            } else if(settings.add_param_type == 'attr_url') {
                _param_value = event_btn.attr('data-'+_param.id);
                _url += '/' + _param_value
            }
        }

        sendAjax(_url, _data, callback, function(req, status, err){
            if( status === 401 ){  //권한 없음 || 미로그인
                notifyAlert('error', '알림', '권한이 없습니다.');
                pop_modal.modal("toggle");
            }
        }, false, 'GET', 'html');


        return this;
    };
}(jQuery));




/*
* input text에 숫자만 입력하고 천단위 콤마표시
* 커서 이동 시 한글이 남는 문제가 있어 blur 이벤트 추가해서 처리함
* ex) $("input.number_format").numberFormat();
 */
(function ($) {
    $.fn.numberFormat = function(){
        var sel = this.selector;
        $(sel).each(function() {
            var val = $(this).val();
            console.log("val : " + val);
            // $(this).addCommas();
            $(this).val(commify(val));
        });
        $(document).on("keydown ", this.selector, function () {
            var code = window.event.keyCode;
            if ((code > 34 && code < 41) || (code > 47 && code < 58) || (code > 95 && code < 106) || code == 8 || code == 9 || code == 13 || code == 46) {
                event.returnValue = true;
            }else {
                event.returnValue = false;
            }
        });

        $(document).on("keyup", this.selector, function () {
            var val = $(this).val().replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
            $(this).val(val);
            var $this = $(this);
            var num = $this.val().replace(/[,]/g, "");

            var parts = num.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $this.val(parts.join("."));
        });

        $(document).on("blur", this.selector, function () {
            var val = $(this).val().replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
            $(this).val(val);
        });
    };

    // $.fn.addCommas = function(){
    //     console.log("addCommas");
    //     var sel = this.selector;
    //     console.log("sel : " + sel);
    //     var $this = $(sel);
    //     var num = $this.val().replace(/[,]/g, "");
    //     console.log("num : " + num);
    //
    //     var parts = num.toString().split(".");
    //     parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //     $this.val(parts.join("."));
    //     $this.css("border", "1px solid red");
    // }
})(jQuery);


//----------------------
// 테이블의 행(tr) 접었다 펴기
// expandRow : 행 확장
// collapseRow : 행 접기
// expandAjax : 행 확장 후 데이터 가져올 ajax
//----------------------

var is_folding = true;
var current_tr_num = 0;
var last_tr_num = 0;

function expandRow(obj, ajax_url, ajax_data) {
    is_folding = false;
    last_tr_num = current_tr_num;

    var td_count = obj.closest("tr").children("td").length; //obj.parents("tr").children("td").length;
    var new_tr = "<tr class='extra_tr' style='background-color: #faf2cc'><td colspan=" + td_count + " class='extra_td'></td></tr>";
    obj.closest("tr").after(new_tr);

    if(ajax_url != null) {
        expandAjax(ajax_url, ajax_data);
    }
}

function collapseRow() {
    $(".extra_tr").remove();
    is_folding = true;
    last_tr_num = 0;
}

function expandAjax(url, data){
    $.ajax({
        url: url,
        type: 'GET',
        data: data,
        dataType: 'text',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }).done(function (result, data) {
        console.log("done");
        $(".extra_td").append(result);
    }).fail(function (result, data) {
        console.log("fail result : " + result);
        console.log("fail data : " + data);
    }).always(function (data) {
        //console.log("always complete : " + data);
    });
}


//----------------------
// 파일 용량, 확장자 체크
// file_obj = document.getElementById('아이디')
//----------------------
function fileCheck( file_obj,  max_size, file_ext)
{
    if(typeof max_size == 'undefined') max_size = 200; //200KB
    if(typeof file_ext == 'undefined') file_ext = 'xls|xlxs|jpg|png|gif|hwp|doc';
    var file_size = 0;
    var return_json = {'result' : true, 'msg' : 'ok'}
    var browser=navigator.appName; // 브라우저 확인


    if (browser=="Microsoft Internet Explorer") { // 익스플로러일 경우
        var oas = new ActiveXObject("Scripting.FileSystemObject");
        file_size = oas.getFile( file_obj.value ).size_size, file_ext;
    } else { // 익스플로러가 아닐경우
        file_size = file_obj.files[0].size_size, file_ext;
    }

    if(file_size > max_size) {
        return_json.result = false;
        return_json.msg = '파일 사이즈가 ' + max_size + 'KB를 초과하였습니다.';
        return return_json;
    }

    var file_name = file_obj.value
    // var ext = (/[.]/.exec(file_name)) ? /[^.]+$/.exec(file_name) : undefined;
    var ext = file_name.split('.').pop().toLowerCase();
    if($.inArray(ext, file_ext.split('|')) == -1) {
        return_json.result = false;
        return_json.msg = file_ext + ' 파일만 업로드 할수 있습니다.';
        return return_json;
    }

    return return_json;
}



//----------------------
// validation 실패 시 에러를 보여준다
// result = ajax로 리턴 받은 laravel validation result
// status = ajax http code
//----------------------
function showValidateError(result, status, display_element_id){
    var $display_element = null;
    if (display_element_id)
        $display_element = $("#"+display_element_id);
    else
        $display_element = $('#form-errors')

    $('.form-group').removeClass('has-error');
    if($display_element.length > 0){
        $display_element.html('');
    } else {
        $('<div id="form-errors"><div>').insertBefore('form:eq(0)'); // 에러를 표시해야 하나 form-errors가 없는 예외 처리
    }

    if( status === 401 ){  //권한 없음 || 미로그인
        notifyAlert('error', '알림', '권한이 없습니다.');
    } else if( status === 422 ) { // validation error (Unprocessable Entity)
        var _result_filter = {}; // 배열의 경우 메시지는 1회만 표시하기 위함 (validation에서 wildcard(*) 사용 시)
        $.each( result, function( key, value ) {
            if(key.indexOf('.') < 0) { // 일반 input
                _result_filter[key] = value;
            } else { // array input
                var _key_filtered = key.substring(0, key.indexOf('.'));
                _result_filter[_key_filtered] = value;
            }
        });
        var errorsHtml = '<div class="alert alert-danger"><ul>';
        $.each( _result_filter, function( key, value ) {
            errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
        });
        errorsHtml += '</ul></di>';
        $display_element.html( errorsHtml ); //appending to a <div id="form-error"></div> inside form

        $.each( result, function( key, value ) { // input 에 error 표시 (테두리)
            var _input_name = key;
            var _input_index = 0;
            if(key.indexOf('.') > 0) { // array input
                _input_name = key.substring(0, key.indexOf('.'))+'[]';
                _input_index = parseInt(key.substring(key.indexOf('.')+1));
            }

            var _input = $('[name="'+_input_name+'"]:eq('+_input_index+')');
            if(_input.is('input[type="text"], input[type="file"], input[type="number"], select')){
                _input.closest('.form-group').addClass('has-error');
            }
            else if(_input.is('input:radio, input:checkbox')){
                _input.closest('.form-group').addClass('has-error');
            }
        });

        $('html,body').scrollTop(0);

    } else {
        /// do some thing else
        //alert(result.ret_msg);
        notifyAlert('error', '알림', result.ret_msg);
    }
}

/**
 * Datepicker 날짜 세팅
 * 기능 : 오늘, 일주일, 한달 등...
 * @param i_period (ex. -1, 0, 1 ...)
 * @param s_period_type (ex. days, weeks, months ...)
 * @param sdate_id_name (ex. id="sDate" id name)
 * @param edate_id_name (ex. id="eDate" id name)
 */
function setDatepickerPeriod(i_period, s_period_type, sdate_id_name, edate_id_name) {
    var return_data = date_calculate(i_period, s_period_type);

    $("#"+sdate_id_name).datepicker("setDate", return_data['s_start_period']);
    $("#"+edate_id_name).datepicker("setDate", return_data['s_end_period']);
}

/**
 * 날짜 계산 처리 로직
 * @param i_period
 * @param s_period_type
 * @returns {*}
 */
function date_calculate(i_period, s_period_type) {
    var return_arr = new Array();
    var s_start_period = "";
    var s_end_period = "";

    if(s_period_type == 'days' || s_period_type == 'weeks' || s_period_type == 'months'){
        var calc_date = moment().add(i_period, s_period_type).format('YYYY-MM-DD');
        if(i_period >= 0){
            // s_period = moment().format('YYYY-MM-DD') + ' - ' + calc_date;
            s_start_period = moment().format('YYYY-MM-DD');
            s_end_period = calc_date;
        }
        else{
            s_start_period = calc_date;
            s_end_period = moment().format('YYYY-MM-DD');
        }
    }
    else if(s_period_type == 'mon'){
        s_start_period = moment().add(i_period, 'months').date(1).format('YYYY-MM-DD');
        s_end_period = moment().add(i_period, 'months').endOf('month').format('YYYY-MM-DD');
    }
    else if(s_period_type == 'all'){
        s_start_period = '';
        s_end_period = '';
    }

    return_arr['s_start_period'] = s_start_period;
    return_arr['s_end_period'] = s_end_period;

    return return_arr;
}

/**
 * sidebar menu 강제 active 처리
 * @param link
 */
function forceSidebarActive(link) {
    // sidebar 고정 처리
    var $active_link = $('.sidebar a[href="' + link + '"]');
    var target = $active_link.closest('.sidebar-menu').data('tab');

    $('.sidebar-tabs>ul>li>a[href="'+target+'"]').trigger('click');

    if($active_link.closest('ul').hasClass('treeview-menu')) {
        // admin.js에서 active class 추가된 내용 초기화
        $active_link.closest('ul.treeview-menu').find('li.menu-url').removeClass('active');

        $active_link.parents('.treeview').addClass('active');
        $active_link.closest('ul.treeview-menu').addClass('menu-open').show();
        $active_link.closest('li').addClass('active');
    } else {
        $active_link.closest('li').addClass('active');
    }
}

/**
 * 상품 카테고리 selectbox ajax
 * 사업코드, 강좌분류1Depth, 강좌분류2Depth, 강좌분류3Depth, 강좌분류4Depth에 ajax로직 추가
 */
function setDisplayCategoryAjax(ajax_url, service_obj, depth1_obj, depth2_obj, depth3_obj, depth4_obj){
    var arr_depth_obj = [
        service_obj, depth1_obj, depth2_obj, depth3_obj, depth4_obj
    ];

    // selectbox별 loop
    for(var index=0; index<5; index++){
        var _obj = arr_depth_obj[index];

        _obj.change(function(){
            var _selected_value = $(this).val();
            var _depth = 0;
            // 현재 클릭한 selectbox의 Depth계산
            for(var _index=0; _index<5; _index++){
                if($(this).attr('name') == arr_depth_obj[_index].attr('name')){
                    _depth = _index;
                    break;
                }
            }
            for(var _sub_index = _depth+1 ; _sub_index < 5 ; _sub_index++){
                // 현재 클릭한 selectbox의 다음Depth의 selectbox값을 계산
                // _sub_index는 다음selectbox의 Depth값
                if(_selected_value != '' && _sub_index == _depth+1){
                    // serialize를 쓰기 위해 이 형태로 만든 것. 그냥 data = {}형식으로 만들어도 무방
                    /*
                    $('body #temp_form').remove();
                    var temp_form  = $('<form id="temp_form" style="display:none;"></form>');
                    temp_form.append('<input type="hidden" name="search_service_id" value="'+service_obj.val()+'">');
                    temp_form.append('<input type="hidden" name="search_parent_code" value="'+_selected_value+'">');
                    temp_form.append('<input type="hidden" name="search_depth" value="'+_sub_index+'">');
                    $('body').append(temp_form);
                    */
                    var params = {
                        search_service_id:service_obj.val(),
                        search_parent_code:_selected_value,
                        search_depth:_sub_index
                    };
                    sendAjax(ajax_url, params /*temp_form.serialize()*/, function(ret){
                        var _html = '<option value="">레벨'+ret.search_param.depth+'</option>';

                        if(ret.ret_cd){
                            var cate_length = ret.data.length;
                            for(var cate_index=0; cate_index<cate_length ; cate_index++){
                                var _data = ret.data[cate_index];
                                _html += '<option value="'+_data['CategoryItemCCDRoute']+'" data-label="'+_data['CategoryItemNameRoute']+'">'+_data['CategoryItemName']+'</option>';
                            }
                            arr_depth_obj[_sub_index].html(_html);

                            // console.log("currentValues[" + _depth + "] : " + currentValues[_depth]);
                            /*if (currentValues[_depth]) {
                                var $options = arr_depth_obj[_sub_index].children();
                                var val = "";
                                $options.each(function(){
                                    if ($(this).val() == currentValues[_depth]){
                                        val = currentValues[_depth];
                                    }
                                });
                                arr_depth_obj[_sub_index].val(val);
                            }*/
                        }
                    }, null, false, 'GET', 'json', false);

                } else { // 기타 selectbox는 초기화
                    arr_depth_obj[_sub_index].html('<option value="">레벨'+_sub_index+'</option>');
                }
            }
        });
    }
}

/**
 * Cookie 생성
 * 
 * @param cName
 * @param cValue
 * @param cDay
 */
function setCookie(cName, cValue, cDay) {
    var expire = new Date();
    expire.setDate(expire.getDate() + cDay);
    var cookies = cName + '=' + escape(cValue) + '; path=/ ';
    if(typeof cDay != 'undefined') cookies += ';expires=' + expire.toGMTString() + ';';
    document.cookie = cookies;
}

/**
 * Cookie 확인
 * 
 * @param cName
 */
function getCookie(cName) {
    cName = cName + '=';
    var cookieData = document.cookie;
    var start = cookieData.indexOf(cName);
    var cValue = '';
    if(start != -1) {
        start += cName.length;
        var end = cookieData.indexOf(';', start);
        if(end == -1) end = cookieData.length;
        cValue = cookieData.substring(start, end);
    }

    return unescape(cValue);
}

// 현재 시간 계산
function getTimeStamp() {
    var d = new Date();
    var s = leadingZeros(d.getHours(), 2) + ':' + leadingZeros(d.getMinutes(), 2) + ':' + leadingZeros(d.getSeconds(), 2);

    return s;
}

function leadingZeros(n, digits) {
    var zero = '';
    n = n.toString();

    if(n.length < digits) {
        for (var i = 0; i < digits - n.length; i++) {
            zero += '0';
        }
    }

    return zero + n;
}

function getFullTimeStamp() {
    var today = new Date();
    var today_year = today.getFullYear();
    var today_month = (today.getMonth()+1) > 9 ? (today.getMonth()+1) : "0"+(today.getMonth()+1);
    var today_day = today.getDate() > 9 ? today.getDate() : "0"+today.getDate();
    var todayYmd = today_year + "-" + today_month + "-" + today_day;
    var todayHis = getTimeStamp();

    var todayYmdHis = todayYmd + " " + todayHis;

    return todayYmdHis;
}


/**
 * 배열변수 이름에서 key값을 증가시켜 리턴해 주는 함수
 * name[0] -> name[1]
 * @param oriArrName
 */
function incrementArrayName(oriArrName) {
    var regExp = /\[[0-9]+\]/;
    if (!regExp.test(oriArrName)) {
        return;
    }

    var e = regExp.exec(oriArrName);
    var regExp2 = /[0-9]+/;
    var e2 = regExp2.exec(e[0]);
    var newArrStr = "[" + (e2[0]*1 + 1) + "]";
    var newName = oriArrName.replace(regExp, newArrStr);
    return newName;
}

/**
 * 다차원 배열 변수에서 마지막 key값 증가
 * name[0][0] -> name[0][1]
 * @param name
 * @returns {string}
 */
function incrementKeyToMultiArray(name) {

    var regExp = /\[[0-9]+\]/;
    if (!regExp.test(name)) {
        return;
    }

    var regExp = /\[[0-9]+\]/gi;
    var ex = regExp.exec(name);
    var ori_name = name.substring(0, ex['index']);  // 배열을 뺀 순수 이름 추출

    var match = name.match(regExp);  // [0]와 같은 배열 형식 추출
    var len = match.length;
    var regExp2 = /[0-9]/;
    var last = match[len-1];
    var key = regExp2.exec(last);  // 마지막 배열에서 형식 "[0]" 에서 숫자 0 만 추출
    var newKey = "[" + (key*1 + 1) + "]";
    match[len-1] = newKey; // 마지막 배열을 1추가한 배열로 교체

    var newArrayString = "";
    $.each(match, function(index, value){
        newArrayString += value;
    });

    var newName = ori_name + newArrayString;

    return newName;
}


/**
 * 배열변수에서 배열 차원 추가
 * name[0] -> name[0][0]
 * name[1] -> name[0][1]
 * @param name
 * @param is_last
 * @returns {string}
 */
function addKeyToMultiArray(name, is_last) {
    var regExp = /\[[0-9]+\]/;
    if (!regExp.test(name)) {
        return;
    }

    if (is_last == null) {
        is_last = true;
    }else {
        is_last = false;
    }

    var regExp = /\[[0-9]+\]/gi;
    var ex = regExp.exec(name);
    var ori_name = name.substring(0, ex['index']);  // 배열을 뺀 순수 이름 추출

    var match = name.match(regExp);  // [0]와 같은 배열 형식 추출
    var len = match.length;  // 배열 갯수 (차원 수)
    var regExp2 = /[0-9]+/;

    var first = match[0];
    var newName = "";
    var newArrayString = "";

    var key = regExp2.exec(first);  // 첫번째 배열에서 형식 "[0]" 에서 숫자 0 만 추출

    // console.log("len : " + len);

    if (len < 2) {
        // 1차원 배열일 경우 배열 추가
        if (is_last) {
            // console.log("복제본에 배열 추가");
            var newKey = "[" + (key * 1 + 1) + "]";
            match[0] = newKey; // 원래 배열에서 첫번째 배열을 1추가한 배열로 교체
            $.each(match, function (index, value) {
                newArrayString += value;
            });
            newName = ori_name + newArrayString + "[0]";
        } else {
            // 복제 원본 베열에 배열 추가 name[1] => name[0][1]
            // console.log("원본에 배열 추가");
            $.each(match, function (index, value) {
                newArrayString += value;
            });
            newName = ori_name + "[0]" + newArrayString;
        }
    }else {
        // 이미 2차원 배열일 경우 첫번째 배열 키값 증가
        var newKey = "[" + (key * 1 + 1) + "]";
        match[0] = newKey; // 원래 배열에서 첫번째 배열을 1추가한 배열로 교체
        $.each(match, function (index, value) {
            newArrayString += value;
        });
        newName = ori_name + newArrayString;
    }

    return newName;
}

/**
 * 배열 변수 이름을 받아 몇차원 배열인지 확인
 * @param oriArrName
 */
function getArrayDepth(oriArrName) {
    var regExp = /\[[0-9]+\]/g;
    if (!regExp.test(oriArrName)) {
        return;
    }
    var match = oriArrName.match(regExp);  // [0]와 같은 배열 형식 추출
    var len = match.length;  // 배열 갯수 (차원 수)
    return len;
}

function getArrayKey(oriArrName, setKey) {
    var regExp = /\[[0-9]+\]/g;
    if (!regExp.test(oriArrName)) {
        return;
    }

    var e = regExp.exec(oriArrName);
    var regExp2 = /[0-9]+/g;
    var e2 = regExp2.exec(e[0]);
    return e2;
}

function setKeytoArrayName(oriArrName, setKey) {
    var regExp = /\[[0-9]+\]/;
    if (!regExp.test(oriArrName)) {
        return;
    }

    var newArrStr = "[" + setKey + "]";
    var newName = oriArrName.replace(regExp, newArrStr);
    return newName;
}

function resetArrayToZero(arrName) {
    var regExp = /\[[0-9]+\]/;
    if (!regExp.test(arrName)) {
        return;
    }

    var regExp = /\[[0-9]+\]/gi;
    var ex = regExp.exec(arrName);
    var ori_name = arrName.substring(0, ex['index']);

    return ori_name + "[0]";
}

function resetArrayNameToOriginal(arrName) {
    var regExp = /\[[0-9]+\]/;
    if (!regExp.test(arrName)) {
        return;
    }

    var regExp = /\[[0-9]+\]/gi;
    var match = arrName.match(regExp);
    var ex = regExp.exec(arrName);
    var new_name = "";

    if (match.length == 1) {
        new_name = arrName.substring(0, ex['index']) + "[0]";
    }else {
        new_name = arrName.replace(ex[0], '');
    }
    return new_name;
}