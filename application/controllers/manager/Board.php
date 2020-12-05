<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Board (BoardController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Board extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model');
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

            $this->global['pageTitle'] = 'SCM Portal Admin : 게시판';

            $this->loadViews("manager/board/list", $this->global, null, NULL);
        }
    }


    function ajax_list(){

        $this->load->library('pagination');
        $_request = $this->security->xss_clean($this->input->get());

        $_search['where'] = array();
        if(isset($_request['BoardName'])  && !empty($_request['BoardName']) ) {
            $_search['where'][] = "A.BoardName = '".$_request['BoardName']."' "; ;
        }
        if(isset($_request['searchText']) && !empty($_request['searchText']) && !empty($_request['searchSubject'])) {
            $_search['where'][] = "A.".$_request['searchSubject']."  LIKE '%".$_request['searchText']."%'";
        }

        if ( $this->input->get('startDate') && $this->input->get('endDate') ) {
            $_search['where'][] = 'A.RegDatetime >= str_to_date(\''.$this->input->get('search_start_date').' 00:00:00\' , \'%Y.%m.%d %H:%i:%s\')';
            $_search['where'][] = 'A.RegDatetime <= str_to_date(\''.$this->input->get('search_end_date').' 23:59:59\' , \'%Y.%m.%d %H:%i:%s\')';
        }

        $count = $this->board_model->ListingCount($_search);

        $returns = $this->paginationCompress( "manager/income/index", $count, 10 );

        $returns['segment'] = $_request["paging"] > 0?$_request["paging"]:null;

        $_select = array(
            "A.*","CASE WHEN A.BoardName = 'Notice' THEN '공지' ELSE 'FAQ' END AS reBoardName"
            ,"CASE WHEN A.IsTemp = 1 THEN '임시저장' ELSE '' END AS StatusName"
            ,"CASE WHEN A.Permission = 'Part' THEN '지정업체만' WHEN A.Permission = 'Hidden' THEN '숨김' ELSE '전체' END AS PermissionName"
            ,"JSON_EXTRACT(A.TargetPlant ,'$[*]') as TargetPlant"
            ,"REGEXP_REPLACE(JSON_EXTRACT(A.TargetPlant ,'$[*]'),'[`\"[\"\]','') as TargetPlant2"
        );
        $data['userRecords'] = $this->board_model->Listing($_search, $returns["page"], $returns["segment"] ,$_select);
        $this->global['pageTitle'] = 'SCM Portal Admin : 게시판';

        $data['totalRecords'] = $count;
        $data['now_page'] = is_numeric($returns["segment"])?$returns["segment"]:0;


        // 출판법인
        $codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
        $data["codeBusiness"] = $codeBusiness;

        $data["search"] = $this->input->get();

        echo json_encode([
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $data['userRecords']
        ]);
        exit;
    }

    function regist()
    {

        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {

            $this->global['pageTitle'] = 'SCM Portal Admin : 게시판';

            // 출판법인
            $codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
            $data["codeBusiness"] = $codeBusiness;

            $this->loadViews("manager/board/regist", $this->global, $data, NULL);
        }
    }


    function insert(){

        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {


            $postData = $this->input->post();
            $this->load->library('form_validation');

            $this->form_validation->set_rules('Title','제목','trim|required|');
            $this->form_validation->set_rules('BoardName','구분','trim|required|');

            if($this->form_validation->run() == FALSE){
                $result = false;
                $message = "필수항목이 누락되었습니다";
                echo json_encode(["ret_cd"=>$result, "ret_msg"=>$message ]);
                exit;
            }else{

                $contents = $this->input->post('content');
                $data['Data'] = array();
                $data['Data']['BoardName'] = $this->input->post('BoardName');
                $data['Data']['Title'] = $this->input->post('Title');
                $data['Data']['HTMLContent'] = $contents;
                $data['Data']['Content'] = $contents;
                $data['Data']['Permission'] = $this->input->post('Permission');
                if ( $this->input->post('IsTemp') == 1 ) {
                    $data['Data']['IsTemp'] = $this->input->post('IsTemp');
                }
                //$data['Data']['IsReply'] = ( $this->input->post('IsReply') == 'on' ) ? 1 : 0 ;

                $_PublishCodeIdx = array();
                $_CompanyIdx = array();
                $_StaffIdx = array();
                if ( !empty( $this->input->post('PublishCode'))  ) {
                    foreach ($this->input->post('PublishCode') as $key => $val) {
                        array_push($_PublishCodeIdx, $val);
                    }
                }
                if ( !empty($this->input->post('CompanyIdx')) ) {
                    foreach ($this->input->post('CompanyIdx') as $key2 => $val2) {
                        array_push($_CompanyIdx, $val2);
                    }
                }
                if ( !empty($this->input->post('BoardManager'))) {
                    foreach ($this->input->post('BoardManager') as $key3 => $val3) {
                        array_push($_StaffIdx, $val3);
                    }
                }

                $data['Data']['TargetPlant']  = json_encode($_PublishCodeIdx);
                $data['Data']['Permission'] == 'Part' ? $data['Data']['TargetUser'] = json_encode($_CompanyIdx) : $data['Data']['TargetUser'] = "";
                $data['Data']['TargetInnerUser']  = json_encode($_StaffIdx);


                $dbresult = $this->board_model->insertdata($data);

                $_return = array();
                $_return['ret_cd'] = $dbresult['result'];
                $_return['ret_data']['idx'] = $dbresult['idx'];
                $_return['ret_msg'] = $dbresult['message'];

                echo  json_encode($_return);exit;

            }
        }

    }


    public function imgupload()
    {

        $_return = array();
        if(empty($_FILES) ) {

            $result = false;
            $message = "파일을 선택해주세요";
            echo json_encode(["ret_cd"=>$result, "ret_msg"=>$message ]);
            exit;
        }

        $uploadRealPath = $_SERVER['DOCUMENT_ROOT']."/assets/uploads";
        $uploadPath = "/assets/uploads/images/".date("Ym");
        if(!is_dir($uploadRealPath)){
            mkdir($uploadRealPath);
        }else{
            $uploadRealPath = $uploadRealPath."/images/".date("Ym");
            if(!is_dir($uploadRealPath)){
                mkdir($uploadRealPath);
            }
        }

        $timestamp = time();

        if ($_FILES['photo_file']['error'] == 4 ) {
            $result = false;
            $message = "파일을 선택해주세요";
            echo json_encode(["ret_cd"=>$result, "ret_msg"=>$message ]);
            exit;
        }

        if ($_FILES['photo_file']['error'] == 1 ) {
            $result = false;
            $message = "파일 사이즈는 10MB는 초과해서는 안됩니다.";
            echo json_encode(["ret_cd"=>$result, "ret_msg"=>$message ]);
            exit;
        }

        if(!empty($_FILES) ) {

            $upName = $timestamp."_".$_FILES['photo_file']['name'];    // 파일명 앞에 UNIX 시간을 붙여 중복되지 않게 구분한다.
            $tempFile = $_FILES['photo_file']['tmp_name'];
            $fileszie = $_FILES['photo_file']['size'];
            $image_size = getimagesize($_FILES['photo_file']['tmp_name']);
            $file_width = $image_size[0];
            $targetPath = $uploadRealPath;
            $targetFile = rtrim($targetPath, "/")."/".$upName;

            // 업로드 할 수 있는 파일의 확장자를 지정한다.
            $fileTypes = array('jpg', 'jpeg', 'gif', 'png','JPG', 'JPEG', 'GIF', 'PNG','bmp');
            $fileParts = pathinfo($_FILES['photo_file']['name']);

            if(in_array($fileParts['extension'], $fileTypes)) {
                move_uploaded_file($tempFile, $targetFile);
            } else {
                $result = false;
                $message = "업로드 할 수 없는 형식의 파일입니다";
                echo json_encode(["ret_cd"=>$result, "ret_msg"=>$message ]);
                exit;
            }
        }else{
            alert_back('파일을 선택해주세요');
        }

        $fullUrl = 'http://'.$_SERVER['SERVER_NAME'].$uploadPath."/".$upName;
        $_return['ret_cd'] = true;
        $_return['ret_data']['file_name'] = $upName;
        $_return['ret_data']['file_path'] = $uploadRealPath;
        $_return['ret_data']['file_url'] = $uploadRealPath.'/'.$upName;
        $_return['ret_data']['file_size'] = $fileszie;
        $_return['ret_data']['file_width'] = $file_width;
        $_return['ret_data']['full_url'] = $fullUrl;

        echo  json_encode($_return);exit;

    }

}

?>