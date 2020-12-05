<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-list-alt"></i> 게시판
            <small>공지사항 & FAQ <?=isset($mode)?'수정':'등록'?></small>
        </h1>
    </section>
    <section class="content">
        <form role="form" id="regBoard" action="<?php echo base_url() ?>manager/board/insert" method="post">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">옵션정보</div>
                        <div class="panel-body">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="Title">제목</label>
                                            <input type="text" class="form-control required" value="" id="Title" name="Title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TargetPlant">대상법인</label>
                                            <div class="btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-primary all_select">
                                                    <input type="checkbox" id="all_select"> 전체
                                                </label>
                                                <?php foreach ( (array)$codeBusiness as $key=>$value ) { ?>
                                                    <label class="btn btn-primary target_label_bcode">
                                                        <input type="checkbox" name="TargetPlant[]" value="<?=$value['Code']?>" class="target_bcode"> <?=$value['Name']?>
                                                    </label>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="BoardName" class="margin-r-5"> 구분 </label><br />
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-default active">
                                                    <input type="radio" name="BoardName" id="BoardName-1" value="Notice" checked > 공지사항
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="radio" name="BoardName" id="BoardName-2" value="Faq" > FAQ
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Permission" class="margin-r-5"> 공개범위 </label><br />
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-default active">
                                                    <input type="radio" name="Permission" id="Permission-1" value="All" checked> 전체공개
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="radio" name="Permission" id="Permission-2" value="Hidden"> 숨김
                                                </label>
                                                <label class="btn btn-default">
                                                    <input type="radio" name="Permission" id="Permission-3" value="Part"> 지정업체만
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                    <div class="col-xs-12">

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <strong>내용 등록 </strong>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <textarea name="content" id="content"  style="width: 100%; height: 390px;" title="내용"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="col-md-1 text-left">
                        <button class="btn btn-m btn-primary btn-move-list">목록</button>
                    </div>
                    <div class="col-md-9">

                    </div>
                    <div class="col-md-2 text-right">
                        <!--<button class="btn btn-m btn-primary btn-regist">등록</button>-->
                        <input type="submit" class="btn btn-primary" value="Submit" />&nbsp;&nbsp;
                        <input type="reset" class="btn btn-default btn_reset" value="Reset" />
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/plugins/validator/validator.js"></script>
<script src="<?php echo base_url(); ?>assets/js/validation_util.js"></script>
<script src="<?php echo base_url(); ?>assets/js/util.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/pnotify.core.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-2.2.4/form/jquery.form.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/regBoard.js" type="text/javascript"></script>

<!-- daum editor -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/daumeditor/css/editor.css">
<script src="<?php echo base_url(); ?>assets/plugins/daumeditor/js/editor_loader.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/daumeditor/js/editor_creator.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/daumeditor/daum_editor.js"></script>

<script type="text/javascript">

    var $regi_form = $('#regBoard');
    jQuery(document).ready(function(){
        // daum editor load
        initEditor('regBoard', 'content', $regi_form.find('textarea[name=content]').val(), true, 1, 'notice');

        jQuery(document).on("click", ".btn-move-list", function(){
            location.href = '/manager/board';
        });


        jQuery(document).on("click", ".btn_reset", function(){
            $(".target_label_bcode").removeClass("active");
            $(".target_bcode").prop("checked", false);
            $(".all_select").removeClass("active");
        });

        jQuery(document).on("click", ".all_select", function(){
            var ischecked = $(this).find("#all_select").is(":checked");
            if ( ischecked === false ) {
                $(this).addClass("active");
                $(".target_label_bcode").addClass("active");
                $(".target_bcode").prop("checked", true);
                $(this).find("#all_select").prop("checked", true);
            }else{
                $(this).removeClass("active");
                $(".target_label_bcode").removeClass("active");
                $(".target_bcode").prop("checked", false);
                $(this).find("#all_select").prop("checked", false);

            }
            return false;
        });
        jQuery(document).on("click", ".target_label_bcode", function(){
            var totalcount = $('.target_label_bcode').length;
            var checkcount = 0;

            var ischecked = $(this).find("input").is(":checked");
            if ( ischecked === false ) {
                $(this).addClass("active");
                $(this).find("input").prop("checked", true);
            }else{
                $(this).removeClass("active");
                $(this).find("input").prop("checked", false);
            }

            $('.target_label_bcode').each(function(){
                if ( $(this).find("input").is(":checked") === true) {
                    checkcount++;
                }
            });
            if ( totalcount == checkcount ) {
                $(this).find("#all_select").prop("checked", true);
                $(".all_select").addClass("active");
            }else{
                $(this).find("#all_select").prop("checked", false);
                $(".all_select").removeClass("active");
            }

            return false;
        });
    });
</script>
