<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class User extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('project_model');
        $this->isLoggedIn();
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {

        if ( $this->session->userdata('role') == ROLE_ADMIN && $this->session->userdata ( 'userId' ) > 1 ) {
            redirect("/statics");
        }else {


            $data = array();
            //echo ENVIRONMENT;
            $_GROUPCode = $this->getGlobalGroupCode($this->session->userdata('role'), $this->session->userdata('groupidx'), $this->session->userdata('parentgroup'));
            $data['GROUPCode'] = $_GROUPCode['Group'];

            $_psearch['where'] = array();
            //$_search['where'][] = "A.ProjectMode != 1"; JSON_EXTRACT(Permission, "$[*]") LIKE "%509%"
            if ($this->session->userdata('role') == ROLE_EMPLOYEE || $this->session->userdata('role') == ROLE_MANAGER || $this->session->userdata('role') == ROLE_SUPERVISOR) {
                //$_search['where'][] = "( A.RegIDGroup = ".$this->session->userdata('groupidx')." OR  A.ProjectIdx = ".MAINTERENCE_IDX." )";
                $_psearch['where'][] = "( JSON_EXTRACT(Permission, '$[*]') LIKE '%" . $this->session->userdata('groupidx') . "%' OR  A.ProjectIdx = " . MAINTERENCE_IDX . " OR A.RegIDGroup = '" . $this->session->userdata('groupidx') . "' )";
            }
            $_psearch['where'][] = "A.DelID IS NULL ";
            $_psearch['where'][] = "A.ProjectStatus = 1";
            $_select = array(
                "A.*", "M.nickname as UserName"
            );
            $data['ProjectList'] = $this->project_model->ProjectListing($_psearch, $_select);

            /*$_search2['where'] = array();
            $_search2['where'][] = "M.roleId != 1 "; ;
            $_select2 = array(
                "M.*"
            );
            $data['TeamMemberList'] = $this->project_model->TeamMemberListing($_search2 ,$_select2);*/
            $_search['where'] = array();
            $_search['where'][] = "wDate = '" . date("Y-m-d") . "' ";
            $_search['where'][] = "userId = '" . $this->session->userdata('userId') . "' ";
            $data['IsReported'] = $this->project_model->getIsTodayReported($_search, 'ReportIdx');

            $data['ReportOk'] = 0;
            $weekday = date("N", strtotime(date("Y-m-d")));
            if ( $this->session->userdata('role') > 2  ) {
                if ($weekday == 0) {
                    $_termday = 2;
                } else if ($weekday == 1) {
                    $_termday = 3;
                } else {
                    $_termday = 1;
                }
                $_rsearch['where'] = array();
                $_rsearch['where'][] = "R.userId = '" . $this->session->userdata('userId') . "' ";
                $_rsearch['where'][] = "R.RegDatetime > DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -" . $_termday . " DAY),'%Y-%m-%d 00:00:00')  ";
                $_ReportOk = $this->project_model->getIsReported($_rsearch, $_termday,$this->session->userdata('userId'));
                if (isset($_ReportOk->ReportIdx)) {
                    $data['ReportOk'] = $_ReportOk->ReportIdx;
                }

            }else{
                $data['ReportOk'] = 1;
            }

            /*여기서부터 일내역 Todo, Doing, Done순 */
            $_dowork_array = [1, 2, 9];

            /* statics */
            $data['TODOSTATICS'] = array();
            $data['DONESTATICS'] = array();
            for ($i = 0; $i < count($_dowork_array); $i++) {
                $data['MyWork'][$_dowork_array[$i]] = array();

                $_search3['where'] = array();
                $_search3['where'][] = "A.IsDel IS NULL ";

                //$_search3['where'][] = "A.ProjectIdx = ".MAINTERENCE_IDX." ";
                $_search3['where'][] = "A.Status = $_dowork_array[$i] ";
                $_search3['where'][] = "A.ToDoID = '" . $this->session->userdata('userId') . "' ";

                if ($i == 0) {
                    $_search3['where'][] = "A.sDate <=  '" . date("Y-m-d") . "' ";
                } else if ($i == 2) {
                    $_search3['where'][] = "A.eDate = '" . date("Y-m-d") . "' ";
                }
                $_select3 = array(
                    "A.ProjectWorkIdx", "A.title", "A.Status", "A.RegID", "A.ToDoID", "A.RegDatetime", "A.IsReOpen", "A.IntraBoardIdx", "A.IntraUrl", "A.Foretime", "A.ChildMode"
                , "IFNULL(M.name,'미정') as UserName", "B.ProjectTitle", "A.Priority", "A.Background","A.PostColor"
                , "CASE WHEN C.Comment = '' THEN '' ELSE CONCAT('최근메모 : ',C.Comment) END AS Comment"
                );
                
                $_dbresult = $this->project_model->getMyWorkListing($_search3, $_select3);
                if ($i == 0 || $i == 2) {

                    if ($_dbresult['cnt'] > 0) {
                        $_targetarray = array();
                        foreach ($_dbresult['res'] as $rkey => $rval) {
                            array_push($_targetarray, $rval['ProjectWorkIdx']);
                        }
                        //print_r($_targetarray);
                        $_todoresult = $this->project_model->getMyWorkStatics($_targetarray, $this->session->userdata('userId'));
                        if (count($_todoresult) > 0 && $i == 0) {
                            $data['TODOSTATICS'] = $_todoresult[0];
                        } else if (count($_todoresult) > 0 && $i == 2) {
                            $data['DONESTATICS'] = $_todoresult[0];

                        }
                    }
                }
                $data['MyWork'][$_dowork_array[$i]] = $_dbresult['res'];
            }

            $data['LoginSession'] = $this->session->userdata();
            $data['HistoryMessages'] = array();
            $data['isTeamView'] = false;
            $data['NodeTeamCode'] = null;
            $data['NodeServiceID'] = null;
            $data['NodeChannelID'] = null;
            $data['NodeTeamName'] = null;


            // 각종코드
            $this->config->load('config', true);
            $_CommonCode = $this->config->item('code');
            $data['CommonCode'] = $_CommonCode;
            $this->global['pageTitle'] = 'Hackers Project : My Jobs';
            $this->loadViews("mykanban5", $this->global, $data, NULL);

        }

    }


    /**
     * This function is used to load the user list
     */
    function userListing()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;

            $this->load->library('pagination');

            $count = $this->user_model->userListingCount($searchText);

            $returns = $this->paginationCompress ( "userListing/", $count, 10 );

            $data['userRecords'] = $this->user_model->userListing($searchText, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'CodeInsect : User Listing';

            $this->loadViews("users", $this->global, $data, NULL);
        }
    }



    /**
     * This function is used to load the add new form
     */
    function addNew()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('user_model');
            $data['roles'] = $this->user_model->getUserRoles();

            $this->global['pageTitle'] = 'CodeInsect : Add New User';

            $this->loadViews("addNew", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to check whether email already exist or not
     */
    function checkEmailExists()
    {
        $userId = $this->input->post("userId");
        $email = $this->input->post("email");

        if(empty($userId)){
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if(empty($result)){ echo("true"); }
        else { echo("false"); }
    }

    /**
     * This function is used to add new user to the system
     */
    function addNewUser()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('password','Password','required|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[20]');
            $this->form_validation->set_rules('role','Role','trim|required|numeric');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[10]');

            if($this->form_validation->run() == FALSE)
            {
                $this->addNew();
            }
            else
            {
                $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                $password = $this->input->post('password');
                $roleId = $this->input->post('role');
                $mobile = $this->security->xss_clean($this->input->post('mobile'));

                $userInfo = array('email'=>$email, 'password'=>getHashedPassword($password), 'roleId'=>$roleId, 'name'=> $name,
                    'mobile'=>$mobile, 'createdBy'=>$this->vendorId, 'createdDtm'=>date('Y-m-d H:i:s'));

                $this->load->model('user_model');
                $result = $this->user_model->addNewUser($userInfo);

                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'New User created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'User creation failed');
                }

                redirect('addNew');
            }
        }
    }


    /**
     * This function is used load user edit information
     * @param number $userId : Optional : This is user id
     */
    function editOld($userId = NULL)
    {
            if($userId == null)
            {
                redirect('userListing');
            }

            $data['roles'] = $this->user_model->getUserRoles();
            $data['userInfo'] = $this->user_model->getUserInfo($userId);

            $this->global['pageTitle'] = 'CodeInsect : Edit User';

            $this->loadViews("editOld", $this->global, $data, NULL);

    }


    /**
     * This function is used to edit the user information
     */
    function editUser()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');

            $userId = $this->input->post('userId');

            $this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('password','Password','matches[cpassword]|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','matches[password]|max_length[20]');
            $this->form_validation->set_rules('role','Role','trim|required|numeric');
            //$this->form_validation->set_rules('mobile','Mobile Number','required|min_length[10]');

            if($this->form_validation->run() == FALSE)
            {
                $this->editOld($userId);
            }
            else
            {
                $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                $password = $this->input->post('password');
                $roleId = $this->input->post('role');
                $mobile = $this->security->xss_clean($this->input->post('mobile'));

                $userInfo = array();

                if(empty($password))
                {
                    $userInfo = array('email'=>$email, 'roleId'=>$roleId, 'name'=>$name,
                        'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
                }
                else
                {
                    $userInfo = array('email'=>$email, 'password'=>getHashedPassword($password), 'roleId'=>$roleId,
                        'name'=>ucwords($name), 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId,
                        'updatedDtm'=>date('Y-m-d H:i:s'));
                }

                $result = $this->user_model->editUser($userInfo, $userId);

                if($result == true)
                {
                    $this->session->set_flashdata('success', 'User updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'User updation failed');
                }

                redirect('userListing');
            }
        }
    }


    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function deleteUser()
    {
        if($this->isAdmin() == TRUE)
        {
            echo(json_encode(array('status'=>'access')));
        }
        else
        {
            $userId = $this->input->post('userId');
            $userInfo = array('isDeleted'=>1,'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));

            $result = $this->user_model->deleteUser($userId, $userInfo);

            if ($result > 0) { echo(json_encode(array('status'=>TRUE))); }
            else { echo(json_encode(array('status'=>FALSE))); }
        }
    }

    /**
     * Page not found : error 404
     */
    function pageNotFound()
    {
        $this->global['pageTitle'] = 'CodeInsect : 404 - Page Not Found';

        $this->loadViews("404", $this->global, NULL, NULL);
    }

    /**
     * This function used to show login history
     * @param number $userId : This is user id
     */
    function loginHistoy($userId = NULL)
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $userId = ($userId == NULL ? 0 : $userId);

            $searchText = $this->input->post('searchText');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data["userInfo"] = $this->user_model->getUserInfoById($userId);

            $data['searchText'] = $searchText;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;

            $this->load->library('pagination');

            $count = $this->user_model->loginHistoryCount($userId, $searchText, $fromDate, $toDate);

            $returns = $this->paginationCompress ( "login-history/".$userId."/", $count, 10, 3);

            $data['userRecords'] = $this->user_model->loginHistory($userId, $searchText, $fromDate, $toDate, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'CodeInsect : User Login History';

            $this->loadViews("loginHistory", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to show users profile
     */
    function profile($active = "details")
    {
        $data["userInfo"] = $this->user_model->getUserInfoWithRole($this->vendorId);
        $data["active"] = $active;

        $this->global['pageTitle'] = $active == "details" ? 'CodeInsect : My Profile' : 'CodeInsect : Change Password';
        $this->loadViews("profile", $this->global, $data, NULL);
    }

    /**
     * This function is used to update the user details
     * @param text $active : This is flag to set the active tab
     */
    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');

        //$this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
        $this->form_validation->set_rules('nickname','Nick Name','trim|required|max_length[6]');
        $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[11]');
        //$this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]|callback_emailExists');

        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            //$name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
            $nickname = ucwords(strtolower($this->security->xss_clean($this->input->post('nickname'))));
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            ///$email = strtolower($this->security->xss_clean($this->input->post('email')));

            $userInfo = array('nickname'=>$nickname, 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));

            $result = $this->user_model->editUser($userInfo, $this->vendorId);

            if($result == true)
            {
                //$this->session->set_userdata('name', $name);
                $this->session->set_userdata('nickname', $nickname);
                $this->session->set_flashdata('success', 'Profile updated successfully');
            }
            else
            {
                $this->session->set_flashdata('error', 'Profile updation failed');
            }

            redirect('profile/'.$active);
        }
    }

    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword','Old password','required|max_length[20]');
        $this->form_validation->set_rules('newPassword','New password','required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword','Confirm new password','required|matches[newPassword]|max_length[20]');

        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);

            if(empty($resultPas))
            {
                $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                redirect('profile/'.$active);
            }
            else
            {
                $usersData = array('password'=>getHashedPassword($newPassword), 'updatedBy'=>$this->vendorId,
                    'updatedDtm'=>date('Y-m-d H:i:s'));

                $result = $this->user_model->changePassword($this->vendorId, $usersData);

                if($result > 0) { $this->session->set_flashdata('success', 'Password updation successful'); }
                else { $this->session->set_flashdata('error', 'Password updation failed'); }

                redirect('profile/'.$active);
            }
        }
    }

    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $userId = $this->vendorId;
        $return = false;

        if(empty($userId)){
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if(empty($result)){ $return = true; }
        else {
            $this->form_validation->set_message('emailExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }
}

?>
