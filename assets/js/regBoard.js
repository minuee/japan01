/**
 * File : regBoard.js
 * 
 * This file contain the validation of add user form
 * 
 * Using validation plugin : jquery.validate.js
 * 
 * @author Noh Seongnam
 */

$(document).ready(function(){
	
	var regBoardForm = $("#regBoard");
	
	var validator = regBoardForm.validate({
		rules:{
            Title :{ required : true }
		},
		messages:{
            Title :{ required : "제목은 필수입력입니다." }
		}
	});

    regBoardForm.on("submit",function() {
        var checkcount = 0;
        $('.target_label_bcode').each(function () {
            if ($(this).find("input").is(":checked") === true) {
                checkcount++;
            }
        });
        if (checkcount == 0) {
            alert("법인은 최소 1개이상 선택하셔야 합니다.");
            return false;
        }

        $('.form-group').removeClass('has-error');
        $('#form-errors').html('');

        var _url = regBoardForm.attr('action');
        // get editor content to textarea
        var content = getEditorContent();
        if(content !== false) {
            regBoardForm.find('textarea[name=content]').val(content);
        }

        ajaxSubmit(regBoardForm, _url, function(ret){
            if(ret.ret_cd) {
                notifyAlert('success', '알림', ret.ret_msg);
                location.href = "manager/board/detail/" + ret.idx;
            }else {
                notifyAlert('error', '알림', ret.ret_msg);
            }
        }, showValidateError, false);
    });
});
