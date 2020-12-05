var Broadcast = function()
{
    if (utils.empty(utils.getCookie('_live_token'))) {
        return;
    }
    this.socket = (typeof io === 'undefined') ? null : io.connect(window.container.connection, {query: {token: utils.getCookie('_live_token')}, transports: ['websocket'], secure: true, forceNew: true, reconnect: true});

    if (this.socket)
    {
        /* 접속 */
        this.socket.on('connect', function() {
            // TODO:
        });

        /* 접속 에러 */
        this.socket.on('error', function(err) {
            location.reload();
        });

        /* 메세지 */
        this.socket.on('message.'+ window.container.channel, ui.message.default);

        /* 접속자 수 */
        this.socket.on('channel:users:count.'+ window.container.channel, ui.user.count);

        /* 접속자 리스트 */
        this.socket.on('channel:users:all.'+ window.container.channel, ui.user.list);

        /* 알림 */
        this.socket.on('message:notify.'+ window.container.channel, ui.message.notify);

        /* 방송 인트로 */
        this.socket.on('broadcast:intro.'+ window.container.channel, ui.broadcast.intro);

        /* 방송 본영상 */
        this.socket.on('broadcast:play.'+ window.container.channel, ui.broadcast.play);

        /* 방송 대기 */
        this.socket.on('broadcast:wait.'+ window.container.channel, ui.broadcast.wait);

        /* 방송 아웃트로 */
        this.socket.on('broadcast:outro.'+ window.container.channel, ui.broadcast.outro);

        /* 방송 종료 */
        this.socket.on('broadcast:termination.'+ window.container.channel, ui.broadcast.termination);

        /* 채널 정보 업데이트 */
        this.socket.on('channel:control:update.'+ window.container.channel, ui.channel.update.control);

        /* 채널 공지사항 업데이트 */
        this.socket.on('channel:notice:update.'+ window.container.channel, ui.channel.update.notice);

        /* 시스템 알림 */
        this.socket.on('system:message.'+ window.container.channel, ui.system.message);

        /* Teacher */
        this.socket.on('system:teacher', ui.system.teacher);

        /* 종료 */
        this.socket.on('system:exit', ui.system.exit);
    }
};