<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-hand-o-right"></i> Project관리
            <small class="text-red">※ 단순업무는 팀별프로젝트로 생성해서 사용하세요 EX) 로고디자인, 개선작업등 </small>
        </h1>
    </section>
    <section class="content">
    <form action="<?php echo base_url() ?>manager/project" method="POST" id="searchList">
        <input type="hidden" name="paging" id="paging" value="<?=!isset($search['paging'])?0:$search['paging']?>">
        <input type="hidden" id="SelectTerm"  name="SelectTerm"  value="<?=(isset($search['SelectTerm']) === false ? 30 : $search['SelectTerm'])?>">
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
                                    <div class="col-md-12">
                                        <div class="box box-primary">
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <div class="col-md-2">
                                                        <label for="srhProjectMode" class="hidden">프로젝트구분</label>
                                                        <select name="srhProjectMode" id="srhProjectMode" class="form-control">
                                                            <option value="">프로젝트구분</option>
                                                            <? foreach ( $CommonCode['ProjectMode'] as $key => $val ) {?>
                                                                <option value="<?=$key?>" <?=selected($key, @$search['srhProjectMode'])?>><?=$val['name']?></option>
                                                            <?} ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-1">
                                                        <label for="srhProjectStatus" class="hidden">진행상태</label>
                                                        <select name="srhProjectStatus" id="srhProjectStatus" class="form-control">
                                                            <option value="">진행상태</option>
                                                            <option value="4" <?=selected('4', @$search['srhProjectStatus'])?>>대기</option>
                                                            <option value="1" <?=selected('1', @$search['srhProjectStatus'])?>>진행중</option>
                                                            <option value="2" <?=selected('2', @$search['srhProjectStatus'])?>>완료</option>
                                                            <option value="3" <?=selected('3', @$search['srhProjectStatus'])?>>중단</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-1 control-label text-right">
                                                        <label for="searchSubject" class="hidden">Role</label>
                                                        <select name="searchSubject" id="searchSubject" class="form-control">
                                                            <option value="ProjectTitle" <?=selected('ProjectTitle', @$search['searchSubject'])?>>프로젝트명</option>
                                                            <option value="UserName" <?=selected('UserName', @$search['searchSubject'])?>>작성자</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="searchText" value="<?=@$search['searchText']; ?>" class="form-control input-sm pull-right "  placeholder="Search"/>

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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-1 control-label text-center">
                                            <label for="search_start_date">기간조회</label>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span id="select_term_30" class="margin-r-5 btn btn-default active select_term" data-term="30"> 1개월 </span>
                                            <span id="select_term_60" class="margin-r-5 btn btn-default select_term" data-term="60"> 2개월 </span>
                                            <span id="select_term_90" class="margin-r-5 btn btn-default select_term" data-term="90"> 3개월 </span>
                                            <span id="select_term_180" class="margin-r-5 btn btn-default select_term" data-term="180"> 6개월 </span>
                                            <span id="select_term_365" class="margin-r-5 btn btn-default select_term" data-term="365"> 12개월 </span>
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
                                            <button type="button" class="btn btn-primary bg-blue btn_reg_project">프로젝트생성</button>
                                        </div>
                                        <div class="col-md-1 <?=$LoggedInfo['role'] == ROLE_EMPLOYEE ?'display_none':''?>">
                                            <button type="button" class="btn btn-primary bg-blue btn_view_all_project ">팀업무보기</button>
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
                                <col style="width:4%;">
                                <col style="width:6%;">
                                <col style="width:6%;">
                                <col style="width:10%;">
                                <col >
                                <col style="width:5%;">
                                <col style="width:7%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th  class="text-center bg-gray">No.</th>
                                <th  class="text-center bg-gray">생성자</th>
                                <th  class="text-center bg-gray">구분</th>
                                <th  class="text-center bg-gray">프로젝트No</th>
                                <th  class="text-center bg-gray">프로젝트명</th>
                                <th  class="text-center bg-gray">상태</th>
                                <th  class="text-center bg-gray">생성일자</th>
                                <th  class="text-center bg-gray">정보</th>
                                <th  class="text-center bg-gray">일정</th>
                            </tr>
                            <tr>
                                <th  class="text-center">고정</th>
                                <th  class="text-center">시스템</th>
                                <th  class="text-center">유지보수</th>
                                <th  class="text-center">20190704162000</th>
                                <th  class="text-left">유지보수업무(공통)</th>
                                <th  class="text-center"></th>
                                <th  class="text-center"></th>
                                <th  class="text-center"></th>
                                <th  class="text-center">
                                    <button type="button" class="btn btn-xs bg-blue btn_move_detail" id="pop_detail_2" data-idx="2" data-mode="2">확인</button>
                                </th>
                            </tr>
                            <tr>
                                <th  class="text-center">고정</th>
                                <th  class="text-center">시스템</th>
                                <th  class="text-center">기타</th>
                                <th  class="text-center">20190719125904</th>
                                <th  class="text-left">research and development(공통)</th>
                                <th  class="text-center"></th>
                                <th  class="text-center"></th>
                                <th  class="text-center">
                                    <button type="button" class="btn btn-xs bg-default btn_view_detail <?=$LoggedInfo['role'] == ROLE_ADMIN ?'':'display_none'?>"  data-idx="5" >확인</button>
                                </th>
                                <th  class="text-center">
                                    <button type="button" class="btn btn-xs bg-blue btn_move_detail " id="pop_detail_2" data-idx="5" data-mode="2">확인</button>
                                </th>
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
   $(document).on("click", ".btn_view_detail", function () {
       let idx = $(this).data('idx');
        $("#popdetail").setLayer({
            'url' : '/manager/project/popmodify/' + idx,
            'width' : 1024,
            'max_height' : 500
        });
    });

   $(document).on("click", ".btn_reg_project", function () {
       $("#popdetail").setLayer({
           'url' : '/manager/project/popreg/',
           'width' : 1024,
           'max_height' : 500
       });
   });

   $(document).on("click", ".btn_view_all_project", function () {
       location.href="/manager/project/allview";
       return false;
   });


