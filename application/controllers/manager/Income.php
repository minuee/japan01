<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Income extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('income_model');
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
            //$searchText = $this->security->xss_clean($this->input->post('searchText'));
            $this->global['pageTitle'] = 'SCM Portal Admin : 입금관리';
            // 출판법인
            $codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
            $data["codeBusiness"] = $codeBusiness;

            //조회용 코드
            $data['codename'] = $this->getCodeName(array('SAP_SPART','SAP_KONDM','SAP_VKORG','SAP_MATKL','COMP_TYPE')); // 카테고리 코드

            $data["search"] = $this->input->post();
            $this->loadViews("manager/income", $this->global, $data, NULL);
        }
    }


    function ajax_list(){

        //$searchText = $this->security->xss_clean($this->input->post('searchText'));

        $_search = $this->security->xss_clean($this->input->get());

        //print_r($this->input->get());
        //$data['searchText'] = $searchText;
        $this->load->library('pagination');
        $count = $this->income_model->userListingCount($_search);

        $returns = $this->paginationCompress( "manager/income/index", $count, 10 );

        $returns['segment'] = $_search["paging"] > 0?$_search["paging"]:null;

        $_select = array(
           "A.*","D.Name AS PublishCorporationCCDName"
        );
        $data['userRecords'] = $this->income_model->userListing($_search, $returns["page"], $returns["segment"] ,$_select);
        $this->global['pageTitle'] = 'SCM Portal Admin : 입금관리';

        $data['totalRecords'] = $count;
        $data['now_page'] = is_numeric($returns["segment"])?$returns["segment"]:0;


        // 출판법인
        $codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
        $data["codeBusiness"] = $codeBusiness;

        //조회용 코드
        $data['codename'] = $this->getCodeName(array('SAP_SPART','SAP_KONDM','SAP_VKORG','SAP_MATKL','COMP_TYPE')); // 카테고리 코드

        $data["search"] = $this->input->get();


        echo json_encode([
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $data['userRecords']
        ]);
        exit;
    }

    function popview(){

        return $this->load->view("manager/income/popview",null,null,false);

    }


    function popdetail( $idx = null){
        $data['OrderingDataIdx'] = $idx;


        $_select = array(
            "A.*","D.Name AS PublishCorporationCCDName","C.CompanyName"
            ,"CASE WHEN A.CASTFlag = 'I' THEN '입금' WHEN A.CASTFlag = 'R' THEN '계좌할당' ELSE '취소' END AS strCASTFlag "
        );
        $dbresult = $this->income_model->getOrderingDataInfo($_select,$idx);
        if ( $dbresult['result'] === false ) {
            alert_back($dbresult['message']);
        }
        $data['OrderingData'] = $dbresult['row'];

        //법인
        // 출판법인
        $codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
        $data["PublishCorporationList"] = $codeBusiness;


        return $this->load->view("manager/income/popdetail",$data);

    }

    function update(){

        $PostData = $this->input->post();


        $this->load->library('form_validation');

        $this->form_validation->set_rules('OrderingDataIdx','입금상세정보 인덱스 키','trim|required');
        if($this->form_validation->run() == FALSE)
        {
            $_result['result'] = false;
            $_result['message'] = '필수 항목이 누락되었습니다.';
            echo json_encode($_result);exit;
        }else{

            $dbresult = $this->income_model->updatedata($PostData);
            echo json_encode($dbresult);
            exit;

        }
    }


}

?>