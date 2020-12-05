<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-files-o"></i> <?=$pageTitle?>
            <small></small>
        </h1>
    </section>
    <section class="content">
        <form action="<?php echo base_url() ?>manager/project" method="POST" id="searchList">
            <input type="hidden" name="paging" id="paging" value="0">
            <input type="hidden" name="searchText" id="searchText" value="<?=@$search['SelectTerm']?>">
            <input type="hidden" id="SelectTerm"  name="SelectTerm"  value="<?=(isset($search['SelectTerm']) === false ? 7 : $search['SelectTerm'])?>">

            <!--<div class="row">
                <div class="col-xs-12 text-right">
                    <div class="form-group">
                        <a class="btn btn-primary" href="<?php /*echo base_url(); */?>addNew"><i class="fa fa-plus"></i> Add New</a>
                    </div>
                </div>
            </div>-->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="box-tools">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-md-2 <?=($LoggedInfo['role'] == ROLE_ADMIN )  ?'' : 'display_none'?>">
                                        <select class="form-control check_target noh_text_14 w_100 ProjectGroup" id="ProjectGroup" name="ProjectGroup">
                                            <option value="0">사업부선택</option>
                                            <? foreach( $BUSINESSCode as $key => $row) { ?>
                                                <option value="<?=$row['code']?>" <?=selected($row['code'], $search['ProjectGroup'])?>><?=$row['name']?></option>
                                            <? }?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 <?=($LoggedInfo['role'] == ROLE_EMPLOYEE || $LoggedInfo['role'] == ROLE_MANAGER)  ?'display_none':''?>">
                                        <select class="form-control check_target noh_text_14 w_100 ProjectTeam" id="ProjectTeam" name="ProjectTeam">
                                            <option value="0">팀선택</option>
                                            <? foreach( $GROUPCode as $key => $row) { ?>
                                                <option value="<?=$key?>" <?=selected($key, $LoggedInfo['groupidx'])?>><?=$row?></option>
                                            <? }?>
                                        </select>
                                    </div>
                                    <!-- <div class="col-md-2 <?/*=$LoggedInfo['role'] !=1?'display_none':''*/?>">
                                        <select class="form-control check_target noh_text_14 w_100" id="ProjectIdx" name="ProjectIdx">
                                            <option value="">유저선택:</option>

                                        </select>
                                    </div>-->
                                    <div class="col-md-3 text-left">
                                        <span id="select_term_0"  class="margin-r-5 btn btn-default select_term" data-term="0"> 하루 </span>
                                        <span id="select_term_7"  class="margin-r-5 btn btn-default active select_term" data-term="7"> 1주일 </span>
                                        <span id="select_term_30"  class="margin-r-5 btn btn-default select_term" data-term="30"> 1개월 </span>
                                        <span id="select_term_60"  class="margin-r-5 btn btn-default select_term" data-term="60"> 2개월 </span>
                                        <span id="select_term_90"  class="margin-r-5 btn btn-default select_term" data-term="90"> 3개월 </span>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control noh_datepicker" id="search_start_date" name="search_start_date" value="<?=$search['search_start_date']?>">
                                            <div class="input-group-addon no-border">~</div>
                                            <div class="input-group-addon no-border-right">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control noh_datepicker" id="search_end_date" name="search_end_date" value="<?=$search['search_end_date']?>">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="input-group-btn">
                                            <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="box col-md-12">
                    <div class="box-body">
                        <table id="list_table"  class="table table-striped table-bordered table-hover">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:15%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:200px;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th  class="text-center">No.</th>
                                <th  class="text-center">팀명</th>
                                <th  class="text-center">작성자</th>
                                <th  class="text-center">작성일자</th>
                                <th  class="text-center">ReportCode</th>
                                <th  class="text-center">상세정보</th>
                                <th  class="text-center">삭제</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
        </form>
    </section>
</div>

