<link href="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban6.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper" id="page_container" >

    <section class="content-header">
        <h1>
            <i class="fa  fa-sitemap" aria-hidden="true"></i> [<?=@$NodeTeamName?>] 팀 현재업무 현황
            <small>
                <img src="/assets/images/emergency.png" height="20"> : 긴급처리 요망업무&nbsp;&nbsp;
                <img src="/assets/images/reopen.png" height="20"> : 재작업(ReOpen)&nbsp;&nbsp;
                <img src="/assets/images/together.png" height="20"> : 협업작업
            </small>
        </h1>
        <ol class="breadcrumb">
            <li style="vertical-align:top;">
                <span class="f_l w_100" >
                    <select class="noh_text_12 w_100" id="ProjectTeam">
                        <? foreach( $TeamViewPermission as $key => $row) { ?>
                            <option value="<?=$row['Code']?>" <?=$row['THIS']?>><?=$row['Name']?></option>
                        <? }?>
                    </select>
                </span>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="row minwidth1024">
            <div class="col-lg-12 ">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12" id="TeamProjectArea" style="text-align:center">

                <ul class="kanban_wrapper" id="teamKanban">
                    <input type="hidden" id="UID" value="<?=$LoginSession->userdata('userId')?>" >
                    <?php if( count($TeamMeberList) > 0 ) { ?>
                        <? foreach( $TeamMeberList as $tkey => $trow )  {
                            $_isPermission = false;
                            if ( $LoginSession->userdata('role') != ROLE_EMPLOYEE && $trow['SITE_IDX'] && empty($trow['ScheduleIdx']) ) {
                                $_isPermission = true;
                            }
                            ?>
                            <li class="kanban-container">
                                <div class="kanban-board profilezone margin_top_0">
                                    <span class="profile_image"><img src="<?=($trow['FACE_URL']===NULL? $FACE_IMAGE_URL."hac.educamp.org/data/user/HAC_".$trow['USER_ID'].".jpg":$trow['FACE_URL'])?>" alt="<?=$trow['USER_NAME']?>"></span>
                                    <?=$trow['USER_NAME']?> <?=$trow['CLASS_NAME']?> <br />[<?=$trow['GROUP_NAME']?>]
                                </div>
                                <div class="kanban-board profilezone margin_top_0 teamKanban2_child display_none">
                                    <span class="profile_image"><img src="<?=($trow['FACE_URL']===NULL? $FACE_IMAGE_URL."hac.educamp.org/data/user/HAC_".$trow['USER_ID'].".jpg":$trow['FACE_URL'])?>" alt="<?=$trow['USER_NAME']?>"></span>
                                    <?=$trow['USER_NAME']?> <?=$trow['CLASS_NAME']?> <br />[<?=$trow['GROUP_NAME']?>]
                                </div>

                                <? if ( isset($trow['SUB'][2])) {?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header warning">
                                            <div class="kanban-title-board">Doing</div>
                                        </header>
                                        <? if ( empty($trow['SITE_IDX']) ) {?>
                                            <span class="not_join_member">미등록 직원</span>
                                        <?}else if( $trow['ScheduleIdx'] ) {?>
                                            <span class="not_join_member"><?=$trow['ScheduleText']?></span>
                                        <? }else{ ?>
                                            <main class="kanban-drag2" style="min-height:100px !important;">
                                                <? foreach( $trow['SUB'][2] as $tkey2 => $trow2 )  {
                                                    $trow2['IntraUrl'] ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link noh_cursor' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                                    ?>
                                                    <div class="kanban-item">
                                                        <?
                                                        $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                                        $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                                        $trow2['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
                                                        ?>
                                                        <?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?>
                                                        <br >
                                                        프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?> <br >
                                                        <span class="project_title_wrap"><i class='fa fa-info-circle  noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<?=htmlspecialchars($trow2['ProjectWorkTitle'])?><?=$_intranetURL?></span>
                                                    </div>
                                                <? } ?>
                                            </main>
                                        <? } ?>
                                    </div>
                                <? }else{ ?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header warning">
                                            <div class="kanban-title-board">Doing</div>
                                        </header>
                                        <main class="kanban-drag2" style="min-height:100px !important;">
                                            <? if ( empty($trow['SITE_IDX']) ) {?>
                                                <span class="not_join_member">미등록 직원</span>
                                            <?}else if( $trow['ScheduleIdx'] ) {?>
                                                <span class="not_join_member"><?=$trow['ScheduleText']?></span>
                                            <? } ?>
                                        </main>
                                    </div>
                                <? } ?>

                                <? if ( isset($trow['SUB'][1])) {?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header info">
                                            <div class="kanban-title-board">To Do</div>
                                        </header>
                                        <main class="kanban-drag2">
                                            <? if ( empty($trow['SITE_IDX']) ) {?>
                                                <span class="not_join_member">미등록 직원</span>
                                            <? }else { ?>
                                                <? foreach( $trow['SUB'][1] as $tkey2 => $trow2 )  {
                                                    $trow2['IntraUrl']  ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link noh_cursor' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                                    ?>
                                                    <div class="kanban-item">
                                                        <?
                                                        $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                                        $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                                        $trow2['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
                                                        ?>
                                                        <?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?>
                                                        <br >
                                                        프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?><br >
                                                        <span class="project_title_wrap "><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<span class="TargetProjectName"><?=htmlspecialchars($trow2['ProjectWorkTitle'])?><?=$_intranetURL?></span></span>
                                                    </div>
                                                <?}?>
                                            <? } ?>
                                        </main>

                                    </div>
                                <? }else{ ?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header info">
                                            <div class="kanban-title-board">To Do</div>
                                        </header>
                                        <main class="kanban-drag2">
                                            <? if ( empty($trow['SITE_IDX']) ) {?>
                                                <span class="not_join_member">미등록 직원</span>
                                            <?}else if( $trow['ScheduleIdx'] ) {?>
                                                <span class="not_join_member"><?=$trow['ScheduleText']?></span>
                                            <? } ?>
                                        </main>
                                    </div>
                                <? } ?>

                                <? if ( isset($trow['SUB'][9])) {?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header success">
                                            <div class="kanban-title-board">Done</div>
                                        </header>
                                        <? if ( empty($trow['SITE_IDX']) ) {?>
                                            <span class="not_join_member">미등록 직원</span>
                                        <?}else if( $trow['ScheduleType'] == 1 ) {?>
                                            <span class="not_join_member"><?=$trow['ScheduleText']?></span>
                                        <? }else{ ?>
                                            <main class="kanban-drag2">
                                                <? foreach( $trow['SUB'][9] as $tkey2 => $trow2 )  {
                                                    $trow2['IntraUrl']  ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link noh_cursor' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                                    ?>
                                                    <div class="kanban-item">
                                                        <?
                                                        $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                                        $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                                        ?>
                                                        <?=$_isemergency?><?=$_isIsReOpen?>
                                                        <br >
                                                        프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?> <br >
                                                        <span class="project_title_wrap"><i class='fa fa-info-circle  noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<?=$trow2['ProjectWorkTitle']?><?=$_intranetURL?></span>
                                                    </div>
                                                <? } ?>
                                            </main>
                                        <? }?>
                                    </div>
                                <? }else{ ?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header success">
                                            <div class="kanban-title-board">Done</div>
                                        </header>
                                        <main class="kanban-drag2">
                                            <? if ( empty($trow['SITE_IDX']) ) {?>
                                                <span class="not_join_member">미등록 직원</span>
                                            <?}else if( $trow['ScheduleIdx'] ) {?>
                                                <span class="not_join_member"><?=$trow['ScheduleText']?></span>
                                            <? } ?>
                                        </main>
                                    </div>
                                <? } ?>
                            </li>
                        <? } ?>
                    <? } ?>
                </ul>
                <p>
                    <span id="prevBtn" class="thisSliderBtn noh_cursor"><img src="<?php echo base_url(); ?>assets/images/left.png" alt="이전 버튼"></span>
                </p>
                <p>
                    <span id="nextBtn"  class="thisSliderBtn noh_cursor"><img src="<?php echo base_url(); ?>assets/images/right.png" alt="다음 버튼"></span>
                </p>
                <div class="top_arrow display_none">
                    <img src="<?php echo base_url(); ?>assets/images/btn_top.png" alt="Top">
                </div>
            </div>
        </div>
        <div class="control-sidebar-bg"></div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.bxslider.js"></script>
<script src="<?php echo base_url(); ?>assets/js/kanban/view.js?v=<?=time()?>"></script>


