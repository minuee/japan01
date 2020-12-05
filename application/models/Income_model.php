<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Income_model (Income Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Income_model extends CI_Model
{
    function userListingCount($search = array())
    {
        $this->db->select('*');

        $this->db->from('testnoh.OrderingData AS A');
        $this->db->join('testnoh.Company AS C', 'A.CompanyCode = C.CompanyCode','LEFT');
        if(isset($search['searchText']) && !empty($search['searchText'])) {
            $likeCriteria = "A.Buyer  LIKE '%".$search['searchText']."%'";
            $this->db->where($likeCriteria);
        }
        if(isset($search['PublishCode'])  && !empty($search['PublishCode']) ) {
            $this->db->where('A.PublishCorporationCCD', $search['PublishCode']);
        }
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        return $query->num_rows();
    }
    
    function userListing($search = array(), $page, $segment ,$_select = array())
    {


        if ( gettype($_select) == 'array' ) {
            // 가지고 올 필드명을 지정 했을 경우
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('testnoh.OrderingData AS A');
        $this->db->join('testnoh.Company AS C', 'A.CompanyCode = C.CompanyCode','LEFT');
        $this->db->join('testnoh.Code AS D',"A.PublishCorporationCCD = D.Code AND D.CategoryCode = 'SAP_VKORG' ",'LEFT');

        if(isset($search['searchText']) && !empty($search['searchText'])) {
            $likeCriteria = "A.Buyer  LIKE '%".$search['searchText']."%'";
            $this->db->where($likeCriteria);
        }
        if(isset($search['PublishCode'])  && !empty($search['PublishCode']) ) {
            $this->db->where('A.PublishCorporationCCD', $search['PublishCode']);
        }

        $this->db->order_by('A.RegDatetime', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());
        $result = $query->result_array();
        return $result;
    }

    function getOrderingDataInfo($_select = array(),$_idx = null) {

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
        $this->db->from('testnoh.OrderingData AS A');
        $this->db->join('testnoh.Company AS C', 'A.CompanyCode = C.CompanyCode','LEFT');
        $this->db->join('testnoh.Code AS D',"A.PublishCorporationCCD = D.Code AND D.CategoryCode = 'SAP_VKORG' ",'LEFT');
        $this->db->where('A.OrderingDataIdx', $_idx);
        $this->db->limit(1);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->row_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }

    function updatedata($_postdata = array() ) {

        $this->db->where('OrderingDataIdx', $_postdata['OrderingDataIdx']);
        $this->db->set('PublishCorporationCCD', $_postdata['PublishCorporationCCD']);
        $this->db->update('testnoh.OrderingData');

        $error = $this->db->error();
        $result['result'] = empty($error['code']) ? true : false ;

        return $result;
    }

}

  