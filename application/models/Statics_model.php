<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Statics_model (Ordering Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */

require APPPATH . '/libraries/BaseModel.php';
class Statics_model extends BaseModel
{
    function ProjectTotalCount($search = array())
    {
        $this->db->select('*');
        $this->db->from('Project');
        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $query = $this->db->get();

        return $query->num_rows();
    }


    function ProjectWorkTotalCount($search = array())
    {
        $this->db->select('*');
        $this->db->from('ProjectWork');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function ProjectWorkRate($search = array())
    {
        $this->db->select(' AVG(Rate) AS AVGRate');
        $this->db->from('ProjectWork');
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $query = $this->db->get();
        return $query->result_array();
    }


    function ProjectWorkType($search = array(),$_select = array())
    {
        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $this->db->from('ProjectWork');
        $this->db->where("IsDel IS NULL");
        $this->db->where($search);
        $this->db->group_by("ChildMode ");
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function UserTotalCount($search = array())
    {
        $this->db->select('*');
        $this->db->from('tbl_users');
        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $query = $this->db->get();

        return $query->num_rows();
    }

    function getRecentlyWorks($search, $_select , $mode = null){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('ProjectWork AS A');
        $this->db->join('tbl_users as M'," A.ToDoID = M.userId  ",'LEFT');
        $this->db->join('tbl_users as M2'," A.RegID = M2.userId  ",'LEFT');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," A.RegIDGroup = G.IDX  ",'LEFT');
        $this->db->join('ADMIN_MEMBER_GROUP as G2'," A.ToDoIDGroup = G2.IDX  ",'LEFT');
        if ( $mode === null ) {
            $this->db->order_by('A.ProjectWorkIdx', 'DESC');
        }else{
            $this->db->order_by('A.ProjectWorkIdx', 'ASC');        }

        $this->db->limit(10);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }

    function getRecentlyWorkTime($search, $_select , $mode = null){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('ProjectWorkHistory AS A');
        $this->db->join('tbl_users as M'," A.userId = M.userId  ",'INNER');
        $this->db->group_by('A.WorkDate');
        $this->db->order_by('A.WorkDate', 'DESC');
        $this->db->limit(8);
        $query = $this->db->get();
        
        $result = $query->result_array();
        return $result;

    }

    function getRecentlyWorkType($search, $_select , $mode = null){

        // Sub Query
        $this->db->select('LEFT(RegDatetime,10) AS RegDate');
        $this->db->select('COUNT(ProjectWorkIdx) as SumProjectWork');
        $this->db->from('ProjectWork');
        $this->db->where("IsDel IS NULL");
        $this->db->group_by('LEFT(RegDatetime,10)');
        $this->db->order_by("RegDatetime","DESC");
        $this->db->limit(11);
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
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('ProjectWorkHistory AS A');
        $this->db->join('ProjectWork as P'," A.ProjectWorkIdx = P.ProjectWorkIdx  ",'INNER');
        $this->db->join('('.$subQuery.") as S","A.WorkDate = S.RegDate",'LEFT');
        $this->db->where("P.IsDel IS NULL");
        $this->db->group_by('A.WorkDate');
        $this->db->order_by('A.WorkDate', 'DESC');
        $this->db->limit(8);
        $query = $this->db->get();
        $result = $query->result_array();




        return $result;

    }

    function getRankManyWorks($search, $_select , $mode = null){

        $_basedate = date("Y-m-d H:i:s",strtotime("-10 day"));
        // Sub Query
        $this->db->reset_query();
        $this->db->select('ToDoID');
        $this->db->select('COUNT(ProjectWorkIdx) as SumAllProjectWork');
        $this->db->select('MAX(ToDoIDGroup) as ToDoIDGroup');
        $this->db->from('ProjectWork');
        $this->db->where("IsDel IS NULL");
        $this->db->where("RegDatetime >= '".$_basedate."'");
        $this->db->group_by('ToDoID');
        $subQuery =  $this->db->get_compiled_select();


        // Sub Query
        $this->db->reset_query();
        $this->db->select('ToDoID');
        $this->db->select('COUNT(ProjectWorkIdx) as SumDoneProjectWork');
        $this->db->from('ProjectWork');
        $this->db->where("IsDel IS NULL");
        $this->db->where("RegDatetime >= '".$_basedate."'");
        $this->db->where("Status",9);
        $this->db->group_by('ToDoID');
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
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('('.$subQuery.") as S");
        $this->db->join('('.$subQuery2.") as S2","S.ToDoID = S2.ToDoID",'LEFT');
        $this->db->join('tbl_users as M'," S.ToDoID = M.userId  ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," S.ToDoIDGroup = G.IDX  ",'LEFT');
        $this->db->order_by('S.SumAllProjectWork', 'DESC');
        $this->db->limit(7);
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;

    }

    function getRankGoodWorks($search, $_select , $mode = null){

        // Sub Query
        $this->db->reset_query();
        $this->db->select('ToDoID');
        $this->db->select('ProjectWorkIdx');
        $this->db->select('MAX(Foretime) as Foretime');
        $this->db->select('MAX(ToDoIDGroup) as ToDoIDGroup');
        $this->db->from('ProjectWork');
        $this->db->where("IsDel IS NULL");
        $this->db->where("Foretime > 0 ");
        $this->db->where("ToDoID > 0 ");
        $this->db->group_by('ToDoID');
        $this->db->group_by('ProjectWorkIdx');
        $subQuery =  $this->db->get_compiled_select();

        // Sub Query
        $this->db->reset_query();
        $this->db->select('userId AS ToDoID');
        $this->db->select('ProjectWorkIdx');
        $this->db->select('SUM(DoingTime) as SumDoingTime');
        $this->db->from('ProjectWorkHistory');
        $this->db->where("DoingTime > 0 ");
        $this->db->group_by('userId');
        $this->db->group_by('ProjectWorkIdx');
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
        if ( is_array($search) && count($search) ) $this->setWhere($search);
        $this->db->from('('.$subQuery.") as S");
        $this->db->join('('.$subQuery2.") as S2","S.ToDoID = S2.ToDoID AND  S.ProjectWorkIdx = S2.ProjectWorkIdx ",'LEFT');
        $this->db->join('tbl_users as M'," S.ToDoID = M.userId  ",'INNER');
        $this->db->join('ADMIN_MEMBER_GROUP as G'," S.ToDoIDGroup = G.IDX  ",'LEFT');
        $this->db->where("S.Foretime > 0 ");
        $this->db->where("S2.SumDoingTime > 0 ");
        $this->db->group_by('S.ToDoID');
        $this->db->having('Rate > 80');
        $this->db->order_by('Rate', 'ASC');
        $this->db->limit(7);
        $query = $this->db->get();
        $result = $query->result_array();


        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());exit;
        };

        return $result;

    }

    function getNotReoprtUseer($search, $_select , $_termday = null){

        $_TODAY = date("Y-m-d");
        $_BaseDay = date("Y-m-d", strtotime("-".$_termday." days", strtotime($_TODAY)));
        $this->db->reset_query();
        $this->db->select("HolidayIdx as ReportIdx");
        $this->db->from('Holidays');
        $this->db->where("wDate",$_BaseDay);
        $tquery = $this->db->get();
        $_tresult =  $tquery->row();
        if  ( $_tresult > 0 ) {
            $result =  array();
        }else {
            $this->db->reset_query();
            // Sub Query2
            $_TODAY = date("Y-m-d");
            $_BaseDay = date("Y-m-d", strtotime("-" . $_termday . " days", strtotime($_TODAY)));
            $this->db->select('RegID');
            $this->db->select('MAX(ScheduleIdx) as ScheduleIdx');
            $this->db->from('Schedule');
            $this->db->where("DelID", 0);
            $this->db->where("Type", 1);
            $this->db->where("sDate <= '" . $_BaseDay . "' AND eDate >= '" . $_BaseDay . "' ");
            $this->db->group_by('RegID');
            $subTopQuery = $this->db->get_compiled_select();
            $this->db->reset_query();

            // Sub Query
            $this->db->select('userId')->from('Report')->where("RegDatetime > DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -" . $_termday . " DAY),'%Y-%m-%d 00:00:00')")->group_by('userId');
            $subQuery = $this->db->get_compiled_select();

            $this->db->reset_query();
            if (gettype($_select) == 'array') {
                // 가지고 올 필드명을 지정 했을 경우
                foreach ((array)$_select as $key => $val) {
                    $this->db->select($val);
                }
            } else {
                $this->db->select('*');
            }
            if (is_array($search) && count($search)) $this->setWhere($search);
            $this->db->from('tbl_users as M');
            $this->db->join('(' . $subTopQuery . ") as T", " M.userId = T.RegID ", 'LEFT');
            $this->db->join('ADMIN_MEMBER as A', " M.hackersid = A.USER_ID  ", 'INNER');
            $this->db->join('ADMIN_MEMBER_GROUP as G', " A.GROUP_IDX = G.IDX  ", 'LEFT');
            $this->db->join('ADMIN_MEMBER_CLASS as C', " A.CLASS_IDX = C.IDX  ", 'LEFT');
            $this->db->join('ADMIN_VARS as V', " A.HR_VAR_9 = V.IDX  ", 'LEFT');
            $this->db->where("M.userId NOT IN ($subQuery)", NULL, FALSE);
            $this->db->order_by('G.IDX', 'ASC');
            $this->db->order_by('(CASE WHEN A.HR_VAR_9 = 0 THEN 1000 ELSE A.HR_VAR_9 END)', 'ASC');
            $this->db->order_by('M.name', 'DESC');
            $query = $this->db->get();
            $result = $query->result_array();

        }

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

}

