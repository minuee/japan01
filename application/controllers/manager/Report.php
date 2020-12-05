<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Board (BoardController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Report extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('project_model');
        $this->load->helper("html");
        $this->isLoggedIn();
    }


    /**
     * This function used to load the first screen of the user
     */
    function index( $_DirectID = null )
    {

        $this->isLoggedIn();
        //$searchText = $this->security->xss_clean($this->input->post('searchText'));
        $this->global['pageTitle'] = '업무리포트';
        //조회용 코드

        if ( $this->session->userdata('role') == ROLE_ADMIN ) {
            $_GROUPCode = $this->getGlobalGroupCodeAll();
            // 각종코드
            $this->config->load('config', true);
            $_CommonCode = $this->config->item('code');

            $data['BUSINESSCode'] = $_CommonCode['BUSINESS'];

        }else{
            $_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'),$this->session->userdata('groupidx'),$this->session->userdata('parentgroup')  );
        }
        $data['GROUPCode'] = $_GROUPCode['Group'];

        $data["search"] = $this->input->post();
        $time = time();
        $data["search"]['search_end_date'] =  date("Y-m-d");
        $data["search"]['search_start_date'] =  date("Y-m-d",strtotime("-7 day", $time));
        $data['LoggedInfo'] = $this->session->userdata();
        $this->loadViews("manager/report/index", $this->global, $data, NULL);

    }


    function ajax_list(){


        $_request = $this->security->xss_clean($this->input->get());

        $_search['where'] = array();
        if ( $this->session->userdata('role') == ROLE_EMPLOYEE || $this->session->userdata('role') == ROLE_MANAGER ){
            $_search['where'][] = "A.userGroup = '".$this->session->userdata('groupidx')."' ";
        }else if (  $this->session->userdata('role') == ROLE_SUPERVISOR ) {
            $_search['where'][] = "G.PARENT = '".$this->session->userdata('parentgroup')."' ";
        }

        if(isset($_request['ProjectGroup'])  && !empty($_request['ProjectGroup']) ) {
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            $_dtmparray_code = $_CommonCode[$_request['ProjectGroup']];
            $_darray_code = "";
            foreach($_dtmparray_code as  $key => $val ){
                $_darray_code .= $val.",";
            }
            $_darray_code2 = substr($_darray_code,0,-1) ;
            $_search['where'][] = "A.userGroup IN ( ".$_darray_code2." ) ";
        }

        if(isset($_request['ProjectTeam'])  && !empty($_request['ProjectTeam']) ) {
            $_search['where'][] = "A.userGroup = '".$_request['ProjectTeam']."' ";
        }
        if ( $_request['search_start_date'] && $_request['search_end_date'] ) {
            $_search['where'][] = "A.wDate >= '".$_request['search_start_date']."' ";
            $_search['where'][] = "A.wDate <= '".$_request['search_end_date']."' ";
        }

        if(isset($_request['searchText']) && !empty($_request['searchText']) ) {
            $_search['where'][] = "M.name  LIKE '%".$_request['searchText']."%'";
        }



        $this->load->library('pagination');
        $count = $this->project_model->ReportListingCount($_search);

        isset($_request['list_table_length']) ? $list_table_length = $_request['list_table_length']: $list_table_length = 10;
        $returns = $this->paginationCompress( "manager/report/index", $count, $list_table_length );

        $returns['segment'] = $_request["paging"] > 0?$_request["paging"]:null;

        $_select = array(
            "A.userId","MAX(A.wDate) as wDate","MAX(M.name) as UserName","MAX(G.NAME) as GroupName","MAX(A.ReportIdx) as ReportIdx","A.reportGroup"
        );
        $data['userRecords'] = $this->project_model->ReportListing($_search, $returns["page"], $returns["segment"] ,$_select);

        $data['totalRecords'] = $count;
        $data['now_page'] = is_numeric($returns["segment"])?$returns["segment"]:0;

        $data["search"] = $this->input->get();
        echo json_encode([
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $data['userRecords']
        ]);
        exit;
    }

    function view( $_idx = null)
    {

        if ( $_idx === null ) {
            $this->loadThis();
            return false;
        }


        $this->config->load('config');
        $data = array();
        $this->global['pageTitle'] = '업무리포트';


        //작업현황 가져온다
        $_ReportArray = array();
        $_select = array(
            "A.RegDatetime","A.wDate","A.ProjectMode", "A.ProjectIdx", "A.ProjectWorkIdx","A.reportData","M.name as UserMame","A.userId","P.IntraBoardIdx","P.IntraUrl","A.reportGroup","Sub2.SUMDoingTime"     );
        $_MyReport = $this->project_model->getReportDetail($_select, $_idx);


        if (count($_MyReport) > 0) {
            foreach ($_MyReport as $key => $val) {

                $_Detail = json_decode($val['reportData']);
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['ProjectMode'] = $val['ProjectMode'];
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['GROUPNAME'] = $_Detail->GROUPNAME;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['ProjectTitle'] = $_Detail->ProjectTitle;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['SUMDoingTime'] = $val['SUMDoingTime'];

                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['Status'] = $_Detail->Status;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['Worktitle'] = $_Detail->Worktitle;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['sDate'] = $_Detail->sDate;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['eDate'] = $_Detail->eDate;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['sTime'] = $_Detail->sTime;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['eTime'] = $_Detail->eTime;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['Rate'] = $_Detail->Rate;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['worktime'] = $_Detail->worktime>0?round($_Detail->worktime/60,1):0; //시간
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['Foretime'] = isset($_Detail->Foretime)?round($_Detail->Foretime/60,1):0; //시간
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['Comment'] = $_Detail->LastComment;
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['ProjectWorkIdx'] = $val['ProjectWorkIdx'];
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['UserMame'] = $val['UserMame'];
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['IntraBoardIdx'] = $val['IntraBoardIdx'];
                $_ReportArray[$val['ProjectMode']][$val['ProjectIdx']][$key]['IntraUrl'] = $val['IntraUrl'];

                $SelectRegDatetime = $val['RegDatetime'];
                $SelectDate = $val['wDate'];
                $SelectreportGroup = $val['reportGroup'];
                $ReprotName = $val['UserMame'];
                $SelectUserId = $val['userId'];
            }
        }else{
            $this->loadThis();
            return false;
        }

        $data['SelectDate'] = $SelectDate;

        /*if ( $this->session->userdata('role') == ROLE_EMPLOYEE ) {
            $data['IsForeData'] = $this->project_model->getForeData($SelectDate, $this->session->userdata('userId'),null);
            $data['IsNextData'] = $this->project_model->getNextData($SelectDate, $this->session->userdata('userId'),null);
            }else if ( $this->session->userdata('role') == ROLE_MANAGER ) {
                   $data['IsForeData'] = $this->project_model->getForeData($SelectDate, null, $this->session->userdata('groupidx'));
                   $data['IsNextData'] = $this->project_model->getNextData($SelectDate, null, $this->session->userdata('groupidx'));
        }else{
            $data['IsForeData'] = $this->project_model->getForeData($SelectDate ,$SelectUserId,null);
            $data['IsNextData'] = $this->project_model->getNextData($SelectDate ,$SelectUserId,null);
        }*/

        $data['IsForeData'] = $this->project_model->getForeData($SelectDate ,$SelectUserId,null,$SelectreportGroup,$SelectRegDatetime);
        $data['IsNextData'] = $this->project_model->getNextData($SelectDate ,$SelectUserId,null,$SelectreportGroup,$SelectRegDatetime);

        //echo $data['IsForeData']->ReportIdx;
        //print_r($data['IsNextData']);

        $data['ReprotName'] = $ReprotName;
        $data['MyReport'] = $_ReportArray;
        $data['reportIdx'] = $_idx;
        $this->loadViews("manager/report/view", $this->global, $data, NULL);

    }


    public function todayinsert(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');



        $this->form_validation->set_rules('UserID','유저아이디','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{

            $LoggedID = $this->session->userdata('userId');
            $LoggedGroup = $this->session->userdata('groupidx');

            $_search['where'] = array();
            $_search['where'][] = "A.ToDoID = '" . $PostData['UserID'] . "' ";
            $_select = array(
                "C.NAME AS GROUPNAME", "B.ProjectTitle", "B.ProjectMode", "A.Status", "A.title", "A.Foretime","A.sDate", "A.eDate","A.sTime", "A.eTime", "A.Rate", "A.ProjectIdx", "A.ProjectWorkIdx","D.Comment","Sub2.SUMDoingTime"
            );
            $_MyReport = $this->project_model->getUserReports($_search, $_select, $PostData['UserID']);
            if (count($_MyReport) > 0) {
                $reportNo = $LoggedID."-".date("YmdHis");
                foreach ($_MyReport as $key => $val) {

                    $dbsenddata = array();
                    /*if ($val['sTime'] > 0 && $val['eTime'] > 0  ){
                        $diff = $val['eTime']-$val['sTime'];
                        $worktime     = round($diff / 60 );
                    }else{
                        $worktime     = 0;
                    }*/

                    $worktime     = $val['SUMDoingTime']; // 분
                    $sessionArray = array('userId' => $LoggedID,
                        'userIdGroup' => ($LoggedGroup===null?1:$LoggedGroup),
                        'ProjectTitle' => $val['ProjectTitle'],
                        'Worktitle' => $val['title'],
                        'GROUPNAME' => $val['GROUPNAME'],
                        'Rate' => $val['Rate'],
                        'Status' => (string)$val['Status'] ? $this->config->item('ProjectWorks_Status')[(int)$val['Status']] : null,
                        'LastComment' => htmlspecialchars($val['Comment']),
                        'sTime' => $val['sTime'],
                        'Foretime' => $val['Foretime'],
                        'eTime' => $val['eTime'],
                        'sDate' => $val['sDate'],
                        'eDate' => $val['eDate'],
                        'worktime' => $worktime
                    );
                    $dbsenddata['reportGroup'] = $reportNo;
                    $dbsenddata['ProjectMode'] = $val['ProjectMode'];
                    $dbsenddata['ProjectIdx'] = $val['ProjectIdx'];
                    $dbsenddata['ProjectWorkIdx'] = $val['ProjectWorkIdx'];
                    $dbsenddata['LoggedID'] = $LoggedID;
                    $dbsenddata['LoggedGroup'] = $LoggedGroup;

                    $_returndata = $this->project_model->insertreportdata($dbsenddata,$sessionArray);
                    if ( !$_returndata['result']) {
                        echo json_encode($_returndata);
                        exit;
                    }

                }
            }else{
                $_returndata['result'] = false;
                $_returndata['message'] = "진행된 프로젝트가 없습니다.";
                echo json_encode($_returndata);
                exit;
            }
            $_returndata['result'] = true;
            echo json_encode($_returndata);
            exit;
        }

    }

    function remove(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ReportIdx','인덱스번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $dbresult = $this->project_model->deletereport($PostData['ReportIdx']);
            echo json_encode($dbresult);
            exit;

        }

    }




}

?>
