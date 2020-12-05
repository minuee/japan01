<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Login (LoginController)
 * Login class to control to authenticate user credentials and starts user's session.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Login extends CI_Controller
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('user_model');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->isLoggedIn();
    }

    /**
     * This function used to check the user is logged in or not
     */
    function isLoggedIn()
    {
        $isLoggedIn = $this->session->userdata('isLoggedIn');

        if(!isset($isLoggedIn) || $isLoggedIn != TRUE)
        {
            $this->load->view('login');
        }
        else
        {
            redirect('/myjobs');
        }
    }


    /**
     * from intranet
     */
    public function intra_login()
    {


        $auth_code = $this->input->get();
        //echo $auth_code->authcode;
        //$act_auth = json_decode($this->input->get());
        $authcode=  $auth_code['authcode'];
        $intranet = '&intra=HAC';

        $site_url = 'http://222.122.234.15/_include/goAuthLinker.php?authcode='.$authcode.'&act=auth_data'.$intranet;
        $result = json_decode(file_get_contents($site_url), true);

        if ( $result['result'] !== 'success' ) {
            //print_r($result);
            alert_back('인증되지 않은 접근방법입니다.');
            redirect('main/home');
            exit;
        }

        if ( !empty($result['user_id']) ) {
            /* 등록여부 확인 */
            $_is_hackers = $this->login_model->getUserInfoByUserID($result['user_id']);

            if ( isset($_is_hackers->userId) ) {

            }else {/* 없으면 신규로 등록해준다.. 인증이 된 인트라넷 회원임이 확인되었으니 */
                $faceurl = "http://hac.educamp.org/data/user/HAC_".$result['user_id'].".jpg";
                $userInfo = array('hackersid'=>$result['user_id'],'hackersidx'=>$result['member_idx'],'email'=>$result['user_id']."@hackers.com", 'password'=>getHashedPassword("hackers".$result['user_id']), 'roleId'=>ROLE_EMPLOYEE, 'name'=> $result['user_name'], 'nickname'=> substr($result['user_name'],3,9), 'face' => $faceurl ,'createdBy'=>1, 'createdDtm'=>date('Y-m-d H:i:s'));

                $dbresult = $this->user_model->addNewUser($userInfo);
                if(! $dbresult){
                    alert_back('오류가 발생하였습니다. 관리자에게 문의해주세요');
                    exit;
                }
            }

        }else{
            alert_back('인증되지 않은 접근방법입니다.');
            redirect('main/home');
            exit;
        }
        //$this->db->select('M.GROUP_IDX,M.HR_VAR_9');
        $email = strtolower($this->security->xss_clean($result['user_id']."@hackers.com"));
        $password = "hackers".$result['user_id'];
        $loginresult = $this->login_model->loginMe($email, $password);

        if(!empty($loginresult))
        {
            if ( !$loginresult->GROUP_IDX ) {
                alert_back('조직정보가 없는 직원입니다. 관리팀에 문의하세요.');
                redirect('main/home');
                exit;
            }
            $lastLogin = $this->login_model->lastLoginInfo($loginresult->userId);

            if( isset($loginresult->DEPTH) &&  isset($loginresult->PARENT)) {
                if ( $loginresult->DEPTH == 2 ) {
                    $_parentCode = $loginresult->GROUP_IDX;
                    $_groupDepth = $loginresult->DEPTH;
                }else if ( $loginresult->DEPTH == 3 ) {
                    $_parentCode = $loginresult->PARENT;
                    $_groupDepth = $loginresult->DEPTH;
                }
            }

            $this->config->load('config',true);
            $_CommonCode = $this->config->item('code');
            if ( in_array($loginresult->GROUP_IDX ,$_CommonCode['DESIGNGROUP'])) {
                $_GROUPCODE = BASE_DESIGN_TEXT_CODE;
            }else if ( in_array($loginresult->GROUP_IDX ,$_CommonCode['PLANNINGGROUP'])) {
                $_GROUPCODE = BASE_PLANNING_TEXT_CODE;
            }else if ( in_array($loginresult->GROUP_IDX ,$_CommonCode['REALTORGROUP'])) {
                $_GROUPCODE = BASE_REALTOR_TEXT_CODE;
            }else if ( in_array($loginresult->GROUP_IDX ,$_CommonCode['BASEENGLISHGROUP'])) {
                $_GROUPCODE = BASE_BASEENGLISH_TEXT_CODE;
            }else{
                $_GROUPCODE = BASE_DEVELOPE_TEXT_CODE;
            }

            $sessionArray = array('userId'=>$loginresult->userId,
                'role'=>$loginresult->roleId,
                'roleText'=>$loginresult->role,
                'name'=>$loginresult->name,
                'nickname'=>$loginresult->nickname,
                'face'=>$loginresult->FACE_URL,
                'hackersid'=>$loginresult->hackersid,
                'hackersidx'=>$loginresult->hackersidx,
                'groupidx'=>$loginresult->GROUP_IDX,
                'groupcode'=>$_GROUPCODE,
                'parentgroup'=>$_parentCode?$_parentCode:$loginresult->GROUP_IDX,
                'groupdepth'=>$_groupDepth?$_groupDepth:1,
                'positionidx'=>$loginresult->HR_VAR_9,
                'lastLogin'=> $lastLogin->createdDtm,
                'isLoggedIn' => TRUE
            );

            $this->session->set_userdata($sessionArray);

            unset($sessionArray['userId'], $sessionArray['isLoggedIn'], $sessionArray['lastLogin']);

            $loginInfo = array("userId"=>$loginresult->userId, "sessionData" => json_encode($sessionArray), "machineIp"=>ip2long($_SERVER['REMOTE_ADDR']), "userAgent"=>getBrowserAgent(), "agentString"=>$this->agent->agent_string(), "platform"=>$this->agent->platform());

            $this->login_model->lastLogin($loginInfo);

            redirect('/myjobs');
        }
        else
        {
            $this->session->set_flashdata('error', 'Email or password mismatch');

            $this->index();
        }


    }

    /**
     * This function used to logged in user
     */
    public function loginMe()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[128]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[32]');

        if($this->form_validation->run() == FALSE)
        {
            $this->index();
        }
        else
        {
            $email = strtolower($this->security->xss_clean($this->input->post('email')));
            $password = $this->input->post('password');

            $result = $this->login_model->loginMe($email, $password);

            if(!empty($result))
            {
                $lastLogin = $this->login_model->lastLoginInfo($result->userId);

                if( isset($result->DEPTH) &&  isset($result->PARENT)) {
                    if ( $result->DEPTH == 2 ) {
                        $_parentCode = $result->GROUP_IDX;
                        $_groupDepth = $result->DEPTH;
                    }else if ( $result->DEPTH == 3 ) {
                        $_parentCode = $result->PARENT;
                        $_groupDepth = $result->DEPTH;
                    }
                }

                $this->config->load('config',true);
                $_CommonCode = $this->config->item('code');
                if ( in_array($result->GROUP_IDX ,$_CommonCode['DESIGNGROUP'])) {
                    $_GROUPCODE = BASE_DESIGN_TEXT_CODE;
                }else if ( in_array($result->GROUP_IDX ,$_CommonCode['PLANNINGGROUP'])) {
                    $_GROUPCODE = BASE_PLANNING_TEXT_CODE;
                }else if ( in_array($result->GROUP_IDX ,$_CommonCode['REALTORGROUP'])) {
                    $_GROUPCODE = BASE_REALTOR_TEXT_CODE;
                }else if ( in_array($result->GROUP_IDX ,$_CommonCode['BASEENGLISHGROUP'])) {
                    $_GROUPCODE = BASE_BASEENGLISH_TEXT_CODE;
                }else{
                    $_GROUPCODE = BASE_DEVELOPE_TEXT_CODE;
                }

                $sessionArray = array('userId'=>$result->userId,
                                        'role'=>$result->roleId,
                                        'roleText'=>$result->role,
                                        'name'=>$result->name,
                                        'face'=>$result->FACE_URL,
                                        'hackersid'=>$result->hackersid,
                                        'hackersidx'=>$result->hackersidx,
                                        'nickname'=>$result->nickname,
                                        'groupidx'=>$result->GROUP_IDX,
                                        'groupcode'=>$_GROUPCODE,
                                        'groupdepth'=>$_groupDepth?$_groupDepth:1,
                                        'parentgroup'=>$_parentCode?$_parentCode:$result->GROUP_IDX,
                                        'positionidx'=>$result->HR_VAR_9,
                                        'lastLogin'=> $lastLogin->createdDtm,
                                        'isLoggedIn' => TRUE
                                );

                $this->session->set_userdata($sessionArray);

                unset($sessionArray['userId'], $sessionArray['isLoggedIn'], $sessionArray['lastLogin']);

                $loginInfo = array("userId"=>$result->userId, "sessionData" => json_encode($sessionArray), "machineIp"=>ip2long($_SERVER['REMOTE_ADDR']), "userAgent"=>getBrowserAgent(), "agentString"=>$this->agent->agent_string(), "platform"=>$this->agent->platform());

                $this->login_model->lastLogin($loginInfo);

                redirect('/myjobs');
            }
            else
            {
                $this->session->set_flashdata('error', 'Email or password mismatch');

                $this->index();
            }
        }
    }

    /**
     * This function used to load forgot password view
     */
    public function forgotPassword()
    {
        $isLoggedIn = $this->session->userdata('isLoggedIn');

        if(!isset($isLoggedIn) || $isLoggedIn != TRUE)
        {
            $this->load->view('forgotPassword');
        }
        else
        {
            redirect('/myjobs');
        }
    }

    /**
     * This function used to generate reset password request link
     */
    function resetPasswordUser()
    {
        $status = '';

        $this->load->library('form_validation');

        $this->form_validation->set_rules('login_email','Email','trim|required|valid_email');

        if($this->form_validation->run() == FALSE)
        {
            $this->forgotPassword();
        }
        else
        {
            $email = strtolower($this->security->xss_clean($this->input->post('login_email')));

            if($this->login_model->checkEmailExist($email))
            {
                $encoded_email = urlencode($email);

                $this->load->helper('string');
                $data['email'] = $email;
                $data['activation_id'] = random_string('alnum',15);
                $data['createdDtm'] = date('Y-m-d H:i:s');
                $data['agent'] = getBrowserAgent();
                $data['client_ip'] = $this->input->ip_address();

                $save = $this->login_model->resetPasswordUser($data);

                if($save)
                {
                    $data1['reset_link'] = base_url() . "resetPasswordConfirmUser/" . $data['activation_id'] . "/" . $encoded_email;
                    $userInfo = $this->login_model->getCustomerInfoByEmail($email);

                    if(!empty($userInfo)){
                        $data1["name"] = $userInfo->name;
                        $data1["email"] = $userInfo->email;
                        $data1["message"] = "Reset Your Password";
                    }

                    $sendStatus = resetPasswordEmail($data1);

                    if($sendStatus){
                        $status = "send";
                        setFlashData($status, "Reset password link sent successfully, please check mails.");
                    } else {
                        $status = "notsend";
                        setFlashData($status, "Email has been failed, try again.");
                    }
                }
                else
                {
                    $status = 'unable';
                    setFlashData($status, "It seems an error while sending your details, try again.");
                }
            }
            else
            {
                $status = 'invalid';
                setFlashData($status, "This email is not registered with us.");
            }
            redirect('/forgotPassword');
        }
    }

    /**
     * This function used to reset the password
     * @param string $activation_id : This is unique id
     * @param string $email : This is user email
     */
    function resetPasswordConfirmUser($activation_id, $email)
    {
        // Get email and activation code from URL values at index 3-4
        $email = urldecode($email);

        // Check activation id in database
        $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);

        $data['email'] = $email;
        $data['activation_code'] = $activation_id;

        if ($is_correct == 1)
        {
            $this->load->view('newPassword', $data);
        }
        else
        {
            redirect('/login');
        }
    }

    /**
     * This function used to create new password for user
     */
    function createPasswordUser()
    {
        $status = '';
        $message = '';
        $email = strtolower($this->input->post("email"));
        $activation_id = $this->input->post("activation_code");

        $this->load->library('form_validation');

        $this->form_validation->set_rules('password','Password','required|max_length[20]');
        $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[20]');

        if($this->form_validation->run() == FALSE)
        {
            $this->resetPasswordConfirmUser($activation_id, urlencode($email));
        }
        else
        {
            $password = $this->input->post('password');
            $cpassword = $this->input->post('cpassword');

            // Check activation id in database
            $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);

            if($is_correct == 1)
            {
                $this->login_model->createPasswordUser($email, $password);

                $status = 'success';
                $message = 'Password reset successfully';
            }
            else
            {
                $status = 'error';
                $message = 'Password reset failed';
            }

            setFlashData($status, $message);

            redirect("/login");
        }
    }
}

?>
