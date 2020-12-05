<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Condition_model (Condition Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Condition_model extends CI_Model
{
    function conditionListingCount($search = array())
    {
        $this->db->select('A.*, C.CompanyName');
        $this->db->from('testnoh.SAP_CONDITION_MASTER1 AS A');
        $this->db->join('testnoh.Company AS C', 'A.KUNNR = C.CompanyCode','LEFT');
        if(isset($search['searchText']) && !empty($search['searchText'])) {
            $likeCriteria = "C.CompanyName  LIKE '%".$search['searchText']."%'";
            $this->db->where($likeCriteria);
        }
        if(isset($search['PublishCode'])  && !empty($search['PublishCode']) ) {
            $this->db->where('A.VKORG', $search['PublishCode']);
        }

        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        return $query->num_rows();
    }
    
    function conditionListing($search = array(), $page, $segment)
    {

        $this->db->select('A.*, C.CompanyName');
        $this->db->select("CASE WHEN A.PLTYP = 10 THEN '현매' ELSE '외상' END AS strPLTYP ");
        $this->db->from('testnoh.SAP_CONDITION_MASTER1 AS A');
        $this->db->join('testnoh.Company AS C', 'A.KUNNR = C.CompanyCode','LEFT');

        if(isset($search['searchText']) && !empty($search['searchText'])) {
            $likeCriteria = "C.CompanyName  LIKE '%".$search['searchText']."%'";
            $this->db->where($likeCriteria);
        }
        if(isset($search['PublishCode'])  && !empty($search['PublishCode']) ) {
            $this->db->where('A.VKORG', $search['PublishCode']);
        }

        $this->db->order_by('A.RegDatetime', 'DESC');
        $this->db->order_by('A.ERDAT', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());
        $result = $query->result();        
        return $result;
    }



}

  