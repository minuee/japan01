<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-dollar"></i> 가격결정
            <small>교재DB 3단계 정보연동</small>
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
                    <div class="box-header">
                        <h3 class="box-title">가격정책 현황</h3>

                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-tools">
                            <form action="<?php echo base_url() ?>manager/condition" method="POST" id="searchList">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <div class="col-md-2 control-label">
                                                    <select name="IsStatus" id="IsStatus" class="form-control">
                                                        <option value="0" <?=@$search['IsStatus'] == ''  ? "selected='selected'" : ""; ?>>상태 선택</option>
                                                        <option value="1" <?=@$search['IsStatus'] == 1  ? "selected='selected'" : ""; ?>>1단계</option>
                                                        <option value="2"  <?=@$search['IsStatus'] == 2  ? "selected='selected'" : ""; ?>>2단계</option>
                                                        <option value="3"  <?=@$search['IsStatus'] == 3  ? "selected='selected'" : ""; ?>>3단계</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="PublishCode" id="PublishCode" class="form-control">
                                                        <option value="">출판법인 선택</option>
                                                        <?php foreach ( (array)$codeBusiness as $key=>$value ) { ?>
                                                            <option value="<?=$value['Code']?>" <?=selected($value['Code'], @$search['PublishCode'])?>><?=$value['Name']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 control-label text-right">
                                                    <label for="search_sap_status_ccd">업체명 검색</label>
                                                </div>
                                                <div class="col-md-4">
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
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-striped table-bordered table-hover">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col>
                                <col style="width:6%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th  class="text-center">No.</th>
                                <th  class="text-center">구분</th>
                                <th  class="text-center">대상법인</th>
                                <th  class="text-center">제품군</th>
                                <th  class="text-center">가격리스트</th>
                                <th  class="text-center">대상고객</th>
                                <th  class="text-center">할인율</th>
                                <th  class="text-center">등록일</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($userRecords))
                            {
                                $num = $totalRecords-$now_page;
                                foreach($userRecords as $record)
                                {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?=$num?></td>
                                        <td class="text-center">1단계</td>
                                        <td class="text-center"><?=!empty($record->VKORG) ? $codename['SAP_VKORG'][$record->VKORG] : '' ?></td>
                                        <td class="text-center"><?=!empty($record->SPART) ? $codename['SAP_SPART'][$record->SPART] : '' ?></td>
                                        <td class="text-center"><?=$record->strPLTYP?></td>
                                        <td><?=$record->CompanyName?></td>
                                        <td class="text-center"><?=$record->PRICE?></td>
                                        <td class="text-center"><?php echo date("Y-m-d", strtotime($record->RegDatetime)) ?></td>
                                    </tr>
                                    <?php
                                    $num--;
                                }
                            }
                            ?>
                            </tbody>
                        </table>

                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <?php echo $this->pagination->create_links(); ?>
                    </div>
                </div><!-- /.box -->
            </div>
        </div>
    </section>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script type="text/javascript">



    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", baseURL + "manager/condition/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>
