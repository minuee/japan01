<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Group extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedIn();
        //$this->load->model('api_model');
        $this->load->model('schedule_model');
    }

    function index ( ) {
        $this->isLoggedIn();

        $this->global ['pageTitle'] = 'Hackers Projects : 기획본부 조직도';
        $data['Group'] =  array();
        $_select = array(
            "G.*"
        );
        $_search['where'] = array();
        $_search['where'][] = "G.DEPTH = 2";
        $_search['where'][] = "G.PARENT = 1 ";

        $_dbresult = $this->schedule_model->getGroupInfo($_select,$_search);
        if (!empty($_dbresult['row'])) {
            foreach($_dbresult['row'] as $key => $val) {
                $data['Group'][$key]['IDX'] = $val['IDX'];
                $data['Group'][$key]['NAME'] = $val['NAME'];

                $_select2 = array(
                    "M.MEMBER_IDX","M.USER_ID","M.USER_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","A.roleId","A.userId"
                );
                $_dbresult2 = $this->schedule_model->getGroupMember($_select2,$val['IDX']);
                $data['Group'][$key]['SUB'] = array();
                $_CNT = 0;
                if ( $_dbresult2 !== null ) {
                    foreach ($_dbresult2['row'] as $key2 => $val2) {
                        $data['Group'][$key]['SUB'][$key2]['MEMBER_IDX'] = $val2['MEMBER_IDX'];
                        $data['Group'][$key]['SUB'][$key2]['USER_ID'] = $val2['USER_ID'];
                        $data['Group'][$key]['SUB'][$key2]['USER_NAME'] = $val2['USER_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['CLASS_NAME'] = $val2['CLASS_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['roleId'] = $val2['roleId'];
                        $data['Group'][$key]['SUB'][$key2]['userId'] = $val2['userId'];
                        $_CNT++;
                    }
                }
                $data['Group'][$key]['COUNT'] = $_CNT;
            }
        }

        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/group/list", $this->global, $data , NULL);
    }

    function view( $_gidx =  null ,$_mode =  null ) {
        $this->isLoggedIn();


        $data['Group'] =  array();
        $_select = array(
            "G.*"
        );
        $_search['where'] = array();
        //$_search['where'][] = "(( G.DEPTH = 3 && G.PARENT = 88 ) || ( G.DEPTH = 2 && G.IDX = 355 ) ) ";

        if ( $_gidx == null ) {
            /*$this->loadThis();
            return false;*/
            $_search['where'][] = "G.DEPTH = 2";
            $_search['where'][] = "G.PARENT = 1 ";
        }else{
            if ( $_mode == null ) {
                $_search['where'][] = "G.DEPTH = 3";
                $_search['where'][] = "G.PARENT = ".$_gidx." ";
            }else{
                $_search['where'][] = "G.DEPTH = 4";
                $_search['where'][] = "G.PARENT = ".$_gidx." ";
            }

        }
        $data['GLOBAL_GROUP_NAME'] = $this->schedule_model->getGroupName($_gidx);
        $this->global ['pageTitle'] = 'Hackers Projects : '. isset($data['GLOBAL_GROUP_NAME'])?$data['GLOBAL_GROUP_NAME']->NAME:'';
        $_dbresult = $this->schedule_model->getGroupInfo($_select,$_search);
        if (!empty($_dbresult['row'])) {
            foreach($_dbresult['row'] as $key => $val) {

                $data['Group'][$key]['IDX'] = $val['IDX'];
                $data['Group'][$key]['NAME'] = $val['NAME'];

                $_select2 = array(
                    "M.MEMBER_IDX","M.USER_ID","M.USER_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","A.roleId","A.userId"
                );
                $_dbresult2 = $this->schedule_model->getGroupMember($_select2,$val['IDX']);
                $data['Group'][$key]['SUB'] = array();
                $_CNT = 0;
                if ( $_dbresult2 !== null ) {
                    foreach ($_dbresult2['row'] as $key2 => $val2) {
                        $data['Group'][$key]['SUB'][$key2]['MEMBER_IDX'] = $val2['MEMBER_IDX'];
                        $data['Group'][$key]['SUB'][$key2]['USER_ID'] = $val2['USER_ID'];
                        $data['Group'][$key]['SUB'][$key2]['USER_NAME'] = $val2['USER_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['CLASS_NAME'] = $val2['CLASS_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['roleId'] = $val2['roleId'];
                        $data['Group'][$key]['SUB'][$key2]['userId'] = $val2['userId'];
                        $_CNT++;
                    }
                }
                $data['Group'][$key]['COUNT'] = $_CNT;
            }
        }else{
            alert_back("더이상 조직이 존재하지 않습니다.");
            exit;
        }

        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/group/view", $this->global, $data , NULL);
    }


    /**
     * This function used to load the first screen of the user
     */
    public function develope()
    {
        $this->global ['pageTitle'] = 'Hackers Projects : 조직도';
        $data['Group'] =  array();
        $_select = array(
            "G.*"
        );
        $_search['where'] = array();
        $_search['where'][] = "(( G.DEPTH = 3 && G.PARENT = 88 ) || ( G.DEPTH = 2 && G.IDX = 355 ) ) ";
        $_dbresult = $this->schedule_model->getGroupInfo($_select,$_search);
        if (!empty($_dbresult['row'])) {
            foreach($_dbresult['row'] as $key => $val) {
                $data['Group'][$key]['IDX'] = $val['IDX'];
                $data['Group'][$key]['NAME'] = $val['NAME'];

                $_select2 = array(
                    "M.MEMBER_IDX","M.USER_ID","M.USER_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","A.roleId","A.userId"
                );
                $_dbresult2 = $this->schedule_model->getGroupMember($_select2,$val['IDX']);
                $data['Group'][$key]['SUB'] = array();
                $_CNT = 0;
                if ( $_dbresult2 !== null ) {
                    foreach ($_dbresult2['row'] as $key2 => $val2) {
                        $data['Group'][$key]['SUB'][$key2]['MEMBER_IDX'] = $val2['MEMBER_IDX'];
                        $data['Group'][$key]['SUB'][$key2]['USER_ID'] = $val2['USER_ID'];
                        $data['Group'][$key]['SUB'][$key2]['USER_NAME'] = $val2['USER_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['CLASS_NAME'] = $val2['CLASS_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['roleId'] = $val2['roleId'];
                        $data['Group'][$key]['SUB'][$key2]['userId'] = $val2['userId'];
                        $_CNT++;
                    }
                }
                $data['Group'][$key]['COUNT'] = $_CNT;
            }
        }

        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/group/layout", $this->global, $data , NULL);
    }

    /**
     * This function used to load the first screen of the user
     */
    public function design()
    {
        $this->global ['pageTitle'] = 'Hackers Projects : 조직도';
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');
        $_tmparray_code = $_CommonCode['DESIGNGROUP'];
        $_array_code = "";
        foreach($_tmparray_code as  $key => $val ){
            $_array_code .= $val.",";
        }
        $_array_code2 = substr($_array_code,0,-1) ;

        $data['Group'] =  array();
        $_select = array(
            "G.*"
        );

        $_search['where'] = array();
        //$_search['where'][] = "( G.DEPTH = 3 AND G.PARENT = ".BASE_DESIGN_PARENT_CODE." ) OR ( G.DEPTH = 2 AND G.PARENT = ".BASE_DESIGN_PARENT2_CODE." ) ";
        $_search['where'][] = "(G.IDX in ( ".$_array_code2." ))";
        $_dbresult = $this->schedule_model->getGroupInfo($_select,$_search);
        if (!empty($_dbresult['row'])) {
            foreach($_dbresult['row'] as $key => $val) {
                $data['Group'][$key]['IDX'] = $val['IDX'];
                $data['Group'][$key]['NAME'] = $val['NAME'];

                $_select2 = array(
                    "M.MEMBER_IDX","M.USER_ID","M.USER_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","A.roleId","A.userId"
                );
                $_dbresult2 = $this->schedule_model->getGroupMember($_select2,$val['IDX']);
                $data['Group'][$key]['SUB'] = array();
                $_CNT = 0;
                if ( $_dbresult2 !== null ) {
                    foreach ($_dbresult2['row'] as $key2 => $val2) {
                        $data['Group'][$key]['SUB'][$key2]['MEMBER_IDX'] = $val2['MEMBER_IDX'];
                        $data['Group'][$key]['SUB'][$key2]['USER_ID'] = $val2['USER_ID'];
                        $data['Group'][$key]['SUB'][$key2]['USER_NAME'] = $val2['USER_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['CLASS_NAME'] = $val2['CLASS_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['roleId'] = $val2['roleId'];
                        $data['Group'][$key]['SUB'][$key2]['userId'] = $val2['userId'];
                        $_CNT++;
                    }
                }
                $data['Group'][$key]['COUNT'] = $_CNT;
            }
        }

        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/group/layout", $this->global, $data , NULL);
    }

    /**
     * This function used to load the first screen of the user
     */
    public function depth3()
    {
        $PostData = $this->input->get();
        if ( $PostData ==  null ) {
            $_BASE_GROUP_CODE = BASE_PLANNING_PARENT_CODE;
        }else{
            $_BASE_GROUP_CODE = $PostData['mode'];
        }
        $this->global ['pageTitle'] = 'Hackers Projects : 조직도';
        $data['Group'] =  array();
        $_select = array(
            "G.*"
        );
        $_search['where'] = array();
        $_search['where'][] = "G.IDX = ".$_BASE_GROUP_CODE." ";
        $_dbresult = $this->schedule_model->getGroupInfo($_select,$_search);
        if (!empty($_dbresult['row'])) {
            foreach($_dbresult['row'] as $key => $val) {
                $data['Group'][$key]['IDX'] = $val['IDX'];
                $data['Group'][$key]['NAME'] = $val['NAME'];

                $_select2 = array(
                    "M.MEMBER_IDX","M.USER_ID","M.USER_NAME","CASE WHEN ISNULL(V.NAME) THEN C.NAME ELSE V.NAME END AS CLASS_NAME","A.roleId","A.userId"
                );
                $_dbresult2 = $this->schedule_model->getGroupMember($_select2,$val['IDX']);
                $data['Group'][$key]['SUB'] = array();
                $_CNT = 0;
                if ( $_dbresult2 !== null ) {
                    foreach ($_dbresult2['row'] as $key2 => $val2) {
                        $data['Group'][$key]['SUB'][$key2]['MEMBER_IDX'] = $val2['MEMBER_IDX'];
                        $data['Group'][$key]['SUB'][$key2]['USER_ID'] = $val2['USER_ID'];
                        $data['Group'][$key]['SUB'][$key2]['USER_NAME'] = $val2['USER_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['CLASS_NAME'] = $val2['CLASS_NAME'];
                        $data['Group'][$key]['SUB'][$key2]['roleId'] = $val2['roleId'];
                        $data['Group'][$key]['SUB'][$key2]['userId'] = $val2['userId'];
                        $_CNT++;
                    }
                }
                $data['Group'][$key]['COUNT'] = $_CNT;
            }
        }

        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/group/layout", $this->global, $data , NULL);
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
            $dbresult = $this->schedule_model->updateseqdata($PostData);
            echo json_encode($dbresult);
            exit;

        }
    }
}

?>