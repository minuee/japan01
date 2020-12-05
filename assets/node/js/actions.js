var actions = {};

/**
 * 채널 정보
 * @param params
 *      params.channel_id
 * @param callback
 */
actions.information = function(params, callback)
{

    let isChannelMode = params.channel_id;

    if ( isChannelMode.indexOf('Team') ===  0) {
        actions.wrap_default('get', '/nodeapi/API/informationteam/{channel_id}', params, callback);
    }else{
        actions.wrap_default('get', '/nodeapi/API/information/{channel_id}', params, callback);
    }

};

/**
 * 인증번호 전송
 * @param params
 *      params.Channel
 *      params.MobileTEL
 * @param callback
 */
actions.send_certify_code = function(params, callback)
{
    actions.wrap_default('post', '/api/certify/code/send/{Channel}', params, callback);
};

/**
 * 휴대폰 인증
 * @param params
 *      params.Channel
 *      params.Nickname
 *      params.MobileTEL
 *      params.CertifyCode
 *      params.TermsAgree
 * @param callback
 */
actions.authentication = function(params, callback) {
    actions.wrap_default('post', '/api/authentication/{Channel}', params, callback);
};

/**
 * 유저 퇴장
 * @param params
 *      params.Channel
 *      params.Nickname
 * @param callback
 */
actions.user_leave = function(params, callback) {
    actions.wrap_default('put', '/api/user/leave/{Channel}/{Nickname}', params, callback);
};

/**
 * Wrapper
 * @param type
 * @param url
 * @param params
 * @param callback
 */
actions.wrap_default = function(type, url, params, callback)
{
    utils.ajax({
        type: type,
        data: params,
        url: utils.replace(url, params),
        processData: false,
        success: callback,
        error: function(e) {
            alert("관리자에게 문의하세요. ("+ e.status +" : "+ e.statusText +")");
            if (e.status == 403) {
                location.reload();
            }
        }
    });
};