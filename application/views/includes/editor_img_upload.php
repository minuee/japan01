<link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/dist/css/common.css" rel="stylesheet" type="text/css" />
<div class="modal-body">

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">사진 첨부</div>
            <div class="panel-body">
                <div class="box-body">
                    <div id="form-errors"></div>
                    <form class="form-horizontal mt-10" id="regi_form" name="regi_form" method="post" enctype="multipart/form-data" onsubmit="return false;">
                        <div class="form-group">
                            <div class="col-xs-8">
                                <input type="file" class="form-control" id="photo_file" name="photo_file">
                            </div>
                            <div class="col-xs-2 no-padding">
                                <button type="submit" class="btn bg-red">업로드</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <ul class="photo-preview mailbox-attachments clearfix">
                        <? for($i = 1; $i <= 5; $i++) { ?>
                            <li class="no-border">
                                <span class="mailbox-attachment-icon has-img img-bordered">
                                    <img id="photo_<?=$i?>" class="img-responsive" src="" alt="">
                                </span>
                            </li>
                        <? } ?>
                    </ul>
                </div>

            </div>
        </div>

        <div class="col-xs-6">
            <button type="button" id="btn_regist" class="btn btn-primary mr-5">등록</button>
            <button type="button" id="btn_cancel" class="btn bg-red">취소</button>
        </div>
        <div class="col-xs-6 text-right">
            <button type="button" id="btn_close" class="btn bg-gray" onclick="closeWindow();">닫기</button>
        </div>
    </div>

</div>
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- form validator -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-2.2.4/form/jquery.form.min.js"></script>
<!--<script src="<?php /*echo base_url(); */?>assets/plugins/validator/multifield.js"></script>-->
<script src="<?php echo base_url(); ?>assets/plugins/validator/validator.js"></script>
<script src="<?php echo base_url(); ?>assets/js/validation_util.js"></script>
<script src="<?php echo base_url(); ?>assets/js/util.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/pnotify.core.js"></script>

<!-- daum editor -->
<script src="<?php echo base_url(); ?>assets/plugins/daumeditor/js/popup.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/daumeditor/daum_editor.js"></script>

<script type="text/javascript">

    var regi_form = $('#regi_form');
    var photo_limit_cnt = 5;
    var photo_cnt = 0;
    var photo_data = {};

    $(document).ready(function() {

        initEditorUploader();

        // 사진 업로드
        regi_form.submit(function() {
            $('.form-group').removeClass('has-error');
            $('#form-errors').html('');

            if(photo_cnt > photo_limit_cnt) {
                alert('사진은 한번에 5개씩 등록하실 수 있습니다.');
                return;
            }

            var result = uploadEditorPhoto(regi_form, '/manager/board/imgupload', 'photo_file', 'photo_' + (photo_cnt+1));
            if(result !== false) {
                photo_data[photo_cnt] = result;
                photo_cnt++;
            }else{
                alert("파일을 선택하세요");
                return false;
            }
            //console.log(JSON.stringify($photo_data));
        });

        // 사진 등록

        jQuery(document).on("click", "#btn_regist", function(){
            setEditorPhoto(photo_data, 'L', true, 500);
            if(photo_cnt == 0) {
                alert("사진 업로드 후 등록 버튼을 눌러주세요.");
                return;
            }

            //closeWindow();
        });



        // 취소 버튼 클릭 (업로드된 사진 삭제)
        jQuery(document).on("click", "#btn_cancel", function(){
            var url = '/manager/board/imgdestory';

            if(removeEditorPhoto(url, photo_data, $regi_form.find('input[name="_token"]').val()) === true) {
                closeWindow();
            }
        });
    });
</script>



