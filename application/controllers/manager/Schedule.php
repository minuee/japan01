<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Board (BoardController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Schedule extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('schedule_model');
        $this->load->helper("html");

        if( !empty($_REQUEST['exemode']) ){
			if( $_REQUEST['exemode'] == "view"){
			}else{
				$this->isLoggedIn();
			}
		}else{
			$this->isLoggedIn();
		}
	}

    /**
     * This function used to load the first screen of the user
     */
    function index()
    {

		if( !empty($_REQUEST['exemode']) ){

			$this->loadViewsSimple("manager/schedule/".$_REQUEST['exemode'], NULL, $data, NULL);
		}else{

			if( $this->session->userdata('userId') == null)
			{
				$this->loadThis();
			}

			// 각종코드
			$this->config->load('config', true);
			$_CommonCode = $this->config->item('code');
			if ( $this->session->userdata('role') == ROLE_ADMIN ) {
				$_GROUPCode = $this->getGlobalGroupCodeAll();
				$data['BUSINESSCode'] = $_CommonCode['BUSINESS'];
			}else{
				$_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'),$this->session->userdata('groupidx'),$this->session->userdata('parentgroup')  );
			}

			$data['GROUPCode'] = $_GROUPCode['Group'];
			$this->global['pageTitle'] = 'Hackers Project Schedule';
			$data['CommonCode'] = $_CommonCode;


			$_usersearch['where'] = array();
			$_usersearch['where'][] = "M.isDeleted = 0 ";
			$_usersearch['where'][] = "M.roleId in ( 3,9) ";
			$_usersearch['where'][] = "M.userId != ".$this->session->userdata('userId')." ";
			$_usersearch['where'][] = "G.IDX = ".$this->session->userdata('groupidx')." ";
			$_userselect = array(
				"M.*","IFNULL(G.NAME,'무소속') AS GROUP_NAME","A.GROUP_IDX"
			);
			$data['Users'] = $this->schedule_model->getUserListing($_usersearch, $_userselect);


			$data['projectTitle'] = "일정관리";
			$data['MyName'] = $this->session->userdata('name');
			$data['MyUserID'] = $this->session->userdata('userId');
			//$data['ResultData'] = json_encode($_return_data);
			$data['LoginSession'] = $this->session->userdata();

			$this->loadViews("manager/schedule/index", $this->global, $data, NULL);
		}
	}

    function get_events(){

        $PostData = $this->input->get();
    // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        //print_r($PostData);
        $_search['where'] = array();
        $_search['where'][] = "DelID = 0";
        $_search['where'][] = "(( sDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( eDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        if ( $this->session->userdata('role') == ROLE_MANAGER  || $this->session->userdata('role') == ROLE_EMPLOYEE ) {
            $_search['where'][] = "A.GroupCode = '".$this->session->userdata('groupcode')."' ";
            //$_search['where'][] = "(  A.Type in ( 10,20,30 ) ||  ( A.Type in ( 1,2,3,4,40 ) && A.RegGroup = '".$this->session->userdata('groupidx')."' ) || ( A.Type = 50 &&  A.RegID = '".$this->session->userdata('userId')."'  ) )";
            $_search['where'][] = "(  A.Type in ( 1,2,3,4,10,20,30,40 ) || ( A.Type = 50 &&  A.RegID = '".$this->session->userdata('userId')."'  ) )";

        }else if (  $this->session->userdata('role') == ROLE_SUPERVISOR ) {
            $_search['where'][] = "A.Type != 50";
            if ( $this->session->userdata('groupdepth') == 2 ) {
                $_search['where'][] = " ( A.RegGroup = '".$this->session->userdata('parentgroup')."' OR G.IDX = '".$this->session->userdata('parentgroup')."' OR G.PARENT = '".$this->session->userdata('parentgroup')."' ) ";
            }else{
                $_search['where'][] = "G.PARENT = '".$this->session->userdata('parentgroup')."' ";
            }
        }else if (  $this->session->userdata('role') == ROLE_ADMIN &&   $this->session->userdata('hackersidx') > 0  ) {
            $_search['where'][] = "A.Type != 50";
        }

        $_select = array(
            "A.ScheduleIdx","A.sDate","A.eDate","A.Type","A.RegGroup","A.RegID","A.SubTitle","A.Color","A.Comment","M.name as UserName","A.GroupCode"
        );
        $data['Records'] = $this->schedule_model->getMyScheduleListing($_search, $_select);

        $_return_data =  array();
        $keyrow = 0;
        foreach($data['Records'] as $key => $val) {
            $_return_data[$key]['id'] = $val['ScheduleIdx'];
            $_return_data[$key]['team'] = $val['RegGroup'];
            $_return_data[$key]['start'] = $val['sDate'];

            if ( $val['sDate'] != $val['eDate'] ) {
                $_return_data[$key]['textend'] = $val['eDate'] ;
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$key]['end'] = $eDate;
            }else{
                $_return_data[$key]['textend'] = $val['eDate'];
                $_return_data[$key]['end'] = $val['eDate'];
            }
            $_return_data[$key]['allDay'] = true;
            $_return_data[$key]['group'] = $val['GroupCode'];
            $_return_data[$key]['todouser'] = $val['UserName'];
            $_return_data[$key]['userid'] = $val['RegID'];
            $_return_data[$key]['borderColor'] = $val['Color'];
            $_return_data[$key]['backgroundColor'] = $val['Color'];
            $_return_data[$key]['type'] = $val['Type'];
            if ( $val['Type'] == 30 || $val['Type'] == 40 ) {
                $_return_data[$key]['title'] = $val['SubTitle'];
                $_return_data[$key]['Comment'] = $val['SubTitle']."<br />Comment :".strip_tags($val['Comment']);
            }else{
                $_return_data[$key]['title'] = "(".$val['UserName'].")".$val['SubTitle'];
                $_return_data[$key]['Comment'] = "(".$val['UserName'].")".$val['SubTitle']."<br />Comment :".strip_tags($val['Comment']);
            }
            $keyrow = $key+1;
        }

        $_search2['where'] = array();
        $_search2['where'][] = "(  	( wDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( wDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        $_select2 = array(
            "A.*"
        );
        $data['HolidayRecords'] = $this->schedule_model->getHolidayListing($_search2,$_select2);
        $keyrow2 = $keyrow > 0 ? $keyrow : $keyrow;
        foreach($data['HolidayRecords'] as $key2 => $val2) {
            $_return_data[$keyrow2]['id'] = "Holy".$val2['HolidayIdx'];
            $_return_data[$keyrow2]['group'] = null;
            $_return_data[$keyrow2]['start'] = $val2['wDate'];
            $_return_data[$keyrow2]['end'] = $val2['wDate'];
            $_return_data[$keyrow2]['textend'] = $val2['wDate'] ;
            $_return_data[$keyrow2]['allDay'] = true;
            $_return_data[$keyrow2]['todouser'] = null;
            $_return_data[$keyrow2]['userid'] = 1;
            $_return_data[$keyrow2]['borderColor'] = HOLIDAY_COLOR;
            $_return_data[$keyrow2]['backgroundColor'] = HOLIDAY_COLOR;
            $_return_data[$keyrow2]['title'] = $val2['Title'];
            $_return_data[$keyrow2]['Comment'] = $val2['Title'];
            $_return_data[$keyrow2]['type'] = 99;
            $keyrow2++;
        }

        echo json_encode(array("events" => $_return_data));
        exit();


    }

   function get_events_view(){

        $PostData = $this->input->get();
    // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        //print_r($PostData);
        $_search['where'] = array();
        $_search['where'][] = "DelID = 0";
        $_search['where'][] = "(( sDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( eDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
		$_search['where'][] = "A.Type != 50";
		$_search['where'][] = "(G.PARENT = '88' OR G.IDX IN(88,421,509,335,15,73,99,424,433,578,74,455,355) )";

        $_select = array(
            "A.ScheduleIdx","A.sDate","A.eDate","A.Type","A.RegGroup","A.RegID","A.SubTitle","A.Color","A.Comment","M.name as UserName","A.GroupCode"
        );
        $data['Records'] = $this->schedule_model->getMyScheduleListing($_search, $_select);

        $_return_data =  array();
        $keyrow = 0;
        foreach($data['Records'] as $key => $val) {
            $_return_data[$key]['id'] = $val['ScheduleIdx'];
            $_return_data[$key]['team'] = $val['RegGroup'];
            $_return_data[$key]['start'] = $val['sDate'];

            if ( $val['sDate'] != $val['eDate'] ) {
                $_return_data[$key]['textend'] = $val['eDate'] ;
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$key]['end'] = $eDate;
            }else{
                $_return_data[$key]['textend'] = $val['eDate'];
                $_return_data[$key]['end'] = $val['eDate'];
            }
            $_return_data[$key]['allDay'] = true;
            $_return_data[$key]['group'] = $val['GroupCode'];
            $_return_data[$key]['todouser'] = $val['UserName'];
            $_return_data[$key]['userid'] = $val['RegID'];
            $_return_data[$key]['borderColor'] = $val['Color'];
            $_return_data[$key]['backgroundColor'] = $val['Color'];
            $_return_data[$key]['type'] = $val['Type'];
            if ( $val['Type'] == 30 || $val['Type'] == 40 ) {
                $_return_data[$key]['title'] = $val['SubTitle'];
                $_return_data[$key]['Comment'] = $val['SubTitle']."<br />Comment :".strip_tags($val['Comment']);
            }else{
                $_return_data[$key]['title'] = "(".$val['UserName'].")".$val['SubTitle'];
                $_return_data[$key]['Comment'] = "(".$val['UserName'].")".$val['SubTitle']."<br />Comment :".strip_tags($val['Comment']);
            }
            $keyrow = $key+1;
        }

        $_search2['where'] = array();
        $_search2['where'][] = "(  	( wDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( wDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        $_select2 = array(
            "A.*"
        );
        $data['HolidayRecords'] = $this->schedule_model->getHolidayListing($_search2,$_select2);
        $keyrow2 = $keyrow > 0 ? $keyrow : $keyrow;
        foreach($data['HolidayRecords'] as $key2 => $val2) {
            $_return_data[$keyrow2]['id'] = "Holy".$val2['HolidayIdx'];
            $_return_data[$keyrow2]['group'] = null;
            $_return_data[$keyrow2]['start'] = $val2['wDate'];
            $_return_data[$keyrow2]['end'] = $val2['wDate'];
            $_return_data[$keyrow2]['textend'] = $val2['wDate'] ;
            $_return_data[$keyrow2]['allDay'] = true;
            $_return_data[$keyrow2]['todouser'] = null;
            $_return_data[$keyrow2]['userid'] = 1;
            $_return_data[$keyrow2]['borderColor'] = HOLIDAY_COLOR;
            $_return_data[$keyrow2]['backgroundColor'] = HOLIDAY_COLOR;
            $_return_data[$keyrow2]['title'] = $val2['Title'];
            $_return_data[$keyrow2]['Comment'] = $val2['Title'];
            $_return_data[$keyrow2]['type'] = 99;
            $keyrow2++;
        }

        echo json_encode(array("events" => $_return_data));
        exit();


    }

    function popdetail( $idx = null){

        $data['ScheduleIdx'] = $idx;
        $data['LoggedInfo'] = $this->session->userdata();
        $_select = array(
            "A.*","M.name as UserName","G.NAME AS GROUPNAME","M2.name as AgentName"
        );
        $dbresult = $this->schedule_model->getDetailInfo($_select,$idx);
        if ( $dbresult['result'] === false ) {
            alert_back($dbresult['message']);
        }

        $data['ScheduleData'] = $dbresult['row'][0];
        $data['LoginSession'] = $this->session->userdata();

        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;

        return $this->load->view("manager/schedule/popdetail",$data);

    }


    function insert(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('Type','휴무구분','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $LoggedGroup = $this->session->userdata('groupidx');
            $dbresult = $this->schedule_model->insertdata($LoggedID,$LoggedGroup,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }

    function commentinsert(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ScheduleIdx','인덱스번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{

            $dbresult = $this->schedule_model->insertcomment($PostData);
            echo json_encode($dbresult);
            exit;

        }

    }


    function update(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ScheduleIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $dbresult = $this->schedule_model->update($PostData);
            echo json_encode($dbresult);
            exit;

        }
    }

    function infoupdate(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ScheduleIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $dbresult = $this->schedule_model->infoupdate($PostData);
            echo json_encode($dbresult);
            exit;

        }
    }


    function delete(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ScheduleIdx','인덱스번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $dbresult = $this->schedule_model->removeschedule($PostData['ScheduleIdx'],$LoggedID);
            echo json_encode($dbresult);
            exit;

        }

    }

}

?>
