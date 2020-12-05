<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Ordering_model (Ordering Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since :  July 2019
 */

require APPPATH . '/libraries/BaseModel.php';
class Schedule_model extends BaseModel
{

    function getMyScheduleListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Schedule AS A');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.RegGroup = G.IDX ",'INNER');
        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.RegDatetime', 'DESC');
        $query = $this->db->get();

        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());
        };
        $result = $query->result_array();
        return $result;

    }

    function getHolidayListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Holidays AS A');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.HolidayIdx', 'ASC');
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());exit;
        $result = $query->result_array();
        return $result;

    }

    function insertdata($LoggedID = null,$LoggedGroup=null, $_postdata = array() ) {

        if ( empty($LoggedID) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->trans_begin(); // 트랜젝션 시작
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $_return['result'] = false;
            $_return['message'] = "트랜잭션 오류.";
            return $_return;
            exit;
        }

        $this->db->reset_query();

        switch ( $_postdata['Type'] ) {
            case "#cccccc" :  $_Type = 1; break;  //연차(휴가)
            case "#878787" :  $_Type = 2; break;  //반차(오전)
            case "#45a9f4" :  $_Type = 4; break;  //반차(오후)
            case "#00a65a" :  $_Type = 3; break;   //조퇴
            case "#f39c12" :  $_Type = 10; break;   //당직
            case "#dd4b39" :  $_Type = 20; break;   //특근
            case "#f012be" :  $_Type = 30; break;   //전체일정
            case "#605ca8" :  $_Type = 40; break;   //팀일정
            case "#39cccc" :  $_Type = 50; break;   // 개인일정
            default :  $_Type = 1; break;  //연차(휴가)
        }

        $tmpGroupID = ($LoggedGroup=== null || $LoggedGroup == "")?1:$LoggedGroup;


        $this->db->set('Type', $_Type);
        $this->db->set('Color', $_postdata['Type']);
        $this->db->set('SubTitle', $_postdata['SubTitle']);
        $this->db->set('sDate', $_postdata['sDate']);
        $this->db->set('eDate', $_postdata['eDate']);

        if ( $_postdata['agentmode'] ) {
            $_tmpdata = explode("_",$_postdata['agentmode']);
            $this->db->set('IsAgent', $LoggedID);
            $this->db->set('RegID', $_tmpdata[0]);
            $this->db->set('RegGroup',$_tmpdata[1]);

        }else{
            $this->db->set('RegID', $LoggedID);
            $this->db->set('RegGroup',$tmpGroupID);
        }


        if ( $tmpGroupID > 1) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            if ( in_array($tmpGroupID ,$_CommonCode['DESIGNGROUP'])) {
                $this->db->set('GroupCode',"Design");
            }else if ( in_array($tmpGroupID ,$_CommonCode['PLANNINGGROUP'])) {
                $this->db->set('GroupCode',"Planning");
            }else{
                $this->db->set('GroupCode',"Develope");
            }
        }
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->insert('Schedule');
        $_idx = $this->db->insert_id();
        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $result['result'] = true;
            $result['result_idx'] = $_idx;
            $this->db->trans_commit();
            //echo nl2br($this->db->last_query()); echo "<br />";exit;
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
        }
        return $result;
    }

    function getGroupName($_groupidx = null)
    {
        $this->db->reset_query();
        $this->db->select('NAME');
        $this->db->from('ADMIN_MEMBER_GROUP');
        $this->db->where("IDX", $_groupidx);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
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
        $this->db->from('Schedule AS A');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'INNER');
        $this->db->join('tbl_users as M2'," A.IsAgent = M2.userId  ",'LEFT');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.RegGroup = G.IDX  ",'INNER');
        $this->db->where('A.ScheduleIdx', $_idx);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }
    function update($_postdata = array() ) {

        if ( empty($_postdata['ScheduleIdx']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        if ( isset($_postdata['sDate'])  && isset($_postdata['eDate']) ) {
            if ($_postdata['sDate'] > $_postdata['eDate'] ) {
                $newSdata = $_postdata['eDate'];
                $newEdata = $_postdata['sDate'];
            } else {
                $newSdata = $_postdata['sDate'];
                $newEdata = $_postdata['eDate'];
            }
        }
        if ( $newSdata !== $newEdata ) {
            $newEdata = date('Y-m-d', strtotime("-1 day", strtotime($newEdata)));
        }

        $this->db->set('sDate', $newSdata);
        $this->db->set('eDate', $newEdata);
        $this->db->where('ScheduleIdx', $_postdata['ScheduleIdx']);
        $this->db->update('Schedule');
        //echo nl2br($this->db->last_query()); echo "<br />";exit;
        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;


        return $result;
    }


    function updateseqdata($_postdata) {
        if ( empty($_postdata['SeqData']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $tmpdate = explode(",", $_postdata['SeqData']);
        foreach( $tmpdate as $key => $val) {
            $this->db->set('seq', $key+1);
            $this->db->where('hackersid', $val);
            $this->db->update('tbl_users');
            $error = $this->db->error();
            if ( empty($error['code']) ) {
                $result['result'] = true;
            }else{
                $_return['message'] = "데이터베이스 업데이트중 에러가 발생하였습니다.";
                $result['result'] = false;
                return $result;
            }
        }

        return $result;

    }

    function infoupdate($_postdata = array() ) {

        if ( empty($_postdata['ScheduleIdx']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();

        $this->db->set('Comment', trim($this->xssClean($_postdata['Comment'])));

        if ( isset($_postdata['Type']) &&  $_postdata['Type']) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            $this->db->set('Type', $_postdata['Type']);
            $this->db->set('Color', $_CommonCode['Schedule'][$_postdata['Type']]['color']);
            $strType = array(30,40,50);
            if ( in_array($strType,$_postdata['Type']) ) {
                $this->db->set('SubTitle', $_CommonCode['Schedule'][$_postdata['Type']]['name']);
            }else{
                if ( isset($_postdata['SubTitle'])){
                    $this->db->set('SubTitle', trim($this->xssClean($_postdata['SubTitle'])));
                }
            }
        }else{
            $this->db->set('SubTitle', trim($this->xssClean($_postdata['SubTitle'])));
        }



        $this->db->where('ScheduleIdx', $_postdata['ScheduleIdx']);
        $this->db->update('Schedule');
        $result['result'] = empty($error['code']) ? true : false ;
        return $result;
    }



    function removeschedule($ScheduleIdx = null , $LoggedID = null){

        if ( empty($ScheduleIdx) || empty($LoggedID) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
        $this->db->set('DelID', $LoggedID);
        $this->db->where('ScheduleIdx', $ScheduleIdx);
        $this->db->update('Schedule');
        $error = $this->db->error();

        $result['result'] = empty($error['code']) ? true : false ;

        return $result;

    }

    function getGroupInfo($_select = array(),$search = array()) {

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('ADMIN_MEMBER_GROUP AS G');
        //$this->db->where('G.DEPTH', 3);
        //$this->db->where('G.PARENT', 88);
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('G.NAME', 'ASC');
        $query = $this->db->get();

        //echo nl2br($this->db->last_query());exit;
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

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
        $this->db->order_by('A.seq','ASC');
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


    function getUserListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('tbl_users AS M');
        $this->db->join('ADMIN_MEMBER as A', 'M.hackersidx = A.MEMBER_IDX','LEFT');
        $this->db->join('ADMIN_MEMBER_GROUP as G', 'A.GROUP_IDX = G.IDX','LEFT');
        $this->db->join('ADMIN_MEMBER_CLASS as C'," A.CLASS_IDX = C.IDX  ",'LEFT');
        $this->db->join('ADMIN_VARS as V'," A.HR_VAR_9 = V.IDX  ",'LEFT');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('G.IDX', 'ASC');
        $this->db->order_by('(CASE WHEN A.HR_VAR_9 = 0 THEN 1000 ELSE A.HR_VAR_9 END)','ASC');
        $this->db->order_by('M.name', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

}

