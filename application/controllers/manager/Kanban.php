<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Kanban extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('project_model');
        $this->load->model('user_model');
        $this->load->helper("html");
        $this->isLoggedIn();   
    }
    
    function index( $_GROUP_IDX = null,$_PARENT_GROUP = null){

        if (( $this->session->userdata('role') == ROLE_EMPLOYEE ||  $this->session->userdata('role') == ROLE_MANAGER )  && $_GROUP_IDX == null ) {
            $_GROUP_IDX = $this->session->userdata('groupidx');
        }else if (($this->session->userdata('role') == ROLE_ADMIN || $this->session->userdata('role') == ROLE_SUPERVISOR ) &&  $_GROUP_IDX == null )  {
            if ( $this->session->userdata('groupidx') && $this->session->userdata('groupidx') > 1 ) {
                $_GROUP_IDX = $this->session->userdata('groupidx');
            }else{
                $_GROUP_IDX = BASE_GROUP_IDX;
            }
        }else if (( $this->session->userdata('role') == ROLE_EMPLOYEE ||  $this->session->userdata('role') == ROLE_MANAGER )  && $_GROUP_IDX !== $this->session->userdata('groupidx')){
            $this->loadThis();
            return false;
        }

        if ( $_GROUP_IDX === null ) {
            $this->loadOnlyEmployee();
            return false;
        }

        if ( $this->session->userdata('role') == ROLE_ADMIN ) {
            $_GROUPCode = $this->getGlobalGroupCodeAll($_PARENT_GROUP);
            // 각종코드
            $this->config->load('config', true);
            $_CommonCode = $this->config->item('code');

            $data['BUSINESSCode'] = $_CommonCode['BUSINESS'];
        }else{
            $_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'),$this->session->userdata('groupidx') ,$this->session->userdata('parentgroup') );
        }

        $data['GROUPCode'] = $_GROUPCode['Group'];

        $data['PARENT_GROUP'] = $_PARENT_GROUP;

        if ( $_GROUP_IDX) {
            $data['NodeTeamCode'] = $_GROUP_IDX;
            $data['isTeamView'] = true;
            $data['NodeChannelID'] = 'Team'.$_GROUP_IDX;
            $data['NodeTeamName'] = isset($_GROUPCode['Group'][$_GROUP_IDX])?$_GROUPCode['Group'][$_GROUP_IDX]:null;
            $data['NodeServiceID'] = 'TEAM';
        }else{
            $data['isTeamView'] = false;
            $data['NodeTeamCode'] = null;
            $data['NodeChannelID'] = null;
            $data['NodeTeamName'] = null;
            $data['NodeServiceID'] = null;
        }

        $_psearch['where'] = array();
        if ( $this->session->userdata('role') == ROLE_EMPLOYEE || $this->session->userdata('role') == ROLE_MANAGER  || $this->session->userdata('role') == ROLE_SUPERVISOR ) {
            $_psearch['where'][] = "( JSON_EXTRACT(Permission, '$[*]') LIKE '%".$this->session->userdata('groupidx')."%' OR  A.ProjectIdx = ".MAINTERENCE_IDX." OR A.RegIDGroup = '".$this->session->userdata('groupidx')."' )";
        }
        $_psearch['where'][] = "A.DelID IS NULL ";
        $_psearch['where'][] = "A.ProjectStatus = 1";
        $_pselect = array(
            "A.*","M.nickname as UserName"
        );
        $data['ProjectList'] = $this->project_model->ProjectListing($_psearch ,$_pselect);


        $data['TeamMeberList'] =  array();
        $_search['where'] = array();
        $_search['where'][] = "M.GROUP_IDX = '$_GROUP_IDX' ";
        $_search['where'][] = "M.DENIED = 'N' ";
        $_select = array(
            "M.MEMBER_IDX","M.USER_ID","M.USER_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","U.userId AS SITE_IDX","IFNULL(M.FACE_URL,U.face) AS FACE_URL","G.NAME AS GROUP_NAME","S.ScheduleIdx","S.ScheduleType"
        );
        $_dbresult = $this->project_model->MyTeamMemberListing($_select,$_search);

        $_CNT = 0;
        if ( isset($_dbresult['row']) && count($_dbresult['row']) > 0 ) {
            foreach ($_dbresult['row'] as $key => $val ){
                $data['TeamMeberList'][$key]['MEMBER_IDX'] = $val['MEMBER_IDX'];
                $data['TeamMeberList'][$key]['USER_ID'] = $val['USER_ID'];
                $data['TeamMeberList'][$key]['USER_NAME'] = $val['USER_NAME'];
                $data['TeamMeberList'][$key]['CLASS_NAME'] = $val['CLASS_NAME'];
                $data['TeamMeberList'][$key]['GROUP_NAME'] = $val['GROUP_NAME'];
                $data['TeamMeberList'][$key]['SITE_IDX'] = $val['SITE_IDX'];
                if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
                    $data['TeamMeberList'][$key]['FACE_URL'] = str_replace("http://","https://", $val['FACE_URL']);
                }else{
                    $data['TeamMeberList'][$key]['FACE_URL'] = $val['FACE_URL'];
                }

                $data['TeamMeberList'][$key]['ScheduleType'] = $val['ScheduleType'];
                if ( $val['ScheduleType'] == 1) {
                    $data['TeamMeberList'][$key]['ScheduleIdx'] = $val['ScheduleIdx'];
                    $data['TeamMeberList'][$key]['ScheduleText'] = "연차(휴가)중";
                }else if ( $val['ScheduleType'] == 3 ) {
                    $data['TeamMeberList'][$key]['ScheduleIdx'] = $val['ScheduleIdx'];
                    $data['TeamMeberList'][$key]['ScheduleText'] = "조퇴";
                }else if ( $val['ScheduleType'] == 2 && date("H") < 14 ) {
                    $data['TeamMeberList'][$key]['ScheduleIdx'] = $val['ScheduleIdx'];
                    $data['TeamMeberList'][$key]['ScheduleText'] = "오전(반차)";
                }else if ( $val['ScheduleType'] == 4 && date("H") >= 14 ) {
                    $data['TeamMeberList'][$key]['ScheduleIdx'] = $val['ScheduleIdx'];
                    $data['TeamMeberList'][$key]['ScheduleText'] = "오후(반차)";
                }else{
                    $data['TeamMeberList'][$key]['ScheduleIdx'] = null;
                    $data['TeamMeberList'][$key]['ScheduleText'] = null;
                }

                $_dowork_array = [1,2,9];
                for( $i = 0 ; $i < count($_dowork_array) ; $i++) {
                    $_search3['where'] = array();
                    $_search3['where'][] = "A.IsDel IS NULL ";;
                    $_search3['where'][] = "A.Status = $_dowork_array[$i] ";;
                    $_search3['where'][] = "A.ToDoID = " .$val['SITE_IDX']. " ";;

                    if ( $i == 0 ) {
                        $_search3['where'][] = "( A.sDate IS NULL ||  ( A.sDate  <= '".date("Y-m-d")."' )) ";
                    }
                    if ( $i == 2 ) {
                        $_search3['where'][] = "A.Status =  9 &&  eDate = '".date("Y-m-d")."' ";
                    }
                    $_select3 = array(
                        "A.ProjectWorkIdx", "A.title as ProjectWorkTitle", "A.Status", "A.RegID", "A.ToDoID", "A.RegDatetime","A.Priority","A.IsReOpen","A.IntraBoardIdx","A.IntraUrl","A.PostColor", "IFNULL(M.name,'미정') as UserName", "B.ProjectTitle","A.Background"
                    );
                    $_dbresult2 = $this->project_model->getMyWorkListing($_search3, $_select3);

                    if ( $_dbresult2['cnt'] > 0 ) {
                        $data['TeamMeberList'][$key]['SUB'][$_dowork_array[$i]] = $_dbresult2['res'];
                    }
                }
                $_CNT++;
            }
        }


        $data['TeamMemberCount'] = $_CNT;
        $data['LoginSession'] = $this->session;

        if ( $this->session->userdata('role') == ROLE_ADMIN || $this->session->userdata('role') == ROLE_SUPERVISOR || ( $this->session->userdata('role') == ROLE_MANAGER &&  $this->session->userdata('groupidx') == $_GROUP_IDX) )  {
            $data['IsRegistPermission'] = true;
        }else{
            $data['IsRegistPermission'] = false;
        }

        $data['PAGE_GROUP_IDX'] = $_GROUP_IDX;
        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;


        if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
            $data['FACE_IMAGE_URL'] = "https://";
        }else{
            $data['FACE_IMAGE_URL'] = "http://";
        }

        if( $this->session->userdata('role') == ROLE_EMPLOYEE )
        {
            $data['ToDoReady'] = array();
            $this->global['pageTitle'] = 'Hackers Project : Team KANBAN';
            $this->loadViews("manager/kanban/index6", $this->global, $data , NULL);
        }else{
            $_search3['where'] = array();
            $_search3['where'][] = "A.IsDel IS NULL ";
            $_search3['where'][] = "A.Status = 1 ";
            $_search3['where'][] = "( A.RegID = '".$this->session->userdata('userId')."' AND ( A.ToDoID IS NULL OR A.ToDoID = '' OR  A.ToDoID = 0 ) ) ";

            $_select3 = array(
                "A.ProjectWorkIdx","A.title","A.RegID","A.RegDatetime","B.ProjectTitle","A.Priority","A.IsReOpen","A.PostColor","A.Background"
            );
            $_dbresultx = $this->project_model->getMyWorkListing($_search3 ,$_select3);
            $data['ToDoReady'] =  ($_dbresultx['cnt']>0 ? $_dbresultx['res'] : array());
            $this->global['pageTitle'] = 'Hackers Project : Team KANBAN';
            $this->loadViews("manager/kanban/index6", $this->global, $data , NULL);

        }
    }

}

?>