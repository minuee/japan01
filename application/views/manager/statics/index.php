<!-- Morris charts -->
<link href="<?php echo base_url(); ?>assets/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

<style>
    .group_wrap {clear:both;position:relative;width:100%;height:auto;}
    .group_wrap ul{clear:both;position:relative;width:96%;height:auto;min-height:30px;padding:5px 2%;}
    .group_wrap ul li{float:left;width:auto;min-width:80px;height:25px;padding:1px 5px;border-radius:5px;background-color: #fff;border:1px solid #ccc;list-style: none; text-align:center;margin-right:10px;margin-bottom:5px;cursor: pointer;color:#000;font-size:14px;}
    .group_wrap ul li.on{background-color: #605ca8;border:1px solid #605ca8;color:#fff}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Report DashBoard
            <input type="hidden" id="RastWorkIdx" value="<?=$RecentlyWorks[0]['ProjectWorkIdx']?>" >
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> 관리</a></li>
            <li class="active">Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 id="Text_ProjectTotalCount"><?=number_format($ProjectTotalCount)?></h3>
                        <p>Registed Project</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-android-apps"></i>
                    </div>
                    <a href="<?php echo base_url(); ?>manager/project" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 id="Text_ProjectWorkTotalCount"><?=number_format($ProjectWorkTotalCount)?></h3>
                        <p>Registed Works</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-android-apps"></i>
                    </div>
                    <a class="small-box-footer">&nbsp;</a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3 id="Text_ProjectWorkRate"><?=number_format($ProjectWorkRate,1)?><sup style="font-size: 20px">%</sup></h3>
                        <p>Average Progress Without Done</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">&nbsp;</a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?=number_format($UserTotalCount)?></h3>
                        <p>Registed User</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person"></i>
                    </div>
                    <a href="#" class="small-box-footer">&nbsp;</a>
                </div>
            </div><!-- ./col -->

        </div>
        <!-- Info boxes -->
        <div class="row">
            <? foreach ( $CommonCode['ChildMode'] as $key => $val ) {?>
                <div class="col-md-2 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon2 <?=$val['class']?>"><?=$val['title']?></span>
                        <div class="info-box-content">
                            <span class="info-box-text">전체 <b class="f_r"><?=number_format($val['Totalcount'])?></b></span>
                            <span class="info-box-text">ToDo <b class="f_r"><?=number_format($val['Todocount'])?></b></span>
                            <span class="info-box-text">Doing <b class="f_r"><?=number_format($val['Doingcount'])?></b></span>
                            <span class="info-box-text">Done <b class="f_r"><?=number_format($val['Donecount'])?></b></span>
                        </div>
                    </div>
                </div>
            <?} ?>

        </div>


        <div class="row">
            <!-- Left col -->
            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Work Type Usage</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="chart-responsive">
                                    <canvas id="pieChart" height="300"></canvas>
                                </div>
                                <!-- ./chart-responsive -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-2">
                                <ul class="chart-legend clearfix">
                                    <? foreach ( $CommonCode['ChildMode'] as $key => $val ) {?>
                                        <li><i class="fa fa-circle-o <?=$val['textclass']?>"></i> <?=$val['title']?></li>
                                    <? } ?>
                                   <!-- <li><i class="fa fa-circle-o text-red"></i> Chrome</li>
                                    <li><i class="fa fa-circle-o text-green"></i> IE</li>
                                    <li><i class="fa fa-circle-o text-yellow"></i> FireFox</li>
                                    <li><i class="fa fa-circle-o text-aqua"></i> Safari</li>
                                    <li><i class="fa fa-circle-o text-light-blue"></i> Opera</li>
                                    <li><i class="fa fa-circle-o text-gray"></i> Navigator</li>-->
                                </ul>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
            <!-- Right col -->
            <div class="col-md-8">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recently Works 10</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <colgroup>
                                    <col style="width: calc( 100% - 350px )">
                                    <col style="width:100px">
                                    <col style="width:150px">
                                    <col style="width:100px">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>Work Title</th>
                                    <th>Worker</th>
                                    <th>Team</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="target_history">
                                <? foreach( $RecentlyWorks as $skey => $row )  {?>
                                <tr class="RecentlyWorkList">
                                    <td><span  class="noh_ellipsis3"><?=htmlspecialchars($row['title'])?></span></td>
                                    <td><?=$row['TodoName']?></td>
                                    <td><?=$row['GroupName']?></td>
                                    <td><span class="label" style="background-color: <?=$row['StatusColor']?>"><?=$row['StatusText']?></span></td>
                                </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Left col -->
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <span class="f_l mr_10">투입시간(최근1주일)</span>
                            <span class="f_l">
                                <label for="ProjectGroup" class="hidden">사업부</label>
                                <select class="form-control noh_text_14 w_100" id="ProjectGroup" style="max-width:150px !important;">
                                    <option value="0">사업부선택</option>
                                    <? foreach( $BUSINESSCode as $key => $row) { ?>
                                        <option value="<?=$row['code']?>" <?=selected($row['code'], $search['ProjectGroup'])?>><?=$row['name']?></option>
                                    <? }?>
                                </select>
                            </span>
                            <span class="f_l">
                                <label for="ProjectTeam" class="hidden">팀</label>
                                <select class="form-control noh_text_14 w_100" id="ProjectTeam" style="max-width:150px !important;">
                                    <option value="0">팀선택</option>
                                </select>
                            </span>
                            <span class="f_l">
                                <label for="ToDoID" class="hidden">직원</label>
                                <select class="form-control noh_text_14" name="ToDoID" id="ToDoID" style="max-width:200px !important;">
                                    <option value="0">직원선택</option>
                                    <? foreach ( $Users as  $key => $val ) {?>
                                        <option value="<?=$val['userId']?>">[<?=$val['GROUP_NAME']?>] <?=$val['name']?> <?=$val['CLASS_NAME']?></option>
                                    <? }?>
                                </select>
                            </span>
                        </h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="line-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <!-- Right col -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Make Works And Done</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="revenue-chart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <!-- Left col -->
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">최근 10일 등록건수(feat 완료) Rank 7</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="bar-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <!-- Right col -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">투입시간대비 효율(투입/예상) Rank 7</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="bar-chart2" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left col -->
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">리포트 미등록자(기준 : 요일(화~토) 전일 0시,일 2일전 0시 ,월 3일전 0시), 색상 : 연차사용자</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body group_wrap">
                        <ul>
                            <?
                            foreach( $ReportNotUser as $tkey => $trow) { ?>
                                <li class="toggle_select <?=$trow['ScheduleIdx'] > 0 ?"on":""?>">[<?=$trow['GROUP_NAME']?>]<?=$trow['UserName']?> <?=$trow['CLASS_NAME']?></li>
                            <? }?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </section>
</div>

<!-- Sparkline -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo base_url(); ?>assets/plugins/chart.js/Chart.js"></script>

<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/plugins/morris/fastclick.js"></script>
<script>
    
    function fn_blink () {
        setTimeout(function() {
            $(".isnewdata").removeClass("bg-light-blue");
        }, 9000);
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $(function () {
        'use strict';

        //$('#list_table').on('click', '.btn-income-send',function() {
        jQuery(document).on("change", "#ProjectGroup", function(){
            $("#ToDoID").html("<option value='0'>직원선택</option>");
            // 불러온다
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/getSubTeam",
                data: "GroupCode=" + $(this).val(),
                async: false,
                success: function (json) {
                    if (json.totalCount > 0 ) {
                        $("#ProjectTeam").html('');
                        let html = "<option value='0'>팀선택</option>";
                        $("#ProjectTeam").append(html);
                        for(var i = 0; i < json.dataList.length; i++) {
                            html = "<option value='"+json.dataList[i]['IDX']+"'>"+json.dataList[i]['NAME']+"</option>";
                            $("#ProjectTeam").append(html);
                        }
                        return false;
                    }
                }
            });
        });


        jQuery(document).on("change", "#ProjectTeam", function(){

            let TeamCode = $(this).val() == 0 ? $("#ProjectGroup").val() : $(this).val();
            // 불러온다
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/getTeamMember",
                data: "TeamCode=" + TeamCode,
                async: false,
                success: function (json) {
                    if (json.totalCount > 0 ) {
                        $("#ToDoID").html('');

                        let html = "<option value='0'>작업자선택</option>";
                        $("#ToDoID").append(html);
                        for(var i = 0; i < json.dataList.length; i++) {
                            html = "<option value='"+json.dataList[i]['userId']+"'>"+json.dataList[i]['name']+" "+json.dataList[i]['CLASS_NAME']+"</option>";
                            $("#ToDoID").append(html);
                        }
                        return false;
                    }
                }
            });
        });

        checkLogin = setInterval(function() {
            var RastWorkIdx = $("#RastWorkIdx").val();
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/statics/getRecentlyWorks",
                data: "RastWorkIdx=" + RastWorkIdx,
                async: false,
                beforeSend: function () {
                },
                success: function (json) {
                    if (json.totalCount > 0) {
                        for (var i = 0; i < json.messageList.length; i++) {
                            var html = json.messageList[i]['MessageData'];
                            $("#target_history").prepend(html);
                            $(".RecentlyWorkList:last-child").remove();
                            $("#RastWorkIdx").val(json.messageList[i]['ProjectWorkIdx']);

                        }
                    }

                    if ( json.ProjectWorkTotalCount ) {
                        $("#Text_ProjectWorkTotalCount").text(numberWithCommas(json.ProjectWorkTotalCount));
                    }
                    if ( json.ProjectTotalCount ) {
                        $("#Text_ProjectTotalCount").text(numberWithCommas(json.ProjectTotalCount));
                    }

                    fn_blink();
                    return false;
                },
                complete: function () {

                }
            });
        }, 10000);


        var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
        var pieChart       = new Chart(pieChartCanvas);
        var PieData        = [
        <?php foreach( $CommonCode['ChildMode'] as $key => $val ) { ?>
            {
                value    : <?=$val['Totalcount']?>,
                color    : "<?=$val['color']?>",
                highlight: "<?=$val['color']?>",
                label    : "<?=$val['title']?>"
            },
            <?php   }?>

        ];
        var pieOptions     = {
            segmentShowStroke    : true,
            segmentStrokeColor   : '#fff',
            segmentStrokeWidth   : 1,
            percentageInnerCutout: 50,
            animationSteps       : 100,
            animationEasing      : 'easeOutBounce',
            animateRotate        : true,
            animateScale         : false,
            responsive           : true,
            maintainAspectRatio  : false,
            legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
            tooltipTemplate      : '<%=label%> <%=value %> '
        };
        pieChart.Doughnut(PieData, pieOptions);

    });

