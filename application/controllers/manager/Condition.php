<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Condition extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('condition_model');
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

            $_search = $this->security->xss_clean($this->input->post());

            //$data['searchText'] = $searchText;
            $this->load->library('pagination');
            $count = $this->condition_model->conditionListingCount($_search);

            $returns = $this->paginationCompress( "manager/condition/index", $count, 10 );
            $data['userRecords'] = $this->condition_model->conditionListing($_search, $returns["page"], $returns["segment"]);
            $this->global['pageTitle'] = 'SCM Portal Admin : 가격결정';

            $data['totalRecords'] = $count;
            $data['now_page'] = is_numeric($returns["segment"])?$returns["segment"]:0;

            // 출판법인
            $codeBusiness = $this->code(array('SAP_VKORG'),'',array('Remark01'=>1) )["SAP_VKORG"];
            $data["codeBusiness"] = $codeBusiness;

            //조회용 코드
            $data['codename'] = $this->getCodeName(array('SAP_SPART','SAP_KONDM','SAP_VKORG','SAP_MATKL','COMP_TYPE')); // 카테고리 코드

            $data["search"] = $this->input->post();
            $this->loadViews("manager/condition", $this->global, $data, NULL);
        }
    }
    

}

?>