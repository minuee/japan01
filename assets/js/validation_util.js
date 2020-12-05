// util.js
/**
 * form submit
 * @param frmObj    jquery object
 */
function formSubmit(frmObj)
{
    var validator = new FormValidator();
    //validator.settings.classes.item = 'item';

    frmObj.submit(function(e) {
        e.preventDefault();
        var submit = true;

        // evaluate the form using generic validaing
        var validatorResult = validator.checkAll(this);
        //var validatorResult = validator.checkFirst(this);
        //console.log(validatorResult);

        if (!validatorResult.valid) {
            submit = false;
        }

        if (submit) {
            this.submit();
        }

        return false;
    });
}

/**
 * ajax form submit
 * @param frmObj
 * @param url
 * @param callback
 * @param error_callback
 * @param async
 * @param error_view :: layer, alert
 */
function ajaxSubmit(frmObj, url, callback, error_callback, async, error_view)
{
    // event.preventDefault();
    if(typeof error_view == 'undefined') error_view = 'alert';

    frmObj.ajaxSubmit({
        url : url,
        type: 'POST',
        dataType : 'json',
        async : (async !== undefined) ? async : false,
        beforeSubmit: function (formData, form, options) {
            // validation
            var validator = new FormValidator();
            validator.settings.classes.item = 'item';
            var validatorResult = {valid : false};

            if(error_view == 'alert') {
                validatorResult = validator.checkFirst(frmObj.get(0));
            } else if(error_view == 'layer') {
                validatorResult = validator.checkAll(frmObj.get(0));
            }

            if (!validatorResult.valid) {
                return false;
            }
        },
        success: function(response, status){
            if(typeof callback === "function") {
                callback(response);
            }
        },
        error: function(xhr, status, error){
            //console.log(xhr);
            //console.log(xhr.status);
            //console.log(xhr.error);
            //console.log(error);
            //console.log(xhr.responseText);
            if(typeof error_callback === "function") {
                try {
                    var ret = JSON.parse(xhr.responseText);
                    error_callback(ret, xhr.status);
                } catch (e) {
                    error_callback(xhr, xhr.status);
                }
            } else {
                alert('에러가 발생하였습니다.');
            }
        }
    });

    return false;
}

/**
 * ajax send
 * @param url
 * @param data
 * @param callback
 * @param error_callback
 * @param async
 * @returns {boolean}
 */
function sendFormAjax(url, data, callback, error_callback, async, method, data_type)
{
    // event.preventDefault();
    var submit = true;

    // validation
    $.each(data, function(key, value) {
        if(key.substr(key.indexOf('.')+1) === 'required') {
            if(!value) {
                alert('필수 파라미터가 없습니다.');
                submit = false;
                return false;
            } else {
                // 기존 데이터 삭제
                delete data[key];
                // required를 삭제한 데이터 추가
                data[key.replace('.required', '')] = value;
            }
        }
    });

    if(submit) {
        $.ajax({
            url: url,
            data: data,
            type: ((method !== undefined) ? method : 'POST'),
            dataType: (data_type !== undefined) ? data_type : 'json',
            async: (async !== undefined) ? async : false
        }).success(function (response, status, xhr) {
            if(typeof callback === "function") {
                callback(response);
            }
        }).error(function(xhr, status, error) {
            if(typeof error_callback === "function") {
                //alert("code:"+xhr.status+"\n"+"message:"+xhr.responseText+"\n"+"error:"+error);
                var ret = JSON.parse(xhr.responseText);
                if (xhr.status == 422) {
                    ret = { 'ret_cd' : false, 'ret_msg' : Object.values(ret)[0][0] };
                }

                error_callback(ret, xhr.status);
            } else {
                alert('에러가 발생하였습니다.');
            }
        });
    }

    return false;
}