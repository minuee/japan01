<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-dollar"></i> 입금관리
            <small>LG U+ 가상계좌 입금관리</small>
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
                            <form action="<?php echo base_url() ?>manager/condition" method="POST" id="searchList">
                                <input type="hidden" name="paging" id="paging" value="0">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <div class="col-md-2">
                                                    <select name="PublishCode" id="PublishCode" class="form-control">
                                                        <option value="">출판법인 선택</option>
                                                        <?php foreach ( (array)$codeBusiness as $key=>$value ) { ?>
                                                            <option value="<?=$value['Code']?>" <?=selected($value['Code'], @$search['PublishCode'])?>><?=$value['Name']?></option>
                                                        <?php } ?>
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
                                                    <label for="search_sap_status_ccd">입금자명 검색</label>
                                                </div>
                                                <div class="col-md-3">
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
                            </form>
                        </div>
                    </div>
                </div>
                <div class="box col-md-12">
                    <div class="box-body table-responsive">
                        <table id="list_table"  class="table table-striped table-bordered table-hover">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:10%;">
                                <col>
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:12%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th  class="text-center">No.</th>
                                <th  class="text-center">법인명</th>
                                <th  class="text-center">PG사주문번호</th>
                                <th  class="text-center">입금입자</th>
                                <th  class="text-center">계좌번호</th>
                                <th  class="text-center">입금금액</th>
                                <th  class="text-center">미처리금액</th>
                                <th  class="text-center">실입금자</th>
                                <th  class="text-center">입금처리</th>
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
                'url' : '/manager/income/ajax_list',
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
                {'data' : 'PublishCorporationCCDName'},
                {'data' : 'OID'},
                {'data' : 'PayDatetime'},
                {'data' : 'AccountNo'},
                {'data' : 'Amount', 'render' : function( data, type, row, meta)  {
                    return formatNumber(data);
                }},
                {'data' : null},
                {'data' : 'Buyer'},
                {'data' : 'OrderingDataIdx', 'render' : function(data, type, row, meta) {
                    return '<button type="button" class="btn btn-xs bg-blue btn-income-detail" id="pop_detail_' + data +'" data-idx="' + data +'">주문내역</button>&nbsp;&nbsp;<button type="button" class="btn btn-xs bg-primary btn-income-send" id="pop_view_' + data +'" data-idx="' + data +'">입금처리</button>';
                }}
            ],
            columnDefs: [
                { className: 'text-center', targets: [0,1,2,3,4,7,8] },
                { className: 'text-right', targets: [5,6] }
            ]
        });

        // search_form submit
        $search_form.submit(function(e) {
            e.preventDefault();
            datatable.draw();
        });

        //$('#list_table').on('click', '.btn-income-send',function() {
         jQuery(document).on("click", ".btn-income-send", function(){
            var thisid = $(this).attr("id");
            $(thisid).setLayer({
                'url' : '/manager/income/popview/' + $(this).data('idx')
            });
        });

        //$('#list_table').on('click', '.btn-income-detail',function() {
        jQuery(document).on("click", ".btn-income-detail", function(){
            var thisid = $(this).attr("id");
            $(thisid).setLayer({
                'url' : '/manager/income/popdetail/' + $(this).data('idx'),
                'width' : 1024
            });
        });


        /*$('#list_table').on('click', '.btn-income-send', function() {
            $('.btn-income-send').setLayer({
                'url' : '/manager/income/popview/' + $(this).data('idx')
            });
        });*/

    });

    function formatNumber(n) {
        return n.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
</script>

