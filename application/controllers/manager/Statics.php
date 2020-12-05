<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Board (BoardController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Statics extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statics_model');
        $this->load->helper("html");
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */
    function index()
    {

        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {

            $this->global['pageTitle'] = 'Hackers Project Report';

            // 각종코드
            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            $data['BUSINESSCode'] = $_CommonCode['BUSINESS'];
            foreach($_CommonCode['ChildMode'] as $key => $val ) {

                $_tsearch['where'] = array();
                $_tsearch['where'][] = "IsDel IS NULL";
                $_tsearch['where'][] = "ChildMode = ".$key."";
                $_count = $this->statics_model->ProjectWorkTotalCount($_tsearch);
                $_CommonCode['ChildMode'][$key]['Totalcount'] = $_count;

                $_t2search = "ChildMode = ".$key."" ;
                $_t2select = array(
                    "COUNT(if(`Status`= 1, 1, null)) as todo_cnt",
                    "COUNT(if(`Status` = 2, 1, null)) AS doing_cnt",
                    "COUNT(if(`Status` = 9 , 1, null)) as done_cnt"
                );
                $_TypeCount = $this->statics_model->ProjectWorkType($_t2search, $_t2select);
                $_CommonCode['ChildMode'][$key]['Todocount'] = $_TypeCount[0]['todo_cnt'];
                $_CommonCode['ChildMode'][$key]['Doingcount'] = $_TypeCount[0]['doing_cnt'];
                $_CommonCode['ChildMode'][$key]['Donecount'] = $_TypeCount[0]['done_cnt'];
            }
            //각종통계
            $_search['where'] = array();
            $_search['where'][] = "DelID IS NULL";
            $projectcount = $this->statics_model->ProjectTotalCount($_search);
            $data['ProjectTotalCount'] = $projectcount;

            $_search['where'] = array();
            $_search['where'][] = "IsDel IS NULL";
            $projectworkcount = $this->statics_model->ProjectWorkTotalCount($_search);
            $data['ProjectWorkTotalCount'] = $projectworkcount;

            $_search['where'] = array();
            $_search['where'][] = "IsDel IS NULL";
            $_search['where'][] = "Status in ( 1,2 )";
            $projectworkrate = $this->statics_model->ProjectWorkRate($_search);
            $data['ProjectWorkRate'] = $projectworkrate[0]['AVGRate'];


            $_search['where'] = array();
            $_search['where'][] = "isDeleted = 0 ";
            $_search['where'][] = "hackersidx > 0 ";
            $usercount = $this->statics_model->UserTotalCount($_search);
            $data['UserTotalCount'] = $usercount;

            $_hsearch['where'] = array();
            $_hsearch['where'][] = "A.IsDel IS NULL";
            $_hselect = array(
                "A.ProjectWorkIdx","A.title","A.Status","A.RegDatetime","CASE WHEN A.Status = 1 THEN '#605ca8' WHEN A.Status = 2 THEN '#137b12' ELSE '#000000' END AS StatusColor "
                ,"CASE WHEN A.Status = 1 THEN 'ToDo' WHEN A.Status = 2 THEN 'Doing' ELSE 'Done' END AS StatusText"
                ,"IFNULL(M2.name,M.name) as TodoName","IFNULL(G2.NAME,G.name) as GroupName"
            );
            $data['RecentlyWorks'] = $this->statics_model->getRecentlyWorks($_hsearch, $_hselect);

            //투입시간
            $_search['where'] = array();
            $_select = array(
                "SUM(A.DoingTime) AS SumDoingTime","A.WorkDate"
            );
            $data['RecentlyWorkTime'] = $this->statics_model->getRecentlyWorkTime($_search, $_select);

            //생성 돈
            $_search['where'] = array();
            $_select = array(
                "SUM( CASE WHEN A.ismode ='done' THEN 1 ELSE 0 END ) AS SumDoneCount","A.WorkDate","S.SumProjectWork"
            );
            $data['RecentlyWorkType'] = $this->statics_model->getRecentlyWorkType($_search, $_select);

            //Rank 1
            $_search['where'] = array();
            $_select = array(
                "S.SumAllProjectWork","S2.SumDoneProjectWork","M.name AS UserName","G.NAME AS GroupName"
            );
            $data['RankManyWorks'] = $this->statics_model->getRankManyWorks($_search, $_select);

            //Rank 2
            $_search['where'] = array();
            $_select = array(
                "S.ToDoID", "AVG(S2.SumDoingTime / S.Foretime * 100) AS Rate","M.name AS UserName","G.NAME AS GroupName"
            );
            $data['RankGoodWorks'] = $this->statics_model->getRankGoodWorks($_search, $_select);

            $_usersearch['where'] = array();
            $_usersearch['where'][] = "M.isDeleted = 0 ";
            $_usersearch['where'][] = "M.roleId in ( 2,3,9) ";
            $_usersearch['where'][] = "A.DENIED = 'N' ";
            $_userselect = array(
                "M.*","IFNULL(G.NAME,'무소속') AS GROUP_NAME","A.GROUP_IDX","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME"
            );
            $data['Users'] = $this->statics_model->getUserListing($_usersearch, $_userselect);


            //Report 미등록자 최근 24
            $weekday = date("N", strtotime(date("Y-m-d")));
            if ( $weekday  == 0  ) {
                $_termday = 2;
            }else if( $weekday  == 1  ) {
                $_termday = 3;
            }else{
                $_termday = 1;
            }
            $_rsearch['where'] = array();
            $_rsearch['where'][] = "M.isDeleted = 0 ";
            $_rsearch['where'][] = "M.roleId in (3,9) ";
            $_rsearch['where'][] = "A.DENIED = 'N' ";
            $_darray_allcode = "";
            $_dtmparray_code = $_CommonCode['DEVELOPEMENTGROUP'];
            foreach ($_dtmparray_code as $key => $val) {
                $_darray_allcode .= $val . ",";
            }

            $_dtmparray_code2 = $_CommonCode['DESIGNGROUP'];
            foreach ($_dtmparray_code2 as $key => $val) {
                $_darray_allcode .= $val . ",";
            }
            $_dtmparray_code3 = $_CommonCode['PLANNINGGROUP'];
            foreach ($_dtmparray_code3 as $key => $val) {
                $_darray_allcode .= $val . ",";
            }
            $_dtmparray_code4 = $_CommonCode['REALTORGROUP'];
            foreach ($_dtmparray_code4 as $key => $val) {
                $_darray_allcode .= $val . ",";
            }
            $_dtmparray_code5 = $_CommonCode['BASEENGLISHGROUP'];
            foreach ($_dtmparray_code5 as $key => $val) {
                $_darray_allcode .= $val . ",";
            }
            $_darray_allcode2 = substr($_darray_allcode, 0, -1);

            $_rsearch['where'][] = "A.GROUP_IDX  IN ( " . $_darray_allcode2 . " )";
            $_rselect = array(
                "M.name AS UserName", "IFNULL(G.NAME,'무소속') AS GROUP_NAME", "CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","T.ScheduleIdx"
            );
            $data['ReportNotUser'] = $this->statics_model->getNotReoprtUseer($_rsearch, $_rselect,$_termday);


            $data['CommonCode'] = $_CommonCode;
            $this->loadViews("manager/statics/index", $this->global, $data, NULL);
        }
    }

    function chartupdate(){
        $PostData = $this->input->POST();

        //투입시간
        $_search['where'] = array();
        if ( $PostData['UserIdx'] ) {
            $_search['where'][] = "A.userId = " . $PostData['UserIdx'] . " ";
        }
        if ( $PostData['TeamCode'] ) {
            $_search['where'][] = "A.userIdGroup = " . $PostData['TeamCode'] . " ";
        }
        $_select = array(
            "SUM(A.DoingTime) AS SumDoingTime","A.WorkDate"
        );
        $RecentlyWorks = $this->statics_model->getRecentlyWorkTime($_search, $_select);
        $today = date("Y-m-d");
        $area_chart =array();
        for($i = 0; $i <= 7; $i++) {
            $_checkdays =date('Y-m-d', strtotime('-'.$i.' days', strtotime($today)));
            $_is_worktime = 0;
            foreach( $RecentlyWorks  as $key => $val ) {
                if ( $_checkdays == $val['WorkDate'] )   {
                    $_is_worktime = number_format($val['SumDoingTime']/60,1);
                }
            }
            array_push($area_chart, array( 'y' => $_checkdays , 'item1' => $_is_worktime));

        }
        echo json_encode($area_chart); exit;
        exit;

    }

    function getRecentlyWorks(){

        $PostData = $this->input->POST();

        $_hsearch['where'] = array();
        $_hsearch['where'][] = "A.IsDel IS NULL";
        $_hsearch['where'][] = "A.ProjectWorkIdx > ".$PostData['RastWorkIdx']." ";
        $_hselect = array(
            "A.ProjectWorkIdx","A.title","A.Status","A.RegDatetime","CASE WHEN A.Status = 1 THEN '#605ca8' WHEN A.Status = 2 THEN '#137b12' ELSE '#000000' END AS StatusColor "
        ,"CASE WHEN A.Status = 1 THEN 'ToDo' WHEN A.Status = 2 THEN 'Doing' ELSE 'Done' END AS StatusText"
        ,"IFNULL(M2.name,M.name) as TodoName","IFNULL(G2.NAME,G.name) as GroupName"
        );
        $data['RecentlyWorks'] = $this->statics_model->getRecentlyWorks($_hsearch, $_hselect,1);

        $RecentlyWorksCount = count($data['RecentlyWorks']);
        $recentlyWorksList = [];
        if ( $RecentlyWorksCount > 0 ) {
            foreach($data['RecentlyWorks'] as $key =>  $row) {
                $recentlyWorksList[$key]['MessageData'] = "<tr class='isnewdata bg-light-blue RecentlyWorkList'><td class='noh_ellipsis'>".htmlspecialchars($row['title'])."</td><td>".$row['TodoName']."</td><td>".$row['GroupName']."</td><td><span class='label' style='background-color: ".$row['StatusColor']."'>".$row['StatusText']."</span></td></tr>";
                $recentlyWorksList[$key]['ProjectWorkIdx'] = $row['ProjectWorkIdx'];
            }
        }

        $_search['where'] = array();
        $_search['where'][] = "IsDel IS NULL";
        $ProjectWorkTotalCount = $this->statics_model->ProjectWorkTotalCount($_search);

        $_search['where'] = array();
        $_search['where'][] = "DelID IS NULL";
        $ProjectTotalCount = $this->statics_model->ProjectTotalCount($_search);

        echo json_encode(["messageList"=>$recentlyWorksList, "totalCount"=>$RecentlyWorksCount ,"ProjectWorkTotalCount"=>$ProjectWorkTotalCount ,"ProjectTotalCount"=>$ProjectTotalCount ]);
        exit;

    }



}

?>