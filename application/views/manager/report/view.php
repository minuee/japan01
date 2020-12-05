<link href="<?php echo base_url(); ?>assets/dist/css/sitemap.css" rel="stylesheet" type="text/css" />


<div class="content-wrapper clear" style="clear:both !important;">
    <section class="content-header">
    <h1>
        <i class="fa fa-user" aria-hidden="true"></i> <?=$ReprotName?>의 업무현황
    </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="box-tools">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-md-4 text-left">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm <?=isset($IsForeData->ReportIdx)?'btn-primary btn_move':'btn-default'?>" data-idx="<?=@$IsForeData->ReportIdx?>"><i class="fa fa-angle-left"></i>&nbsp;&nbsp;이전보고&nbsp;<?=@$IsForeData->wDate?>&nbsp;<?=@$IsForeData->reportGroup?"(".$IsForeData->reportGroup.")":""?></button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center center_title_20"><?=$SelectDate?></div>
                                    <div class="col-md-4 text-right">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm <?=isset($IsNextData->ReportIdx)?'btn-primary btn_move':'btn-default'?>" data-idx="<?=@$IsNextData->ReportIdx?>">&nbsp;다음보고&nbsp;<?=@$IsNextData->wDate?>&nbsp;<?=@$IsNextData->reportGroup?"(".$IsNextData->reportGroup.")":""?>&nbsp;<i class="fa fa-angle-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h4>
                    <i class="fa  fa-sitemap" aria-hidden="true"></i>  프로젝트 업무
                </h4>
            </div>
            <div class="col-xs-12" style="overflow-x:auto;margin-left:10px;padding:0 !important;height:auto;min-height:200px;border:1px solid #ccc;background-color: #fff;" id="HorizontalArea2">
                <div style="width:1800px;height:auto;min-height:200px;padding:0;margin:0" >
                    <table class="table table-striped table-fixed">
                        <colgroup>
                            <col style="width:2%;">
                            <col style="width:7%;">
                            <col style="width:25%;">
                            <col style="width:100px;">
                            <col style="width:7%;">
                            <col style="width:6%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col >
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="text-center bg-todo fc-white"></th>
                            <th class="text-center bg-todo fc-white">팀명</th>
                            <th class="text-center bg-todo fc-white">업무명</th>
                            <th class="text-center bg-todo fc-white">&nbsp;</th>
                            <th class="text-center bg-todo fc-white">현재상태</th>
                            <th class="text-center bg-todo fc-white">진행률</th>
                            <th class="text-center bg-todo fc-white">업무시작일</th>
                            <th class="text-center bg-todo fc-white">업무종료일</th>
                            <th class="text-center bg-todo fc-white">예상소요(H)</th>
                            <th class="text-center bg-todo fc-white">작업시간(件)</th>
                            <th class="text-center bg-todo fc-white">작업시간(누적)</th>
                            <th class="text-center bg-todo fc-white">작업상세내역(Last Comment)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        if ( isset($MyReport[1]) ) {
                            foreach ($MyReport[1] as $key => $val) { ?>
                                <?
                                $_indexnum = 0;
                                $_sumforeTime = 0;
                                $_sumworkTime = 0;
                                $_sumworkAllTime = 0;
                                foreach ($val as $inkey => $row) {
                                    ?>
                                    <? if ($_indexnum == 0) { ?>
                                        <tr>
                                            <td class="text-left bg-gray" colspan="12"><?= $row['ProjectTitle'] ?></td>
                                        </tr>
                                    <? } ?>
                                    <tr>
                                        <td class="text-center">└</td>
                                        <td class="text-center"><?= $row['GROUPNAME'] ?></td>
                                        <td class="noh_ellipsis2 noh_cursor"
                                            title="<?= $row['Worktitle'] ?>" onclick="fn_info(<?= $row['ProjectWorkIdx'] ?>)"><?= $row['Worktitle'] ?></td>
                                        <td class="text-center"><?=($row['IntraUrl']?"<button class='btn btn-sm btn-default btn_move_url' data-url='".$row['IntraUrl']."'>인트라넷</button>":'')?></td>
                                        <td class="text-center"><?= $row['Status'] ?></td>
                                        <td class="text-center"><?= $row['Rate'] ?></td>
                                        <td class="text-center"><?= $row['sDate'] ?></td>
                                        <td class="text-center"><?= $row['eDate'] ?></td>
                                        <td class="text-center"><?=($row['Foretime']==null?0:$row['Foretime'])?></td>
                                        <td class="text-center"><?=($row['worktime']==null?0:$row['worktime'])?></td>
                                        <td class="text-center"><?=($row['SUMDoingTime']==null?0:number_format($row['SUMDoingTime']/60,1))?></td>
                                        <td class="noh_ellipsis2"
                                            title="<?= $row['Comment'] ?>"><?= $row['Comment'] ?></td>
                                    </tr>
                                    <?
                                    $_sumforeTime = $_sumforeTime+$row['Foretime'];
                                    $_sumworkTime = $_sumworkTime+$row['worktime'];
                                    $_sumworkAllTime = $_sumworkAllTime+$row['SUMDoingTime'];
                                    $_indexnum++;
                                } ?>
                                <tr>
                                    <td class="text-center" colspan="8">소계</td>
                                    <td class="text-center"><?=$_sumforeTime?></td>
                                    <td class="text-center"><?=$_sumworkTime?></td>
                                    <td class="text-center"><?=($_sumworkAllTime>0?number_format($_sumworkAllTime/60,1):0)?></td>
                                    <td class="text-center">&nbsp;</td>
                                </tr>
                            <? }
                        }else{?>
                            <tr>
                                <td class="text-center" colspan="11">해당내역의 작업의 없습니다.</td>
                            </tr>
                        <? }?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

        <div class="row mt-10">
            <div class="col-xs-12">
                <h4>
                    <i class="fa fa-cog" aria-hidden="true"></i>  유지보수
                </h4>
            </div>
            <div class="col-xs-12" style="overflow-x:auto;margin-left:10px;padding:0 !important;height:auto;min-height:200px;border:1px solid #ccc;background-color: #fff;" id="HorizontalArea1">
                <div style="width:1800px;height:auto;min-height:200px;padding:0;margin:0" >
                    <table class="table table-striped table-fixed">
                        <colgroup>
                            <col style="width:2%;">
                            <col style="width:7%;">
                            <col style="width:25%;">
                            <col style="width:100px;">
                            <col style="width:7%;">
                            <col style="width:6%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col >
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="text-center bg-todo fc-white"></th>
                            <th class="text-center bg-todo fc-white">팀명</th>
                            <th class="text-center bg-todo fc-white">업무명</th>
                            <th class="text-center bg-todo fc-white">&nbsp;</th>
                            <th class="text-center bg-todo fc-white">현재상태</th>
                            <th class="text-center bg-todo fc-white">진행률</th>
                            <th class="text-center bg-todo fc-white">업무시작일</th>
                            <th class="text-center bg-todo fc-white">업무종료일</th>
                            <th class="text-center bg-todo fc-white">예상소요(H)</th>
                            <th class="text-center bg-todo fc-white">작업시간(件)</th>
                            <th class="text-center bg-todo fc-white">작업시간(누적)</th>
                            <th class="text-center bg-todo fc-white">작업상세내역(Last Comment)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        if ( isset($MyReport[2]) ) {
                            foreach ($MyReport[2] as $key => $val) { ?>
                                <?
                                $_indexnum = 0;
                                $_sumforeTime = 0;
                                $_sumworkTime = 0;
                                $_sumworkAllTime = 0;
                                foreach ($val as $inkey => $row) { ?>
                                    <? if ($_indexnum == 0) { ?>
                                        <tr>
                                            <td class="text-left bg-gray" colspan="12"><?= $row['ProjectTitle'] ?></td>
                                        </tr>
                                    <? } ?>
                                    <tr>
                                        <td class="text-center">└</td>
                                        <td class="text-center"><?= $row['GROUPNAME'] ?></td>
                                        <td class="noh_ellipsis2 noh_cursor"
                                            title="<?= $row['Worktitle'] ?>" onclick="fn_info(<?= $row['ProjectWorkIdx'] ?>)"><?= $row['Worktitle'] ?></td>
                                        <td class="text-center"><?=($row['IntraUrl']?"<button class='btn btn-sm btn-default btn_move_url' data-url='".$row['IntraUrl']."'>인트라넷</button>":'')?></td>
                                        <td class="text-center"><?=$row['Status'] ?></td>
                                        <td class="text-center"><?=$row['Rate'] ?></td>
                                        <td class="text-center"><?=$row['sDate'] ?></td>
                                        <td class="text-center"><?=$row['eDate'] ?></td>
                                        <td class="text-center"><?=($row['Foretime']==null?0:$row['Foretime'])?></td>
                                        <td class="text-center"><?=($row['worktime']==null?0:$row['worktime'])?></td>
                                        <td class="text-center"><?=($row['SUMDoingTime']==null?0:number_format($row['SUMDoingTime']/60,1))?></td>
                                        <td class="noh_ellipsis2"
                                            title="<?= $row['Comment'] ?>"><?= $row['Comment'] ?></td>
                                    </tr>
                                    <?
                                    $_sumforeTime = $_sumforeTime+$row['Foretime'];
                                    $_sumworkTime = $_sumworkTime+$row['worktime'];
                                    $_sumworkAllTime = $_sumworkAllTime+$row['SUMDoingTime'];
                                    $_indexnum++;
                                } ?>
                                <tr>
                                    <td class="text-center" colspan="8">소계</td>
                                    <td class="text-center"><?=$_sumforeTime?></td>
                                    <td class="text-center"><?=$_sumworkTime?></td>
                                    <td class="text-center"><?=($_sumworkAllTime>0?number_format($_sumworkAllTime/60,1):0)?></td>
                                    <td class="text-center">&nbsp;</td>
                                </tr>
                            <? }
                        }else{?>
                        <tr>
                            <td class="text-center" colspan="11">해당내역의 작업의 없습니다.</td>
                        </tr>
                        <? }?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

        <div class="row mt-10">
            <div class="col-xs-12">
                <h4>
                    <i class="fa fa-cog" aria-hidden="true"></i>  기타
                </h4>
            </div>
            <div class="col-xs-12" style="overflow-x:auto;margin-left:10px;padding:0 !important;height:auto;min-height:200px;border:1px solid #ccc;background-color: #fff;" id="HorizontalArea1">
                <div style="width:1800px;height:auto;min-height:200px;padding:0;margin:0" >
                    <table class="table table-striped table-fixed">
                        <colgroup>
                            <col style="width:2%;">
                            <col style="width:7%;">
                            <col style="width:25%;">
                            <col style="width:100px;">
                            <col style="width:7%;">
                            <col style="width:6%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col style="width:7%;">
                            <col >
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="text-center bg-todo fc-white"></th>
                            <th class="text-center bg-todo fc-white">팀명</th>
                            <th class="text-center bg-todo fc-white">업무명</th>
                            <th class="text-center bg-todo fc-white">&nbsp;</th>
                            <th class="text-center bg-todo fc-white">현재상태</th>
                            <th class="text-center bg-todo fc-white">진행률</th>
                            <th class="text-center bg-todo fc-white">업무시작일</th>
                            <th class="text-center bg-todo fc-white">업무종료일</th>
                            <th class="text-center bg-todo fc-white">예상소요(H)</th>
                            <th class="text-center bg-todo fc-white">작업시간(件)</th>
                            <th class="text-center bg-todo fc-white">작업시간(누적)</th>
                            <th class="text-center bg-todo fc-white">작업상세내역(Last Comment)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        if ( isset($MyReport[9]) ) {
                            foreach ($MyReport[9] as $key => $val) { ?>
                                <?
                                $_indexnum = 0;
                                $_sumforeTime = 0;
                                $_sumworkTime = 0;
                                $_sumworkAllTime = 0;
                                foreach ($val as $inkey => $row) { ?>
                                    <? if ($_indexnum == 0) { ?>
                                        <tr>
                                            <td class="text-left bg-gray" colspan="12"><?= $row['ProjectTitle'] ?></td>
                                        </tr>
                                    <? } ?>
                                    <tr>
                                        <td class="text-center">└</td>
                                        <td class="text-center"><?= $row['GROUPNAME'] ?></td>
                                        <td class="noh_ellipsis2 noh_cursor"
                                            title="<?= $row['Worktitle'] ?>" onclick="fn_info(<?= $row['ProjectWorkIdx'] ?>)"><?= $row['Worktitle'] ?></td>
                                        <td class="text-center"><?=($row['IntraUrl']?"<button class='btn btn-sm btn-default btn_move_url' data-url='".$row['IntraUrl']."'>인트라넷</button>":'')?></td>
                                        <td class="text-center"><?=$row['Status'] ?></td>
                                        <td class="text-center"><?=$row['Rate'] ?></td>
                                        <td class="text-center"><?=$row['sDate'] ?></td>
                                        <td class="text-center"><?=$row['eDate'] ?></td>
                                        <td class="text-center"><?=($row['Foretime']==null?0:$row['Foretime'])?></td>
                                        <td class="text-center"><?=($row['worktime']==null?0:$row['worktime'])?></td>
                                        <td class="text-center"><?=($row['SUMDoingTime']==null?0:number_format($row['SUMDoingTime']/60,1))?></td>
                                        <td class="noh_ellipsis2"
                                            title="<?= $row['Comment'] ?>"><?= $row['Comment'] ?></td>
                                    </tr>
                                    <?
                                    $_sumforeTime = $_sumforeTime+$row['Foretime'];
                                    $_sumworkTime = $_sumworkTime+$row['worktime'];
                                    $_sumworkAllTime = $_sumworkAllTime+$row['SUMDoingTime'];
                                    $_indexnum++;
                                } ?>
                                <tr>
                                    <td class="text-center" colspan="8">소계</td>
                                    <td class="text-center"><?=$_sumforeTime?></td>
                                    <td class="text-center"><?=$_sumworkTime?></td>
                                    <td class="text-center"><?=($_sumworkAllTime>0?number_format($_sumworkAllTime/60,1):0)?></td>
                                    <td class="text-center">&nbsp;</td>
                                </tr>
                            <? }
                        }else{?>
                            <tr>
                                <td class="text-center" colspan="11">해당내역의 작업의 없습니다.</td>
                            </tr>
                        <? }?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

    </section>

