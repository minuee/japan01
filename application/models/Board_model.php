<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Board_model (Board Model)
 * User model class to get to handle user related data 
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */

require APPPATH . '/libraries/BaseModel.php';

class Board_model extends BaseModel
{
    function ListingCount($search = array())
    {
        $this->db->select('*');

        $this->db->from('testnoh.Board AS A');
        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $query = $this->db->get();
        //echo nl2br($this->db->last_query()); echo "<br />";exit;
        return $query->num_rows();
    }
    

    function Listing($search = array(), $page, $segment ,$_select = array())
    {


        if ( gettype($_select) == 'array' ) {
            foreach( (array)$_select as $key=>$val ) {
                $this->db->select($val);
            }
        } else {
            $this->db->select('*');
        }

        $this->db->from('testnoh.Board AS A');
        if ( is_array($search) && count($search) ) $this->setWhere($search);

        $this->db->order_by('A.RegDatetime', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        //echo nl2br($this->db->last_query());exit;
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

    function insertdata($data = array() ) {

        if(empty($data)){
            // update할 target이 없을시 리턴
            $_return["result"] = false;
            $message = "업데이트 대상이 없습니다.\n다시 확인 해 주세요.";
            $_return["message"] = $message;
            return $_return;
        }

        $_return["result"] = false;
        $message = "게시판 등록에 실패하였습니다.";
        $_return["message"] = $message;

        $this->db->trans_begin(); // 트랜젝션 시작

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $_return;
            exit;
        }

        if ( isset($data['Data']) && gettype($data['Data'])=='array' ) {

            if ( isset($data['Data']['IsTemp']) === false || $data['Data']['IsTemp'] == '') $_IsTemp = 0;
            else $_IsTemp = 1;

            $this->db->set('BoardName',$data['Data']['BoardName']);
            $this->db->set('Title',$data['Data']['Title']);
            $this->db->set('HTMLContent',$data['Data']['HTMLContent']);
            $this->db->set('Content',$data['Data']['Content']);
            $this->db->set('Permission',$data['Data']['Permission']);
            $this->db->set('TargetPlant',$data['Data']['TargetPlant']);
            $this->db->set('TargetUser',$data['Data']['TargetUser']);
            $this->db->set('TargetInnerUser',$data['Data']['TargetInnerUser']);
            $this->db->set('IsTemp',$this->str2bit($_IsTemp));
            $this->db->set('AdminID',$this->data['session']['auth']['UserID']);
            $this->db->set('AdminName',$this->data['session']['auth']['UserName']);
            $this->db->set('RegDatetime','now()',false);
            $this->db->set('ModifyDatetime','now()',false);
            //$insertData = $this->str2bit($this->setEmpty($this->xssClean($data['Data'])));
            $this->db->insert( 'Board' );
            $insert_id = $this->db->insert_id();

            $_return["result"] = true;
            $_return["idx"] = $insert_id;
            $message = "코드 등록에 성공하였습니다.";
            $_return["message"] = $message;

            if ( $_return['result'] === false || empty($insert_id) ) {
                $this->db->trans_rollback();
                $message = "데이터베이스 등록중 실패하였습니다.";
                $_return["message"] = $message;
                return $_return;
                exit;
            }

        }
        $this->db->trans_commit();
        return $_return;

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

  