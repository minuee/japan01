<link href="<?php echo base_url(); ?>assets/dist/css/sitemap.css" rel="stylesheet" type="text/css" />


<div class="content-wrapper clear" style="clear:both !important;">
    <div class="row">
        <div class="col-lg-12" style="overflow-x:auto;padding:20px 0;" id="TeamProjectArea">
            <div class="nohsitemap">
                <nav class="primary">
                    <ul class="this_sitemap"  id="teamKanban">
                        <?
                        foreach ( $Group as $key => $val) {
                            if ( $LoginSession['role'] != ROLE_EMPLOYEE) {
                                $_group_icon_class = "fa fa-building";
                                $_group_bg_class= "cursor: pointer;";
                                $_is_per = true;
                            }else if ( $LoginSession['role'] == ROLE_EMPLOYEE && $LoginSession['groupidx'] == $val['IDX'] ){
                                $_group_icon_class = "fa fa-building";
                                $_group_bg_class= "background-color:#ebebeb !important;cursor: pointer;";
                                $_is_per = true;
                            }else{
                                $_group_icon_class= "";
                                $_group_bg_class= "";
                                $_is_per = false;
                            }
                        ?>
                        <li id="home" class="this_sitemap kanban-container">
                            <a onclick="move_page(<?=$val['IDX']?>,<?=$_is_per?>)"  style="<?=$_group_bg_class?>"><i class="<?=$_group_icon_class?>"></i> <?=$val['NAME']?> <small><!--조직번호 : --><?/*=$val['IDX']*/?>인원수 : <?=number_format($val['COUNT'])?></small> </a>
                            <? if ( count($val['SUB']) > 0 ) {?>
                            <ul class="this_sitemap">
                                <li class="this_sitemap">
                                    <?
                                    foreach ( $val['SUB'] as $key2 => $val2) {
                                        if ( $LoginSession['hackersid'] == $val2['USER_ID'] ){
                                            $_group_bg_class2= "background-color:#ebebeb !important;cursor: pointer;";
                                            $_is_per2 = true;
                                        }else{
                                            $_my_report_href= "";
                                            $_group_bg_class2= "";
                                            $_is_per2 = false;
                                        }
                                    ?>
                                    <a  onclick="move_report('<?=$val2['USER_ID']?>',<?=$_is_per2?>)"  style="<?=$_group_bg_class2?>"><i class="fa fa-user"></i> <?=$val2['USER_NAME']?> <?=$val2['CLASS_NAME']?>(<?=$val2['roleId']?>)</a>
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