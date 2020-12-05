<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popheader.php'; ?>

<link href="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban.css" rel="stylesheet" type="text/css" />
<style>
    .group_wrap {clear:both;position:relative;width:100%;height:auto;}
    .group_wrap ul{clear:both;position:relative;width:96%;height:auto;min-height:40px;padding:5px 2%;}
    .group_wrap ul li{float:left;width:auto;min-width:100px;height:30px;padding:5px 10px;border-radius:10px;background-color: #fff;border:1px solid #ccc;list-style: none; text-align:center;margin-right:10px;margin-bottom:10px;cursor: pointer;color:#000;font-size:14px;}
    .group_wrap ul li.on{background-color: #605ca8;border:1px solid #605ca8;color:#fff}
</style>



<div class="modal-body">
    <div class="col-md-12">
        <form action="<?php echo base_url() ?>manager/project/update" method="POST" id="PopData">
            <input type="hidden" name="TargetUserID"  id="TargetUserID"  value="<?=$TargetUser['userId']?>" >
            <input type="hidden" name="Permission"  id="Permission"  value="<?=$PermissionData['Permission']?>" >
            <div class="panel panel-default">
                <div class="panel-heading">기본 정보</div>
                <!--<div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool text-white" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-plus" id="member_detail_info_icon"></i>
                    </button>
                </div>-->
                <div class="panel-body">
                    <div class="box-body p-sm">
                        <table class="table table-bordered">
                            <colgroup>
                                <col style="width:15%;">
                                <col style="width:35%;">
                                <col style="width:10%;">
                                <col style="width:40%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td class="table-active text-center bg-gray">이름</td>
                                    <td class="text-left"><?=$TargetUser['name']?></td>
                                    <td class="table-dark text-center bg-gray">팀명</td>
                                    <td class="text-left"><?=$TargetUser['GROUP_NAME']?></td>
                                </tr>
                                <tr id="viewProjectTeam" >
                                    <td class="table-dark text-center bg-gray">타팀 KANBAN<br >조회권한</td>
                                    <td class="text-left group_wrap" colspan="3">
                                        <ul>
                                            <?
                                            if ( $PermissionData['Permission'] ) {
                                                $Permission = json_decode($PermissionData['Permission']);
                                            }else{
                                                $Permission = array();
                                            }

                                            foreach( $GROUPCode as $key => $row) { ?>
                                                <li id="<?=$key?>" class="toggle_select <?=(in_array( $key, $Permission)?'on':'')?>"><?=$row?></li>
                                            <? }?>
                                        </ul>

                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type='text/javascript'>
    $(function(){
        $(".modal-title").text("사용자 정보.");
        $(".modal-body").append("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10' id='btn_pop_modify'>수정</button></span>");


        $(".toggle_select").click(function(){
        //$(".toggle_select").on("click", ".toggle_select", function () {
            $(this).toggleClass('on');
        });

        $(document).off('click', '#btn_pop_modify').on('click', '#btn_pop_modify',function() {

            let strmode =  '수정';
            let sendURL = "/manager/project/userinfoupdate";

            let permitssion_list = [];

            $.each( $(".toggle_select"), function(i,e ) {
                if ( $(this).hasClass('on')) {
                    permitssion_list.push($(this).attr('id'))
                }
            });
            if ( permitssion_list.length > 0 ) {
                $("#Permission").val(permitssion_list);
            }else{
                $("#Permission").val(null);
            }

            let confirmation = confirm(strmode + "하시겠습니까?");

            if(confirmation)
            {
                let formData = new FormData($('#PopData')[0]);

                jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : sendURL,
                    data: formData,
                    async : false,
                    processData: false,
                    contentType: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert(strmode + '되었습니다.');
                            return false;
                        } else {
                            alert('업데이트 처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }
        });

    });

</script>

<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



