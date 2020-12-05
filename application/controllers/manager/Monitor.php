<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';


/**
 * Class : Kanban
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 July 2019
 */
class Monitor extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('project_model');
        $this->load->model('user_model');
        $this->load->helper("html");
        $this->isLoggedIn();
    }

    function index( $_GROUP_IDX = null){

        $this->isLoggedIn();
        $_request = $this->security->xss_clean($this->input->get());
        if ( $_GROUP_IDX === null && isset($_request['idx']) ) {
            $_GROUP_IDX  = $_request['idx'];
        }

        $_GROUPCode = $this->getGlobalGroupCodeAll();
        //$_GROUPCode['Group']
        $_TeamViewPermission = $this->project_model->getTeamViewPermission($this->session->userdata('userId'));

        $TeamViewPermission = array();
        $NodeTeamName = "";
        if($_TeamViewPermission['result'] && count($_TeamViewPermission['row']) > 0 ){
            $_tmpdata = json_decode($_TeamViewPermission['row'][0]['Permission']);
            if ( $_GROUP_IDX ) {
                if ( !in_array($_GROUP_IDX, $_tmpdata ) ) {
                    $this->loadThis();
                    return false;
                }
            }
            if ( count($_tmpdata) > 0  ) {
                foreach ($_tmpdata as $ckey => $cval) {
                    if ( $_GROUP_IDX === null && $ckey == 0 ) {
                        $_GROUP_IDX  = $cval;
                    }
                    $TeamViewPermission[$ckey]['Code'] = $cval;
                    $TeamViewPermission[$ckey]['Name'] = $_GROUPCode['Group'][$cval];
                    $TeamViewPermission[$ckey]['THIS'] = $cval == $_GROUP_IDX ? "selected" : "";
                    if ($cval == $_GROUP_IDX) $NodeTeamName = $TeamViewPermission[$ckey]['Name'];
                }
            }else{
                $this->loadThis();
                return false;
            }
        }else{
            $this->loadThis();
            return false;
        }

        if ( $_GROUP_IDX === null || $_GROUP_IDX == 0  ) {
            $this->loadThis();
            return false;
        }

        $data['TeamViewPermission'] =  $TeamViewPermission;
        $data['NodeTeamName'] =  $NodeTeamName;
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
                        "A.ProjectWorkIdx", "A.title as ProjectWorkTitle", "A.Status", "A.RegID", "A.ToDoID", "A.RegDatetime","A.Priority","A.IsReOpen","A.IntraBoardIdx","A.IntraUrl", "IFNULL(M.name,'미정') as UserName", "B.ProjectTitle"
                    );
                    $_dbresult2 = $this->project_model->getMyWorkListing($_search3, $_select3);

                    if ( $_dbresult2['cnt'] > 0 ) {
                        $data['TeamMeberList'][$key]['SUB'][$_dowork_array[$i]] = $_dbresult2['res'];
                    }
                }


                $_CNT++;
            }
        }
        if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
            $data['FACE_IMAGE_URL'] = "https://";
        }else{
            $data['FACE_IMAGE_URL'] = "http://";
        }

        $data['TeamMemberCount'] = $_CNT;
        $data['LoginSession'] = $this->session;

        $data['PAGE_GROUP_IDX'] = $_GROUP_IDX;
        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;

        $this->global['pageTitle'] = 'Hackers Project : Team KANBAN';
        $this->loadViews("manager/kanban/view", $this->global, $data , NULL);


    }


}

?>