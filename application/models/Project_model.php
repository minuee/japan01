<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Ordering_model (Ordering Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */

require APPPATH . '/libraries/BaseModel.php';
class Project_model extends BaseModel
{

    function getDoingNowCoung($_userid = null)
    {


        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('ProjectWork AS A');
        $this->db->join('Project as B'," A.ProjectIdx = B.ProjectIdx  ",'INNER');
        $this->db->where('ToDoID', $_userid);

        $this->db->where('IsDel IS NULL');
        $this->db->where('Status', PROJECT_WORK_DOING_STATUS);
        $query = $this->db->get();
        $return['err']  = $this->db->error();
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());
        };
        if (empty($return['error']['code'])) {
            $return['result'] = true;
            $return['count'] = $query->num_rows();


        }else{
            $return['result'] = false;
            $return['count'] = 0;
        }
        return $return;
    }

    function ListingCount($search = array())
    {

        // Sub Query
        $this->db->select('AVG(A1.Rate) as SumRate');
        $this->db->select('MAX(A1.ProjectIdx) as ProjectIdx');
        $this->db->from('ProjectWork AS A1');
        $this->db->group_by('A1.ProjectIdx');
        $subQuery =  $this->db->get_compiled_select();

        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('Project AS A');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.RegIDGroup = G.IDX ",'INNER');
        $this->db->join('('.$subQuery.") as C"," A.ProjectIdx = C.ProjectIdx ",'LEFT');
        $this->db->join('tbl_users as M'," A.RegID = M.userId ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $query = $this->db->get();

        return $query->num_rows();
    }

    function Listing($search = array(), $page, $segment ,$_select = array())
    {

        // Sub Query
        $this->db->select('AVG(A1.Rate) as SumRate');
        $this->db->select('MAX(A1.ProjectIdx) as ProjectIdx');
        $this->db->from('ProjectWork AS A1');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A1.RegIDGroup = G.IDX ",'INNER');
        $this->db->group_by('A1.ProjectIdx');
        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();


        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Project AS A');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.RegIDGroup = G.IDX ",'INNER');
        $this->db->join('('.$subQuery.") as C"," A.ProjectIdx = C.ProjectIdx ",'LEFT');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.RegDatetime', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());
        };

        $result = $query->result_array();
        return $result;
    }


    function ReportListingCount($search = array())
    {

        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('Report AS A');
        $this->db->join('tbl_users as M'," A.userId = M.userId ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.userGroup = G.IDX ",'INNER');

        $this->db->group_by('A.userId');
        $this->db->group_by('A.wDate');
        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $query = $this->db->get();
        return $query->num_rows();
    }

    function getProjectWorkArray($_workidx = null)
    {

        $this->db->reset_query();
        $this->db->select('ProjectWorkIdx');
        $this->db->from("ProjectWork");
        $this->db->where("ProjectIdx",$_workidx);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());
        //exit;
        $result = $query->result_array();
        return $result;
    }


    function getProjectWorkMember($search = array(), $_select = array())
    {

        // Sub Query
        $this->db->select('MAX(A.ToDoIDGroup) as ToDoIDGroup');
        $this->db->select('A.ProjectIdx');
        $this->db->select('A.ToDoID');
        $this->db->from('ProjectWork AS A');
        $this->db->join('Project as P'," A.ProjectIdx = P.ProjectIdx",'INNER');
        $this->db->where_not_in("A.Status",3);
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->group_by('A.ToDoID');
        $this->db->group_by('A.ProjectIdx');
        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();

        if ( gettype($_select) == 'array' ) {
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }


        $this->db->from("(".$subQuery.") as A");
        $this->db->join('tbl_users as M'," A.ToDoID = M.userId  ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.ToDoIDGroup = G.IDX  ",'INNER');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('A.ToDoIDGroup', 'ASC');
        $this->db->order_by('M.userId', 'ASC');
        $query = $this->db->get();
        if ( $_SERVER['REMOTE_ADDR'] == '172.20.3.74' ) {
            //echo nl2br($this->db->last_query());
            //exit;
        };
        $result = $query->result_array();
        return $result;
    }

    function ReportListing($search = array(), $page, $segment ,$_select = array())
    {

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Report AS A');
        $this->db->join('tbl_users as M'," A.userId = M.userId ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.userGroup = G.IDX ",'INNER');
        $this->db->group_by('A.userId');
        $this->db->group_by('A.reportGroup');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.RegDatetime', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());

        $result = $query->result_array();
        return $result;
    }


    function getChartGroupListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('Project AS A');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.ProjectIdx', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function ProjectListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Project AS A');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.RegDatetime', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

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
        $this->db->order_by('M.seq', 'ASC');
        $this->db->order_by('(CASE WHEN A.HR_VAR_9 = 0 THEN 1000 ELSE A.HR_VAR_9 END)','ASC');
        $this->db->order_by('M.name', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();


        return $result;

    }

    function MyTeamMemberListing($_select = array(),$search = array() ){

        if ( count($search) == 0 ) {
            return null;
        }

        // Sub Query
        $_TODAY = date("Y-m-d");
        $this->db->select('RegID');
        $this->db->select('MAX(ScheduleIdx) as ScheduleIdx');
        $this->db->select('MIN(Type) as ScheduleType');
        $this->db->from('Schedule');
        $this->db->where("DelID",0);
        $this->db->where("Type < 5 ");
        $this->db->where("sDate <= '".$_TODAY."' AND eDate >= '".$_TODAY."' ");
        $this->db->group_by('RegID');
        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('ADMIN_MEMBER AS M');
        $this->db->join('tbl_users as U'," M.USER_ID = U.hackersid  ",'LEFT');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," M.GROUP_IDX = G.IDX  ",'INNER');
        $this->db->join('ADMIN_MEMBER_CLASS as C'," M.CLASS_IDX = C.IDX  ",'INNER');
        $this->db->join('ADMIN_VARS as V'," M.HR_VAR_9 = V.IDX  ",'LEFT');
        //오늘 연차 또는 조퇴인지
        $this->db->join('('.$subQuery.") as S","U.userId = S.RegID",'LEFT');


        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('U.seq','ASC');
        $this->db->order_by('(CASE WHEN M.HR_VAR_9 = 0 THEN 1000 ELSE M.HR_VAR_9 END)','ASC');
        $query = $this->db->get();
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }


    function getMyWorkListing($search, $_select){

        // Sub Query
        $this->db->select('MAX(ProjectWorkIdx) as ProjectWorkIdx');
        $this->db->select('MAX(ProjectWorkCommentIdx) as Commentidx');
        $this->db->from('ProjectWorkComment');
        $this->db->where("DelID = ''");
        $this->db->group_by('ProjectWorkComment.ProjectWorkIdx');

        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('ProjectWork AS A');
        $this->db->join('Project as B'," A.ProjectIdx = B.ProjectIdx  ",'INNER');
        $this->db->join('tbl_users as M'," A.TodoID = M.userId  ",'LEFT');
        $this->db->join('('.$subQuery.") as S","A.ProjectWorkIdx = S.ProjectWorkIdx",'LEFT');
        $this->db->join('ProjectWorkComment as C'," S.Commentidx = C.ProjectWorkCommentIdx  ",'LEFT');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('A.seq', 'ASC');
        $this->db->order_by('A.RegDatetime', 'DESC');

        $query = $this->db->get();

        $return['res'] = $query->result_array();
        $return['cnt'] = $query->num_rows();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();


        //$sql = $this->db->last_query();
        //print_r(nl2br($sql));exit;
        return $return;

    }

    function getSubTeamList($search, $_select){


        $this->db->reset_query();
        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('ADMIN_MEMBER_GROUP AS A');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('A.SORT', 'ASC');
        $this->db->order_by('A.IDX', 'ASC');

        $query = $this->db->get();
        $return['res'] = $query->result_array();
        $return['cnt'] = $query->num_rows();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();
        return $return;

    }

    function getMyWorkStatics( $_target_array = array(), $_userid = null){

        if ( empty($_userid) || count($_target_array) == 0) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        // Sub Query 업무시간
        $_Today = date("Y-m-d");
        $this->db->reset_query();
        $this->db->select('SUM(DoingTime) as SUMDoingTime');
        $this->db->select('MAX(userId) as wuserId');
        $this->db->select('MAX(ProjectWorkIdx) as wProjectWorkIdx');
        $this->db->from('ProjectWorkHistory');
        $this->db->where('userId', $_userid);
        $this->db->where('WorkDate', $_Today);
        $this->db->group_by('ProjectWorkIdx');
        $this->db->group_by('userId');

        $subQuery =  $this->db->get_compiled_select();

        $this->db->reset_query();
        $this->db->select('A.ToDoID');
        $this->db->select('COUNT(A.ProjectWorkIdx) AS SUMCount');
        $this->db->select('SUM(Sub2.SUMDoingTime) AS SUMDoingTime');
        $this->db->select('SUM(A.Foretime) AS SUMDForetime');
        $this->db->select('SUM(CASE WHEN A.Priority = 9 THEN 1 ELSE 0 END ) AS SUMEmergency');
        $this->db->select('SUM(CASE WHEN A.IsReOpen = 1 THEN 1 ELSE 0 END ) AS SUMReopen');
        $this->db->from('ProjectWork AS A');
        $this->db->join('('.$subQuery.") as Sub2","A.ToDoID = Sub2.wuserId AND A.ProjectWorkIdx = Sub2.wProjectWorkIdx ",'LEFT');
        $this->db->where('A.ToDoID', $_userid);
        $this->db->where_in('A.ProjectWorkIdx', $_target_array);
        $this->db->group_by('A.ToDoID');
        $query = $this->db->get();

        //echo nl2br($this->db->last_query());exit;
        $result = $query->result_array();
        return $result;


    }



    function TeamMemberListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('tbl_users as M');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('M.name', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    function getMessageListing($search, $_select){

        // Sub Query
        $this->db->select('*');
        $this->db->from('Message as A');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('A.RegDatetime','DESC');
        $this->db->limit(50);
        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('('.$subQuery.") as A");
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'LEFT');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('A.RegDatetime', 'ASC');
        $this->db->limit(50);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());exit;
        };
        $result = $query->result_array();
        return $result;

    }



    function getMessageNoticeListing($search, $_select){

        $this->db->reset_query();
        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Notice as A');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'LEFT');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('A.RegDatetime', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }


    function getUserReports($search, $_select,$_userid){

        if ( empty($_userid) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }
        $_TODAY = date("Y-m-d");
        //Last get Report date
        $this->db->reset_query();
        $this->db->select('RegDatetime');
        $this->db->select('ReportIdx');
        $this->db->from('Report');
        $this->db->where("userId", $_userid);
        $this->db->order_by('RegDatetime', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $_lastReport =  $query->row();

        // Sub Query
        $this->db->reset_query();
        $this->db->select('MAX(RegID) as userId');
        $this->db->select('MAX(ProjectWorkIdx) as ProjectWorkIdx');
        $this->db->select('MAX(ProjectWorkCommentIdx) as Commentidx');
        $this->db->from('ProjectWorkComment');
        $this->db->where("ProjectWorkComment.DelID = '' ");
        $this->db->where('ProjectWorkComment.RegID', $_userid);
        $this->db->group_by('ProjectWorkComment.ProjectWorkIdx');
        $this->db->group_by('ProjectWorkComment.RegID');

        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();

        // Sub Query 업무시간
        $this->db->select('SUM(DoingTime) as SUMDoingTime');
        $this->db->select('MAX(userId) as userId');
        $this->db->select('MAX(ProjectWorkIdx) as ProjectWorkIdx');
        $this->db->from('ProjectWorkHistory');
        $this->db->where('userId', $_userid);
        if ( isset($_lastReport->ReportIdx)) {
            $this->db->where("createdDtm > '".$_lastReport->RegDatetime."' ");
        }
        //$this->db->where('WorkDate', $_TODAY);
        $this->db->group_by('ProjectWorkIdx');
        $this->db->group_by('userId');

        $subQuery2 =  $this->db->get_compiled_select();
        $this->db->reset_query();

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('ProjectWork AS A');
        $this->db->join('Project as B'," A.ProjectIdx = B.ProjectIdx  ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as C'," A.ToDoIDGroup = C.IDX  ",'LEFT');
        $this->db->join('('.$subQuery.") as Sub","A.ToDoID = Sub.userId AND A.ProjectWorkIdx = Sub.ProjectWorkIdx ",'LEFT');
        $this->db->join('('.$subQuery2.") as Sub2","A.ToDoID = Sub2.userId AND A.ProjectWorkIdx = Sub2.ProjectWorkIdx ",'LEFT');
        $this->db->join('ProjectWorkComment as D',"Sub.Commentidx = D.ProjectWorkCommentIdx  ",'LEFT');
        $this->db->where('A.IsDel IS NULL');
        if ( $_lastReport->RegDatetime ) {
            $this->db->where("( A.sDate is NULL || ( A.sDate <= '".$_TODAY."' && A.eDate >= '".$_TODAY."' )  || ( A.sDate <= '".$_TODAY."' && A.eDate IS NULL ) || ( A.eDate <= '".$_TODAY."' && A.Status in ( 1,2) )  || ( DATE_FORMAT(FROM_UNIXTIME(`A`.`sTime`), '%Y-%m-%d %h:%i:%s') > '".$_lastReport->RegDatetime."' && A.Status = 9 )  )");
        }else{
            $this->db->where("( A.sDate is NULL || ( A.sDate <= '".$_TODAY."' && A.eDate >= '".$_TODAY."' )  || ( A.sDate <= '".$_TODAY."' && A.eDate IS NULL ) || ( A.eDate <= '".$_TODAY."' && A.Status in ( 1,2,9 ) )  )");
        }

        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->order_by('B.ProjectMode', 'ASC');
        $this->db->order_by('B.ProjectIdx', 'DESC');
        $this->db->order_by('A.RegDatetime', 'ASC');
        //$this->db->limit(10);
        $query = $this->db->get();

        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());exit;
        };
        $result = $query->result_array();
        return $result;

    }


    function getReportDetail($_select,$_reportIdx){

        if ( empty($_reportIdx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->select("RegDatetime");
        $this->db->from('Report');
        $this->db->where('ReportIdx', $_reportIdx);
        $tquery = $this->db->get();
        $_tresult =  $tquery->row();

        // Sub Queryr
        $this->db->reset_query();
        $this->db->select('userId');
        $this->db->select('wDate');
        $this->db->select('reportGroup');
        $this->db->from('Report');
        $this->db->where('ReportIdx', $_reportIdx);
        $subQuery =  $this->db->get_compiled_select();
        $this->db->reset_query();

        // Sub Query 업무시간
        $this->db->select('SUM(DoingTime) as SUMDoingTime');
        $this->db->select('MAX(userId) as userId');
        $this->db->select('MAX(ProjectWorkIdx) as ProjectWorkIdx');
        $this->db->from('ProjectWorkHistory');
        if ( isset($_tresult->RegDatetime) ) {
            $this->db->where('createdDtm <= ', $_tresult->RegDatetime);
        }
        $this->db->group_by('ProjectWorkIdx');
        $this->db->group_by('userId');

        $subQuery2 =  $this->db->get_compiled_select();
        $this->db->reset_query();

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('('.$subQuery.") as Main");
        $this->db->join('Report as A',"Main.userId = A.userId AND Main.wDate = A.wDate AND Main.reportGroup = A.reportGroup ",'INNER');
        $this->db->join('('.$subQuery2.") as Sub2","A.userId = Sub2.userId AND A.ProjectWorkIdx = Sub2.ProjectWorkIdx ",'LEFT');
        $this->db->join('ProjectWork as P'," A.ProjectWorkIdx = P.ProjectWorkIdx ",'INNER');
        $this->db->join('tbl_users as M'," A.userId = M.userId  ",'INNER');
        //$this->db->where('A.IsDel IS NULL');
        $this->db->order_by('A.ProjectMode', 'ASC');
        $this->db->order_by('A.ProjectIdx', 'DESC');
        $this->db->order_by('A.RegDatetime', 'ASC');
        $query = $this->db->get();


        $result = $query->result_array();
        return $result;

    }

    function getJobListinglimit1($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Project AS A');
        $this->db->join('ProjectWork as B'," A.ProjectIdx = B.ProjectIdx  AND B.IsDel IS NULL ",'LEFT');
        $this->db->join('tbl_users as M'," B.ToDoID = M.userId  ",'LEFT');
        $this->db->join('tbl_users as M2'," A.RegID = M2.userId  ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('B.RegDatetime', 'DESC');
        $this->db->order_by('B.Priority', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    function getJobListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('Project AS A');
        $this->db->join('ProjectWork as B'," A.ProjectIdx = B.ProjectIdx  AND B.IsDel IS NULL ",'LEFT');
        $this->db->join('tbl_users as M'," B.ToDoID = M.userId  ",'LEFT');
        $this->db->join('tbl_users as M2'," A.RegID = M2.userId  ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('B.RegDatetime', 'DESC');
        $this->db->order_by('B.Priority', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();
        return $result;

    }
    function getDetailInfo($_select = array(),$_idx = null ,$_userinfo = array()) {

        if ( empty($_idx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }


        // Sub Query 업무시간
        $this->db->select('SUM(DoingTime) as SUMDoingTime');
        $this->db->select('MAX(userId) as userId');
        $this->db->select('MAX(ProjectWorkIdx) as ProjectWorkIdx');
        $this->db->from('ProjectWorkHistory');
        //$this->db->where('userId', $_userinfo['userId']);
        $this->db->where('ProjectWorkIdx', $_idx);
        $this->db->group_by('ProjectWorkIdx');
        $this->db->group_by('userId');

        $subQuery2 =  $this->db->get_compiled_select();
        $this->db->reset_query();


        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('ProjectWork AS B');
        $this->db->join('Project as A'," B.ProjectIdx = A.ProjectIdx  ",'INNER');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'LEFT');
        $this->db->join('tbl_users as M1'," B.RegID = M1.userId  ",'LEFT');
        $this->db->join('tbl_users as M2'," B.ToDoID = M2.userId  ",'LEFT');
        $this->db->join('('.$subQuery2.") as Sub2","B.ToDoID = Sub2.userId AND B.ProjectWorkIdx = Sub2.ProjectWorkIdx ",'LEFT');
        $this->db->where('B.ProjectWorkIdx', $_idx);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }


    function getIsTodayReported($search = array(),$_select = null)
    {
        if ( empty($_select) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->select($_select);
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('Report');
        $query = $this->db->get();

        return $query->row();
    }

    function getIsReported($search = array(),$_termday = null,$_userid = null)
    {
        $_TODAY = date("Y-m-d");
        $_BaseDay = date("Y-m-d", strtotime("-".$_termday." days", strtotime($_TODAY)));
        $this->db->reset_query();
        $this->db->select("HolidayIdx as ReportIdx");
        $this->db->from('Holidays');
        $this->db->where("wDate",$_BaseDay);
        $tquery = $this->db->get();
        $_tresult =  $tquery->row();
        if  ( $_tresult > 0 ) {
            $_result =  $_tresult;
        }else{
            $this->db->reset_query();
            $this->db->select("MAX(R.ReportIdx) as ReportIdx");
            if ( is_array($search) && count($search) ) $this->setWhere($search);
            $this->db->from('Report as R');
            $this->db->group_by('R.userId');
            $query = $this->db->get();
            $_result =  $query->row();

            if  ( $_result == "" || $_result == null ) {
                $this->db->reset_query();
                $this->db->select('MAX(ScheduleIdx) as ReportIdx');
                $this->db->from('Schedule');
                $this->db->where("DelID",0);
                $this->db->where("Type",1);
                $this->db->where("RegID",$_userid);
                $this->db->where("sDate <= '".$_BaseDay."' AND eDate >= '".$_BaseDay."' ");
                $this->db->group_by('RegID');
                $query2 = $this->db->get();
                $_result =  $query2->row();
            }
        }


        return $_result;
    }


    function getNextData($_date = null,$_userid = null ,$_groupid = null,$_portGroup = null, $_RegDatetime = null)
    {
        if ( empty($_date) ) {
            $_return['result'] = false;
            return $_return;
        }
        $this->db->reset_query();
        $this->db->select('wDate');
        $this->db->select('ReportIdx');
        $this->db->select('reportGroup');
        $this->db->from('Report');
        if ( $_userid) {
            $this->db->where("userId", $_userid);
        }
        if ( $_groupid) {
            $this->db->where("userGroup", $_userid);
        }
        $this->db->where("wDate >= '".$_date."' ");
        $this->db->where("RegDatetime > '".$_RegDatetime."' ");
        $this->db->where_not_in("reportGroup",$_portGroup);
        $this->db->order_by('RegDatetime', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get();


        return $query->row();
    }


    function getForeData($_date = null,$_userid = null,$_groupid = null,$_portGroup = null, $_RegDatetime = null)
    {
        if ( empty($_date) ) {
            $_return['result'] = false;
            return $_return;
        }
        $this->db->reset_query();
        $this->db->select('wDate');
        $this->db->select('ReportIdx');
        $this->db->select('reportGroup');
        $this->db->from('Report');
        if ( $_userid  ) {
            $this->db->where("userId", $_userid);
        }
        if ( $_groupid) {
            $this->db->where("userGroup", $_groupid);
        }
        $this->db->where("wDate <= '".$_date."' ");
        $this->db->where("RegDatetime < '".$_RegDatetime."' ");
        $this->db->where_not_in("reportGroup",$_portGroup);
        $this->db->order_by('RegDatetime', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->row();
    }


    function getProjectInfo($_select = array(),$_idx = null) {

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

        $this->db->from('Project as A');
        $this->db->join('tbl_users as M'," A.RegID = M.userId  ",'LEFT');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.RegIDGroup = G.IDX  ",'LEFT');
        $this->db->where('A.ProjectIdx', $_idx);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }


    function updateworkdata($LoggedID = null, $LoggedGroupIdx = null, $_postdata = array() ) {

        if ( empty($_postdata['ProjectWorkIdx']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        if ( isset($_postdata['sDate'])  && isset($_postdata['eDate']) ) {
            if ($_postdata['sDate'] > $_postdata['eDate'] && $_postdata['eDate'] !== '0000-00-00') {
                $newSdata = $_postdata['eDate'];
                $newEdata = $_postdata['sDate'];
            } else {
                $newSdata = $_postdata['sDate'];
                $newEdata = $_postdata['eDate'];
            }
        }else{
            if ( isset($_postdata['sDate']) ) {
                $newSdata = $_postdata['sDate'];
            }else{
                $newSdata = null;
            }
            if ( isset($_postdata['eDate']) ) {
                $newEdata = $_postdata['sDate'];
            }else{
                $newEdata = null;
            }
        }
        if ( $newSdata !== $newEdata  && isset($_postdata['ModifyMode']) && $_postdata['ModifyMode'] !== 1) {

        }else if ($newSdata !== $newEdata ) {
            $newEdata = date('Y-m-d', strtotime("-1 day", strtotime($newEdata)));

        }


        if ( isset($_postdata['ToDoID']) && isset($_postdata['Modifyer'])   ) {
            if ( $_postdata['Modifyer'] < ROLE_EMPLOYEE ) {
                $this->db->set('ToDoID', $_postdata['ToDoID']);
                $_ToDoIDGroup = $this->getUserGroupCode($_postdata['ToDoID']);
                $this->db->set('ToDoIDGroup', $_ToDoIDGroup[0]['GROUP_IDX']);
            }
        }

        //$this->db->set('sDate', $newSdata);
        //$this->db->set('eDate', $newEdata);
        if ( isset($_postdata['Rate']) )  $this->db->set('Rate', $_postdata['Rate']>100?100:$_postdata['Rate']);
        //if ( isset($_postdata['Status']))  $this->db->set('Status', $_postdata['Status']);
        if ( isset($_postdata['ProjectIdx']))   $this->db->set('ProjectIdx', $this->xssClean($_postdata['ProjectIdx']));
        if ( isset($_postdata['title']))   $this->db->set('title', $this->xssClean($_postdata['title']));
        if ( isset($_postdata['Priority']))   $this->db->set('Priority', $_postdata['Priority']);
        if ( isset($_postdata['Foretime']))   $this->db->set('Foretime', $_postdata['Foretime']);
        if ( isset($_postdata['ChildMode']))   $this->db->set('ChildMode', $_postdata['ChildMode']);
        if ( isset($_postdata['IntraUrl']))   $this->db->set('IntraUrl', $_postdata['IntraUrl']);
        if ( isset($_postdata['IntraUrl']))  {
            $this->db->set('IntraBoardIdx', 1);
        }
        /*else{
            $this->db->set('IntraBoardIdx', null);
        }*/
        if ( isset($_postdata['Background']))   $this->db->set('Background', $_postdata['Background']);
        if ( isset($_postdata['PostColor']))   $this->db->set('PostColor', $_postdata['PostColor']);

        $this->db->set('ModID', $LoggedID);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->update('ProjectWork');
        //echo nl2br($this->db->last_query()); echo "<br />";exit;
        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;

        //History   OriginsDate    OrigineDate  OriginStatus  OriginToDoID  OriginRate
        $_ischange = false;
        $_messages = "";
        /*if (  isset($_postdata['Status']) && isset($_postdata['OriginStatus']) && $_postdata['OriginStatus'] !== $_postdata['Status'] ) {
            $this->config->load('config');
            $_nowProjectWorks_Status = (string)$_postdata['OriginStatus']?$this->config->item('ProjectWorks_Status')[(int)$_postdata['OriginStatus']]:null;
            $_newProjectWorks_Status = (string)$_postdata['Status']?$this->config->item('ProjectWorks_Status')[(int)$_postdata['Status']]:null;
            $_ischange = true;
            $_messages .= "- 상태변경 : $_nowProjectWorks_Status ▶ $_newProjectWorks_Status ";
        }*/
        /*if (  isset($_postdata['OriginToDoID']) && isset($_postdata['ToDoID']) &&  $_postdata['OriginToDoID'] !== $_postdata['ToDoID'] ) {
            if ( $_postdata['OriginToDoID'] ) {
                $this->db->reset_query();
                $_nowPToDoID = $this->getUserName($_postdata['OriginToDoID']);
                $this->db->reset_query();
                $_newPToDoID = $this->getUserName($_postdata['ToDoID']);
                $_ischange = true;
                $_messages .= "- 담당자변경 : $_nowPToDoID->name ▶ $_newPToDoID->name ";
            }else{
                $this->db->reset_query();
                $_newPToDoID = $this->getUserName($_postdata['ToDoID']);
                $_ischange = true;
                $_messages .= "- 담당자지정 : $_newPToDoID->name ";
            }
        }*/

        if ( isset($_postdata['ToDoID']) && isset($_postdata['Modifyer'])   ) {
            if ($_postdata['Modifyer'] < ROLE_EMPLOYEE) {
                if (isset($_postdata['OriginToDoID']) && isset($_postdata['ToDoID']) && $_postdata['OriginToDoID'] !== $_postdata['ToDoID']) {
                    if ($_postdata['OriginToDoID']) {
                        $this->db->reset_query();
                        $_nowPToDoID = $this->getUserName($_postdata['OriginToDoID']);
                        $this->db->reset_query();
                        $_newPToDoID = $this->getUserName($_postdata['ToDoID']);
                        $_ischange = true;
                        $_messages .= "- 담당자변경 : $_nowPToDoID->name ▶ $_newPToDoID->name ";
                    } else {
                        $this->db->reset_query();
                        $_newPToDoID = $this->getUserName($_postdata['ToDoID']);
                        $_ischange = true;
                        $_messages .= "- 담당자지정 : $_newPToDoID->name ";
                    }

                }
            }
        }

        if (  isset($_postdata['Rate']) && isset($_postdata['OriginRate']) && $_postdata['OriginRate'] !== $_postdata['Rate'] ) {
            $_ischange = true;
            $_messages .= "- 진척도 변경 :".$_postdata['OriginRate']." ▶ ".($_postdata['Rate']>100?100:$_postdata['Rate'])." ";
        }
        if ( $_ischange ) {
            $this->db->reset_query();
            $_regname = $this->getUserName($LoggedID);
            $sessionArray = array('userId' => $LoggedID,
                'userIdGroup' => ($LoggedGroupIdx===null?1:$LoggedGroupIdx),
                'message' => $_messages,
                'regname' => $_regname->name
            );

            $this->db->reset_query();
            $this->db->set('createdDtm', date("Y-m-d H:i:s"));
            $this->db->set('userId', $LoggedID);
            $this->db->set('userIdGroup', ($LoggedGroupIdx===null?1:$LoggedGroupIdx));
            $this->db->set('sessionData', json_encode($sessionArray));
            $this->db->set('WorkDate', date("Y-m-d"));
            $this->db->set('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
            $this->db->insert('ProjectWorkHistory');
        }
        return $result;
    }

    function updateworkdate($LoggedID = null, $LoggedGroupIdx = null, $_postdata = array() ) {

        if ( empty($_postdata['ProjectWorkIdx']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }


        if ( isset($_postdata['sDate'])  && isset($_postdata['eDate']) ) {
            if ($_postdata['sDate'] > $_postdata['eDate'] && $_postdata['eDate'] !== '0000-00-00') {
                $newSdata = $_postdata['eDate'];
                $newEdata = $_postdata['sDate'];
            } else {
                $newSdata = $_postdata['sDate'];
                $newEdata = $_postdata['eDate'];
            }
        }else{
            if ( isset($_postdata['sDate']) ) {
                $newSdata = $_postdata['sDate'];
            }else{
                $newSdata = null;
            }
            if ( isset($_postdata['eDate']) ) {
                $newEdata = $_postdata['sDate'];
            }else{
                $newEdata = null;
            }
        }
        if ( $newSdata !== $newEdata  && isset($_postdata['ModifyMode']) && $_postdata['ModifyMode'] !== 1) {

        }else if ($newSdata !== $newEdata ) {
            $newEdata = date('Y-m-d', strtotime("-1 day", strtotime($newEdata)));

        }

        $this->db->set('sDate', $newSdata);
        $this->db->set('eDate', $newEdata);
        $this->db->set('ModID', $LoggedID);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->update('ProjectWork');
        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;


        $_messages = "- 기간 변경 :".$newSdata." ~ ".$newEdata." ";
        $this->db->reset_query();
        $_regname = $this->getUserName($LoggedID);
        $sessionArray = array('userId' => $LoggedID,
            'userIdGroup' => ($LoggedGroupIdx===null?1:$LoggedGroupIdx),
            'message' => $_messages,
            'regname' => $_regname->name
        );

        $this->db->reset_query();
        $this->db->set('createdDtm', date("Y-m-d H:i:s"));
        $this->db->set('userId', $LoggedID);
        $this->db->set('userIdGroup', ($LoggedGroupIdx===null?1:$LoggedGroupIdx));
        $this->db->set('sessionData', json_encode($sessionArray));
        $this->db->set('WorkDate', date("Y-m-d"));
        $this->db->set('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->insert('ProjectWorkHistory');

        return $result;
    }

    function insertreportdata($basicdata = array() , $sessionArray = array() ) {

        if ( empty($basicdata['LoggedID']) || count($sessionArray) == 0 ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('ProjectMode', $basicdata['ProjectMode']);
        $this->db->set('ProjectIdx', $basicdata['ProjectIdx']);
        $this->db->set('ProjectWorkIdx', $basicdata['ProjectWorkIdx']);
        $this->db->set('userId', $basicdata['LoggedID']);
        $this->db->set('userGroup', $basicdata['LoggedGroup']);
        $this->db->set('reportGroup', $basicdata['reportGroup']);
        if ( $basicdata['LoggedGroup'] > 1) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            if ( in_array($basicdata['LoggedGroup'] ,$_CommonCode['DESIGNGROUP'])) {
                $this->db->set('GroupCode',"Design");
            }else if ( in_array($basicdata['LoggedGroup'] ,$_CommonCode['PLANNINGGROUP'])) {
                $this->db->set('GroupCode',"Planning");
            }
        }

        $this->db->set('reportData', json_encode($sessionArray));
        $this->db->set('wDate', date("Y-m-d"));
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->insert('Report');
        $error = $this->db->error();

        //$result['result'] = empty($error['code']) ? true : false ;
        if ( empty($error['code']) ) {
            $result['result'] = true;
            $result['message'] = '성공';
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
            $result['message'] = '데이터베이스 저장에 실패하였습니다.';
        }

        return $result;
    }

    function updatetodoset($LoggedID = null,  $LoggedGroupIdx = null,  $_postdata = array() ) {

        if ( empty($_postdata['ProjectWorkIdx']) ) {
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
        if ( $_postdata['ToDoID'] == 'CLEAR') {
            $this->db->set('ToDoID', NULL);
            $this->db->set('ToDoIDGroup', 0);
        }else{

            $_ToDoIDGroup = $this->getUserGroupCode($_postdata['ToDoID']);

            $this->db->set('ToDoID', $_postdata['ToDoID']);
            $this->db->set('ToDoIDGroup', $_ToDoIDGroup[0]['GROUP_IDX']);

        }

        $this->db->set('ModID', $LoggedID);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->update('ProjectWork');
        //echo nl2br($this->db->last_query()); echo "<br />";exit;
        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $result['result'] = true;
            $result['message'] = '성공';
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
            $result['message'] = '데이터베이스 저장에 실패하였습니다.';
        }

        if ( $result['result']  ) {

            if ( $_postdata['ToDoID'] == 'CLEAR') {
                $_resetPToDoID = $this->getUserName($_postdata['OriginToDoID']);
                $_messages = "- 담당자지정 취소 : $_resetPToDoID->name ";
                $result['ToDoUser'] = $_resetPToDoID->name;
                $result['OldToDoUser'] = null;
            }else{
                if ($_postdata['OriginToDoID'] == 'undefined'  ) {
                    $_newPToDoID = $this->getUserName($_postdata['ToDoID']);
                    $_messages = "- 담당자지정 : $_newPToDoID->name ";
                    $result['ToDoUser'] = $_newPToDoID->name;
                    $result['OldToDoUser'] = null;
                }else{
                    $_oldPToDoID = $this->getUserName($_postdata['OriginToDoID']);
                    $_newPToDoID = $this->getUserName($_postdata['ToDoID']);
                    $_messages = "- 담당자변경 : $_oldPToDoID->name > $_newPToDoID->name ";
                    $result['ToDoUser'] = $_newPToDoID->name;
                    $result['OldToDoUser'] = $_oldPToDoID->name;
                }
            }


            $this->db->reset_query();
            $_regname = $this->getUserName($LoggedID);
            $sessionArray = array('userId' => $LoggedID,
                'userIdGroup' => ($LoggedGroupIdx===null?1:$LoggedGroupIdx),
                'message' => $_messages,
                'regname' => $_regname->name
            );

            $this->db->reset_query();
            $this->db->set('createdDtm', date("Y-m-d H:i:s"));
            $this->db->set('userId', $LoggedID);
            $this->db->set('userIdGroup', ($LoggedGroupIdx===null?1:$LoggedGroupIdx));
            $this->db->set('sessionData', json_encode($sessionArray));
            $this->db->set('WorkDate', date("Y-m-d"));
            $this->db->set('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
            $this->db->insert('ProjectWorkHistory');

            $this->db->trans_commit();

        }
        return $result;
    }

    function updateworkstatus($LoggedID = null,  $LoggedGroupIdx = null,  $_postdata = array() ) {

        if ( empty($_postdata['ProjectWorkIdx']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $_subinfo = $this->getWorkStarttime($_postdata['ProjectWorkIdx']);

        $this->db->reset_query();
        if ( $_postdata['mode'] == 'doing') {
            $newSdata = date("Y-m-d H:i:s");
            if ( $_subinfo->ProjectMode == PROJECT_MAINTERENCE_MODE ) {
                $this->db->set('sDate', $newSdata);
            }
            $this->db->set('sTime', time());
        }else if( $_postdata['mode'] == 'done') {
            $newEdata = date("Y-m-d H:i:s");
            //if ( $_subinfo->ProjectMode == PROJECT_MAINTERENCE_MODE ) {
                $this->db->set('eDate', $newEdata);
            //}
            $this->db->set('eTime', time());
            $this->db->set('Rate', 100);
        }else if( $_postdata['mode'] == 'return') {
            $newSdata = date("Y-m-d H:i:s");
            //$this->db->set('sDate', $newSdata);
            if ( $_subinfo->ProjectMode == PROJECT_MAINTERENCE_MODE ) {
                $this->db->set('eDate', null);
            }else{
                $this->db->set('eDate',date("Y-m-d"));
            }
            $this->db->set('sTime', time());
            $this->db->set('eTime', null);
        }else if( $_postdata['mode'] == 'start') {
            if ( $_subinfo->ProjectMode == PROJECT_MAINTERENCE_MODE ) {
                //$this->db->set('sDate', null);
                $this->db->set('eDate', null);
                //$this->db->set('sTime', null);
            }
            $this->db->set('eTime', null);
        }

        $this->db->set('Status', $_postdata['Status']);
        $this->db->set('ModID', $LoggedID);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->update('ProjectWork');
        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;

        if ( isset($_subinfo->Rate)){
            $result['Rate'] = $_subinfo->Rate;
        }
        $_doingtime = 0;
        if ( $result['result']  ) {
            if ( $_postdata['mode'] == 'doing') {
                $_messages = "- 업무시작";
            }else if ( $_postdata['mode'] == 'done') {
                $_messages = "- 업무종료 , 진척도 100% 조정";

                if ( $_subinfo->sTime == null ) {
                    $_doingtime     = 0;
                }else{
                    $diff = time()-$_subinfo->sTime;
                    $_doingtime     = round($diff / 60 );
                }

            }else if ( $_postdata['mode'] == 'return') {
                $_messages = "- 업무종료 -> 업무재개";
            }else if ( $_postdata['mode'] == 'start') {
                $_messages = "- 업무중 -> 업무대기";
                //echo $_subinfo->sTime; echo "<br >";
                if ( $_subinfo->sTime == null ) {
                    $_doingtime     = 0;
                }else{


                    //echo time(); echo "<br >";
                    $diff = time()-$_subinfo->sTime;
                    //echo $diff; echo "<br >";
                    $_doingtime     = round($diff / 60 );
                   // echo $_doingtime; echo "<br >";
                   // exit;
                }
            }else{
                $_messages = "- 업무대기";
            }

            $this->db->reset_query();
            $_regname = $this->getUserName($LoggedID);
            $sessionArray = array('userId' => $LoggedID,
                'userIdGroup' => ($LoggedGroupIdx===null?1:$LoggedGroupIdx),
                'message' => $_messages,
                'regname' => $_regname->name
            );

            $this->db->reset_query();
            $this->db->set('createdDtm', date("Y-m-d H:i:s"));
            $this->db->set('userId', $LoggedID);
            $this->db->set('ismode', isset($_postdata['mode']) ? $_postdata['mode'] :  null);
            $this->db->set('DoingTime', $_doingtime);
            $this->db->set('WorkDate', date("Y-m-d"));
            $this->db->set('userIdGroup', ($LoggedGroupIdx===null?1:$LoggedGroupIdx));
            $this->db->set('sessionData', json_encode($sessionArray));
            $this->db->set('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
            $this->db->insert('ProjectWorkHistory');
        }
        return $result;
    }


    function updatedata($LoggedID = null, $_postdata = array() ) {


        if ( empty($LoggedID) || empty($_postdata['ProjectIdx']) || empty($_postdata['ProjectTitle']) ) {
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

        if ( $_postdata['Permission'] !==null  ){
            $_tmp = explode(",",$_postdata['Permission']);
            $Permission = json_encode($_tmp);
        }else{
            $Permission = null;
        }
        $this->db->reset_query();
        $this->db->set('Permission', $Permission);
        $this->db->set('IsChat', ($_postdata['IsChat'] == 1?1:`b(0)`));
        $this->db->set('ProjectStatus', $_postdata['ProjectStatus']);
        $this->db->set('ProjectMode', $_postdata['ProjectMode']);
        $this->db->set('ProjectTitle', $_postdata['ProjectTitle']);
        $this->db->set('ModID', $LoggedID);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->where('ProjectIdx', $_postdata['ProjectIdx']);
        $this->db->update('Project');
        $error = $this->db->error();

        if ( empty($error['code']) ) {
            $result['result'] = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
        }
        return $result;
    }



    function teamviewupdate($_postdata = array() ) {

        if ( empty($_postdata['TargetUserID']) ) {
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


        if ( $_postdata['Permission'] !==null  ){
            $_tmp = explode(",",$_postdata['Permission']);
            $Permission = json_encode($_tmp);
        }else{
            $Permission = null;
        }

        $this->db->reset_query();
        $data = array(
            'UserID' => $_postdata['TargetUserID'],
            'Permission'  => $Permission
        );
        $this->db->replace('TeamView', $data);
        $error = $this->db->error();

        if ( empty($error['code']) ) {
            $result['result'] = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
        }
        return $result;
    }

    function insertworkdata($LoggedInfo = array(), $_postdata = array() ) {

        if ( count($LoggedInfo) == 0 ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }
        /*if ( $LoggedInfo['role'] == ROLE_EMPLOYEE ) {
            $this->db->reset_query();
            $_TodoGroup = $this->getUserGroupCode( $LoggedInfo['userId']);
        }*/

        $this->db->reset_query();
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->set('Status', 1);
        $this->db->set('Rate', 0);
        $this->db->set('RegID', $LoggedInfo['userId']);
        $this->db->set('RegIDGroup', isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1);

        if ( $LoggedInfo['groupidx'] > 1 ) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            if ( in_array($LoggedInfo['groupidx'],$_CommonCode['DESIGNGROUP'])) {
                $this->db->set('GroupCode',"Design");
            }else if ( in_array($LoggedInfo['groupidx'],$_CommonCode['PLANNINGGROUP'])) {
                $this->db->set('GroupCode',"Planning");
            }
        }

        if ( $LoggedInfo['role'] == ROLE_EMPLOYEE ) {
            $this->db->set('ToDoID', $LoggedInfo['userId']);
            $this->db->set('ToDoIDGroup', isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1);
        }
        $this->db->set('ModID', $LoggedInfo['userId']);
        $this->db->set('sDate', $_postdata['sDate']);
        $this->db->set('eDate', $_postdata['eDate']);
        $this->db->set('title', $_postdata['title']);
        $this->db->set('ProjectIdx', $_postdata['ProjectIdx']);
        $this->db->insert('ProjectWork');
        $_idx = $this->db->insert_id();
        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $result['result'] = true;
            $result['result_idx'] = $_idx;
            //History
            $this->db->reset_query();
            $_regname = $this->getUserName($LoggedInfo['userId']);
            $sessionArray = array('userId'=>$LoggedInfo['userId'],
                'userIdGroup' => (isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1),
                'message'=>"프로젝트 일감 최초 등록 , 시작일 : ".$_postdata['sDate']." ,종료일 : ".$_postdata['eDate']." ",
                'regname'=>$_regname->name
            );

            $this->db->set('createdDtm', date("Y-m-d H:i:s"));
            $this->db->set('userId', $LoggedInfo['userId']);
            $this->db->set('userIdGroup',(isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1));
            $this->db->set('sessionData', json_encode($sessionArray));
            $this->db->set('WorkDate', date("Y-m-d"));
            $this->db->set('ProjectWorkIdx', $_idx);
            $this->db->insert('ProjectWorkHistory');

        }else{
            $result['result'] = false;
        }

        //$result['result'] = empty($error['code']) ? true : false ;

        return $result;
    }

    function getTeamViewPermission($_idx = null) {

        if ( empty($_idx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }
        $this->db->select('Permission');
        $this->db->from('TeamView');
        $this->db->where('UserID', $_idx);
        $this->db->limit(1);
        $query = $this->db->get();
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }


    function getUserListPermission($_idx = null) {

        if ( empty($_idx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->select('TeamView.UserID');
        $this->db->select('tbl_users.name as USER_NAME');
        $this->db->select('ADMIN_MEMBER.GROUP_IDX');
        $this->db->select('ADMIN_MEMBER_GROUP.NAME AS GROUP_NAME');
        $this->db->select('CASE WHEN ISNULL(ADMIN_VARS.NAME) THEN ADMIN_MEMBER_CLASS.NAME ELSE ADMIN_VARS.NAME END AS CLASS_NAME');
        $this->db->from('TeamView');
        $this->db->join('tbl_users', 'TeamView.UserID = tbl_users.userId','INNER');
        $this->db->join('ADMIN_MEMBER', 'tbl_users.hackersid = ADMIN_MEMBER.USER_ID','INNER');
        $this->db->join('ADMIN_MEMBER_GROUP', 'ADMIN_MEMBER.GROUP_IDX = ADMIN_MEMBER_GROUP.IDX','INNER');
        $this->db->join('ADMIN_MEMBER_CLASS'," ADMIN_MEMBER.CLASS_IDX = ADMIN_MEMBER_CLASS.IDX  ",'LEFT');
        $this->db->join('ADMIN_VARS'," ADMIN_MEMBER.HR_VAR_9 = ADMIN_VARS.IDX  ",'LEFT');
        $this->db->where('tbl_users.isDeleted', 0);
        $this->db->where('ADMIN_MEMBER.DENIED', 'N');
        $this->db->where("JSON_CONTAINS(TeamView.Permission, '[\"".$_idx."\"\]')");
        $this->db->order_by('ADMIN_MEMBER_GROUP.IDX', 'ASC');
        $this->db->order_by('(CASE WHEN ADMIN_MEMBER.HR_VAR_9 = 0 THEN 1000 ELSE ADMIN_MEMBER.HR_VAR_9 END)','ASC');
        $this->db->order_by('tbl_users.name', 'DESC');
        $query = $this->db->get();

        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }


    function getWorkInfo($_idx = null) {

        if ( empty($_idx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }
        $this->db->select('*');
        $this->db->from('ProjectWork');
        $this->db->where('ProjectWorkIdx', $_idx);
        $this->db->limit(1);
        $query = $this->db->get();
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }

    function cloneworkdata($LoggedInfo = array(), $_postdata = array() ) {

        if ( count($LoggedInfo) == 0 ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $_clonetmpData = $this->getWorkInfo($_postdata['ProjectWorkIdx']);
        if ( !$_clonetmpData['result']) {
            $_return['result'] = false;
            $_return['message'] = "복제할 대상이 없습니다..";
            return $_return;
        }
        $_cloneData = $_clonetmpData['row'][0];


        $_postdata['CloneCount'] > 10 ? $CloneCount = 10 : $CloneCount = $_postdata['CloneCount'];
        for( $i = 0; $i <  $CloneCount ; $i++ ) {

            $this->db->reset_query();
            $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
            $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
            $this->db->set('Status', 1);
            $this->db->set('Rate', 0);
            $this->db->set('RegID', $LoggedInfo['userId']);
            $this->db->set('RegIDGroup', isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1);
            if ( $LoggedInfo['groupidx'] > 1 ) {
                $this->config->load('config',true);
                $_CommonCode = $this->config->item('code');
                if ( in_array($LoggedInfo['groupidx'],$_CommonCode['DESIGNGROUP'])) {
                    $this->db->set('GroupCode',"Design");
                }else if ( in_array($LoggedInfo['groupidx'],$_CommonCode['PLANNINGGROUP'])) {
                    $this->db->set('GroupCode',"Planning");
                }
            }
            $this->db->set('ToDoID', "");
            $this->db->set('ToDoIDGroup', 0);
            $this->db->set('ModID', $LoggedInfo['userId']);
            $this->db->set('title', $_cloneData['title']);
            $this->db->set('sDate', $_cloneData['sDate']);
            $this->db->set('eDate', $_cloneData['eDate']);
            $this->db->set('IntraBoard', $_cloneData['IntraBoard']);
            $this->db->set('IntraUrl', $_cloneData['IntraUrl']);
            $this->db->set('IntraBoardIdx', $_cloneData['IntraBoardIdx']);
            $this->db->set('ChildMode', $_cloneData['ChildMode']);
            $this->db->set('IsReOpen', 2);
            $this->db->set('ProjectIdx', $_cloneData['ProjectIdx']);
            $this->db->insert('ProjectWork');
            $_idx = $this->db->insert_id();
            $error = $this->db->error();
            if ( empty($error['code']) ) {
                $result['result'] = true;
                $result['result_idx'] = $_idx;
                //History
                $this->db->reset_query();
                $sessionArray = array('userId'=>$LoggedInfo['userId'],
                    'userIdGroup' => (isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1),
                    'message'=>"프로젝트 일감 최초 등록(복제) , 시작일 : ".$_cloneData['sDate']." ,종료일 : ".$_cloneData['eDate']." ",
                    'regname'=>$LoggedInfo['name']
                );

                $this->db->set('createdDtm', date("Y-m-d H:i:s"));
                $this->db->set('userId', $LoggedInfo['userId']);
                $this->db->set('userIdGroup',(isset($LoggedInfo['groupidx'])?$LoggedInfo['groupidx']:1));
                $this->db->set('sessionData', json_encode($sessionArray));
                $this->db->set('WorkDate', date("Y-m-d"));
                $this->db->set('ProjectWorkIdx', $_idx);
                $this->db->insert('ProjectWorkHistory');

            }else{
                $result['result'] = false;
                return $result;
            }
        }

        return $result;
    }

    function insertprojectdata($LoggedID = null, $_postdata = array() ) {

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
        if ( $_postdata['Permission'] !==null  ){
            $_tmp = explode(",",$_postdata['Permission']);
            $Permission = json_encode($_tmp);
        }else{
            $Permission = null;
        }
        $this->db->set('Permission', $Permission);
        $this->db->set('IsChat', $_postdata['IsChat']);
        $this->db->set('ProjectMode', $_postdata['ProjectMode']);
        $this->db->set('ProjectStatus', $_postdata['ProjectStatus']);
        $this->db->set('ProjectNo', date("YmdHis") );
        $this->db->set('ProjectTitle', $_postdata['ProjectTitle']);
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->set('RegID', $LoggedID);
        $this->db->set('RegIDGroup', $_postdata['RegIDGroup']);
        $this->db->set('ModID', $LoggedID);
        $this->db->insert('Project');
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


    function inserttododata($_postdata = array(), $_sessiondata ) {


        if ( empty($_postdata['ProjectIdx']) || empty($_postdata['title']) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }
        $_returnData = array();
        $this->db->trans_begin(); // 트랜젝션 시작
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $_return['result'] = false;
            $_return['message'] = "트랜잭션 오류.";
            return $_return;
            exit;
        }
        $this->db->reset_query();
        $_RegIDGroup = $this->getUserGroupCode($_postdata['RegID']);

        $this->db->reset_query();
        $this->db->set('ModDatetime', date("Y-m-d H:i:s"));
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->set('sDate', date("Y-m-d"));
        $this->db->set('eDate', date("Y-m-d"));
        $this->db->set('Status', 1);
        $this->db->set('Rate', 0);
        if ( isset($_postdata['TodoID'] ) && $_postdata['TodoID'] ) {
            $this->db->set('TodoID', $_postdata['TodoID']);
            $_ToDoIDGroup = $this->getUserGroupCode($_postdata['TodoID']);
            $this->db->set('ToDoIDGroup', $_ToDoIDGroup[0]['GROUP_IDX']);
        }
        $this->db->set('RegID', $_postdata['RegID']);
        $this->db->set('RegIDGroup',  isset($_RegIDGroup[0]['GROUP_IDX'])?$_RegIDGroup[0]['GROUP_IDX']:1);
        if ( $_RegIDGroup[0]['GROUP_IDX'] > 1 ) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            if ( in_array($_RegIDGroup[0]['GROUP_IDX'],$_CommonCode['DESIGNGROUP'])) {
                $this->db->set('GroupCode',"Design");
            }else if ( in_array($_RegIDGroup[0]['GROUP_IDX'],$_CommonCode['PLANNINGGROUP'])) {
                $this->db->set('GroupCode',"Planning");
            }
        }
        $this->db->set('ModID', $_postdata['RegID']);
        $this->db->set('title', $_postdata['title']);
        $this->db->set('ChildMode', $_postdata['ChildMode']);
        $this->db->set('Priority', $_postdata['Priority']);
        $this->db->set('Foretime', $_postdata['Foretime']);
        $this->db->set('ProjectIdx', $_postdata['ProjectIdx']);
        $this->db->insert('ProjectWork');

        $_idx = $this->db->insert_id();
        $error = $this->db->error();
        if ( empty($error['code']) ) {

            $result['result'] = true;
            $_returnData['RegDatetime'] =  date("Y-m-d H:i");
            $_returnData['TmpSdate'] =  date("Y-m-d");
            $_returnData['title'] =  htmlspecialchars($_postdata['title']);
            $_returnData['Foretime'] =  $_postdata['Foretime'];
            $_returnData['ChildMode'] =  $_postdata['ChildMode'];
            $_returnData['Priority'] =  $_postdata['Priority'];
            if ( isset($_postdata['TodoID'] ) && $_postdata['TodoID'] ) {
                $_username = $this->getUserName($_postdata['TodoID']);
                $_returnData['TodoUser'] =  $_username->name;
            }else{
                $_returnData['TodoUser'] =  "미정";
            }
            if ( isset($_postdata['TodoID'] ) && $_postdata['TodoID'] ) {
                $_returnData['TodoID'] = $_postdata['TodoID'];
            }

            $result['Priority'] = $_postdata['Priority'];
            $result['data'] = $_returnData;
            $result['result_idx'] = $_idx;

            //History
            $this->db->reset_query();
            $_regname = $this->getUserName($_postdata['RegID']);
            $sessionArray = array('userId'=>$_postdata['RegID'],
                'userIdGroup'=>$_sessiondata['groupidx'],
                'message'=>"프로젝트 일감 최초 등록",
                'regname'=>$_regname->name
            );

            $this->db->set('createdDtm', date("Y-m-d H:i:s"));
            $this->db->set('userId', $_postdata['RegID']);
            $this->db->set('userIdGroup', ($_sessiondata['groupidx']===null?0:$_sessiondata['groupidx']));
            $this->db->set('sessionData', json_encode($sessionArray));
            $this->db->set('WorkDate', date("Y-m-d"));
            $this->db->set('ProjectWorkIdx', $_idx);
            $this->db->insert('ProjectWorkHistory');

            if ( isset($_postdata['TodoID'] ) && $_postdata['TodoID'] ) {
                $this->db->reset_query();
                $sessionArray2 = array('userId'=>$_postdata['RegID'],
                    'userIdGroup'=>$_sessiondata['groupidx'],
                    'message'=>"프로젝트 일감 업무할당 ▶ ".$_username->name,
                    'regname'=>$_regname->name
                );
                $this->db->set('createdDtm', date("Y-m-d H:i:s"));
                $this->db->set('userId', $_postdata['RegID']);
                $this->db->set('userIdGroup', ($_sessiondata['groupidx']===null?0:$_sessiondata['groupidx']));
                $this->db->set('sessionData', json_encode($sessionArray2));
                $this->db->set('WorkDate', date("Y-m-d"));
                $this->db->set('ProjectWorkIdx', $_idx);
                $this->db->insert('ProjectWorkHistory');
            }

            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
        }

        //$result['result'] = empty($error['code']) ? true : false ;

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
            $this->db->where('ProjectWorkIdx', $val);
            $this->db->update('ProjectWork');
            $error = $this->db->error();
            if ( empty($error['code']) ) {
                $_return['message'] = "성공.";
                $result['result'] = true;
            }else{
                $_return['message'] = "데이터베이스 업데이트중 에러가 발생하였습니다.";
                $result['result'] = false;
                return $result;
            }
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

    function getWorkStartTime($_widx = null) {
        if ( empty($_widx) ) {
            $result[0]['sTime'] = 0;
            return $result[0];
        }

        $this->db->select('ProjectWork.sTime');
        $this->db->select('ProjectWork.Rate');
        $this->db->select('Project.ProjectMode');
        $this->db->from('ProjectWork');
        $this->db->join('Project', 'ProjectWork.ProjectIdx = Project.ProjectIdx','INNER');
        $this->db->where('ProjectWorkIdx', $_widx);
        $query = $this->db->get();
        $result = $query->result();

        return $result[0];
    }

    function getUserGroupCode($_userid = null) {
        if ( empty($_userid) ) {
            $result[0]['GROUP_IDX'] = 0;
            return $result[0]['GROUP_IDX'];
        }

        $this->db->select('ADMIN_MEMBER.GROUP_IDX');
        $this->db->from('tbl_users');
        $this->db->join('ADMIN_MEMBER', 'tbl_users.hackersidx = ADMIN_MEMBER.MEMBER_IDX','INNER');
        $this->db->where('tbl_users.userId', $_userid);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }






    function getUserNameByHackesrIdx($_hackersidx = null) {
        if ( empty($_hackersidx) ) {
            return false;
        }

        $this->db->select('userId');
        $this->db->select('name');
        $this->db->where('hackersidx', $_hackersidx);
        $query = $this->db->get('tbl_users');
        $result = $query->result();
        //echo nl2br($this->db->last_query());exit;
        $error = $this->db->error();
        if ( empty($error['code']) && isset($result[0]->name)) {
            $_return['result'] = true;
            $_return['UserName'] = $result[0]->name;
            $_return['userId'] = $result[0]->userId;

        }else{
            $_return['result'] = false;
        }
        return $_return;
    }

    function insertcomment($LoggedID = null,$LoggedName = null, $_postdata = array() ) {

        if ( empty($LoggedID) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('RegDatetime', date("Y-m-d H:i:s"));
        $this->db->set('RegID', $LoggedID);
        $this->db->set('Comment', $this->xssClean($_postdata['Comment']));
        $this->db->set('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->insert('ProjectWorkComment');

        $_idx = $this->db->insert_id();
        $error = $this->db->error();
        if ( empty($error['code']) ) {
            $result['result'] = true;
            $result['result_idx'] = $_idx;
            $result['RegName'] = $LoggedName;
            $result['RegDatetime'] = date("Y-m-d");

        }else{
            $result['result'] = false;
        }

        //$result['result'] = empty($error['code']) ? true : false ;

        return $result;
    }

    function removecomment($LoggedID = null ,$_postdata = array()){
         if ( empty($LoggedID) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        // Sub Query
        $this->db->reset_query();
        $this->db->select('ProjectWorkIdx');
        $this->db->from('ProjectWorkComment');
        $this->db->where('ProjectWorkCommentIdx', $_postdata['ProjectWorkCommentIdx']);
        $query = $this->db->get();
        $subQuery = $query->result();

        $this->db->reset_query();
        $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
        $this->db->set('DelID', $LoggedID);
        $this->db->set('IsDel', 1);
        $this->db->where('ProjectWorkCommentIdx', $_postdata['ProjectWorkCommentIdx']);
        $this->db->update('ProjectWorkComment');
        $error = $this->db->error();


        if ( empty($error['code']) ) {
            $result['result'] = true;
            $this->db->reset_query();
            $this->db->select('Comment');
            $this->db->from('ProjectWorkComment');
            $this->db->where('ProjectWorkIdx', $subQuery[0]->ProjectWorkIdx);
            $this->db->where("DelID = ''");
            $this->db->order_by('RegDatetime', 'DESC');
            $this->db->limit(1);
            $squery = $this->db->get();
            $sresult = $squery->result();
            //echo nl2br($this->db->last_query());exit;
            $serror = $this->db->error();
            if ( empty($serror['code']) && isset($sresult[0]->Comment)) {
                $result['lastcomment'] = htmlspecialchars($sresult[0]->Comment);
            }else{
                $result['lastcomment'] = "";
            }
        }else{
            $result['result'] = false;
            $result['message'] = '데이터베이스 저장에 실패하였습니다.';
        }
        return $result;
    }

    function getReportDate($ReportIdx = null) {
        if ( empty($ReportIdx) ) {
            return false;
        }

        $this->db->select('wDate');
        $this->db->select('userId');
        $this->db->select('userGroup');
        $this->db->from('Report');
        $this->db->where('ReportIdx', $ReportIdx);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    function getUserInfo($_Idx = null) {
        if ( empty($_Idx) ) {
            return false;
        }

        $this->db->select('M.name');
        $this->db->select('M.userId');
        $this->db->select('G.Name as GROUP_NAME');
        $this->db->from('tbl_users as M');
        $this->db->join('ADMIN_MEMBER as A'," M.hackersid = A.USER_ID  ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.GROUP_IDX = G.IDX  ",'INNER');
        $this->db->where('M.userId', $_Idx);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }


    function old_deletereport($ReportIdx = null){

        if ( empty($ReportIdx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $_wdate =  $this->getReportDate($ReportIdx);
        $this->db->reset_query();
        $this->db->where('wDate', $_wdate[0]['wDate']);
        $this->db->where('userId', $_wdate[0]['userId']);
        $this->db->where('userGroup', $_wdate[0]['userGroup']);
        $this->db->delete('Report');
        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;

        return $result;

    }

    function deletereport($ReportIdx = null){

        if ( empty($ReportIdx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }
        $this->db->reset_query();
        $this->db->where('reportGroup', $ReportIdx);
        $this->db->delete('Report');
        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;

        return $result;

    }


    function removework($LoggedID = null ,$_postdata = array()){
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
        $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
        $this->db->set('DelID', $LoggedID);
        $this->db->set('IsDel', 1);
        $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
        $this->db->update('ProjectWork');
        $error = $this->db->error();

        if ( empty($error['code']) ) {

            /*일단 save
            $this->db->reset_query();
            $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
            $this->db->delete('ProjectWorkComment');

            $this->db->reset_query();
            $this->db->where('ProjectWorkIdx', $_postdata['ProjectWorkIdx']);
            $this->db->delete('ProjectWorkHistory');
            */

            $result['result'] = true;
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
        }

        return $result;

    }

    function isCheckeProject($_idx = null)
    {
        if ( empty($_idx) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('ProjectWork AS A');
        $this->db->where('ProjectIdx', $_idx);
        $this->db->where('IsDel IS NULL');
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";exit;
        return $query->num_rows();
    }

    function removeproject($LoggedID = null ,$_postdata = array()){
        if ( empty($LoggedID) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
        $this->db->set('DelID', $LoggedID);
        $this->db->where('ProjectIdx', $_postdata['ProjectIdx']);
        $this->db->update('Project');
        $error = $this->db->error();

        $result['result'] = empty($error['code']) ? true : false ;

        return $result;

    }


    function removeprojectforce($LoggedID = null ,$_postdata = array()){
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

        $_tmptarget_array = $this->getProjectWorkArray($_postdata['ProjectIdx']);
        if ( count($_tmptarget_array) > 0 ) {
            $_target_array = array();
            foreach( $_tmptarget_array as $inkey => $inrow ) {
                array_push($_target_array ,$inrow['ProjectWorkIdx']);
            }

            $this->db->reset_query();
            $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
            $this->db->set('DelID', $LoggedID);
            $this->db->where_in('ProjectWorkIdx', $_target_array);
            $this->db->update('ProjectWorkComment');
            $error = $this->db->error();
            if ( !empty($error['code']) ) {
                $this->db->trans_rollback();
                $result['result'] = false;
                $result['message'] = 'ProjectWorkComment 업무 삭제 실패';
            }

            $this->db->reset_query();
            $this->db->where_in('ProjectWorkIdx', $_target_array);
            $this->db->delete('ProjectWorkHistory');
            $error = $this->db->error();
            if ( !empty($error['code']) ) {
                $this->db->trans_rollback();
                $result['result'] = false;
                $result['message'] = 'ProjectWork 업무 삭제 실패';
            }
        }

        $this->db->reset_query();
        $this->db->set('IsDel', 1);
        $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
        $this->db->set('DelID', $LoggedID);
        $this->db->where('ProjectIdx', $_postdata['ProjectIdx']);
        $this->db->update('ProjectWork');
        $error = $this->db->error();
        if ( !empty($error['code']) ) {
            $this->db->trans_rollback();
            $result['result'] = false;
            $result['message'] = 'ProjectWork 업무 삭제 실패';
        }


        $this->db->reset_query();
        $this->db->set('DelDatetime', date("Y-m-d H:i:s"));
        $this->db->set('DelID', $LoggedID);
        $this->db->where('ProjectIdx', $_postdata['ProjectIdx']);
        $this->db->update('Project');
        $error = $this->db->error();


        if ( empty($error['code']) ) {
            $result['result'] = true;
            $result['message'] = '성공';
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
            $result['result'] = false;
            $result['message'] = '데이터베이스 저장에 실패하였습니다.';
        }

        return $result;

    }

    function removenotice($LoggedID = null ,$_postdata = array()){
        if ( empty($LoggedID) ) {
            $_return['result'] = false;
            $_return['message'] = "필수값이 누락되었습니다.";
            return $_return;
        }

        $this->db->reset_query();
        $this->db->set('IsDel', $LoggedID);
        $this->db->where('NoticeIdx', $_postdata['NoticeIdx']);
        $this->db->update('Notice');
        $error = $this->db->error();

        $result['result'] = empty($error['code']) ? true : false ;

        return $result;

    }


    function getReplyListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('ProjectWorkComment AS R');
        $this->db->join('tbl_users as M'," R.RegID = M.userId  ",'LEFT');

        $this->db->order_by('R.RegDatetime', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    function getHistoryListing($search, $_select){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('ProjectWorkHistory AS H');
        $this->db->order_by('H.idx', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

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

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.RegDatetime', 'DESC');
        $query = $this->db->get();

        //echo nl2br($this->db->last_query());exit;
        $result = $query->result_array();
        return $result;

    }

}