<script type="text/javascript">
    var datatable;
    var $search_form = $('#searchList');

    var selectTerm2 = localStorage.getItem('selectTerm2');
    if ( selectTerm2 !== null ) {
        $(".select_term").removeClass('active');
        $("#select_term_" + selectTerm2 ).addClass('active');
        change_termdate(selectTerm2);
    }

    let nowdate = new Date($.now());
    let nowMotnh = nowdate.getMonth()+1;
    let nowDays = nowdate.getDate();
    let todays = nowdate.getFullYear()+"-"+(nowMotnh < 10 ? '0'+nowMotnh : nowMotnh)+"-"+(nowDays < 10  ? '0'+nowDays : nowDays);

    console.log("todays",todays);
    $(document).ready(function() {
        !function(a) {
            a.fn.datepicker.dates.kr = {
                days : [ "일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일" ],
                daysShort : [ "일", "월", "화", "수", "목", "금", "토" ],
                daysMin : [ "일", "월", "화", "수", "목", "금", "토" ],
                months : [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월","11월", "12월" ],
                monthsShort : [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ],
                titleFormat : "yyyy년 MM", /* Leverages same syntax as 'format' */
            }
        }(jQuery);

        $('.noh_datepicker').datepicker({
            format : "yyyy-mm-dd",
            language : "kr",
            autoclose : true,
            todayHighlight : true
        });

        // DataTable 호출
        datatable = $('#list_table').DataTable({
            language: {
                "info": "전체수 : _TOTAL_",
                "infoEmpty": "0 entries",
                "emptyTable": "검색결과가 없습니다.",
                "lengthMenu":     "Rows  _MENU_  개씩",
                paginate: {
                    "first":      "맨처음",
                    "last":       "맨마지막",
                    "next":       "다음",
                    "previous":   "이전"
                }
            },
            processing: true,
            serverSide: true,
            searching: true,
            lengthChange: true,
            responsive: true,
            ordering: false,
            bStateSave : true,
            fnStateSave: function (oSettings, oData) {
                localStorage.setItem('list_tableDataTables', JSON.stringify(oData));
            },
            fnStateLoad: function (oSettings) {
                return JSON.parse(localStorage.getItem('list_tableDataTables'));
            },
            pagingType: "full_numbers",
            "lengthMenu": [[10, 30, 50 ], [10, 30, 50 ]],
            ajax: {
                'url' : '/manager/report/ajax_list',
                'type' : 'get',
                'data' : function(data) {
                    $('#paging').val(data.start);
                    let searchtext = $("#list_table_filter").find("input").val();
                    $('#searchText').val(searchtext);
                    return $.extend($search_form.serializeArray(), { 'start' : data.start, 'length' : data.length});
                }
            },
            columns: [
                {'data' : null, 'render' : function(data, type, row, meta) {
                        return datatable.page.info().recordsTotal - (meta.row + meta.settings._iDisplayStart);
                    }},
                {'data' : 'GroupName'},
                {'data' : 'UserName'},
                {'data' : 'wDate'},
                {'data' : 'reportGroup'},
                {'data' : 'ReportIdx', 'render' : function(data, type, row, meta) {
                        return '<button type="button" class="btn btn-xs bg-default btn_view_detail"  data-idx="' + data +'">확인</button>';
                    }},
                {'data' : 'ReportIdx', 'render' : function(data, type, row, meta) {
                        if ( todays == row.wDate && row.userId == "<?=$LoggedInfo['userId']?>" ) {
                            return '<button type="button" class="btn btn-xs bg-default btn_remove"  data-idx="' + row.reportGroup +'" data-code="'+row.wDate+'">삭제</button>';
                        }else{
                            return null;

                        }

                    }}
            ],
            columnDefs: [
                { className: 'text-center', targets: [0,1,2,3,4,5,6] }
            ]
        });

        // search_form submit
        $search_form.submit(function(e) {
            e.preventDefault();
            datatable.draw();
        });

        //$('#list_table').on('click', '.btn-income-send',function() {
        jQuery(document).on("click", ".btn_view_detail", function(){
            let thisid = encodeURI($(this).data("idx"));// $(this).attr("id");
            location.href="/manager/report/view/" + thisid;
        });

        //$('#list_table').on('click', '.btn-income-send',function() {
        jQuery(document).on("click", ".btn_remove", function(){
            let thisid = $(this).data("idx");

            let wdate = $(this).data("code");
            if ( todays > wdate) {
                alert('오늘 이전보고는 삭제하실수 없습니다');
                return false;
            }
            if (!confirm('정말로 삭제하시겠습니까?')) return false;

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/report/remove",
                data: "ReportIdx=" + thisid,
                async: false,
                beforeSend: function () {
                    $('.wrap-loading').removeClass('display_none');
                },
                success: function (json) {
                    if (json.result === true) {
                        alert('정상적으로 삭제되었습니다.');
                        location.reload();
                        return false;
                    } else {
                        alert(json.message);
                        return false;

                    }
                },
                complete: function () {
                    $('.wrap-loading').addClass('display_none');
                }
            });
            return false;
        });

        //$('#list_table').on('click', '.btn-income-send',function() {
        jQuery(document).on("change", "#ProjectGroup", function(){

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


        jQuery(document).on("click", ".select_term", function(){
            var thisval = $(this).data("term");
            localStorage.setItem('selectTerm2', thisval);
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


    function change_termdate(thisval) {

        localStorage.setItem('selectTerm2', thisval);
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
        return false;
    }



</script>

