<link href="<?php echo base_url(); ?>assets/dist/css/sitemap.css?v=<?=time()?>" rel="stylesheet" type="text/css" />


<div class="content-wrapper clear" style="clear:both !important;">
    <div class="row">
        <div class="col-lg-12" style="overflow-x:auto;padding:20px 0;" id="TeamProjectArea">
            <div class="nohsitemap">
                <nav class="primary">
                    <ul class="this_sitemap"  id="teamKanban">
                        <?
                        foreach ( $Group as $key => $val) {
                            if ( $LoginSession['role'] != ROLE_EMPLOYEE) {
                                $_group_icon_class = "fa fa-building noh_cursor";
                                $_group_bg_class= "";
                                $_is_per = true;
                            }else if ( $LoginSession['role'] == ROLE_EMPLOYEE && $LoginSession['groupidx'] == $val['IDX'] ){
                                $_group_icon_class = "fa fa-building noh_cursor";
                                $_group_bg_class= "background-color:#ebebeb !important;";
                                $_is_per = true;
                            }else{
                                $_group_icon_class= "";
                                $_group_bg_class= "";
                                $_is_per = false;
                            }
                            ?>
                            <li id="home" class="this_sitemap kanban-container">
                                <a style="<?=$_group_bg_class?>">
                                    <i class="f_r <?=$_group_icon_class?>" <?if ( $val['COUNT'] > 0 ) { ?> onclick="move_page(<?=$val['IDX']?>,<?=$_is_per?>)" <? } ?> ></i>
                                    <? if( $LoginSession['role'] == ROLE_ADMIN ) { ?>
                                        <i class="f_r fa fa-tripadvisor fa_second go_teaminfo_allview noh_cursor" data-idx="<?=$val['IDX']?>"></i>
                                    <? } ?>
                                    <?=$val['NAME']?> <small><!--조직번호 : --><?/*=$val['IDX']*/?>인원수 : <?=number_format($val['COUNT'])?>
                                    <?php
                                    if ( $LoginSession['role'] <=  ROLE_SUPERVISOR || ( $LoginSession['role'] == ROLE_MANAGER && $LoginSession['groupidx'] == $val['IDX'] )) { ?>
                                        <button type='button' class='btn btn-default btn-xs  btn_reg_seq' data-idx="<?=$val['IDX']?>">정렬순저장</button>
                                    <? }?>
                                    </small>
                                </a>
                                <? if ( count($val['SUB']) > 0 ) {?>
                                    <script>
                                        $(function() {
                                            $('#sortable' + <?=$val['IDX']?>).sortable();
                                            $('#sortable' + <?=$val['IDX']?>).disableSelection();
                                        });
                                    </script>
                                    <ul class="this_sitemap" >
                                        <li class="this_sitemap " id="sortable<?=$val['IDX']?>">
                                            <?
                                            foreach ( $val['SUB'] as $key2 => $val2) {
                                                $_is_my_project_view = "display_none";
                                                $_is_my_info_view = "";
                                                if( $LoginSession['role'] == ROLE_ADMIN  ) {
                                                    $_is_my_info_view = "go_myinfo_allview noh_cursor";
                                                    $_is_my_project_view = "go_my_allview noh_cursor text-red";
                                                }else  if ( $LoginSession['role'] <=  ROLE_SUPERVISOR  ){
                                                    $_is_my_project_view = "go_my_allview noh_cursor text-red";
                                                }else if( $LoginSession['role'] == ROLE_MANAGER && $LoginSession['groupidx'] == $val['IDX'] ) {
                                                    $_is_my_project_view = "go_my_allview noh_cursor text-red";
                                                }


                                                if ( $LoginSession['hackersid'] == $val2['USER_ID'] ){
                                                    $_group_bg_class2= "background-color:#ebebeb !important;cursor: pointer;";
                                                    $_is_per2 = true;
                                                    $_is_my_project_view = "go_my_allview noh_cursor text-red";
                                                }else{
                                                    $_my_report_href= "";
                                                    $_group_bg_class2= "";
                                                    $_is_per2 = false;
                                                }
                                                ?>
                                                <a class="sortable<?=$val['IDX']?> " data-idx="<?=$val2['userId']?>" data-id="<?=$val2['USER_ID']?>" style="<?=$_group_bg_class2?>"><i class="fa fa-user <?=$_is_my_info_view?>" data-idx="<?=$val2['userId']?>"></i> <i class="fa fa-calendar fa_second <?=$_is_my_project_view?>" data-idx="<?=$val2['userId']?>"></i> <?=$val2['USER_NAME']?> <?=$val2['CLASS_NAME']?>(<?=$val2['roleId']?>)</a>
                                            <? } ?>


                                        </li>
                                    </ul>
                                <? } ?>
                            </li>
                        <? } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type='text/javascript' src='<?php echo base_url(); ?>assets/js/jquery.mousewheel.min.js'></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!-- Slimscroll -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>


<script>

    $(function() {
        $("#TeamProjectArea").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 30);
            event.preventDefault();
        });
    });
</script>
<script>
    let membercount =  $(".kanban-container").length;
    let kanban_container = parseInt($(".kanban-container" ).width());

    $("#teamKanban").css('width',(kanban_container*membercount*1.1));
    $(window).resize( function() {
        $("#teamKanban").css('width',(kanban_container*membercount*1.1));
    });


    $(document).off('click', '.go_my_allview').on('click', '.go_my_allview',function() {
        let thisidx = $(this).data("idx");
        location.href = "/manager/project/myview/" + thisidx;
        return false;
    });

    $(document).off('click', '.go_myinfo_allview').on('click', '.go_myinfo_allview',function() {
        let thisidx = $(this).data("idx");
        $("#popdetail").setLayer({
            'url' : '/manager/project/popuserinfo/' + thisidx,
            'width' : 1024,
            'max_height' : 500
        });
    });

    $(document).off('click', '.go_teaminfo_allview').on('click', '.go_teaminfo_allview',function() {
        let thisidx = $(this).data("idx");
        $("#popdetail").setLayer({
            'url' : '/manager/project/popteaminfo/' + thisidx,
            'width' : 1024,
            'max_height' : 500
        });
    });

    $(document).off('click', '.btn_reg_seq').on('click', '.btn_reg_seq',function() {
        let thisidx = $(this).data("idx");
        let newArray = [];
        $('.sortable' + thisidx ).each(function(){
            newArray.push($(this).data('id'));
        });

        if (newArray.length >0 ) {
            if (confirm("조직의 정렬순서를 조정하시겠습니까?")) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/manager/group/sequpdate",
                    data: "SeqData=" + newArray,
                    async: false,
                    success: function (json) {
                        if (json.result === true) {
                            return false;
                        } else {
                            alert(json.message);
                            return false;
                        }
                    }
                });
            }
        }
        return false;

    });



    function move_page(_idx,_per) {
        if ( _per ) {
            location.href = "/manager/kanban/" + _idx;
        }
    }
    function move_report(_idx,_per) {
        if ( _per ) {
            //location.href = "/manager/report/" + encodeURI(_idx);
        }
    }
</script>