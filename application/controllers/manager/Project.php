<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Project
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 5 July 2019
 */
class Project extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('project_model');
        $this->load->model('user_model');
        $this->load->helper("html");
        $this->isLoggedIn();
    }

    /**
     * This function used to load the first screen of the user
     */
    function index()
    {

        $this->isLoggedIn();
        //$searchText = $this->security->xss_clean($this->input->post('searchText'));
        $this->global['pageTitle'] = 'Hackers Develope Project';
        //조회용 코드
        $_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'),$this->session->userdata('groupidx') );
        $this->global['GROUPCode'] = $_GROUPCode['Group'];

        //방송채널 리스트
        $group_array = array(1,2);
        $broadcastList = array();
        foreach ( $group_array as  $val ) {
            $_redissearch['where'] = array();
            $_redissearch['where'][] = "A.IsChat = 1";
            $_redissearch['where'][] = "A.ProjectMode = " . $val;
            $_redisselect = array(
                "A.ProjectNo"
            );
            $RedisArray = $this->project_model->getChartGroupListing($_redissearch, $_redisselect);

            if ( count($RedisArray) > 0 ) {
                foreach( $RedisArray as $inkey => $inval) {
                    array_push($broadcastList ,$inval['ProjectNo']);
                }
                // 소켓서버 채널 목록 저장
                $this->CallApis_saveServiceChannels(($val == 1 ? 'PROJECT' : 'MAINTENANCE'), ['channels' => $broadcastList]);

            }
        }
        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;
        $data['LoggedInfo'] = $this->session->userdata();
        $data["search"] = $this->input->post();
        $time = time();
        $data["search"]['search_end_date'] =  date("Y-m-d");
        $data["search"]['search_start_date'] =  date("Y-m-d",strtotime("-30 day", $time));
        $this->loadViews("manager/project/list", $this->global, $data, NULL);

    }


    function ajax_list(){


        $_request = $this->security->xss_clean($this->input->get());

        $_search['where'] = array();
        $_search['where'][] = "A.DelID IS NULL";
        $_search['where'][] = "A.ProjectIdx not in (2,5)";
        if(isset($_request['srhProjectMode'])  && !empty($_request['srhProjectMode']) ) {
            $_search['where'][] = "A.ProjectMode = '".$_request['srhProjectMode']."' ";
        }

        if(isset($_request['srhProjectStatus'])  && !empty($_request['srhProjectStatus']) ) {
            $_search['where'][] = "A.ProjectStatus = '".$_request['srhProjectStatus']."' ";
        }

        if(isset($_request['searchText']) && !empty($_request['searchText']) && !empty($_request['searchSubject'])) {
            if ( $_request['searchSubject'] == "UserName") {
                $_search['where'][] = "M.name  LIKE '%".$_request['searchText']."%'";
            }else{
                $_search['where'][] = "A.".$_request['searchSubject']."  LIKE '%".$_request['searchText']."%'";
            }
        }
        if ( $this->session->userdata('role') == ROLE_EMPLOYEE || $this->session->userdata('role') == ROLE_MANAGER ) {

            $_search['where'][] = "( JSON_EXTRACT(A.Permission, '$[*]') LIKE '%".$this->session->userdata('groupidx')."%' OR A.RegIDGroup = '".$this->session->userdata('groupidx')."' ) ";

        }else if (  $this->session->userdata('role') == ROLE_SUPERVISOR ) {
            /*
            2020.02.26 슈퍼바이저 권한이 이상해서 수정함 by Noh
            if ( $this->session->userdata('groupdepth') == 2 ) {
                $_search['where'][] = " ( A.RegIDGroup = '".$this->session->userdata('parentgroup')."' OR G.IDX = '".$this->session->userdata('parentgroup')."' ) ";
            }else{
                $_search['where'][] = "G.PARENT = '".$this->session->userdata('parentgroup')."' ";
            }
            */
            $_search['where'][] = "( JSON_EXTRACT(A.Permission, '$[*]') LIKE '%".$this->session->userdata('groupidx')."%' OR A.RegIDGroup = '".$this->session->userdata('groupidx')."' ) ";

        }

        if ( $this->input->get('search_start_date') && $this->input->get('search_end_date') ) {
            $_search['where'][] = "A.RegDatetime >= '".$this->input->get('search_start_date')." 00:00:00' ";
            $_search['where'][] = "A.RegDatetime <= '".$this->input->get('search_end_date')." 23:59:59' ";
        }
        $this->load->library('pagination');
        $count = $this->project_model->ListingCount($_search);

        isset($_request['list_table_length']) ? $list_table_length = $_request['list_table_length']: $list_table_length = 10;
        $returns = $this->paginationCompress( "manager/project/index", $count, $list_table_length );

        $returns['segment'] = $_request["paging"] > 0?$_request["paging"]:null;

        $_select = array(
            "A.*","M.name as UserName",
            "CASE WHEN A.ProjectStatus = 1 THEN '진행중' WHEN A.ProjectStatus = 2 THEN '완료' WHEN A.ProjectStatus = 3 THEN '중단' ELSE '대기' END AS strProjectStatus",
            "CASE WHEN A.IsChat = 1  THEN '사용' ELSE '안함' END AS strIsChat"
        );
        $data['userRecords'] = $this->project_model->Listing($_search, $returns["page"], $returns["segment"] ,$_select);

        $data['totalRecords'] = $count;

        // 출판법인
        /*$codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
        $data["codeBusiness"] = $codeBusiness;
        //조회용 코드
        $data['codename'] = $this->getCodeName(array('SAP_SPART','SAP_KONDM','SAP_VKORG','SAP_MATKL','COMP_TYPE')); // 카테고리 코드*/

        $data["search"] = $this->input->get();


        echo json_encode([
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $data['userRecords']
        ]);
        exit;
    }

    function sequpdate(){

        $PostData = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('SeqData','데이터','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $dbresult = $this->project_model->updateseqdata($PostData);
            echo json_encode($dbresult);
            exit;

        }
    }


    public function getReadyWorks( ){

        $PostData = $this->input->POST();

        $_search3['where'] = array();
        $_search3['where'][] = "A.IsDel IS NULL ";
        $_search3['where'][] = "A.Status = 1 ";
        $_search3['where'][] = "( A.RegID = '".$this->session->userdata('userId')."' AND ( A.ToDoID IS NULL OR A.ToDoID = '' OR  A.ToDoID = 0 ) ) ";

        $_select3 = array(
            "A.ProjectWorkIdx","A.title","A.RegID","A.RegDatetime","B.ProjectTitle","A.Priority","A.IsReOpen","A.IntraUrl"
        );

        $_dbresultx = $this->project_model->getMyWorkListing($_search3 ,$_select3);

        $HistoryMessagesCount = $_dbresultx['cnt'];
        $messageList = [];
        if ( $HistoryMessagesCount > 0 ) {
            foreach($_dbresultx['res'] as $key =>  $row) {
                $messageList[$key]['ProjectWorkIdx'] = $row['ProjectWorkIdx'];
                $messageList[$key]['title'] = htmlspecialchars($row['title']);
                $messageList[$key]['RegID'] = $row['RegID'];
                $messageList[$key]['RegDatetime'] = $row['RegDatetime'];
                $messageList[$key]['ProjectTitle'] =  htmlspecialchars($row['ProjectTitle']);
                $messageList[$key]['IsReOpen'] = $row['IsReOpen'];
                $messageList[$key]['Priority'] = $row['Priority'];
                $messageList[$key]['IntraUrl'] = $row['IntraUrl'];
            }
        }

        echo json_encode(["dataList"=>$messageList, "totalCount"=>$HistoryMessagesCount ]);
        exit;

    }

    public function getMyTodoWorks( ){

        $PostData = $this->input->POST();

        $_search3['where'] = array();
        $_search3['where'][] = "A.IsDel IS NULL ";
        $_search3['where'][] = "A.Status = 1";
        if ( $PostData['UserIdx'] > 0  ) {
            $_search3['where'][] = "A.ToDoID = '" . $PostData['UserIdx'] . "' ";
        }else{

            $_search3['where'][] = "A.ToDoID = '" . $this->session->userdata('userId') . "' ";
        }

        $_search3['where'][] = "A.sDate <=  '" . date("Y-m-d") . "' ";

        $_select3 = array(
            "A.ProjectWorkIdx", "A.title", "A.IsReOpen","IFNULL(M.name,'미정') as UserName", "B.ProjectTitle", "A.Priority"
        );
        $_dbresult = $this->project_model->getMyWorkListing($_search3, $_select3);

        $HistoryMessagesCount = count($_dbresult['cnt']);
        $messageList = [];
        if ( $HistoryMessagesCount > 0 ) {
            foreach($_dbresult['res'] as $key =>  $row) {
                $messageList[$key]['ProjectWorkIdx'] = $row['ProjectWorkIdx'];
                $messageList[$key]['title'] = htmlspecialchars($row['title']);
                $messageList[$key]['RegDatetime'] = $row['RegDatetime'];
                $messageList[$key]['ProjectTitle'] =  htmlspecialchars($row['ProjectTitle']);
                $messageList[$key]['Priority'] = $row['Priority'];
            }
        }

        echo json_encode(["dataList"=>$messageList, "totalCount"=>$HistoryMessagesCount ]);
        exit;

    }

    public function getSubTeam( ){

        $PostData = $this->input->post();
        $this->load->library('form_validation');
        // 각종코드
        $this->config->load('config', true);
        $_CommonCode = $this->config->item('code');
        $this->form_validation->set_rules('GroupCode','프로젝트번호','trim|required');

        if($this->form_validation->run() == FALSE)
        {
            echo json_encode(["dataList" => array(), "totalCount" => 0]);
            exit;
        }else {

            if ( strlen($PostData['GroupCode']) < 2 ) {
                $_GROUPCode = $this->getGlobalGroupCodeAll();
                $messageList = [];
                $num = 0;
                foreach ($_GROUPCode['Group'] as $key => $row) {
                    $messageList[$num]['IDX'] = $key;
                    $messageList[$num]['NAME'] = htmlspecialchars($row);
                    $num++;
                }

                echo json_encode(["dataList" => $messageList, "totalCount" => count($_GROUPCode['Group'])]);
                exit;
            }else{

                $_tmparray_code = $_CommonCode[$PostData['GroupCode']];
                $_array_code = "";
                foreach($_tmparray_code as  $key => $val ){
                    $_array_code .= $val.",";
                }
                $_array_code2 = substr($_array_code,0,-1) ;
                $_search['where'] = array();
                $_search['where'][] = "A.IDX in ( ".$_array_code2." ) ";
                $_select = array(
                    "A.IDX", "A.NAME"
                );

                $_dbresult = $this->project_model->getSubTeamList($_search, $_select);

                $HistoryMessagesCount = $_dbresult['cnt'];
                $messageList = [];
                if ($HistoryMessagesCount > 0) {
                    foreach ($_dbresult['res'] as $key => $row) {
                        $messageList[$key]['IDX'] = $row['IDX'];
                        $messageList[$key]['NAME'] = htmlspecialchars($row['NAME']);
                    }
                }
                echo json_encode(["dataList" => $messageList, "totalCount" => $HistoryMessagesCount]);
                exit;
            }
        }

    }

    public function getTeamMember( ){

        $PostData = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('TeamCode','TeamCode','trim|required');

        if($this->form_validation->run() == FALSE)
        {
            echo json_encode(["dataList" => array(), "totalCount" => 0]);
            exit;
        }else {

            $_usersearch['where'] = array();
            $_usersearch['where'][] = "M.isDeleted = 0 ";
            $_usersearch['where'][] = "M.roleId in ( 2,3,9) ";
            $_usersearch['where'][] = "A.DENIED = 'N' ";
            if ( is_numeric($PostData['TeamCode']) ) {
                $_usersearch['where'][] = "A.GROUP_IDX = '".$PostData['TeamCode']."' ";
            }else{
                // 각종코드
                $this->config->load('config', true);
                $_CommonCode = $this->config->item('code');
                $_tmparray_code = $_CommonCode[$PostData['TeamCode']];
                $_array_code = "";
                foreach($_tmparray_code as  $key => $val ){
                    $_array_code .= $val.",";
                }
                $_array_code2 = substr($_array_code,0,-1) ;
                $_usersearch['where'][] = "A.GROUP_IDX in ( ".$_array_code2." ) ";
            }

            $_userselect = array(
                "M.userId","M.name","IFNULL(G.NAME,'무소속') AS GROUP_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME"
            );
            $_dbresult = $this->project_model->getUserListing($_usersearch, $_userselect);


            $HistoryMessagesCount = count($_dbresult);
            $messageList = [];
            if ($HistoryMessagesCount > 0) {
                foreach ($_dbresult as $key => $row) {
                    $messageList[$key]['userId'] = $row['userId'];
                    $messageList[$key]['GROUP_NAME'] = $row['GROUP_NAME'];
                    $messageList[$key]['name'] = $row['name'];
                    $messageList[$key]['CLASS_NAME'] = $row['CLASS_NAME'];
                }
            }
            echo json_encode(["dataList" => $messageList, "totalCount" => $HistoryMessagesCount]);
            exit;
        }


    }

    public function addtodo(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectIdx','프로젝트번호','trim|required');
        $this->form_validation->set_rules('title','Todo타이틀','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{

            $LoggedID = $this->session->userdata();

            $dbresult = $this->project_model->inserttododata($PostData,$LoggedID);
            echo json_encode($dbresult);
            exit;
        }

    }

    function popview(){

        return $this->load->view("manager/project/popview",null,null,false);

    }


    function popreg( ){

        $_GROUPCode = $this->getGlobalGroupCodeAll();
        // $_GROUPCode = $this->getGlobalGroupCode2($this->session->userdata('role'),$this->session->userdata('groupidx'),$this->session->userdata('parentgroup'));

        $data['GROUPCode'] = $_GROUPCode['Group'];
        $data['LoggedInfo'] = $this->session->userdata();
        return $this->load->view("manager/project/popreg",$data);
    }


    public function popuserinfo( $_idx = null ) {
        if ( $_idx === null ) {
            alert_back("잘못된 접근입니다");
            exit;
        }

        $_GROUPCode = $this->getGlobalGroupCodeAll();

        $data['GROUPCode'] = $_GROUPCode['Group'];
        $_TargetUserInfo = $this->project_model->getUserInfo($_idx);
        $data['TargetUser'] = $_TargetUserInfo[0];

        $_TeamViewPermission = $this->project_model->getTeamViewPermission($_idx);

        $PermissionData = array();
        if($_TeamViewPermission['result'] && count($_TeamViewPermission['row']) > 0 ){
            $PermissionData = $_TeamViewPermission['row'][0];
        }
        $data['PermissionData'] = $PermissionData;
        return $this->load->view("manager/group/popinfo",$data);
    }

    public function popteaminfo( $_idx = null ) {
        if ( $_idx === null ) {
            alert_back("잘못된 접근입니다");
            exit;
        }

        $_TeamViewPermission = $this->project_model->getUserListPermission($_idx);

        $PermissionData = array();
        if($_TeamViewPermission['result'] && count($_TeamViewPermission['row']) > 0 ){
            foreach( $_TeamViewPermission['row'] as $key => $val ) {
                $PermissionData[$val['GROUP_IDX']]['GROUP_IDX'][] = $val['GROUP_IDX'];
                $PermissionData[$val['GROUP_IDX']]['GROUP_NAME'][] = $val['GROUP_NAME'];
                $PermissionData[$val['GROUP_IDX']]['UserID'][] = $val['UserID'];
                $PermissionData[$val['GROUP_IDX']]['USER_NAME'][] = $val['USER_NAME'];
                $PermissionData[$val['GROUP_IDX']]['CLASS_NAME'][] = $val['CLASS_NAME'];
            }
        }


        $data['PermissionData'] = $PermissionData;
        return $this->load->view("manager/group/popteaminfo",$data);
    }

    function popmodify( $idx = null)
    {

        $_GROUPCode = $this->getGlobalGroupCodeAll();
        // $_GROUPCode = $this->getGlobalGroupCode2($this->session->userdata('role'),$this->session->userdata('groupidx'),$this->session->userdata('parentgroup'));

        $data['GROUPCode'] = $_GROUPCode['Group'];

        $data['LoggedInfo'] = $this->session->userdata();
        $_select = array(
            "A.*", "M.name as Register", "G.NAME as GROUPNAME"
        );
        $dbresult = $this->project_model->getProjectInfo($_select, $idx);
        if ($dbresult['result'] === false) {
            alert_back($dbresult['message']);
        }

        $data['ProjectData'] = $dbresult['row'][0];


        $_search3['where'] = array();
        $_search3['where'][] = "A.ProjectIdx = '".$idx."' ";
        $_select3 = array(
            "M.name AS USERNAME","G.NAME AS GROUPNAME","A.ToDoIDGroup"
        );
        $_workmeber = $this->project_model->getProjectWorkMember($_search3, $_select3);
        $data['ProjectMember'] = $_workmeber;
        if ( $data['ProjectData']['RegID'] == $this->session->userdata('userId') || $this->session->userdata('role') != ROLE_EMPLOYEE ) {
            return $this->load->view("manager/project/popmodify",$data);
        } else{
            return $this->load->view("manager/project/popmodifyread",$data);
        }
    }

    function popdetail( $idx = null,$_mode = null){

        $data['viewmode'] = $_mode;
        $data['ProjectWorkIdx'] = $idx;
        $data['LoggedInfo'] = $this->session->userdata();
        $_select = array(
            "A.ProjectNo","A.ProjectMode","A.ProjectTitle","B.*","M.name as Register","M1.name as Indicator","M2.name as Commander","Sub2.SUMDoingTime"
        );
        $dbresult = $this->project_model->getDetailInfo($_select,$idx, $data['LoggedInfo']);
        if ( $dbresult['result'] === false ) {
            alert_back($dbresult['message']);
        }

        if ( $dbresult['row'][0]['IsDel'] == 1 ) {
            alert('존재하지 않은 업무입니다.');
            echo "<script>location.reload();</script>";
            return false;
        }

        $data['ProjectData'] = $dbresult['row'][0];

        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;

        $_usersearch['where'] = array();
        $_usersearch['where'][] = "M.isDeleted = 0 ";
        $_usersearch['where'][] = "M.roleId in ( 3,9) ";
        $_usersearch['where'][] = "A.DENIED = 'N' ";
        if( $this->session->userdata('role') == ROLE_MANAGER ) {
            $_usersearch['where'][] = "G.IDX = ".$this->session->userdata('groupidx')." ";
        }else if( $this->session->userdata('role') == ROLE_SUPERVISOR ) {
            $_darray_allcode = "";
            $_parentCode = $this->session->userdata('parentgroup');
            if ( isset($_parentCode) && $_parentCode == BASE_DEVELOPE_PARENT_CODE ) {
                $_dtmparray_code = $_CommonCode['DEVELOPEMENTGROUP'];
                foreach($_dtmparray_code as  $key => $val ){
                    $_darray_allcode .= $val.",";
                }
                $_darray_allcode2 = substr($_darray_allcode,0,-1) ;
                $_usersearch['where'][] = "( A.GROUP_IDX  IN ( ".$_darray_allcode2." ) ) ";
                //$codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( DEPTH = 2 && IDX = 355 ) ) ";
                //$_usersearch['where'][] = "(( G.DEPTH = 3 && G.PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( G.DEPTH = 2 && G.IDX = 355 )) ";
            }else if ( isset($_parentCode) && $_parentCode == BASE_DESIGN_PARENT_CODE  ) {
                $_dtmparray_code = $_CommonCode['DESIGNGROUP'];
                foreach($_dtmparray_code as  $key => $val ){
                    $_darray_allcode .= $val.",";
                }
                $_darray_allcode2 = substr($_darray_allcode,0,-1) ;
                $_usersearch['where'][] = "( A.GROUP_IDX  IN ( ".$_darray_allcode2." ) ) ";
                //$_usersearch['where'][] = "(( G.DEPTH = 3 && G.PARENT = ".$_parentCode."  ))";
            //}else if ( isset($_parentCode) && ($_parentCode == BASE_REALTOR_PARENT_CODE || $_parentCode == BASE_BASEENGLISH_PARENT_CODE ) ) {
            //    $_usersearch['where'][] = "(( G.DEPTH = 2 && G.IDX = ".$_parentCode."  ))";
            }else{
                $_usersearch['where'][] = "G.IDX = ".$this->session->userdata('groupidx')." ";
            }
        }

        $_userselect = array(
            "M.*","IFNULL(G.NAME,'무소속') AS GROUP_NAME"
        );
        $data['Users'] = $this->project_model->getUserListing($_usersearch, $_userselect);

        $_replysearch['where'] = array();
        $_replysearch['where'][] = "R.IsDel = 0";
        $_replysearch['where'][] = "R.ProjectWorkIdx = ".$idx." ";
        $_replyselect = array(
            "R.*","M.name as RegName"
        );
        $data['Replys'] = $this->project_model->getReplyListing($_replysearch, $_replyselect);


        $_hsearch['where'] = array();
        $_hsearch['where'][] = "H.ProjectWorkIdx = ".$idx." ";
        $_hselect = array(
            "H.*"
        );
        $data['Historys'] = $this->project_model->getHistoryListing($_hsearch, $_hselect);



        $_psearch['where'] = array();
        if ( $this->session->userdata('role') == ROLE_EMPLOYEE || $this->session->userdata('role') == ROLE_MANAGER  || $this->session->userdata('role') == ROLE_SUPERVISOR  ) {
            $_psearch['where'][] = "( JSON_EXTRACT(Permission, '$[*]') LIKE '%".$this->session->userdata('groupidx')."%' OR  A.ProjectIdx = ".MAINTERENCE_IDX." OR A.RegIDGroup = '".$this->session->userdata('groupidx')."' )";
        }
        $_psearch['where'][] = "A.DelID IS NULL ";
        $_psearch['where'][] = "A.ProjectStatus = 1";
        $_select = array(
            "A.*","M.nickname as UserName"
        );
        $data['ProjectList'] = $this->project_model->ProjectListing($_psearch ,$_select);

        //     if ( $data['ProjectData']['RegID'] == $this->session->userdata('userId') || $this->session->userdata('role') != 3 ) {
        if (  $this->session->userdata('role') == ROLE_ADMIN || $this->session->userdata('role') == ROLE_SUPERVISOR) {
            return $this->load->view("manager/project/popdetail",$data);
        }else if ( $data['ProjectData']['RegID'] == $this->session->userdata('userId') || $data['ProjectData']['ToDoID'] == $this->session->userdata('userId') ) {
            return $this->load->view("manager/project/popdetail",$data);
        } else{
            return $this->load->view("manager/project/popdetailread",$data);
        }
    }

    /* 일반프로젝트용 */
    function popdetail2( $idx = null){

        $data['ProjectWorkIdx'] = $idx;
        $data['LoggedInfo'] = $this->session->userdata();
        $_select = array(
            "A.RegID as ProjectRegID","A.ProjectNo","A.ProjectMode","A.ProjectTitle","B.*","M.name as Register","M1.name as Indicator","M2.name as Commander","Sub2.SUMDoingTime"
        );
        $dbresult = $this->project_model->getDetailInfo($_select,$idx, $data['LoggedInfo']);
        if ( $dbresult['result'] === false ) {
            alert_back($dbresult['message']);
        }

        if ( $dbresult['row'][0]['IsDel'] == 1 ) {
            alert('존재하지 않은 업무입니다.');
            echo "<script>location.reload();</script>";
            return false;
        }

        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;


        $data['ProjectData'] = $dbresult['row'][0];

        $_usersearch['where'] = array();
        $_usersearch['where'][] = "M.isDeleted = 0 ";
        $_usersearch['where'][] = "M.roleId in ( 3,9) ";
        $_usersearch['where'][] = "A.DENIED = 'N' ";
        if( $this->session->userdata('role') == ROLE_MANAGER ) {
            $_usersearch['where'][] = "G.IDX = ".$this->session->userdata('groupidx')." ";
        }else if( $this->session->userdata('role') == ROLE_SUPERVISOR ) {
            $_parentCode = $this->session->userdata('parentgroup');
            if ( isset($_parentCode) && $_parentCode == BASE_DEVELOPE_PARENT_CODE ) {
                $_dtmparray_code = $_CommonCode['DEVELOPEMENTGROUP'];
                foreach($_dtmparray_code as  $key => $val ){
                    $_darray_allcode .= $val.",";
                }
                $_darray_allcode2 = substr($_darray_allcode,0,-1) ;
                $_usersearch['where'][] = "( A.GROUP_IDX  IN ( ".$_darray_allcode2." ) ) ";
                //$_usersearch['where'][] = "(( G.DEPTH = 3 && G.PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( G.DEPTH = 2 && G.IDX = 355 )) ";
            }else if ( isset($_parentCode) && $_parentCode == BASE_DESIGN_PARENT_CODE  ) {
                $_dtmparray_code = $_CommonCode['DESIGNGROUP'];
                foreach($_dtmparray_code as  $key => $val ){
                    $_darray_allcode .= $val.",";
                }
                $_darray_allcode2 = substr($_darray_allcode,0,-1) ;
                $_usersearch['where'][] = "( A.GROUP_IDX  IN ( ".$_darray_allcode2." ) ) ";
                //$_usersearch['where'][] = "(( G.DEPTH = 3 && G.PARENT = ".$_parentCode."  ))";

            }else{
                $_usersearch['where'][] = "G.IDX = ".$this->session->userdata('groupidx')." ";
            }
        }

        $_userselect = array(
            "M.*","IFNULL(G.NAME,'무소속') AS GROUP_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME"
        );
        $data['Users'] = $this->project_model->getUserListing($_usersearch, $_userselect);

        $_replysearch['where'] = array();
        $_replysearch['where'][] = "R.IsDel = 0";
        $_replysearch['where'][] = "R.ProjectWorkIdx = ".$idx." ";
        $_replyselect = array(
            "R.*","M.name as RegName"
        );
        $data['Replys'] = $this->project_model->getReplyListing($_replysearch, $_replyselect);


        $_hsearch['where'] = array();
        $_hsearch['where'][] = "H.ProjectWorkIdx = ".$idx." ";
        $_hselect = array(
            "H.*"
        );
        $data['Historys'] = $this->project_model->getHistoryListing($_hsearch, $_hselect);


        $_psearch['where'] = array();
        if ( $this->session->userdata('role') == ROLE_EMPLOYEE || $this->session->userdata('role') == ROLE_MANAGER  || $this->session->userdata('role') == ROLE_SUPERVISOR  ) {
            $_psearch['where'][] = "( JSON_EXTRACT(Permission, '$[*]') LIKE '%".$this->session->userdata('groupidx')."%' OR  A.ProjectIdx = ".MAINTERENCE_IDX." OR A.RegIDGroup = '".$this->session->userdata('groupidx')."' )";
        }
        $_psearch['where'][] = "A.DelID IS NULL ";
        $_psearch['where'][] = "A.ProjectStatus = 1";
        $_select = array(
            "A.*","M.nickname as UserName"
        );
        $data['ProjectList'] = $this->project_model->ProjectListing($_psearch ,$_select);

        //     if ( $data['ProjectData']['RegID'] == $this->session->userdata('userId') || $this->session->userdata('role') != 3 ) {
        if (  $this->session->userdata('role') == ROLE_ADMIN || $this->session->userdata('role') == ROLE_SUPERVISOR) {
            return $this->load->view("manager/project/popdetail2",$data);
        }else if ( $data['ProjectData']['RegID'] == $this->session->userdata('userId') || $data['ProjectData']['ToDoID'] == $this->session->userdata('userId') ) {
            if ( $data['ProjectData']['Status']  == 9 ) {
                return $this->load->view("manager/project/popdetailread",$data);
            }else{
                return $this->load->view("manager/project/popdetail2",$data);
            }

        } else{
            return $this->load->view("manager/project/popdetailread",$data);
        }
    }


    function popdetailread($idx = null){
        $data['ProjectWorkIdx'] = $idx;
        $data['LoggedInfo'] = $this->session->userdata();
        $_select = array(
            "A.ProjectNo","A.ProjectMode","A.ProjectTitle","B.*","M.name as Register","M1.name as Indicator","M2.name as Commander","Sub2.SUMDoingTime"
        );
        $dbresult = $this->project_model->getDetailInfo($_select,$idx, $data['LoggedInfo']);
        if ( $dbresult['result'] === false ) {
            alert_back($dbresult['message']);
        }

        $data['ProjectData'] = $dbresult['row'][0];

        $_usersearch['where'] = array();
        $_usersearch['where'][] = "M.isDeleted = 0 ";
        $_usersearch['where'][] = "M.roleId in ( 3,9) ";
        $_usersearch['where'][] = "A.DENIED = 'N' ";
        if( $this->session->userdata('role') == ROLE_MANAGER ) {
            $_usersearch['where'][] = "G.IDX = ".$this->session->userdata('groupidx')." ";
        }

        $_userselect = array(
            "M.*","IFNULL(G.NAME,'무소속') AS GROUP_NAME"
        );
        $data['Users'] = $this->project_model->getUserListing($_usersearch, $_userselect);

        $_replysearch['where'] = array();
        $_replysearch['where'][] = "R.IsDel = 0";
        $_replysearch['where'][] = "R.ProjectWorkIdx = ".$idx." ";
        $_replyselect = array(
            "R.*","M.name as RegName"
        );
        $data['Replys'] = $this->project_model->getReplyListing($_replysearch, $_replyselect);


        $_hsearch['where'] = array();
        $_hsearch['where'][] = "H.ProjectWorkIdx = ".$idx." ";
        $_hselect = array(
            "H.*"
        );
        $data['Historys'] = $this->project_model->getHistoryListing($_hsearch, $_hselect);

        // 각종코드
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;

        return $this->load->view("manager/project/popdetailread",$data);


    }

    function workstatusupdate(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        $this->form_validation->set_rules('mode','업데이트구분','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $Loggedgroupidx = $this->session->userdata('groupidx');
            $dbresult = $this->project_model->updateworkstatus($LoggedID,$Loggedgroupidx,$PostData);

            /*여기서부터 일내역 Todo, Doing, Done순 */
            $_dowork_array = [1,2,9];

            /* statics */
            $dbresult['TODOSTATICS'] = array();
            $dbresult['DONESTATICS'] = array();
            for( $i = 0 ; $i < count($_dowork_array) ; $i++) {
                $_search3['where'] = array();
                $_search3['where'][] = "A.IsDel IS NULL "; ;
                $_search3['where'][] = "A.Status = $_dowork_array[$i] "; ;
                $_search3['where'][] = "A.ToDoID = '".$this->session->userdata('userId')."' ";
                if ( $i == 2 ) {
                    $_search3['where'][] = "A.Status =  9 &&  A.eDate = '" . date("Y-m-d") . "' ";
                }
                $_select3 = array("A.ProjectWorkIdx");
                $_dbresult = $this->project_model->getMyWorkListing($_search3 ,$_select3);
                if ( $i == 0 || $i == 2 ) {
                    if ( $_dbresult['cnt'] > 0 ) {
                        $_targetarray = array();
                        foreach($_dbresult['res'] as $rkey => $rval ) {
                            array_push($_targetarray,$rval['ProjectWorkIdx']);
                        }
                        $_todoresult = $this->project_model->getMyWorkStatics($_targetarray,$this->session->userdata('userId'));
                        if ( count($_todoresult) > 0 && $i == 0) {
                            $dbresult['TODOSTATICS'] = $_todoresult[0];
                        }else if ( count($_todoresult) > 0 && $i == 2 ) {
                            $dbresult['DONESTATICS'] = $_todoresult[0];

                        }
                    }
                }
            }


            echo json_encode($dbresult);
            exit;
        }

    }

    function myjobdate(){

        $PostData = $this->input->post();
        if($PostData['USERID'] =="")
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{

            /*여기서부터 일내역 Todo, Doing, Done순 */
            $_dowork_array = [1,2,9];

            /* statics */
            $dataresult = array();
            $dataresult['TODOSTATICS'] = array();
            $dataresult['DONESTATICS'] = array();
            $dataresult['result'] = true;
            $dataresult['TODOSTATICS'][0]['SUMCount'] = 0;
            $dataresult['DONESTATICS'][0]['SUMCount'] = 0;
            for( $i = 0 ; $i < count($_dowork_array) ; $i++) {
                $_search3['where'] = array();
                $_search3['where'][] = "A.IsDel IS NULL "; ;
                $_search3['where'][] = "A.Status = $_dowork_array[$i] "; ;
                $_search3['where'][] = "A.ToDoID = '".$PostData['USERID']."' ";
                if ( $i == 2 ) {
                    $_search3['where'][] = "A.Status =  9 &&  A.eDate = '" . date("Y-m-d") . "' ";
                }
                $_select3 = array("A.ProjectWorkIdx");
                $_dbresult = $this->project_model->getMyWorkListing($_search3 ,$_select3);
                if ( $i == 0 || $i == 2 ) {
                    if ( $_dbresult['cnt'] > 0 ) {
                        $_targetarray = array();
                        foreach($_dbresult['res'] as $rkey => $rval ) {
                            array_push($_targetarray,$rval['ProjectWorkIdx']);
                        }
                        $_todoresult = $this->project_model->getMyWorkStatics($_targetarray,$PostData['USERID']);
                        if ( count($_todoresult) > 0 && $i == 0) {
                            $dataresult['TODOSTATICS'] = $_todoresult[0];
                        }else if ( count($_todoresult) > 0 && $i == 2 ) {
                            $dataresult['DONESTATICS'] = $_todoresult[0];

                        }
                    }
                }
            }

            echo json_encode($dataresult);
            exit;


        }
    }

    function settodo(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        $this->form_validation->set_rules('ToDoID','ToDo대상','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $Loggedgroupidx = $this->session->userdata('groupidx');
            $dbresult = $this->project_model->updatetodoset($LoggedID,$Loggedgroupidx,$PostData);
            echo json_encode($dbresult);
            exit;

        }
    }


    function workupdate(){

        $PostData = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $Loggedgroupidx = $this->session->userdata('groupidx');
            $dbresult = $this->project_model->updateworkdata($LoggedID,$Loggedgroupidx,$PostData);
            echo json_encode($dbresult);
            exit;

        }
    }

    /* date resize only function */
    function workdateupdate(){

        $PostData = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $LoggedGroup = $this->session->userdata('groupidx');
            $dbresult = $this->project_model->updateworkdate($LoggedID,$LoggedGroup,$PostData);
            echo json_encode($dbresult);
            exit;

        }
    }
    function update(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectIdx','프로젝트번호','trim|required');
        $this->form_validation->set_rules('ProjectTitle','프로젝트명','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $dbresult = $this->project_model->updatedata($LoggedID,$PostData);
            echo json_encode($dbresult);
            exit;

        }
    }

    function userinfoupdate(){
        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('TargetUserID','대상직원','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $dbresult = $this->project_model->teamviewupdate($PostData);
            echo json_encode($dbresult);
            exit;

        }

    }


    function insert(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectTitle','프로젝트명','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');

            $dbresult = $this->project_model->insertprojectdata($LoggedID,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }


    function workinsert(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedInfo = $this->session->userdata();

            $dbresult = $this->project_model->insertworkdata($LoggedInfo,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }

    function workcopy(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{
            $LoggedInfo = $this->session->userdata();

            $dbresult = $this->project_model->cloneworkdata($LoggedInfo,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }

    function replyinsert(){

        $PostData = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{

            $LoggedID = $this->session->userdata('userId');
            $LoggedName = $this->session->userdata('name');

            $dbresult = $this->project_model->insertcomment($LoggedID,$LoggedName,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }


    function workdelete(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{

            $LoggedID = $this->session->userdata('userId');
            $dbresult = $this->project_model->removework($LoggedID,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }

    function delete(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{

            $_ISUsed = $this->project_model->isCheckeProject($PostData['ProjectIdx']);

            if ( $_ISUsed > 0 ) {
                if ( $this->session->userdata('hackersid') == "hanacody" ) {
                    $LoggedID = $this->session->userdata('userId');
                    $dbresult = $this->project_model->removeprojectforce($LoggedID,$PostData);
                    echo json_encode($dbresult);exit;
                }else{
                    $_result['result'] = false;
                    if ( !isset($_result['message']) ) $_result['message'] = '해당프로젝트에 사용중인 업무가 있어 삭제가 불가합니다.';
                    echo json_encode($_result);exit;
                }
            }

            $LoggedID = $this->session->userdata('userId');
            $dbresult = $this->project_model->removeproject($LoggedID,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }

    function checkdoingcnt(){
        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('UserID','필수정보(유저)','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{
            $_result = $this->project_model->getDoingNowCoung($PostData['UserID']);
            echo json_encode($_result);
            exit;

        }
    }

    function islogin(){
        if ( $this->session->userdata('userId') ) {
            $_return['islogin'] = $this->session->userdata('userId');
            echo json_encode($_return);
            exit;
        }else{
            $_return['islogin'] = null;
            echo json_encode($_return);
            exit;
        }

    }


    function noticedelete(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('NoticeIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{
            $LoggedID = $this->session->userdata('userId');
            $dbresult = $this->project_model->removenotice($LoggedID,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }



    function replydelete(){

        $PostData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ProjectWorkCommentIdx','프로젝트번호','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다1111.';
            echo json_encode($_result);exit;
        }else{

            $LoggedID = $this->session->userdata('userId');
            $dbresult = $this->project_model->removecomment($LoggedID,$PostData);
            echo json_encode($dbresult);
            exit;

        }

    }




    function dailyview($idx = null )
    {
        if( $idx == null)
        {
            $this->loadThis();
            return false;
        }
        $this->global['pageTitle'] = 'Hackers Project Admin';
        $_search['where'] = array();
        $_search['where'][] = "A.ProjectIdx = '".$idx."' ";
        $_select = array(
            "A.ProjectTitle","A.ProjectNo","A.ProjectMode","A.IsChat","A.Permission","B.*","M.name as UserName","M2.name as RegName"
        );
        $dbdata['Records'] = $this->project_model->getJobListing($_search, $_select);


        if ( $this->session->userdata('role') == ROLE_EMPLOYEE &&  $dbdata['Records'][0]['Permission'] != null && $dbdata['Records'][0]['RegID'] != $this->session->userdata('userId')) {
            if ( !$this->isPermission('calendar_view', $dbdata['Records'][0]['Permission'])) {
                $this->loadThis();
                return false;
            }
        }
        if ( $dbdata['Records'][0]['ProjectMode'] != 2 ) {
            $this->loadThis();
            return false;
        }

        $_return_data =  array();
        $channelInfo =  array();
        foreach($dbdata['Records'] as $key => $val) {
            $_return_data[$key]['id'] = $val['ProjectWorkIdx'];
            $_return_data[$key]['title'] = $val['title'];
            $_return_data[$key]['start'] = $val['sDate'];
            $_return_data[$key]['allDay'] = true;
            if ( $val['sDate'] != $val['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$key]['end'] = $eDate;
            }
            $_return_data[$key]['todouser'] = $val['UserName'];
            ///0:대기(할당),1(진행중),2(중단),9(완료)
            switch( $val['Status']) {
                case 3 : $_return_data[$key]['backgroundColor'] = "#dd4b39"; $_return_data[$key]['borderColor'] = "#dd4b39"; break;
                case 2 : $_return_data[$key]['backgroundColor'] = "#605ca8"; $_return_data[$key]['borderColor'] = "#605ca8";break;
                case 9 : $_return_data[$key]['backgroundColor'] = "#000000"; $_return_data[$key]['borderColor'] = "#000000"; break;
                default :  $_return_data[$key]['backgroundColor'] = "#00c0ef"; $_return_data[$key]['borderColor'] = "#00c0ef"; break;
            }

            $_return_data[$key]['rate'] = ($val['Rate']>0? $val['Rate'] : 0);

            if ( $key == 0 ) {
                $channelInfo[0]['ChannelPoolID'] = $val['ProjectNo'];
                $channelInfo[0]['ChannelName'] = $val['ProjectTitle'];
                $channelInfo[0]['IsUse'] = true;
                $channelInfo[0]['IsUseName'] = 'Y';
                $channelInfo[0]['ServiceID'] = ( $val['ProjectMode'] == 1 ? 'PROJECT' : 'MAINTENANCE' );
                $channelInfo[0]['ChatRoomIdx'] = $val['ProjectIdx'];
                $channelInfo[0]['IsUseChat'] = true;
                $channelInfo[0]['IsVisiblePlayCnt'] = true;
            }

        }

        if ( count($dbdata['Records']) > 0 ) {
            switch ($dbdata['Records'][0]['ProjectMode']) {
                case 1 :
                    $serviceID = 'PROJECT';
                    break;
                case 2 :
                    $serviceID = 'MAINTENANCE';
                    break;
                case 3 :
                    $serviceID = 'ETC';
                    break;
                default :
                    $serviceID = 'ETC';
                    break;
            }

            if ($dbdata['Records'][0]['IsChat']) {
                //이전 메시지 조회
                $_mmsearch['where'] = array();
                $_mmsearch['where'][] = "A.ServiceID = '$serviceID' ";
                $_mmsearch['where'][] = "A.RoomIdx = '$idx' ";
                $_mmselect = array(
                    "A.*", "M.name as UserName"
                );
                $data['HistoryMessages'] = $this->project_model->getMessageListing($_mmsearch, $_mmselect);
            }


            $data['ProjectIdx'] = $idx;
            $data['ResultData'] = json_encode($_return_data);
            $data['IsChat'] = $dbdata['Records'][0]['IsChat'];
            $data['ProjectNo'] = $dbdata['Records'][0]['ProjectNo'] ? $dbdata['Records'][0]['ProjectNo'] : null;
            $data['projectTitle'] = $dbdata['Records'][0]['ProjectTitle'] ? $dbdata['Records'][0]['ProjectTitle'] : 'ProjectName is Null';
            $data['UserName'] = $this->session->userdata('name');
            $data['RegName'] = $dbdata['Records'][0]['RegName'];

            if ( $idx &&$data['ProjectNo']) {
                $this->load->helper('cookie');
                $params = [
                    'service_id' => $serviceID,
                    'channel_id' => $data['ProjectNo'],
                    'channel_name' => $data['projectTitle'],
                    'nickname' => $this->session->userdata('name'),
                    'UID' => $this->session->userdata('userId'),
                    'is_auth' => 1,
                    'user_idx' => $this->session->userdata('userId'),
                    'is_admin' => ($this->session->userdata('roleId') === ROLE_ADMIN) ? 1 : 0,
                    'is_hidden' => 0,
                    'is_block' => 0,
                    'is_block_history' => 0,
                    'is_user_count' => 1,
                    'ip_client' => $_SERVER['REMOTE_ADDR']
                ];

                $token = $this->CallApis_token($params);

                // 토큰 쿠키 저장
                $cookie = array(
                    'name' => '_chatroom_token',
                    'value' => $token,
                    'expire' => 86500,
                    'domain' => '.hackers.com',
                    'path' => '/',
                    'prefix' => null,
                );
                set_cookie($cookie);
                //Cookie::queue(Cookie::make('_live_token', $token, 1, '/', null, false, false));


            }

            if ( count($channelInfo) > 0) {
                $this->CallApis_saveChannelInfo($serviceID, $data['ProjectNo'], $channelInfo);

            }

        }
        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/project/dailyview", $this->global, $data, NULL);


    }


    function getChatMsg( $mode = null ){

        $PostData = $this->input->POST();
        $_ServiceID = ( $mode == 1 || $mode == null ) ? 'PROJECT' : "TEAM";
        //이전 메시지 조회

        $_mmsearch['where'] = array();
        $_mmsearch['where'][] = "A.ServiceID = '".$_ServiceID."' ";
        $_mmsearch['where'][] = "A.RoomIdx = '".$PostData['RoomIdx']."' ";
        $_mmselect = array(
            "A.*", "M.name as UserName"
        );
        $data['HistoryMessages'] = $this->project_model->getMessageListing($_mmsearch, $_mmselect);
        $HistoryMessagesCount = count($data['HistoryMessages']);
        $messageList = [];
        if ( $HistoryMessagesCount > 0 ) {
            $_preArray = array();
            foreach($data['HistoryMessages'] as $key =>  $row) {

                $_preArray[$key]['HRegID'] = $row['RegID'];
                $_aling_css = "f_l text-left";
                if ( $row['RegID'] == $this->session->userdata('userId') ) {
                    $_aling_css = "f_r text-right";
                }
                $before_user_css = "";
                $before_user_li_css = "";
                if ( $key  > 0   ) {
                    if ( $_preArray[($key-1)]['HRegID'] == $data['HistoryMessages'][$key]['RegID']) {
                        $before_user_css = "display_none ";
                        $before_user_li_css = "margin_top_0";
                    }
                }
                if ( substr($row['RegDatetime'],0,10) == date("Y-m-d") ) {
                    $RegDatetime = substr($row['RegDatetime'],10,6);
                }else{
                    $RegDatetime = substr($row['RegDatetime'],0,10);
                }

                $messageList[$key]['MessageData'] = "<li class='w_100 ".$before_user_li_css."'><dd class='".$_aling_css."'><strong class='".$before_user_css."'>".$row['UserName']."</strong><p class='speech-bubble'>".nl2br($row['Message'])."</p><p class='end_txt'>".$RegDatetime."</p></dd></li>";

            }
        }
        echo json_encode(["messageList"=>$messageList, "totalCount"=>$HistoryMessagesCount ]);
        exit;
    }

    function view($idx = null,$gidx =  null )
    {

        if( $idx == null)
        {
            $this->loadThis();
            return false;
        }
        $this->global['pageTitle'] = 'Hackers Project';

        $_search['where'] = array();
        //$_search['where'][] = "B.IsDel IS NULL";
        $_search['where'][] = "A.ProjectIdx = '".$idx."' ";
        $_select = array(
            "A.ProjectTitle","A.ProjectNo","A.ProjectMode","A.IsChat","A.Permission","B.*","M.nickname as UserName","M2.name as RegName"
        );
        $dbdata['Records'] = $this->project_model->getJobListinglimit1($_search, $_select);

        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $data['CommonCode'] = $_CommonCode;

        if ( isset($dbdata['Records'][0]['ProjectMode']) === false ||  count($dbdata['Records']) == 0) {
            $this->loadThis();
            return false;
        }else{
            if ( $this->session->userdata('role') == ROLE_EMPLOYEE &&  $dbdata['Records'][0]['Permission'] != null && $dbdata['Records'][0]['RegID'] != $this->session->userdata('userId')) {
                if ( !$this->isPermission('calendar_view', $dbdata['Records'][0]['Permission'])) {
                    $this->loadThis();
                    return false;
                }
            }

            if ( $this->session->userdata('role') == ROLE_ADMIN ) {
                $_GROUPCode = $this->getGlobalGroupCodeAll();
                $data['BUSINESSCode'] = $_CommonCode['BUSINESS'];
            }else{
                //$_GROUPCode = $this->getGlobalGroupCode();
                $_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'),$this->session->userdata('groupidx'),$this->session->userdata('parentgroup')  );
            }
            $data['GROUPCode'] = $_GROUPCode['Group'];
            $ServiceID = ($_CommonCode['ProjectMode'][$dbdata['Records'][0]['ProjectMode']]['code']?$_CommonCode['ProjectMode'][$dbdata['Records'][0]['ProjectMode'] ]['code']:'PROJECT');
            $channelInfo =  array();
            $channelInfo[0]['ChannelPoolID'] = $dbdata['Records'][0]['ProjectNo'];
            $channelInfo[0]['ChannelName'] = $dbdata['Records'][0]['ProjectTitle'];
            $channelInfo[0]['IsUse'] = true;
            $channelInfo[0]['IsUseName'] = 'Y';
            $channelInfo[0]['ServiceID'] = $ServiceID;
            $channelInfo[0]['ChatRoomIdx'] = $dbdata['Records'][0]['ProjectIdx'];
            $channelInfo[0]['IsUseChat'] = true;
            $channelInfo[0]['IsVisiblePlayCnt'] = true;


            $data['ProjectIdx'] = $idx;
            $data['IsChat'] = $dbdata['Records'][0]['IsChat'];
            $data['ProjectNo'] = $dbdata['Records'][0]['ProjectNo'] ? $dbdata['Records'][0]['ProjectNo'] : null;
            $data['projectTitle'] = $dbdata['Records'][0]['ProjectTitle'] ? $dbdata['Records'][0]['ProjectTitle'] : 'ProjectName is Null';
            $data['UserName'] = $this->session->userdata('name');
            $data['RegName'] = $dbdata['Records'][0]['RegName'];

            if ( $dbdata['Records'][0]['ProjectIdx'] == 2 || $dbdata['Records'][0]['ProjectIdx'] == 5) {
                $data['LoginSession'] = $this->session->userdata();
                $this->loadViews("manager/project/dailyview", $this->global, $data, NULL);
            }else{
                $data['LoginSession'] = $this->session->userdata();
                $this->loadViews("manager/project/view", $this->global, $data, NULL);
            }

        }

    }

    function allview( $gidx =  null,$_PARENT_GROUP = null){

        //$_GROUPCode = $this->getGlobalGroupCode();
        // 각종코드
        $this->config->load('config', true);
        $_CommonCode = $this->config->item('code');
        if ( $this->session->userdata('role') == ROLE_ADMIN ) {
            $_GROUPCode = $this->getGlobalGroupCodeAll($_PARENT_GROUP);
            $data['BUSINESSCode'] = $_CommonCode['BUSINESS'];
        }else{
            $_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'),$this->session->userdata('groupidx') ,$this->session->userdata('parentgroup') );
        }

        $data['GROUPCode'] = $_GROUPCode['Group'];
        $data['PARENT_GROUP'] = $_PARENT_GROUP;
        $this->global['pageTitle'] = 'Hackers Project ALL View';
        $data['CommonCode'] = $_CommonCode;
        $data['GROUPIDX'] = $gidx;
        if( $this->session->userdata("role") < ROLE_MANAGER  && $gidx == null) {
            if ( $this->session->userdata("groupcode") == BASE_DESIGN_TEXT_CODE || $this->session->userdata("groupcode") == BASE_REALTOR_PARENT_CODE || $this->session->userdata("groupcode") == BASE_BASEENGLISH_PARENT_CODE  ) {
                $data['GROUPIDX'] = $this->session->userdata("parentgroup");
            }else{
                $data['GROUPIDX'] = BASE_GROUP_IDX;
            }
        }else if ($this->session->userdata("role") ==  ROLE_MANAGER  && $gidx == null ) {
            $data['GROUPIDX'] = $this->session->userdata("groupidx");

        }
        $_usersearch['where'] = array();
        $_usersearch['where'][] = "M.isDeleted = 0 ";
        $_usersearch['where'][] = "M.roleId in ( 3,9) ";
        $_usersearch['where'][] = "A.DENIED = 'N' ";
        $_usersearch['where'][] = "G.IDX = ".$data['GROUPIDX']." ";
        $_userselect = array(
            "M.*","IFNULL(G.NAME,'무소속') AS GROUP_NAME","A.GROUP_IDX"
        );
        $data['Users'] = $this->project_model->getUserListing($_usersearch, $_userselect);

        $data['LoginSession'] = $this->session->userdata();
        if( $this->session->userdata("role") == ROLE_EMPLOYEE ) {
            $this->load->view ( 'includes/header', $this->global );
            $this->load->view ( 'access' );
            $this->load->view ( 'includes/footer' );
        }else{
            $this->loadViews("manager/project/allview", $this->global, $data, NULL);
        }

    }


    function myview( $uid =  null){
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');

        if ( $uid ==  null ) {
            $data['GROUPIDX'] = $this->session->userdata("userId");
        }else{
            if ( $this->session->userdata("role") == ROLE_EMPLOYEE && $uid !== $this->session->userdata("userId") )  {
                $this->loadThis();
                return false;
            }else{
                $data['GROUPIDX'] = $uid;
            }

        }


        $data['CommonCode'] = $_CommonCode;
        $this->global['pageTitle'] = 'Hackers Project My View';
        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/project/myview", $this->global, $data, NULL);


    }

    function get_events( ){

        $_return_data =  array();

        $PostData = $this->input->get();


        $_search['where'] = array();
        $_search['where'][] = "B.IsDel IS NULL";
        $_search['where'][] = "(( B.sDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( B.eDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        $_search['where'][] = "A.ProjectIdx = ".$PostData['ProjectIdx']." ";

        if ( $PostData['ProjectIdx'] == RESEARCHANDDEVELOPEMENT_IDX ) {
            if ($this->session->userdata("role") == ROLE_MANAGER || $this->session->userdata("role") == ROLE_EMPLOYEE) {
                $_search['where'][] = "( B.RegIDGroup = '" . $this->session->userdata("groupidx") . "' OR B.ToDoIDGroup = '" . $this->session->userdata("groupidx") . "' )  ";
            }else if ($this->session->userdata("role") == ROLE_SUPERVISOR  && !isset($PostData['gidx'])) {
                $_search['where'][] = "( B.GroupCode = '" . $this->session->userdata("groupcode") . "'  )  ";
            } else {
                if (isset($PostData['gidx'])) {
                    if ($PostData['gidx'] > 0) {
                        $_search['where'][] = "B.RegIDGroup = '" . $PostData["gidx"] . "' ";
                    }
                } else {
                    $_search['where'][] = "B.RegIDGroup = '" . BASE_GROUP_IDX . "' ";

                }
            }
        }

        $_select = array(
            "A.ProjectTitle","A.ProjectNo","A.ProjectMode","A.IsChat","A.Permission","B.*","M.name as UserName","M2.name as RegName"
        );

        $dbdata['Records'] = $this->project_model->getJobListing($_search, $_select);
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');

        foreach($dbdata['Records'] as $key => $val) {
            $_return_data[$key]['id'] = $val['ProjectWorkIdx'];
            $_TextBgColor =  $val['ChildMode']?$_CommonCode['ChildMode'][$val['ChildMode']]['color']:"#555555";
            if ( $val['Rate'] > 0 ) {
                $_return_data[$key]['textrate']= "<p class='barWrapper'><span class='barOuter'><span class='barInner' style='width:".$val['Rate']."%;'></span><span class='barLabel'>".$val['Rate']."%</span></span></p>";
            }else{
                $_return_data[$key]['textrate']= "";
            }

            $_return_data[$key]['title'] = $val['UserName']?("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(미지정)</span> ".$val['title']);
            $_return_data[$key]['start'] = $val['sDate'];
            $_return_data[$key]['wstatus'] = $val['Status'];
            $_return_data[$key]['textend'] = $val['eDate'];
            $_return_data[$key]['userid'] = $val['RegID'];
            if ( $val['sDate'] != $val['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$key]['end'] = $eDate;
            }
            $_return_data[$key]['allDay'] = true;

            $_return_data[$key]['todouser'] = $val['UserName'];
            $_return_data[$key]['backgroundColor'] = $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";
            $_return_data[$key]['borderColor'] =  $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";



            $_return_data[$key]['type'] = 1;//프로젝트조회
        }


        echo json_encode(array("events" => $_return_data));
        exit();

    }

    function get_events_daily( ){

        $_return_data =  array();

        $PostData = $this->input->get();


        $_search['where'] = array();
        $_search['where'][] = "B.IsDel IS NULL";
        $_search['where'][] = "(( B.sDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( B.eDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        $_search['where'][] = "A.ProjectIdx = ".$PostData['ProjectIdx']." ";
        if( $this->session->userdata("role") == ROLE_MANAGER || $this->session->userdata("role") == ROLE_EMPLOYEE ) {
            $_search['where'][] = "( B.RegIDGroup = '".$this->session->userdata("groupidx")."' OR B.ToDoIDGroup = '".$this->session->userdata("groupidx")."' )  ";
        }else{
            if ( isset($PostData['gidx'])) {
                if ( $PostData['gidx'] > 0 ) {
                    $_search['where'][] = "B.RegIDGroup = '" . $PostData["gidx"] . "' ";
                }
            }else{
                $_search['where'][] = "B.RegIDGroup = '".BASE_GROUP_IDX."' ";

            }
        }
        $_select = array(
            "A.ProjectTitle","A.ProjectNo","A.ProjectMode","A.IsChat","A.Permission","B.*","M.name as UserName","M2.name as RegName"
        );

        $dbdata['Records'] = $this->project_model->getJobListing($_search, $_select);
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');

        foreach($dbdata['Records'] as $key => $val) {
            $_return_data[$key]['id'] = $val['ProjectWorkIdx'];
            $_TextBgColor =  $val['ChildMode']?$_CommonCode['ChildMode'][$val['ChildMode']]['color']:"#555555";
            if ( $val['Rate'] > 0 ) {
                $_return_data[$key]['textrate']= "<p class='barWrapper'><span class='barOuter'><span class='barInner' style='width:".$val['Rate']."%;'></span><span class='barLabel'>".$val['Rate']."%</span></span></p>";
            }else{
                $_return_data[$key]['textrate']= "";
            }

            $_return_data[$key]['title'] = $val['UserName']?("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(미지정)</span> ".$val['title']);
            $_return_data[$key]['start'] = $val['sDate'];
            $_return_data[$key]['wstatus'] = $val['Status'];
            $_return_data[$key]['textend'] = $val['eDate'];
            $_return_data[$key]['userid'] = $val['RegID'];
            if ( $val['sDate'] != $val['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$key]['end'] = $eDate;
            }
            $_return_data[$key]['allDay'] = true;

            $_return_data[$key]['todouser'] = $val['UserName'];
            $_return_data[$key]['backgroundColor'] = $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";
            $_return_data[$key]['borderColor'] =  $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";



            $_return_data[$key]['type'] = 1;//프로젝트조회
        }


        echo json_encode(array("events" => $_return_data));
        exit();

    }

    function get_events_my( ){

        $_return_data =  array();
        if( $this->session->userdata("userId") == "" )
        {
            echo json_encode(array("events" => $_return_data));
            exit();
        }

        $PostData = $this->input->get();
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');


        //여기서부터 일정
        $_search3['where'] = array();
        $_search3['where'][] = "A.DelID = 0";
        $_search3['where'][] = "A.RegID = '".$PostData["gidx"]."' ";
        $_search3['where'][] = "(  	( A.sDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( A.eDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        $_search3['where'][] = "A.Type NOT IN ( 50 ) ";
        $_select3 = array(
            "A.ScheduleIdx","A.sDate","A.eDate","A.Type","A.RegGroup","A.RegID","A.SubTitle","A.Color","M.name as UserName"
        );

        $_Schedules = $this->project_model->getMyScheduleListing($_search3, $_select3);



        $keyrow = 0;
        foreach($_Schedules as $tkey => $tval) {
            $_return_data[$tkey]['id'] = $tval['ScheduleIdx'];
            if ( $tval['Type'] == 30 || $tval['Type'] == 40 ) {
                $_return_data[$tkey]['title'] = $tval['SubTitle'];
            }else{
                $_return_data[$tkey]['title'] = "<span class='fc_event_user_logo'>(".$tval['UserName'].")</span>".$tval['SubTitle'];

            }

            $_return_data[$tkey]['start'] = $tval['sDate'];
            if ( $tval['sDate'] != $tval['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($tval['eDate'] . "+1 days"));
                $_return_data[$tkey]['end'] = $eDate;
                $_return_data[$tkey]['textend'] = $tval['eDate'] ;
            }else{
                if ( $tval['eDate'] == null ) {
                    $_return_data[$tkey]['end'] = $tval['sDate'];
                    $_return_data[$tkey]['textend'] = $tval['sDate'] ;
                }else{
                    $_return_data[$tkey]['end'] = $tval['eDate'];
                    $_return_data[$tkey]['textend'] = $tval['sDate'] ;
                }

            }
            $_return_data[$tkey]['textrate']= "";
            $_return_data[$tkey]['allDay'] = true;
            $_return_data[$tkey]['todouser'] = $tval['UserName'];
            $_return_data[$tkey]['borderColor'] = $tval['Color'];
            $_return_data[$tkey]['backgroundColor'] = $tval['Color'];
            $_return_data[$tkey]['type'] = 2;//일정조회
            $keyrow = $tkey == 0 ? $tkey+1 : $tkey;
        }

        $_search['where'] = array();
        $_search['where'][] = "B.DelID = ''";
        $_search['where'][] = "B.ToDoID = '".$PostData["gidx"]."' ";

        $_select = array(
            "A.ProjectTitle","A.ProjectNo","A.ProjectMode","A.IsChat","A.Permission","M.name as UserName","M2.name as RegName","B.Rate"
            ,"B.title","B.ProjectWorkIdx","IFNULL(B.sDate,LEFT(B.RegDatetime,10)) AS sDate","IFNULL(B.eDate,LEFT(B.RegDatetime,10)) AS eDate","B.Status","B.ToDoIDGroup","B.RegID","B.ToDoID","B.Rate","B.DelID","B.RegID","B.ChildMode"
        );
        $dbdata['Records'] = $this->project_model->getJobListing($_search, $_select);


        $keyrow2 = $keyrow;// > 0 ? $keyrow+1 : 0;
        foreach($dbdata['Records'] as $key => $val) {
            $_return_data[$keyrow2]['id'] = $val['ProjectWorkIdx'];
            $_TextBgColor =  $val['ChildMode']?$_CommonCode['ChildMode'][$val['ChildMode']]['color']:"#555555";
            if ( $val['DelID'] > 0 ) {
                $_return_data[$keyrow2]['title'] = $val['UserName']?("<strike><span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo'>(미지정)</span> ".$val['title']."</strike>");
            }else{
                /*$_return_data[$keyrow2]['title'] = $val['UserName']?("<span class='fc_event_user_logo'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo'>(미지정)</span> ".$val['title']);*/
                $_return_data[$keyrow2]['title'] = $val['UserName']?("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(미지정)</span> ".$val['title']);
            }

            if ( $val['Rate'] > 0 ) {
                $_return_data[$keyrow2]['textrate']= "<p class='barWrapper'><span class='barOuter'><span class='barInner' style='width:".$val['Rate']."%;'></span><span class='barLabel'>".$val['Rate']."%</span></span></p>";
            }else{
                $_return_data[$keyrow2]['textrate']= "";
            }

            $_return_data[$keyrow2]['start'] = $val['sDate'];
            $_return_data[$keyrow2]['wstatus'] = $val['Status'];
            if ( $val['sDate'] != $val['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$keyrow2]['end'] = $eDate;
                $_return_data[$keyrow2]['textend'] = $val['eDate'] ;
            }else{
                if ( $val['eDate'] == null ) {
                    $_return_data[$keyrow2]['end'] = $val['sDate'];
                    $_return_data[$keyrow2]['textend'] = $val['sDate'] ;
                }else{
                    $_return_data[$keyrow2]['end'] = $val['eDate'];
                    $_return_data[$keyrow2]['textend'] = $val['sDate'] ;
                }
            }
            $_return_data[$keyrow2]['allDay'] = true;
            $_return_data[$keyrow2]['todouser'] = $val['UserName'];
            $_return_data[$keyrow2]['userid'] = $val['RegID'];
            $_return_data[$keyrow2]['backgroundColor'] = $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";
            $_return_data[$keyrow2]['borderColor'] =  $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";

            $_return_data[$keyrow2]['type'] = 1;//프로젝트조회
            $keyrow2++;

        }



        echo json_encode(array("events" => $_return_data));
        exit();

    }


    function get_events_all( ){

        $_return_data =  array();
        if( $this->session->userdata("role") == ROLE_EMPLOYEE )
        {
            echo json_encode(array("events" => $_return_data));
            exit();
        }

        $PostData = $this->input->get();
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');


        //여기서부터 일정
        $_search3['where'] = array();
        $_search3['where'][] = "A.DelID = 0";
        $_search3['where'][] = "A.GroupCode = '".$this->session->userdata('groupcode')."' ";
        if( $this->session->userdata("role") == ROLE_MANAGER ) {
            $_search3['where'][] = "A.RegGroup = '".$this->session->userdata("groupidx")."' ";
        }else{
            if ( isset($PostData['gidx'])) {
                if ( $PostData['gidx'] > 0 ) {
                    $_search3['where'][] = "A.RegGroup = '" . $PostData["gidx"] . "' ";
                }
            }else{

                if ( $this->session->userdata("groupcode") == BASE_DESIGN_TEXT_CODE ){
                    $_search3['where'][] = "A.RegGroup in ( ".$_CommonCode['DESIGNGROUP']." ) ";
                }else if ( $this->session->userdata("groupcode") == BASE_PLANNING_TEXT_CODE ){
                    $_search3['where'][] = "A.RegGroup in ( ".$_CommonCode['PLANNINGGROUP']." ) ";
                }else if ( $this->session->userdata("groupcode") == BASE_REALTOR_PARENT_CODE ){
                    $_search3['where'][] = "A.RegGroup in ( ".$_CommonCode['REALTORGROUP']." ) ";
                }else if ( $this->session->userdata("groupcode") == BASE_BASEENGLISH_PARENT_CODE ){
                    $_search3['where'][] = "A.RegGroup in ( ".$_CommonCode['BASEENGLISHGROUP']." ) ";
                }else{
                    $_search3['where'][] = "A.RegGroup = '".BASE_GROUP_IDX."' ";
                }

            }
        }

        $_search3['where'][] = "(  	( A.sDate between '".$PostData['start']."' AND '".$PostData['end']."' )	OR ( A.eDate between '".$PostData['start']."'  AND '".$PostData['end']."' ))";
        $_search3['where'][] = "A.Type NOT IN ( 50 ) ";
        $_select3 = array(
            "A.ScheduleIdx","A.sDate","A.eDate","A.Type","A.RegGroup","A.RegID","A.SubTitle","A.Color","M.name as UserName"
        );

        $_Schedules = $this->project_model->getMyScheduleListing($_search3, $_select3);
        $keyrow = 0;
        foreach($_Schedules as $tkey => $tval) {
            $_return_data[$tkey]['id'] = $tval['ScheduleIdx'];
            if ( $tval['Type'] == 30 || $tval['Type'] == 40 ) {
                $_return_data[$tkey]['title'] = $tval['SubTitle'];
            }else{
                $_return_data[$tkey]['title'] = "<span class='fc_event_user_logo'>(".$tval['UserName'].")</span>".$tval['SubTitle'];

            }

            $_return_data[$tkey]['start'] = $tval['sDate'];
            if ( $tval['sDate'] != $tval['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($tval['eDate'] . "+1 days"));
                $_return_data[$tkey]['end'] = $eDate;
            }
            $_return_data[$tkey]['textrate']= "";
            $_return_data[$tkey]['allDay'] = true;
            $_return_data[$tkey]['todouser'] = $tval['UserName'];
            $_return_data[$tkey]['todouserid'] = $tval['RegID'];
            $_return_data[$tkey]['borderColor'] = $tval['Color'];
            $_return_data[$tkey]['backgroundColor'] = $tval['Color'];
            $_return_data[$tkey]['type'] = 2;//일정조회
            $keyrow = $tkey;
        }

        $_search['where'] = array();
        $_search['where'][] = "B.DelID = ''";
        $_search['where'][] = "B.GroupCode = '".$this->session->userdata('groupcode')."' ";

        if( $this->session->userdata("role") == ROLE_MANAGER ) {
            $_search['where'][] = "( B.RegIDGroup = '".$this->session->userdata("groupidx")."' OR B.ToDoIDGroup = '".$this->session->userdata("groupidx")."' )  ";

        }

        if ( isset($PostData['gidx'])) {
            if ( $PostData['gidx'] > 0 ) {
                $_search['where'][] = "( B.RegIDGroup = '" . $PostData["gidx"] . "' OR B.ToDoIDGroup = '" . $PostData["gidx"] . "' )  ";

            }else{
                if ( $this->session->userdata("groupcode") == BASE_DESIGN_TEXT_CODE ){
                    $_search['where'][] = "B.RegIDGroup in ( ".$_CommonCode['DESIGNGROUP']." ) ";
                }else if ( $this->session->userdata("groupcode") == BASE_PLANNING_TEXT_CODE ){
                    $_search['where'][] = "B.RegIDGroup in ( ".$_CommonCode['PLANNINGGROUP']." ) ";
                }else if ( $this->session->userdata("groupcode") == BASE_REALTOR_PARENT_CODE ){
                    $_search['where'][] = "A.RegGroup in ( ".$_CommonCode['REALTORGROUP']." ) ";
                }else if ( $this->session->userdata("groupcode") == BASE_BASEENGLISH_PARENT_CODE ){
                    $_search['where'][] = "A.RegGroup in ( ".$_CommonCode['BASEENGLISHGROUP']." ) ";
                }else{
                    $_search['where'][] = "B.RegIDGroup = '".BASE_GROUP_IDX."' ";
                }
            }
        }



        $_select = array(
            "A.ProjectTitle","A.ProjectNo","A.ProjectMode","A.IsChat","A.Permission","M.name as UserName","M2.name as RegName","B.Rate"
        ,"B.title","B.ProjectWorkIdx","IFNULL(B.sDate,LEFT(B.RegDatetime,10)) AS sDate","IFNULL(B.eDate,LEFT(B.RegDatetime,10)) AS eDate","B.Status","B.ToDoIDGroup","B.RegID","B.ToDoID","B.Rate","B.DelID","B.RegID","B.ChildMode"
        );
        $dbdata['Records'] = $this->project_model->getJobListing($_search, $_select);


        $keyrow2 = $keyrow > 0 ? $keyrow+1 : 0;
        foreach($dbdata['Records'] as $key => $val) {
            $_return_data[$keyrow2]['id'] = $val['ProjectWorkIdx'];
            $_TextBgColor =  $val['ChildMode']?$_CommonCode['ChildMode'][$val['ChildMode']]['color']:"#555555";
            if ( $val['DelID'] > 0 ) {
                $_return_data[$keyrow2]['title'] = $val['UserName']?("<strike><span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo'>(미지정)</span> ".$val['title']."</strike>");
            }else{
                /*$_return_data[$keyrow2]['title'] = $val['UserName']?("<span class='fc_event_user_logo'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo'>(미지정)</span> ".$val['title']);*/
                $_return_data[$keyrow2]['title'] = $val['UserName']?("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(".$val['UserName'].")</span> ".$val['title']):("<span class='fc_event_user_logo' style='background-color:".$_TextBgColor."'>(미지정)</span> ".$val['title']);
            }

            if ( $val['Rate'] > 0 ) {
                $_return_data[$keyrow2]['textrate']= "<p class='barWrapper'><span class='barOuter'><span class='barInner' style='width:".$val['Rate']."%;'></span><span class='barLabel'>".$val['Rate']."%</span></span></p>";
            }else{
                $_return_data[$keyrow2]['textrate']= "";
            }

            $_return_data[$keyrow2]['start'] = $val['sDate'];
            $_return_data[$keyrow2]['wstatus'] = $val['Status'];
            if ( $val['sDate'] != $val['eDate'] ) {
                $eDate = date('Y-m-d',strtotime($val['eDate'] . "+1 days"));
                $_return_data[$keyrow2]['end'] = $eDate;
            }
            $_return_data[$keyrow2]['allDay'] = true;
            $_return_data[$keyrow2]['todouser'] = $val['UserName'];
            $_return_data[$keyrow2]['userid'] = $val['RegID'];
            $_return_data[$keyrow2]['todouserid'] = $val['ToDoID']?$val['ToDoID']:$val['RegID'];
            $_return_data[$keyrow2]['backgroundColor'] = $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";
            $_return_data[$keyrow2]['borderColor'] =  $_CommonCode['WorksStatus'][$val['Status']]['color']?$_CommonCode['WorksStatus'][$val['Status']]['color']:"00c0ef";

            $_return_data[$keyrow2]['type'] = 1;//프로젝트조회
            $keyrow2++;

        }

        echo json_encode(array("events" => $_return_data));
        exit();

    }
}

?>