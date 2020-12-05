var Broadcast = function()
{
    if (utils.empty(utils.getCookie('_chatroom_token'))) {
        return;
    }
    this.socket = (typeof io === 'undefined') ? null : io.connect(window.container.connection, {query: {token: utils.getCookie('_chatroom_token')}, transports: ['websocket'], secure: true, forceNew: true, reconnect: true});

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

        /* 달력 개별 정보 업데이트 */
        this.socket.on('channel:control:calupdate.'+ window.container.channel, ui.display.calupdate);

        /* DashBoard Todo업무 추가 */
        this.socket.on('channel:control:dotoinsert.'+ window.container.channel, ui.display.dotoinsert);

        /* 채팅룽 공지 추가제거 */
        this.socket.on('channel:control:noticeupdate.'+ window.container.channel, ui.display.actionnoticeupdate);

        /* 알림 */
        this.socket.on('message:notify.'+ window.container.channel, ui.message.notify);

        /* 채널 정보 업데이트 */
        //this.socket.on('channel:control:update.'+ window.container.channel, ui.channel.update.control);


        /* 시스템 알림 */
        //this.socket.on('system:message.'+ window.container.channel, ui.system.message);

        /* 종료 */
        this.socket.on('system:exit', ui.system.exit);
    }
};