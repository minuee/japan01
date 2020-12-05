<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Board (BoardController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Email extends BaseController
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

            $this->global['pageTitle'] = 'SCM Portal Admin : 메일함';

            $this->loadViews("manager/email/list", $this->global, null, NULL);
        }
    }


    function view()
    {

        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {

            $this->global['pageTitle'] = 'SCM Portal Admin : 메일발송';


            $this->loadViews("manager/email/view", $this->global, null, NULL);
        }
    }

    function send()
    {

        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {

            $this->global['pageTitle'] = 'SCM Portal Admin : 메일발송';


            $this->loadViews("manager/email/send", $this->global, null, NULL);
        }
    }



}

?>