</div>


<script type="text/javascript">

    function fn_info(idx) {
        $("#popdetail").setLayer({
            'url' : '/manager/project/popdetail/' + idx,
            'width' : 1024,
            'max_height' : 500
        });
    }


    $(document).ready(function() {
        !function (a) {
            a.fn.datepicker.dates.kr = {
                days: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"],
                daysShort: ["일", "월", "화", "수", "목", "금", "토"],
                daysMin: ["일", "월", "화", "수", "목", "금", "토"],
                months: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                monthsShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                titleFormat: "yyyy년 MM", /* Leverages same syntax as 'format' */
            }
        }(jQuery);

        $('.noh_datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "kr",
            autoclose: true,
            todayHighlight: true
        });

        jQuery(document).on("click", ".btn_move", function(){
            location.href="/manager/report/view/" + $(this).data("idx");
        });

        $(document).on("click", ".btn_move_url", function () {
            let go_url = $(this).data('url');
            window.open(go_url, '_blank');
            return false;
        });


        jQuery(document).on("click", ".select_term", function(){
            var thisval = $(this).data("term");
            var months = [1,2,3,4,5,6,7,8,9,10,11,12];
            var startdate = new Date();
            var nowYear = startdate.getFullYear();
            if ( months[startdate.getMonth()] < 10 ) {
                var nowMonth = '0'+months[startdate.getMonth()];
            }else{
                var nowMonth = months[startdate.getMonth()];
            }
            if ( startdate.getDate() < 10 ) {
                var nowDate = '0'+startdate.getDate();
            }else{
                var nowDate = startdate.getDate();
            }

            document.getElementById('search_end_date').value =  nowYear + "-" + nowMonth + "-" + nowDate;
            var endate = new Date(startdate);
            endate.setDate(endate.getDate() - thisval);
            var nda = new Date(endate);
            var newYear = nda.getFullYear();
            if ( months[nda.getMonth()] < 10 ) {
                var newMonth = '0'+months[nda.getMonth()];
            }else{
                var newMonth = months[nda.getMonth()];
            }
            if ( nda.getDate() < 10 ) {
                var newDate = '0'+nda.getDate();
            }else{
                var newDate = nda.getDate();
            }
            document.getElementById('search_start_date').value =  newYear + "-" + newMonth + "-" + newDate;
            document.getElementById('SelectTerm').value = thisval;

            $(".select_term").removeClass("active");
            $(this).addClass("active");
            return false;

        });


    });

  </script>


<script type='text/javascript' src='<?php echo base_url(); ?>assets/js/jquery.mousewheel.min.js'></script>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script>
    $(function() {
        $("#HorizontalArea1").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 30);
            event.preventDefault();
        });
        $("#HorizontalArea2").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 30);
            event.preventDefault();
        });
    });

</script>