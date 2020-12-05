<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Code_model (Login Model)
 * Login model class to get to authenticate user credentials
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Code_model extends CI_Model
{

    public function codeInfo($_categoryCode=array(),$_where,$_code)
    {
        $return = false;

        if ( gettype($_categoryCode) == 'array' ) {
            foreach( (array)$_categoryCode as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        if(!empty($where)) $this->db->where($_where);
        $this->db->where('Code', $_code);

        $this->db->from('testnoh.Code');

        $query = $this->db->get();

        $return['res'] = $query;
        $return['cnt'] = $query->num_rows();
        $return['row'] = $query->row_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }

    public function codeList( $_select=array() , $_where=array() , $_orderBy=false )
    {
        $return = false;

        // 전체 tcnt를 구한다.
        $this->db->where($_where);
        $this->db->from('testnoh.Code');
        $return['tcnt'] = $this->db->count_all_results();


        // 조건에 의한 SQL 시작
        $this->db->select('@ROWNUM := @ROWNUM + 1 AS ROWNUM',false);
        if ( is_array($_select) ) {
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('testnoh.Code.*');
        }

        if ( $_orderBy !== false ) {
            foreach( (array)$_orderBy as $field=>$sort ) {
                $this->db->order_by($field,$sort);
            }
        }

        $this->db->from('testnoh.Code, (SELECT @ROWNUM := 0) R');
        $this->db->where($_where);

        $query = $this->db->get();

        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }



    public function getMyJobs($search=null ,$_select=array(),$_UserId = null  ){

        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }
        $_Today = date('Y-m-d');
        $this->db->from('ProjectWork AS A');
        $this->db->join('Project as B'," A.ProjectIdx = B.ProjectIdx  ",'INNER');
        $this->db->where($search);
        $this->db->where("A.IsDel IS NULL");
        $this->db->where("A.ToDoID", $_UserId);
        $this->db->where("(A.sDate <= '".$_Today."' || A.eDate = '".$_Today."' )");
        $this->db->group_by("A.ToDoID ");
        //$this->db->group_by("CASE WHEN A.ToDoID = '' &&  A.RegID = '".$_UserId."' THEN A.RegID ELSE A.ToDoID  END", false);
        $query = $this->db->get();

        //echo nl2br($this->db->last_query()); echo "<br />";exit;
        $result = $query->result_array();
        return $result;
    }

    public function getMyTeamViewPermissions($_UserId = null  ){

        $this->db->select('*');
        $this->db->from('TeamView');
        $this->db->where('UserID', $_UserId);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }


    public function getgroupList( $_select=array() , $_where=array() , $_orderBy=false )
    {
        $return = false;
        $this->db->where($_where);
        $this->db->from('ADMIN_MEMBER_GROUP');
        $return['tcnt'] = $this->db->count_all_results();

        $this->db->select('@ROWNUM := @ROWNUM + 1 AS ROWNUM',false);
        if ( is_array($_select) ) {
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('ADMIN_MEMBER_GROUP.*');
        }

        if ( $_orderBy !== false ) {
            foreach( (array)$_orderBy as $field=>$sort ) {
                $this->db->order_by($field,$sort);
            }
        }

        $this->db->from('ADMIN_MEMBER_GROUP, (SELECT @ROWNUM := 0) R');
        $this->db->where($_where);

        $query = $this->db->get();
        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }

    public function getgroupList2( $_select=array() , $_where=array() , $_orderBy=false )
    {

        $return = false;
        $this->db->where_in("IDX",$_where);
        $this->db->from('ADMIN_MEMBER_GROUP');
        $return['tcnt'] = $this->db->count_all_results();

        $this->db->select('@ROWNUM := @ROWNUM + 1 AS ROWNUM',false);
        if ( is_array($_select) ) {
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('ADMIN_MEMBER_GROUP.*');
        }

        if ( $_orderBy !== false ) {
            foreach( (array)$_orderBy as $field=>$sort ) {
                $this->db->order_by($field,$sort);
            }
        }

        $this->db->from('ADMIN_MEMBER_GROUP, (SELECT @ROWNUM := 0) R');
        $this->db->where_in("IDX",$_where);

        $query = $this->db->get();
        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }

    public function getgroupAllList( $_select=array() , $_where=array() , $_orderBy=false )
    {
        $return = false;

        // 전체 tcnt를 구한다.
        $this->db->where($_where);
        $this->db->from('ADMIN_MEMBER_GROUP');
        $return['tcnt'] = $this->db->count_all_results();

        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //echo nl2br($this->db->last_query());
        };


        // 조건에 의한 SQL 시작
        $this->db->select('@ROWNUM := @ROWNUM + 1 AS ROWNUM',false);
        if ( is_array($_select) ) {
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('ADMIN_MEMBER_GROUP.*');
        }

        if ( $_orderBy !== false ) {
            foreach( (array)$_orderBy as $field=>$sort ) {
                $this->db->order_by($field,$sort);
            }
        }

        $this->db->from('ADMIN_MEMBER_GROUP, (SELECT @ROWNUM := 0) R');
        $this->db->where($_where);

        $query = $this->db->get();

        $return['res'] = $query;
        $return['scnt'] = $query->num_rows();
        $return['row'] = $query->result_array();
        $return['sql'] = $this->db->last_query();
        $return['err'] = $this->db->error();

        return $return;
    }


    function getgroupList_old($_select = array(),$search = array()) {

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
        $this->db->from('ADMIN_MEMBER_GROUP AS G');

        if ( is_array($search) && count($search) ) $this->setWhere($search);


        $this->db->order_by('G.NAME','ASC');
        $query = $this->db->get();
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }



}

  