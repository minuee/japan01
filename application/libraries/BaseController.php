<?php
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
//header("Access-Control-Expose-Headers: Content-Length, X-My-Custom-Header, X-Another-Custom-Header, EUC-KR, UTF-8, charset");
//header("Access-Control-Request-Credentials: true");
header("Access-Control-Allow-Method: POST, GET, OPTIONS");


//defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * Class : BaseController
 * Base Class to control over all the classes
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class BaseController extends CI_Controller {

	protected $role = '';
	protected $vendorId = '';
    protected $hackersid = '';
    protected $parentgroup = '';
	protected $name = '';
	protected $roleText = '';
	protected $global = array ();
	protected $lastLogin = '';

    public function __construct(array $flag = array())
    {
        parent::__construct();
        $this->load->model('code_model');
        $_UserId = $this->session->userdata ( 'userId' );
        $_today = date("Y-m-d");
        //$_search = "( A.ToDoID = ".$_UserId." || ( A.ToDoID = '' && A.RegID =  ".$_UserId." ) )" ;
        $_search = "A.ToDoID = ".$_UserId."" ;
        $_select = array(
	        "COUNT(if(`Status`= 1, 1, null)) as todo_cnt",
	        "COUNT(if(`Status` = 2 &&  A.ToDoID =  '$_UserId', 1, null)) AS doing_cnt",
            "COUNT(if(`Status` = 9 &&  A.ToDoID =  '$_UserId' && eDate = '$_today', 1, null)) as done_cnt"
        );
        $this->global['MyTeamViewPermission'] = 0;
        if ( $_UserId ) {
            $this->global['MyJobsCount'] = $this->code_model->getMyJobs($_search, $_select, $_UserId);
            $_GMyTeamViewPermissions = $this->code_model->getMyTeamViewPermissions($_UserId);
            if(count($_GMyTeamViewPermissions) > 0 ) {
                $_TmpMyTeamViewPermissions = json_decode($_GMyTeamViewPermissions[0]['Permission']);
                $this->global['MyTeamViewPermission'] = $_TmpMyTeamViewPermissions[0];
            }
        }

        if ( $_SERVER['SERVER_NAME'] == '10.100.0.29') {
            alert('wront access!!1');
            exit;
        }

    }

	/**
	 * Takes mixed data and optionally a status code, then creates the response
	 *
	 * @access public
	 * @param array|NULL $data
	 *        	Data to output to the user
	 *        	running the script; otherwise, exit
	 */
	public function response($data = NULL) {
		$this->output->set_status_header ( 200 )->set_content_type ( 'application/json', 'utf-8' )->set_output ( json_encode ( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )->_display ();
		exit ();
	}

	/**
	 * This function used to check the user is logged in or not
	 */
	function isLoggedIn() {

		$isLoggedIn = $this->session->userdata('isLoggedIn');

		if (! isset ( $isLoggedIn ) || $isLoggedIn != TRUE) {
            redirect ( 'http://hac.educamp.org' );
		} else {
			$this->role = $this->session->userdata('role' );
            $this->hackersid = $this->session->userdata('hackersid' );
			$this->vendorId = $this->session->userdata('userId' );
			$this->name = $this->session->userdata('name' );
			$this->roleText = $this->session->userdata('roleText' );
			$this->lastLogin = $this->session->userdata('lastLogin' );
            $this->parentgroup = $this->session->userdata('parentgroup' );
            $this->global['global_userId'] = $this->vendorId;
			$this->global['name'] = $this->name;
			$this->global['role'] = $this->role;
			$this->global['role_text'] = $this->roleText;
			$this->global['last_login'] = $this->lastLogin;
            $this->global['global_hackersid'] = $this->hackersid;
            $this->global['global_parentCode'] = $this->parentgroup;
            if ( $this->role == 1 && $this->session->userdata('hackersidx') == 0 ) {
                $this->global['global_face'] = base_url() . "assets/dist/img/avatar5.png";
            }else if ( !empty($this->session->userdata ('face')) ) {
                if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
                    $this->global['global_face'] = str_replace("http://","https://", $this->session->userdata ('face'));
                }else{
                    $this->global['global_face'] = $this->session->userdata ('face');
                }

            }else {
                if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
                    $this->global['global_face'] = "https://hac.educamp.org/data/user/HAC_".$this->hackersid.".jpg";
                }else{
                    $this->global['global_face'] = "http://hac.educamp.org/data/user/HAC_".$this->hackersid.".jpg";
                }

            }
            $this->global['csrf_token'] = $this->security->get_csrf_hash();
		}
	}

	/**
 * This function is used to check the access
 */
    function isAdmin() {
        if ($this->role != ROLE_ADMIN || $this->role != ROLE_SUPERVISOR) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * This function is used to check the access
     */
    function isPermission( $_mode = null, $_data) {

        $_return = false;
        if ( $_mode == 'calendar_view' ) {
            $_PermissionOk =  (array)json_decode($_data,true);

            if ( in_array( $this->session->userdata('groupidx'), $_PermissionOk) ) {
                $_return = true;
            }
        }

        return $_return;

    }

	/**
	 * This function is used to check the access
	 */
	function isTicketter() {
		if ($this->role == ROLE_EMPLOYEE ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * This function is used to load the set of views
	 */
	function loadThis() {
		$this->global ['pageTitle'] = 'Hackers Project: Access Denied';

		$this->load->view ( 'includes/header', $this->global );
		$this->load->view ( 'access' );
		$this->load->view ( 'includes/footer' );
	}

    /**
     * This function is used to load the set of views
     */
    function loadOnlyEmployee() {
        $this->global ['pageTitle'] = 'Hackers Project: Access Denied';

        $this->load->view ( 'includes/header', $this->global );
        $this->load->view ( 'onlyempolyee' );
        $this->load->view ( 'includes/footer' );
    }

    /**
     * This function is used to load the set of views
     */
    function loadNotMember() {
        $this->global ['pageTitle'] = 'Hackers Project : Member is not find';

        $this->load->view ( 'includes/header', $this->global );
        $this->load->view ( 'notmember' );
        $this->load->view ( 'includes/footer' );
    }

	/**
	 * This function is used to logged out user from system
	 */
	function logout() {
        $_role = $this->session->userdata ( 'role' );
        $_userId = $this->session->userdata ( 'userId' );
		$this->session->sess_destroy ();
		redirect ( 'login' );
	}

	/**
     * This function used to load views
     * @param {string} $viewName : This is view name
     * @param {mixed} $headerInfo : This is array of header information
     * @param {mixed} $pageInfo : This is array of page information
     * @param {mixed} $footerInfo : This is array of footer information
     * @return {null} $result : null
     */
    function loadViews($viewName = "", $headerInfo = NULL, $pageInfo = NULL, $footerInfo = NULL){

        $this->load->view('includes/header', $headerInfo);
        $this->load->view($viewName, $pageInfo);
        $this->load->view('includes/footer', $footerInfo);
    }
    //view simple ( no header,no left,no footer , only content
	function loadViewsSimple($viewName = "", $headerInfo = NULL, $pageInfo = NULL, $footerInfo = NULL){

        $this->load->view('includes/header_simple', $headerInfo);
        $this->load->view($viewName, $pageInfo);
        $this->load->view('includes/footer_simple', $footerInfo);
    }


	/**
	 * This function used provide the pagination resources
	 * @param {string} $link : This is page link
	 * @param {number} $count : This is page count
	 * @param {number} $perPage : This is records per page limit
	 * @return {mixed} $result : This is array of records and pagination data
	 */
    function paginationCompress($link, $count, $perPage = 10, $segment = SEGMENT) {
        $this->load->library ( 'pagination' );

        if ( count($this->uri->segments) > 2 ) {
            $segment2 = count($this->uri->segments);
        }else{
            $segment2 = 2;
        }
        $config ['base_url'] = base_url () . $link;
        $config ['total_rows'] = $count;
        $config ['uri_segment'] = $segment2;
        $config ['per_page'] = $perPage;
        $config ['num_links'] = 10;
        $config ['full_tag_open'] = '<nav><ul class="pagination">';
        $config ['full_tag_close'] = '</ul></nav>';
        $config ['first_tag_open'] = '<li class="arrow">';
        $config ['first_link'] = 'First';
        $config ['first_tag_close'] = '</li>';
        $config ['prev_link'] = 'Previous';
        $config ['prev_tag_open'] = '<li class="arrow">';
        $config ['prev_tag_close'] = '</li>';
        $config ['next_link'] = 'Next';
        $config ['next_tag_open'] = '<li class="arrow">';
        $config ['next_tag_close'] = '</li>';
        $config ['cur_tag_open'] = '<li class="active"><a href="#">';
        $config ['cur_tag_close'] = '</a></li>';
        $config ['num_tag_open'] = '<li>';
        $config ['num_tag_close'] = '</li>';
        $config ['last_tag_open'] = '<li class="arrow">';
        $config ['last_link'] = 'Last';
        $config ['last_tag_close'] = '</li>';

        $this->pagination->initialize ( $config );
        $page = $config ['per_page'];

        if ( count($this->uri->segments) > 2 ) {
            $segment = end($this->uri->segments);
        }else{
            $segment = $this->uri->segment ( $segment );
        }
        return array (
            "page" => $page,
            "segment" => $segment
        );
    }

    // 코드 데이터 호출
    // $_categoryCode 값은 array타입와 string타입으로 받을수 있다.
    // array타입 : 복수개의 코드 리스트를 반환
    // string타입 : 하나의 코드 리스트를 반환
    // $_remark 는 2차 조건절로 쓰로 배열형태로 받아야 적용된다. 예시) array('Remak01'=>0,'Remark02'=>1)
    public function code($_categoryCode=array(),$_code=false,$_remark=false)
    {
        $this->load->model('code_model');

        $return = false;

        if ( gettype($_code)=='array' ) {
            $_remark = $_code;
            $_code = false;
        }

        $codeSelect = array('Code','Name','OrderNo','Remark01','Remark02'); // 리턴받을 필드들
        $codeOrderBy = array('OrderNo'=>'ASC'); // 목록 정렬 정의

        if ( $_code ) {
            // 코드값이 있으면 코드의 정보만을 가지고 온다.
            if ( gettype($_categoryCode)=='array' ) {
                foreach( (array)$_categoryCode as $key=>$categoryCode ) {
                    $codeWhere = array();
                    $codeWhere['CategoryCode'] = $categoryCode;
                    $codeWhere['IsUse'] = 1;

                    $codeWhere = array('CategoryCode'=>$categoryCode,'IsUse'=>1);
                    if ( $_remark!==false ) {
                        foreach ( (array)$_remark as $key_remark=>$val_remark ) {
                            $codeWhere[$key_remark]	= $val_remark;
                        }
                    }
                    $return[$categoryCode] = $this->code_model->codeInfo($codeSelect,$codeWhere,$_code);
                }
            } else {
                // $codeWhere = array('CategoryCode'=>$_categoryCode,'IsUse'=>1);
                // if ( $_remark!==false ) {
                //     array_push($codeWhere, $_remark);
                // }
                $codeWhere = array('CategoryCode'=>$_categoryCode,'IsUse'=>1);
                if ( $_remark!==false ) {
                    foreach ( (array)$_remark as $key_remark=>$val_remark ) {
                        $codeWhere[$key_remark]	= $val_remark;
                    }
                }
                $return = $this->code_model->codeInfo($codeSelect,$codeWhere,$_code);
            }
        } else {
            // 코드목록을 가지고 온다.
            if ( gettype($_categoryCode)=='array' ) {
                foreach( (array)$_categoryCode as $key=>$categoryCode ) {
                    $codeWhere = array('CategoryCode'=>$categoryCode,'IsUse'=>1);
                    if ( $_remark!==false ) {
                        foreach ( (array)$_remark as $key_remark=>$val_remark ) {
                            $codeWhere[$key_remark]	= $val_remark;
                        }
                    }
                    $codeList = $this->code_model->codeList($codeSelect,$codeWhere,$codeOrderBy);
                    $return[$categoryCode] = $codeList['row'];
                }
            } else {
                $codeWhere = array('CategoryCode'=>$_categoryCode,'IsUse'=>1);
                if ( $_remark!==false ) {
                    foreach ( (array)$_remark as $key_remark=>$val_remark ) {
                        $codeWhere[$key_remark]	= $val_remark;
                    }
                }
                $codeList = $this->code_model->codeList($codeSelect,$codeWhere,$codeOrderBy);
                $return = $codeList['row'];
            }

        }

        return $return;
    }

    // 코드 Code => Name 추출위한
    public function getCodeName($_categoryCode=array(),$_remark=false)
    {

        $this->load->model('code_model');
        $return = false;

        $codeSelect = array('Code','Name'); // 리턴받을 필드들
        $codeOrderBy = array('OrderNo'=>'ASC'); // 목록 정렬 정의



        // 코드목록을 가지고 온다.
        if ( gettype($_categoryCode)=='array' ) {
            foreach( (array)$_categoryCode as $key=>$categoryCode ) {
                $codeWhere = array('CategoryCode'=>$categoryCode,'IsUse'=>1);
                if ( $_remark!==false ) {
                    foreach ( (array)$_remark as $key_remark=>$val_remark ) {
                        $codeWhere[$key_remark]	= $val_remark;
                    }
                }
                $codeList = $this->code_model->codeList($codeSelect,$codeWhere,$codeOrderBy);
                foreach( $codeList['row'] as $key => $val ) {
                    $return[$categoryCode][$val['Code']] = $val['Name'];
                }

            }
        } else {
            $codeWhere = array('CategoryCode'=>$_categoryCode,'IsUse'=>1);
            if ( $_remark!==false ) {
                foreach ( (array)$_remark as $key_remark=>$val_remark ) {
                    $codeWhere[$key_remark]	= $val_remark;
                }
            }
            $codeList = $this->code_model->codeList($codeSelect,$codeWhere,$codeOrderBy);
            $return = $codeList['row'];
        }
        return $return;
    }

    /**
     * @param $params
     * @return null
     *
     * 사용자 정보 토큰
     */
    public function CallApis_token($params)
    {

        $this->config->load('config');
        $token = null;
        $tokenResult = $this->curl_post($this->config->item('socket_host')."/token/".$params['service_id']."/".$params['channel_id'],$params);
        if ($tokenResult['status'] == 200) {
            $response = json_decode($tokenResult['response']);
            $token = $response->token;
        }
        return $token ;
    }


    public  function CallApis_saveServiceChannels($serviceID, $params)
    {
        $this->config->load('config');
        //  'channels'              => $socket_host .'/service/channels/save/{service_id}', // 서비스 채널 목록 저장
        $result = $this->curl_post( $this->config->item('socket_host')."/service/channels/save/".$serviceID, $params);
        if ($result['status'] == 200) {
            $response = json_decode($result['response']);
        }
        return $response ?? null;
    }

    public function CallApis_saveChannelInfo($serviceID, $channelPoolID, $params)
    {
        $this->config->load('config');

        // => $socket_host .'/channel/save/{service_id}/{channel_id}', // 채널 정보 저장
        $result = $this->curl_post( $this->config->item('socket_host')."/channel/save/".$serviceID."/".$channelPoolID, $params);

        if ($result['status'] == 200) {
            $response = json_decode($result['response']);
        }
        return $response ?? null;
    }

    public static function curl_post($url, $params = [], $headers = []) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array(
            'status'    => $status,
            'response'  => $response,
            'error'     => $error
        );
    }

    // 팀조직코드
    public function getGlobalGroupCode( $_role = null , $_groupidx = null , $_parentCode = null)
    {


        $this->load->model('code_model');
        $return = array();
        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');

        $codeSelect = array('IDX','Name'); // 리턴받을 필드들
        $codeOrderBy = array('NAME'=>'ASC'); // 목록 정렬 정의

        $query_mode = 1;
        if ( ( $_role == ROLE_EMPLOYEE || $_role == ROLE_MANAGER )  && $_groupidx ) {
                $codeWhere = array('IDX' => $_groupidx);
                //$codeWhere = array('DEPTH' => 3, 'PARENT' => 88, 'IDX' => $_groupidx);
        }else if ( $_role == ROLE_SUPERVISOR  ) {
            if ( isset($_parentCode) && $_parentCode == BASE_DEVELOPE_PARENT_CODE ) {
                $codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( DEPTH = 2 && IDX = 355 ) ) ";
            }else if ( isset($_parentCode) && $_parentCode == BASE_DESIGN_PARENT_CODE ) {
                //$codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DESIGN_PARENT_CODE."  ))";
                $codeWhere = $_CommonCode['DESIGNGROUP'];
                $query_mode = 2;
            }else if ( isset($_parentCode) && $_parentCode == BASE_REALTOR_PARENT_CODE ) {
                $codeWhere = $_CommonCode['REALTORGROUP'];
                $query_mode = 2;
            }else if ( isset($_parentCode) && $_parentCode == BASE_BASEENGLISH_PARENT_CODE ) {
                $codeWhere = $_CommonCode['BASEENGLISHGROUP'];
                $query_mode = 2;

            }else{
                $codeWhere = "(( IDX = 0 )) ";
            }
        }else{
            //$codeWhere = array('DEPTH'=>3,'PARENT'=>88);
            $codeWhere = "(( IDX > 0 )) ";
        }
        if ( $query_mode == 1 ) {
            $codeList = $this->code_model->getgroupList($codeSelect,$codeWhere,$codeOrderBy);
        }else{
            $codeList = $this->code_model->getgroupList2($codeSelect,$codeWhere,$codeOrderBy);
        }

        //$return = $codeList['row'];
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //print_r($codeList['sql']);
        };
        if ( isset($codeList['row']) ) {
            foreach ($codeList['row'] as $key => $val) {
                $return['Group'][$val['IDX']] = $val['Name'];
            }
        }

        return $return;
    }

    // 팀조직코드
    public function getGlobalGroupCode2( $_role = null , $_groupidx = null , $_parentCode = null)
    {

        $this->load->model('code_model');
        $return = array();

        $codeSelect = array('IDX','Name'); // 리턴받을 필드들
        $codeOrderBy = array('NAME'=>'ASC'); // 목록 정렬 정의

        if ( isset($_parentCode) && $_parentCode == BASE_DEVELOPE_PARENT_CODE ) {
            $codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( DEPTH = 2 && IDX = 355 ) ) ";
        }else if ( isset($_parentCode) && $_parentCode == BASE_DESIGN_PARENT_CODE ) {
            $codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DESIGN_PARENT_CODE."  ) || ( DEPTH = 3 && PARENT = ".BASE_DESIGN_PARENT2_CODE."  ) )";
        }else{
            $codeWhere = "(( IDX = 0 )) ";
        }

        $codeList = $this->code_model->getgroupList($codeSelect,$codeWhere,$codeOrderBy);
        //$return = $codeList['row'];
        if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
            //print_r($codeList['sql']);
        };
        if ( isset($codeList['row']) ) {
            foreach ($codeList['row'] as $key => $val) {
                $return['Group'][$val['IDX']] = $val['Name'];
            }
        }

        return $return;
    }

    // 팀조직코드
    public function getGlobalGroupCodeAll( $_subcode =  null)
    {

        $this->load->model('code_model');
        $return = array();

        $codeSelect = array('IDX','Name'); // 리턴받을 필드들
        $codeOrderBy = array(
            'PARENT'=>'ASC','DEPTH'=>'ASC','NAME'=>'ASC'
        );

        $this->config->load('config',true);
        $_CommonCode = $this->config->item('code');

        if ( $_subcode == null || strlen($_subcode) < 2  ) {

            $_darray_allcode = "";
            $_dtmparray_code = $_CommonCode['DEVELOPEMENTGROUP'];
            foreach($_dtmparray_code as  $key => $val ){
                $_darray_allcode .= $val.",";
            }

            $_dtmparray_code2 = $_CommonCode['DESIGNGROUP'];
            foreach($_dtmparray_code2 as  $key => $val ){
                $_darray_allcode .= $val.",";
            }
            $_dtmparray_code3 = $_CommonCode['PLANNINGGROUP'];
            foreach($_dtmparray_code3 as  $key => $val ){
                $_darray_allcode .= $val.",";
            }
            $_dtmparray_code4 = $_CommonCode['REALTORGROUP'];
            foreach($_dtmparray_code4 as  $key => $val ){
                $_darray_allcode .= $val.",";
            }
            $_dtmparray_code5 = $_CommonCode['BASEENGLISHGROUP'];
            foreach($_dtmparray_code5 as  $key => $val ){
                $_darray_allcode .= $val.",";
            }
            $_darray_allcode2 = substr($_darray_allcode,0,-1) ;
            $codeWhere = "( IDX  IN ( ".$_darray_allcode2." ) ) ";
            /*$codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( DEPTH = 2 && IDX = 355 ) || ( IDX  IN ( ".$_darray_code2." )  )  || ( DEPTH = 2 && IDX = ".BASE_PLANNING_PARENT_CODE."  )  || ( DEPTH = 2 && IDX = ".BASE_REALTOR_PARENT_CODE."  ) || ( DEPTH = 2 && IDX = ".BASE_BASEENGLISH_PARENT_CODE."  ) ) ";*/

        }else{

            $_tmparray_code = $_CommonCode[$_subcode];
            $_array_code = "";
            foreach($_tmparray_code as  $key => $val ){
                $_array_code .= $val.",";
            }
            $_array_code2 = substr($_array_code,0,-1) ;
            if ( $_SERVER['REMOTE_ADDR'] == '172.16.1.14' ) {
                $codeWhere = "(IDX in ( ".$_array_code2." ))";
            }else{
                $codeWhere = "(IDX in ( ".$_array_code2." ))";
                //$codeWhere = "(( DEPTH = 3 && PARENT = ".BASE_DEVELOPE_PARENT_CODE." ) || ( DEPTH = 2 && IDX = 355 ) || ( DEPTH = 3 && PARENT = ".BASE_DESIGN_PARENT_CODE."  ) || ( DEPTH = 2 && IDX = ".BASE_PLANNING_PARENT_CODE."  )  || ( DEPTH = 2 && IDX = ".BASE_REALTOR_PARENT_CODE."  ) || ( DEPTH = 2 && IDX = ".BASE_BASEENGLISH_PARENT_CODE."  ) ) ";
            }

        }

        $codeList = $this->code_model->getgroupAllList($codeSelect,$codeWhere,$codeOrderBy);
        //$return = $codeList['row'];

        if ( isset($codeList['row']) ) {
            foreach ($codeList['row'] as $key => $val) {
                $return['Group'][$val['IDX']] = $val['Name'];
            }
        }

        return $return;
    }

}
