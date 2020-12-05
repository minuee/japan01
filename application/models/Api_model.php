<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Ordering_model (Ordering Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */

require APPPATH . '/libraries/BaseModel.php';
class Api_model extends BaseModel
{

    var $db_sap_code;
    var $db_cron;

    public function __construct() {
        parent::__construct();

        $this->db_sap_code = $this->load->database('sap_code', TRUE);

        $charquery = "SET NAMES utf8";
        $this->db_sap_code->query($charquery);
    }

    function getDetailInfo($_select = array(),$_idx = null) {

        if ( empty($_idx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('Project AS A');
        $this->db->where('A.ProjectNo', $_idx);
        $this->db->limit(1);
        $query = $this->db->get();
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }

    function messageinsert($_postdata = array() ) {



        $this->db->trans_begin(); // 트랜젝션 시작
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $_return['result'] = false;
            $_return['message'] = "트랜잭션 오류.";
            return $_return;
            exit;
        }

        //중복체크
        $this->db->reset_query();
        $IsDupmsgcheck = $this->IsDupmsgcheck($_postdata);
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //      print_r($IsDupmsgcheck);exit;
        };

        if ( $IsDupmsgcheck > 0 ) {
            //pass
            $_return['result'] = true;
            return $_return;
        }
        /*$this->load->library('encrypt');
        $encrypted_string = $this->encrypt->encode($_postdata['message']);*/
        $this->db->reset_query();
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->set('ServiceID', $_postdata['service_id']);
        $this->db->set('RoomIdx', $_postdata['chatroom']);
        $this->db->set('Message', htmlspecialchars($_postdata['message']));
        $this->db->set('RegID', $_postdata['regid']);
        $this->db->insert('Message');
        $error = $this->db->error();
        //echo nl2br($this->db->last_query());exit;
        $result['new_idx'] = 0;
        if ( empty($error['code']) ) {

            //만약에 공지면 공지테이블에도 넣어준다
            if ( $_postdata['IsNotice']) {

                $this->db->reset_query();
                $this->db->set('ServiceID', $_postdata['service_id']);
                $this->db->set('RoomIdx', $_postdata['chatroom']);
                $this->db->set('Message', isset($_postdata['noticemessage'])?strip_tags($_postdata['noticemessage']):strip_tags($_postdata['message']));
                $this->db->set('RegID', $_postdata['regid']);
                $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
                $this->db->insert('Notice');
                //echo nl2br($this->db->last_query());exit;
                $error = $this->db->error();
                if ( empty($error['code']) ) {
                    $_idx = $this->db->insert_id();
                    $this->db->trans_commit();
                    $result['new_idx'] = $_idx;
                    $result['result'] = true;
                }else{
                    $this->db->trans_rollback();
                    $result['message'] = "공지 메시지 등록실패";
                    $result['code'] = $error;
                    $result['result'] = false;

                }
            }else{
                $this->db->trans_commit();
                $result['result'] = true;
            }

        }else{
            $this->db->trans_rollback();
            $result['code'] = $error;
            $result['result'] = false;
        }

        return $result;
    }

    function denied_update_batch( $_UID ) {


        $this->db->reset_query();
        $this->db->set('isDeleted', 1);
        $this->db->set('updatedDtm', date("Y-m-d H:i:s"));
        $this->db->where("hackersid != '' ");
        $this->db->where('hackersid', $_UID);
        $this->db->update('tbl_users');
        $error = $this->db->error();

        if ( empty($error['code']) ){
            $result['result'] = true;
        }else {
            $result['message'] = "상태수정실패";
            $result['code'] = $error;
            $result['result'] = false;
        }
        return $result;
    }

    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getUserInfoByUserID($userId)
    {
        $this->db->select('userId, name,roleId');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
        $this->db->where('hackersid', $userId);
        $query = $this->db->get();

        return $query->row();
    }


    function IsRegistedTeamview( $_data = array()){

        if ( empty($_data['GIDX']) || empty($_data['USERIDX'])  ) {
            return false;
        }
        $_Gidx = $_data['GIDX'];
        $this->db->reset_query();
        $this->db->select('UserID');
        $this->db->select('Permission');
        $this->db->select("JSON_CONTAINS(Permission, '[\"".$_Gidx."\"\]') AS ISREG");
        $this->db->select("JSON_ARRAY_APPEND(Permission, '$', '".$_Gidx."') AS ADD_RESULT");
        $this->db->where('UserID', $_data['USERIDX']);
        $this->db->limit(1);
        $query = $this->db->get('TeamView');
        //echo nl2br($this->db->last_query());exit;
        $result = $query->result_array();

        return $result;

    }

    function intranteamviewinsert($_postdata = array() ) {
        //중복체크
        if ( empty($_postdata['USERID']) || empty($_postdata['GIDX'])  ) {
            $_return['result'] = false;
            $_return['message'] = "필수값 누락입니다.";
            return $_return;
            exit;
        }

        if ( $_postdata['MODE'] == 'N') {

        }else{
            $_getInfo = $this->IsRegistedTeamview($_postdata);
            if (isset($_getInfo[0]['UserID'])) {
                if ( $_getInfo[0]['ISREG'] == 1 ) { //가지고 있다 바로 리턴
                    $_return['result'] = true;
                    return $_return;
                }else{//등록은 되어 있으니 추가해준다
                    $this->db->reset_query();
                    //$this->db->set('Permission',"JSON_ARRAY_APPEND(Permission, '$', '".$_postdata['GIDX']."')");
                    $this->db->set('Permission',$_getInfo[0]['ADD_RESULT']);
                    $this->db->where('UserID',$_postdata['USERIDX']);
                    $this->db->update('TeamView');

                    $error = $this->db->error();
                    if ( !empty($error['code']) ) {
                        $_return['result'] = false;
                        $_return['message'] = "등록실패, 관리자에게 문의바람";
                        return $_return;
                    }else{
                        $_return['result'] = true;
                        return $_return;

                    }
                }

            }else{ //신규로 등록해준다

                $this->db->reset_query();
                $this->db->set('UserID',$_postdata['USERIDX']);
                $this->db->set('Permission',json_encode(array($_postdata['GIDX'])));
                $this->db->insert('TeamView');
                $error = $this->db->error();
                if ( !empty($error['code']) ) {
                    $_return['result'] = false;
                    $_return['message'] = "등록실패, 관리자에게 문의바람";
                    return $_return;
                }else{
                    $_return['result'] = true;
                    return $_return;
                }
            }
        }

    }

    function intranettodoinsert($_postdata = array() ) {

        //중복체크
        if ( $_postdata['MODE'] == null || $_postdata['MODE'] == ""  ) {
            $_ISDup = $this->IsDpucheck($_postdata);
            if (isset($_ISDup[0]['Status'])) {
                $_return['result'] = 2;
                $_return['message'] = "이미 등록된 업무입니다.";
                /*if ($_ISDup[0]['Status'] == 9) {
                    $_return['result'] = 2;
                    $_return['message'] = "이미 마감된 업무입니다.";
                } else {
                    $_return['result'] = 4;
                    $_return['message'] = "이미 진행중인 업무로 재등록불가합니다.";
                }*/
                return $_return;
                exit;
            }
        }

        $this->db->trans_begin(); // 트랜젝션 시작
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $_return['result'] = 3;
            $_return['message'] = "트랜잭션 오류.";
            return $_return;
            exit;
        }

        $USER_INFO = $this->getUserGroupCode($_postdata['USERID']);
        if ( isset($USER_INFO[0]['GROUP_IDX']) === false) {
            $_return['result'] = 3;
            $_return['message'] = "등록되지 않은 직원입니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('ProjectIdx', MAINTERENCE_IDX);
        if ( $_postdata['MODE'] == 1 ) {
            $this->db->set('IsReOpen', 1);
        }else if ( $_postdata['MODE'] == 2 ) {
            $this->db->set('IsReOpen', 2);
        }

        $this->db->set('title', urldecode($_postdata['SUBJECT']));
		//$this->db->set('IntraUrl',htmlspecialchars($_postdata['URL']));
 	    $this->db->set('IntraUrl', $this->xssClean($_postdata['URL']));
        $this->db->set('IntraBoard',$_postdata['BOARD_ID']);
        $this->db->set('IntraBoardIdx',$_postdata['DOC_ID']);
        $this->db->set('RegID',$USER_INFO[0]['userId']);
        $this->db->set('RegIDGroup',$USER_INFO[0]['GROUP_IDX']);

        if ( $USER_INFO[0]['GROUP_IDX'] ) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            if ( in_array($USER_INFO[0]['GROUP_IDX'] ,$_CommonCode['DESIGNGROUP'])) {
                $this->db->set('GroupCode',"Design");
            }else if ( in_array($USER_INFO[0]['GROUP_IDX'],$_CommonCode['PLANNINGGROUP'])) {
                $this->db->set('GroupCode',"Planning");
            }
        }

        if ( isset($USER_INFO[0]['roleId'])) {
            if ( $USER_INFO[0]['roleId'] == ROLE_EMPLOYEE) {
                $this->db->set('ToDoID', $USER_INFO[0]['userId']);
                $this->db->set('ToDoIDGroup', $USER_INFO[0]['GROUP_IDX']);
            }
        }
        if ( isset($_postdata['WUSERID'])) {
            $this->db->set('IntraWriter',$_postdata['WUSERID']);
        }
        $this->db->set('sDate',date("Y-m-d"));
        $this->db->set('RegDatetime',date("Y-m-d H:i:s"));
        $this->db->set('ModID', $USER_INFO[0]['userId']);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->insert('ProjectWork');

        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $_idx = $this->db->insert_id();
            //History
            $this->db->reset_query();
            $_regname = $this->getUserName($USER_INFO[0]['userId']);
            if ( $_postdata['MODE'] == 1 ) {
                $sessionArray = array('userId' => $USER_INFO[0]['userId'],
                    'userIdGroup' => $USER_INFO[0]['GROUP_IDX'],
                    'message' => "인트라넷에서 전송되어 등록된 업무(해당업무 Reopen)",
                    'regname' => $_regname->name
                );
            }else if ( $_postdata['MODE'] == 2 ) {
                    $sessionArray = array('userId'=>$USER_INFO[0]['userId'],
                        'userIdGroup'=>$USER_INFO[0]['GROUP_IDX'],
                        'message'=>"인트라넷에서 전송되어 등록된 업무(협업업무)",
                        'regname'=>$_regname->name
                    );
            }else{
                $sessionArray = array('userId'=>$USER_INFO[0]['userId'],
                    'userIdGroup'=>$USER_INFO[0]['GROUP_IDX'],
                    'message'=>"인트라넷에서 전송되어 등록된 업무(최초등록)",
                    'regname'=>$_regname->name
                );
            }


            $this->db->set('createdDtm', date("Y-m-d H:i:s"));
            $this->db->set('userId', $USER_INFO[0]['userId']);
            $this->db->set('userIdGroup', ($USER_INFO[0]['GROUP_IDX']===null?0:$USER_INFO[0]['GROUP_IDX']));
            $this->db->set('sessionData', json_encode($sessionArray));
            $this->db->set('WorkDate', date("Y-m-d"));
            $this->db->set('ProjectWorkIdx', $_idx);
            $this->db->insert('ProjectWorkHistory');

            $this->db->trans_commit();
            $result['message'] = "등록완료";
            $result['result'] = 1;
        }else{
            $this->db->trans_rollback();
            $result['result'] = 3;
            $result['message'] = "등록실패, 관리자에게 문의바람";
        }

        return $result;
    }

    function intranetworkinsert($_postdata = array() ) {


        //중복체크
        $_ISDup = $this->IsWorkdayDpucheck($_postdata);
        //echo $_ISDup;exit;
        if ($_ISDup > 0 ) {
            $_return['result'] = 2;
            return $_return;
            exit;
        }


        $this->db->trans_begin(); // 트랜젝션 시작
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $_return['result'] = 3;
            $_return['message'] = "트랜잭션 오류.";
            return $_return;
            exit;
        }

        $USER_INFO = $this->getUserGroupCode($_postdata['USER_ID']);
        if ( isset($USER_INFO[0]['GROUP_IDX']) === false) {
            $_return['result'] = false;
            $_return['message'] = $_postdata['USER_ID']."  계정은 등록되지 않은 직원입니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('Type', $_postdata['WORKTYPE']==4 ? 20:10); //10은 당직임, 20은 특근임
        $this->db->set('Color', $_postdata['WORKTYPE']==4 ? "#dd4b39":"#f39c12");
        $this->db->set('SubTitle',$_postdata['WORKTYPE']==4 ? "특근(토익)":"당직");
        $this->db->set('Comment',"인트라넷API를 통한 등록");
        $this->db->set('RegID',$USER_INFO[0]['userId']);
        $this->db->set('RegGroup',$USER_INFO[0]['GROUP_IDX']);
        $this->db->set('sDate',$_postdata['DATE']);
        $this->db->set('eDate',$_postdata['DATE']);
        $this->db->set('RegDatetime',date("Y-m-d H:i:s"));
        $this->db->insert('Schedule');
        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $this->db->trans_commit();
            $result['message'] = "등록완료";
            $result['result'] = true;
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
            $result['message'] = "등록실패, 관리자에게 문의바람";
        }
        return $result;
    }


    function getUserName($_userid = null) {
        if ( empty($_userid) ) {
            $result[0]['name'] = "미정";
            return $result[0];
        }

        $this->db->select('name');
        $this->db->where('UserId', $_userid);
        $query = $this->db->get('tbl_users');
        $result = $query->result();

        return $result[0];
    }

    function IsDpucheck( $_data = array()){

        if ( empty($_data['BOARD_ID']) || empty($_data['DOC_ID'])  ) {
            return false;
        }

        $this->db->select('Status');
        $this->db->where('IntraBoard', $_data['BOARD_ID']);
        $this->db->where('IntraBoardIdx', $_data['DOC_ID']);
        $this->db->where('IsDel IS NULL');
        $this->db->order_by('RegDatetime', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('ProjectWork');
        //echo nl2br($this->db->last_query());exit;
        $result = $query->result_array();

        return $result;

    }


    function IsDupmsgcheck( $_data = array()){

        if ( empty($_data['service_id']) || empty($_data['regid'])  ) {
            return false;
        }
        $this->db->select('*');
        $this->db->where('ServiceID', $_data['service_id']);
        $this->db->where('RoomIdx', $_data['chatroom']);
        $this->db->where('RegID', $_data['regid']);
        $this->db->where('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->limit(1);
        $query = $this->db->get('Message');
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
        //    echo nl2br($this->db->last_query());exit;
        };
        $result = $query->num_rows();
        //if ( !$result ) $result = 0;

        return $result;

    }


    function IsWorkdayDpucheck( $_data = array()){

        if ( empty($_data['DATE']) || empty($_data['USER_ID'])  ) {
            return 0;
        }
        $this->db->select('Schedule.ScheduleIdx');
        $this->db->where('sDate', $_data['DATE']);
        $this->db->where('eDate', $_data['DATE']);
        $this->db->where('RegID', $_data['USER_ID']);
        $this->db->where('Type',20); //20은 특근타입임
        $this->db->where('DelID',0);
        $this->db->from('Schedule');
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());exit;
        $result = $query->num_rows();
        if ( !$result ) $result = 0;

        return $result;

    }


    function getUserGroupCode($_userid = null) {
        if ( empty($_userid) ) {
            $result[0]['GROUP_IDX'] = 0;
            return $result[0]['GROUP_IDX'];
        }
        $this->db->reset_query();
        $this->db->select('ADMIN_MEMBER.GROUP_IDX');
        $this->db->select('tbl_users.userId');
        $this->db->select('tbl_users.roleId');
        $this->db->from('ADMIN_MEMBER');
        $this->db->join('tbl_users'," ADMIN_MEMBER.USER_ID = tbl_users.hackersid  ",'INNER');
        $this->db->where('ADMIN_MEMBER.USER_ID', $_userid);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    public function getLocalHackersInfo( ){


        $return = array();
        $this->db->reset_query();
        $this->db->select('tbl_users.roleId');
        $this->db->select('ADMIN_MEMBER.*');
        $this->db->from('ADMIN_MEMBER');
        $this->db->join('tbl_users'," ADMIN_MEMBER.USER_ID = tbl_users.hackersid ",'LEFT');
        $this->db->where('ADMIN_MEMBER.DENIED', "N");
        $this->db->where_in('ADMIN_MEMBER.GROUP_IDX', array(15,578,73,74,335,99,421,424,433,455,509));
        //$this->db->where('ADMIN_MEMBER.USER_ID', "minuee");
        $this->db->order_by('ADMIN_MEMBER.MEMBER_IDX', 'ASC');
        $query = $this->db->get();

        //echo nl2br($this->db->last_query());exit;

        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }

    public function getLocalHackersInfo2(  $_array = array()){


        $return = array();
        $this->db->reset_query();
        $this->db->select('tbl_users.roleId');
        $this->db->select('ADMIN_MEMBER.*');
        $this->db->from('ADMIN_MEMBER');
        $this->db->join('tbl_users'," ADMIN_MEMBER.USER_ID = tbl_users.hackersid ",'LEFT');
        $this->db->where('ADMIN_MEMBER.DENIED', "N");
        $this->db->where_in('ADMIN_MEMBER.GROUP_IDX', $_array);
        //$this->db->where('ADMIN_MEMBER.USER_ID', "minuee");
        $this->db->order_by('ADMIN_MEMBER.MEMBER_IDX', 'ASC');
        $query = $this->db->get();

        //echo nl2br($this->db->last_query());exit;

        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }


    public function getHackersInfo( $table = null , $idx = null){


        $return = array();
        $this->db_sap_code->reset_query();
        $this->db_sap_code->select('*');
        $this->db_sap_code->from($table);
        //if ( $table == "ADMIN_MEMBER") $this->db_sap_code->where('DENIED', "N");
        if ( $table == "ADMIN_MEMBER") {
            $Limitedate = date("Ymd",strtotime("-1 week"));
            $this->db_sap_code->where("USER_NAME !='' ");
            $this->db_sap_code->where("( LIMIT_DATE = '' OR ( LIMIT_DATE !='' AND LIMIT_DATE > '".$Limitedate."' ) )");
        }
        $this->db_sap_code->order_by($idx, 'ASC');
        $query = $this->db_sap_code->get();
        //echo nl2br($this->db_sap_code->last_query());exit;
        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db_sap_code->last_query();
        $return['err'] = $this->db_sap_code->error();

        return $return;
    }

    public function getHackersInfoAll( $table = null , $idx = null){


        $return = array();
        $this->db_sap_code->reset_query();
        $this->db_sap_code->select('*');
        $this->db_sap_code->from($table);
        //if ( $table == "ADMIN_MEMBER") $this->db_sap_code->where('DENIED', "N");
        if ( $table == "ADMIN_MEMBER") {
            $this->db_sap_code->where("USER_NAME !='' ");
        }
        $this->db_sap_code->order_by($idx, 'ASC');
        $query = $this->db_sap_code->get();
        //echo nl2br($this->db_sap_code->last_query());exit;
        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db_sap_code->last_query();
        $return['err'] = $this->db_sap_code->error();

        return $return;
    }


    public function insert_on_duplicate_update_batch($table,$insert_values,$update_values)
    {


        $this->db_cron = $this->load->database('defaultcron', TRUE);

        $charquery = "SET NAMES utf8";
        $this->db_cron->query($charquery);


        if (empty($table) || empty($insert_values)) return false;
        $duplicate_data = array();

        foreach($update_values AS $key => $value) {
            if ( $key == 'IsUse') {
                $duplicate_data[] = sprintf("%s=%s", $key, addslashes($value));
            }else {
                $duplicate_data[] = sprintf("%s='%s'", $key, addslashes($value));
            }
        }

        $sql = sprintf("%s ON DUPLICATE KEY UPDATE %s", $this->db_cron->insert_string($table, $insert_values), implode(',', $duplicate_data));
        $query = $this->db_cron->query($sql);
        $error = $this->db_cron->error();
        $_return['sql'] = $this->db_cron->last_query();

        if(!$query && !empty($error["message"])){
            $_return["result"] = false;
            $_return["message"] = $error;
            return $_return;
        }

        $_return["result"] = true;
        return $_return;

    }


    public function insert_origin($insert_values)
    {


        $this->db->reset_query();
        $this->db->set('hackersidx', $insert_values['hackersidx']);
        $this->db->set('hackersid', $insert_values['hackersid']);
        $this->db->set('email', $insert_values['email']);
        $this->db->set('password', $insert_values['password']);
        $this->db->set('name', $insert_values['name']);
        $this->db->set('nickname', $insert_values['nickname']);
        $this->db->set('roleId', $insert_values['roleId']);
        $this->db->set('face', $insert_values['face']);
        $this->db->set('isDeleted', $insert_values['isDeleted']);
        $this->db->set('createdBy', $insert_values['createdBy']);
        $this->db->set('createdDtm', date("Y-m-d H:i:s"));
        $this->db->insert('tbl_users');


        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $result['result'] = true;
        }else{
            $result['result'] = false;
        }
        return $result;

    }



    function getGroupMember($_select = array(),$_idx) {

        if ( empty($_idx) ) {
            return null;
        }

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('ADMIN_MEMBER AS M');
        $this->db->join('ADMIN_MEMBER_CLASS as C'," M.CLASS_IDX = C.IDX",'INNER');
        $this->db->join('ADMIN_VARS as V'," M.HR_VAR_9 = V.IDX",'LEFT');
        $this->db->join('tbl_users as A'," M.MEMBER_IDX = A.hackersidx",'LEFT');
        $this->db->where('M.GROUP_IDX', $_idx);
        $this->db->where('M.DENIED', 'N');
        $this->db->order_by('(CASE WHEN M.HR_VAR_9 = 0 THEN 1000 ELSE M.HR_VAR_9 END)','ASC');
        //$this->db->order_by('M.REGDATE', 'ASC');
        $query = $this->db->get();
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        //print_r($return['sql']);
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }



}