</script>
<script type="text/javascript">
    var datatable;
    var $search_form = $('#searchList');

    var selectTerm = localStorage.getItem('selectTerm');

    if ( selectTerm !== null ) {
        $(".select_term").removeClass('active');
        $("#select_term_" + selectTerm ).addClass('active');
        change_termdate(selectTerm);
    }

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
            searching: false,
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
            "lengthMenu": [[10, 25, 50], [10, 25, 50]],
            ajax: {
                'url' : '/manager/project/ajax_list',
                'type' : 'get',
                'data' : function(data) {
                    //console.log('data.start',data);
                    $('#paging').val(data.start);
                    var searchValue = data.search.value;
                    console.log("searchValue",searchValue);
                    return $.extend($search_form.serializeArray(), { 'start' : data.start, 'length' : data.length});
                }
            },
            columns: [
                {'data' : null, 'render' : function(data, type, row, meta) {
                    return datatable.page.info().recordsTotal - (meta.row + meta.settings._iDisplayStart);
                }},
                {'data' : 'UserName'},
                {'data' : 'ProjectMode', 'render' : function(data, type, row, meta) {
                    let ProjectModeText = ( data == 1 ? '일반' : (( data == 2) ? '유지보수' : '기타') );
                        return  ProjectModeText ;
                    }},
                {'data' : 'ProjectNo', 'render' : function(data, type, row, meta) {
                    return '<span class="bnt_pop_detail noh_cursor">' + data + '</span>';
                }},
                {'data' : 'ProjectTitle', 'render' : function(data, type, row, meta) {
                        return data.length > 50 ?
                            data.substr( 0, 55 ) +'…' :
                            data;
                }},
                {'data' : 'strProjectStatus'},
                {'data' : 'RegDatetime', 'render' : function(data, type, row, meta) {
                        return data.substr(0, 10);
                }},
                {'data' : 'ProjectIdx', 'render' : function(data, type, row, meta) {
                    return '<button type="button" class="btn btn-xs bg-default btn_view_detail"  data-idx="' + data +'" >확인</button>';
                }},
                {'data' : 'ProjectIdx', 'render' : function(data, type, row, meta) {
                    if ( row.ProjectStatus == 1 || row.ProjectStatus == 2) {
                        return '<button type="button" class="btn btn-xs bg-blue btn_move_detail" id="pop_detail_' + data +'" data-idx="' + data +'" data-mode="' + row.ProjectMode +'">확인</button>';
                    }else{
                        return '';
                    }
                 }}
            ],
            columnDefs: [
                { className: 'text-center', targets: [0,1,2,3,5,6,7,8] },
                { className: 'text-left', targets: [4] }
            ]
        });

        // search_form submit
        $search_form.submit(function(e) {
            e.preventDefault();
            datatable.draw();
        });

        //$('#list_table').on('click', '.btn-income-send',function() {
        jQuery(document).on("click", ".btn_move_detail", function(){
            let thisid = encodeURI($(this).data("idx"));// $(this).attr("id");
            location.href="/manager/project/view/" + thisid;

            /*if ( $(this).data("mode") == 2 ) {
                location.href="/manager/project/dailyview/" + thisid;
            }else{
                location.href="/manager/project/view/" + thisid;
            }*/

        });

        jQuery(document).on("click", ".select_term,#select_term_30,#select_term_60,#select_term_90,#select_term_180,#select_term_365", function(){
            var thisval = $(this).data("term");

            localStorage.setItem('selectTerm', thisval);
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

    function formatNumber(n) {
        return n.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }


    function change_termdate(thisval) {

        localStorage.setItem('selectTerm', thisval);
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

