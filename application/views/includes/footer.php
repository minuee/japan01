

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b></b><!--Made By Hackers Noh S.N-->
        </div>
        <strong>Copyright &copy; 2019 <a href="<?php echo base_url(); ?>">Hackers</a>.</strong> All rights reserved.
    </footer>
    
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js" type="text/javascript"></script>
    <!-- <script src="<?php echo base_url(); ?>assets/dist/js/pages/dashboard.js" type="text/javascript"></script> -->
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/js/validation.js" type="text/javascript"></script>
    <script type="text/javascript">
        var windowURL = window.location.href;
        pageURL = windowURL.substring(0, windowURL.lastIndexOf('/'));

        var x= $('a[href="'+pageURL+'"]');
        x.addClass('active');
        x.parent().addClass('active');
        var y= $('a[href="'+windowURL+'"]');
        y.addClass('active');
        y.parent().addClass('active');

        function readyAlert(){
            jQuery("body").animate({scrollTop:0},"slow",function(){
                message_layer_open(1);
            })
        }
        function recieveMsgAlert(){
            jQuery("body").animate({scrollTop:0},"slow",function(){
                message_layer_open(2);
            })
        }

        function message_layer_open(mode) {
            if (window.innerWidth) {
                windowWidth = window.innerWidth;
            } else {
                if (document.documentElement && document.documentElement.clientWidth) {
                    windowWidth = document.documentElement.clientWidth;
                } else {
                    if (document.body) {
                        windowWidth = document.body.offsetWidth;
                    }
                }
            }

            if (window.innerHeight) {
                windowHeight = window.innerHeight;
            } else {
                if (document.documentElement && document.documentElement.clientHeight) {
                    windowHeight = document.documentElement.clientHeight;
                } else {
                    if (document.body) {
                        windowHeight = document.body.clientHeight;
                    }
                }
            }

            if ( mode == 1 ) {
                var height_tmp = jQuery("div.message_layer").height() + 60;
                var windowHeight_tmp = windowHeight;
                var windowWidth_tmp = windowWidth - $("div.message_layer").width() - 50;

                $("div.message_layer").css({top: windowHeight_tmp + "px", left: windowWidth_tmp + "px"});
                $("div.message_layer").show().animate({"top": "-=" + height_tmp + "px"}, 1000)
            }else{
                var height_tmp = jQuery("div.message_layer2").height() + 60;
                var windowHeight_tmp = windowHeight;
                var windowWidth_tmp = windowWidth - $("div.message_layer2").width() - 50;

                $("div.message_layer2").css({top: windowHeight_tmp + "px", left: windowWidth_tmp + "px"});
                $("div.message_layer2").show().animate({"top": "-=" + height_tmp + "px"}, 1000)
            }

        }

        $(function () {
            $('#message_close').click(function () {
                $('.message_layer').css('display', 'none');
            });
            $('#message_close1').click(function () {
                $('.message_layer2').css('display', 'none');
            });
        });

        checkLogin = setInterval(function() {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/islogin",
                data: "ismode=1",
                success: function (json) {
                    if (json.islogin == null) {
                        clearInterval(checkLogin);
                        alert("세션이 만료되어 로그아웃 됩니다.");
                        location.href = "<?php echo base_url(); ?>logout";
                        return false;
                    }else{
                        if ( json.islogin != $("#global_userId").val()) {
                            clearInterval(checkLogin);
                            alert("계정이 이중으로 사용중입니다.");
                            location.reload();
                            return false;
                        }
                    }
                    return false;
                },error : function () {
                    clearInterval(checkLogin);
                    alert("세션이 만료되어 로그아웃 됩니다.");
                    location.href = "<?php echo base_url(); ?>logout";
                    return false;
                }
            });
        }, 15000);

    </script>

    <div class="message_layer">
        <table border="0" cellspacing="0" cellpadding="0" class="message_layer_table">
            <tr>
                <td class="message_content">
                    업무리포트 미제출 상태입니다. <br />
                    기준 : 화~토(전일 0시이후 미작성), 일(2일전 0시이후 미작성),월(3일전 0시이후 미작성)<br />
                    (연차사용이나 메시지가 뜨는 분들은 일정관리 미등록상태)<br />
                    확인후 리포트 작성후, 업무를 진행해 주세요.
                </td>
            </tr>
            <tr>
                <td align="right"><span id="message_close" style="cursor:pointer;" class="close_btn">[닫기]</span></td>
            </tr>
        </table>
    </div>

    <!-- 로딩이미지 -->
    <div class="wrap-loading display_none" id="loading">
        <div><img src="<?php echo base_url(); ?>assets/images/load_red.gif" /></div>
    </div>
  </body>
</html>