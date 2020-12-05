<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-list-alt"></i> 게시판
            <small>공지사항 & FAQ</small>
        </h1>
    </section>
    <section class="content">
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
                            <form action="<?php echo base_url() ?>manager/board" method="POST" id="searchList">
                                <input type="hidden" name="paging" id="paging" value="0">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <div class="col-md-1">
                                                    <select name="BoardName" id="BoardName" class="form-control">
                                                        <option value="">구분</option>
                                                        <option value="Notice" <?=selected('Notice', @$search['BoardName'])?>>공지사항</option>
                                                        <option value="Faq" <?=selected('Faq', @$search['BoardName'])?>>FAQ</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-1 control-label">
                                                    <label for="search_start_date">기간조회</label>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group date">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" class="form-control noh_datepicker" id="search_start_date" name="search_start_date" value="">
                                                        <div class="input-group-addon no-border">~</div>
                                                        <div class="input-group-addon no-border-right">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" class="form-control noh_datepicker" id="search_end_date" name="search_end_date" value="">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 control-label text-right">
                                                    <select name="searchSubject" id="searchSubject" class="form-control">
                                                        <option value="Title" <?=selected('Title', @$search['searchSubject'])?>>제목</option>
                                                        <option value="Content" <?=selected('Content', @$search['searchSubject'])?>>내용</option>
                                                        <option value="AdminName" <?=selected('AdminName', @$search['searchSubject'])?>>작성자</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="searchText" value="<?=@$search['searchText']; ?>" class="form-control input-sm pull-right "  placeholder="Search"/>

                                                </div>
                                                <div class="col-md-1">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button class="btn btn-sm btn-primary btn-regist">등록</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="box col-md-12">
                    <div class="box-body table-responsive">
                        <table id="list_table"  class="table table-striped table-bordered table-hover">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:8%;">
                                <col>
                                <col style="width:20%;">
                                <col style="width:7%;">
                                <col style="width:10%;">
                                <col style="width:15%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th  class="text-center">No.</th>
                                <th  class="text-center">구분</th>
                                <th  class="text-center">제목</th>
                                <th  class="text-center">대상법인</th>
                                <th  class="text-center">공개범위</th>
                                <th  class="text-center">작성자</th>
                                <th  class="text-center">작성일</th>
                                <th  class="text-center">상태</th>
                                <th  class="text-center">정보</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script>

    jQuery('.noh_datepicker').datepicker({
        autoClose: true,
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        dayNames: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

</script>

<script type="text/javascript">
    var datatable;
    var $search_form = $('#searchList');

    $(document).ready(function() {

        // DataTable 호출
        datatable = $('#list_table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            ordering: false,
            ajax: {
                'url' : '/manager/board/ajax_list',
                'type' : 'get',
                'data' : function(data) {
                    $('#paging').val(data.start);
                    return $.extend($search_form.serializeArray(), { 'start' : data.start, 'length' : data.length});
                }
            },
            columns: [
                {'data' : null, 'render' : function(data, type, row, meta) {
                    // 리스트 번호
                    return datatable.page.info().recordsTotal - (meta.row + meta.settings._iDisplayStart);
                }},
                {'data' : 'reBoardName'},
                {'data' : 'Title', 'render' : function(data, type, row, meta) {
                        return data.length > 25 ?
                            data.substr( 0, 25 ) +'…' :
                            data;
                }},
                {'data' : null},
                {'data' : 'PermissionName'},
                {'data' : 'AdminName'},
                {'data' : 'RegDatetime'},
                {'data' : 'StatusName'},
                {'data' : 'BoardIdx', 'render' : function(data, type, row, meta) {
                    return '<button type="button" class="btn btn-xs bg-blue btn-detail"  data-idx="' + data +'">상세정보</button>';
                }}
            ],
            columnDefs: [
                { className: 'text-center', targets: [0,1,3,4,5,6,7,8] },
                { className: 'text-left', targets: [2] }
            ]
        });

        // search_form submit
        $search_form.submit(function(e) {
            e.preventDefault();
            datatable.draw();
        });

        //$('#list_table').on('click', '.btn-income-detail',function() {
        jQuery(document).on("click", ".btn-detail", function(){
            location.href = '/manager/board/detail/' + $(this).data('idx');
        });

        //$('#list_table').on('click', '.btn-income-detail',function() {
        jQuery(document).on("click", ".btn-regist", function(){
            location.href = '/manager/board/regist';
        });

    });

    function formatNumber(n) {
        return n.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
</script>