</script>
<!-- Morris.js charts -->
<script src="<?php echo base_url(); ?>assets/plugins/morris/morris.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/morris/raphael.min.js"></script>
<script>
    $(function () {
        "use strict";
        $(document).off('change', '#ToDoID,#ProjectTeam').on('change', '#ToDoID,#ProjectTeam',function() {
            var targetIdx = $("#ToDoID").val();
            var teamidx = $("#ProjectTeam").val();
            $.ajax({
                type        : "POST",
                dataType    : "json",
                url         : "/manager/statics/chartupdate",
                data        : "UserIdx="+targetIdx+"&TeamCode="+teamidx,
                async: false,
                success: function(json)
                {
                    Chartline.setData(json);
                }
            });
        });

        // AREA CHART
        var area = new Morris.Area({
            element: 'revenue-chart',
            resize: true,
            data: [
                <?php foreach( $RecentlyWorkType as $key => $val ) { ?>
                {y: "<?=$val['WorkDate']?>", item1: <?=$val['SumProjectWork']?>, item2: <?=$val['SumDoneCount']?>},
                <?php   }?>
            ],
            xkey: 'y',
            ykeys: ['item1', 'item2'],
            labels: ['등록업무', '완료업무'],
            lineColors: ['#605ca8', '#000000'],
            fillOpacity: 0.6,
            hideHover: 'auto',
            behaveLikeLine: true,
            resize: true,
            pointFillColors:['#ffffff'],
            pointStrokeColors: ['black'],
            hideHover: 'auto'
        });

        // LINE CHART
        var Chartline = new Morris.Line({
            element: 'line-chart',
            resize: true,
            redraw: true,
            data: [
                <?php foreach( $RecentlyWorkTime as $key => $val ) { ?>
                {y: '<?=$val['WorkDate']?>', item1: <?=round($val['SumDoingTime']/60,1)?>},
                <?php   }?>
            ],
            xkey: 'y',
            ykeys: ['item1'],
            labels: ['투입시간(시)'],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto'
        });

        // Bar CHART
        var Barline = new Morris.Bar({
            element: 'bar-chart',
            resize: true,
            redraw: true,
            behaveLikeLine: true,
            data: [
                <?php foreach( $RankManyWorks as $key => $val ) { ?>
                {y: '[<?=$val['GroupName']?>]\n<?=$val['UserName']?>', item1: <?=number_format($val['SumAllProjectWork'])?>, item2: <?=number_format($val['SumDoneProjectWork'])?>},
                <?php   }?>
            ],
            xkey: 'y',
            ykeys: ['item1','item2'],
            labels: ['등록건수','완료건수'],
            barColors: ['#605ca8', '#000000'],
            hideHover: 'auto'
        });

        // Bar CHART GroupName
        var Barline2 = new Morris.Bar({
            element: 'bar-chart2',
            resize: true,
            redraw: true,
            behaveLikeLine: true,
            data: [
                <?php foreach( $RankGoodWorks as $key => $val ) { ?>
                {y: '[<?=$val['GroupName']?>]\n<?=$val['UserName']?>', item1: <?=number_format($val['Rate'])?>},
                <?php   }?>
            ],
            xkey: 'y',
            ykeys: ['item1'],
            labels: ['투입률(%)'],
            barColors: ['#ff821f'],
            hideHover: 'auto'
        });

    });
</script>