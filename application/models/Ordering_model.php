<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Ordering_model (Ordering Model)
 * User model class to get to handle user related data
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */

require APPPATH . '/libraries/BaseModel.php';
class Ordering_model extends BaseModel
{
    function ListingCount($search = array())
    {

        // Sub Query
        $this->db->select('A1.PublishOrderingNo,SUM(A1.Qty) as RequestQty,MAX(A1.BookCode) as BookCode,MAX(A1.PrintNo) as PrintNo,COUNT(*) AS BulkCount');
        $this->db->select('MAX(A1.SAPOrderNo) as SAPOrderNo');
        $this->db->select('MAX(CASE WHEN B1.`BookStatus` =  5 THEN 1 ELSE 0 END )  AS BookStatus');
        $this->db->from('testnoh.OrderingProduct AS A1');
        $this->db->join('testnoh.Book B1'," A1.BookCode = B1.BookCode AND  A1.PrintNo = B1.PrintNo  ",'LEFT');
        $this->db->group_by('A1.PublishOrderingNo');
        $subQuery =  $this->db->get_compiled_select();

        $this->db->reset_query();
        $this->db->select('*');
        $this->db->from('testnoh.OrderingPayment AS A');
        $this->db->join("testnoh.Ordering AS B","A.OrderingIdx =  B.OrderingIdx ", "LEFT");
        $this->db->join('('.$subQuery.") as C"," A.PublishOrderingNo = C.PublishOrderingNo ",'LEFT');
        $this->db->join('testnoh.Book as D'," C.BookCode = D.BookCode AND  C.PrintNo = D.PrintNo  ",'LEFT');
        $this->db->join('testnoh.Code as F'," A.OrderingStatus = F.Code AND  F.CategoryCode = 'OrderingStatusCCD' ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        return $query->num_rows();
    }
    
    function Listing($search = array(), $page, $segment ,$_select = array())
    {

        // Sub Query
        $this->db->select('A1.PublishOrderingNo,SUM(A1.Qty) as RequestQty,MAX(A1.BookCode) as BookCode,MAX(A1.PrintNo) as PrintNo,COUNT(*) AS BulkCount');
        $this->db->select('MAX(A1.SAPOrderNo) as SAPOrderNo');
        $this->db->select('MAX(CASE WHEN B1.`BookStatus` =  5 THEN 1 ELSE 0 END )  AS BookStatus');
        $this->db->from('testnoh.OrderingProduct AS A1');
        $this->db->join('testnoh.Book B1'," A1.BookCode = B1.BookCode AND  A1.PrintNo = B1.PrintNo  ",'LEFT');
        $this->db->group_by('A1.PublishOrderingNo');
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

        $this->db->from('testnoh.OrderingPayment AS A');
        $this->db->join("testnoh.Ordering AS B","A.OrderingIdx =  B.OrderingIdx ", "LEFT");
        $this->db->join('('.$subQuery.") as C"," A.PublishOrderingNo = C.PublishOrderingNo ",'LEFT');
        $this->db->join('testnoh.Book as D'," C.BookCode = D.BookCode AND  C.PrintNo = D.PrintNo  ",'LEFT');
        $this->db->join('testnoh.Code as F'," A.OrderingStatus = F.Code AND  F.CategoryCode = 'OrderingStatusCCD' ",'LEFT');

        if ( is_array($search) && count($search) ) $this->setWhere($search);

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
        $this->db->from('testnoh.OrderingPayment AS A');
        $this->db->join('testnoh.Ordering AS B', 'A.OrderingIdx = B.OrderingIdx','INNER');
        $this->db->join('testnoh.Company AS C', 'B.UserIdx = C.UserIdx','LEFT');
        $this->db->join('testnoh.Code AS D',"A.PublishCorporationCCD = D.Code AND D.CategoryCode = 'SAP_VKORG' ",'LEFT');
        $this->db->join("testnoh.Code as D2","A.OrderingStatus =  D2.Code AND D2.CategoryCode = 'OrderingStatusCCD' ", "LEFT");
        $this->db->join('testnoh.OrderingDelivery AS F', 'B.OrderingIdx = F.OrderingIdx','INNER');
        $this->db->where('A.PublishOrderingNo', $_idx);
        $this->db->limit(1);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->row_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }

    function getOrderingProductInfo($_select = array(),$_idx = null) {

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
        $this->db->from('testnoh.OrderingPayment AS A');
        $this->db->join('testnoh.OrderingProduct AS F', 'A.PublishOrderingNo = F.PublishOrderingNo','INNER');
        $this->db->join('testnoh.Book AS C', 'F.SAPCode = C.SAPCode','LEFT');
        $this->db->join('testnoh.Code AS Code'," Code.Code = C.MaterialGroup AND  Code.CategoryCode ='SAP_MATKL' ",'LEFT');
        $this->db->where('A.PublishOrderingNo', $_idx);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";
        $return['sql'] = $this->db->last_query();
        $return['row']  = $query->result_array();
        $return['err']  = $this->db->error();
        $return['result'] = (empty($return['error']['code'])) ? true : false;
        return $return;

    }

}

  