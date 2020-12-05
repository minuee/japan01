<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popheader.php'; ?>
<div class="modal-body">
    <!--<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Panel Heading</div>
            <div class="panel-body">Panel Content</div>
        </div>
    </div>-->
    <div class="col-md-12">
        <form action="<?php echo base_url() ?>manager/income/update" method="POST" id="PopData">
            <input type="hidden" name="OrderingDataIdx" value="<?=$OrderingData['OrderingDataIdx']?>" >
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
                            <col style="width:15%;">
                            <col style="width:35%;">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td class="table-active text-center bg-gray">법인</td>
                            <td class="text-center">
                                <select class="form-control control-member" id="PublishCorporationCCD" name="PublishCorporationCCD">
                                    <option selected="selected">법인 선택</option>
                                    <? foreach($PublishCorporationList as $key => $name) { ?>
                                    <option value="<?=$name['Code']?>" <?=selected($name['Code'],$OrderingData['PublishCorporationCCD'])?>><?=$name['Name']?></option>
                                    <? } ?>
                                </select>
                                <?/*=$OrderingData['PublishCorporationCCDName']*/?>
                            </td>
                            <td class="table-dark text-center bg-gray">PG사 주문번호</td>
                            <td class="text-center"><?=$OrderingData['OID']?></td>
                        </tr>
                        <tr>
                            <td class="table-active text-center bg-gray">업체명</td>
                            <td class="text-center"><?=$OrderingData['CompanyName']?></td>
                            <td class="table-dark text-center bg-gray">최종입금일자</td>
                            <td class="text-center"><?=$OrderingData['PayDatetime']?></td>
                        </tr>
                        <tr>
                            <td class="table-active text-center bg-gray">은행(금융)명</td>
                            <td class="text-center"><?=$OrderingData['FinanceName']?></td>
                            <td class="table-dark text-center bg-gray">계좌번호</td>
                            <td class="text-center"><?=$OrderingData['AccountNo']?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </form>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">거래 이력 정보</div>
            <div class="panel-body">
                <div class="box-body p-sm">
                    <table class="table table-bordered">
                        <colgroup>
                            <col style="width:15%;">
                            <col style="width:35%;">
                            <col style="width:15%;">
                            <col style="width:35%;">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td class="table-active text-center bg-gray">상태구분</td>
                            <td class="text-center"><?=$OrderingData['strCASTFlag']?></td>
                            <td class="table-dark text-center bg-gray">분할납부순서</td>
                            <td class="text-center"><?=$OrderingData['AccountSeq']?></td>
                        </tr>
                        <tr>
                            <td class="table-active text-center bg-gray">입금금액</td>
                            <td class="text-center"><?=$OrderingData['Amount']?></td>
                            <td class="table-dark text-center bg-gray">입금일자</td>
                            <td class="text-center"><?=$OrderingData['PayDatetime']?></td>
                        </tr>
                        <tr>
                            <td class="table-active text-center bg-gray">실입급자</td>
                            <td colspan='3' class="text-center"><?=$OrderingData['Buyer']?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">관리 코멘트</div>
            <div class="panel-body">
                <div class="box-body p-sm">

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 collapsed-box">
        <div class="panel panel-default ">
            <div class="panel-heading" role="tab" id="headingOne">
                이력 관리
                <span style="position:relative;float:left;width:100%;height:1px;">
                    <span style="position:absolute;right:5px;bottom:0">
                        <button type="button" class="btn btn-box-tool text-white btn-toggle-ox" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fa fa-plus" ></i>
                        </button>
                    </span>
                </span>
            </div>
            <div id="collapseOne" class="panel-body panel-collapse collapse out" role="tabpanel" aria-labelledby="headingOne">
                <div class="box-body p-sm">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cras justo odio
                            <span class="badge badge-primary badge-pill">2019-01-30 15:10</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dapibus ac facilisis in
                            <span class="badge badge-primary badge-pill">2019-01-30 15:10</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Morbi leo risus
                            <span class="badge badge-primary badge-pill">2019-01-30 15:10</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</div>

<script type="text/javascript">
    jQuery(function($) {
        $(".modal-title").text("입금 상세정보");
        $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_modify'>수정</button>");

        jQuery(document).on("click", "#btn_modify", function(){

            var confirmation = confirm("수정하시겠습니까?");

            if(confirmation)
            {
                var formData = new FormData($('#PopData')[0]);
                var hitURL = "/manager/income/update";
                jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : hitURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert('수정되었습니다.');
                            $('#pop_modal').remove();
                            location.reload();
                            return false;
                        } else {
                            alert('업데이트 처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }
        });


        $('#btn_modify').on('hidden.bs.collapse', function () {
            // do something…
            alert(1);
        });

        $('#list_table').off('click', '.btn-income-send').on('click', '.btn-income-send',function() {
            $("#member_detail_info").addClass('collapsed-box');
            $("#member_detail_info_icon").removeClass('fa-minus').addClass('fa-plus');
            $("#member_detail_info_tbody").css("display", "none");
        })
    })
</script>
<!-- [a] -->
<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



