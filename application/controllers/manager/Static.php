<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Statics
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 5 July 2019
 */
class Statics extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('schedule_model');
        $this->load->helper("html");
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */
    function index()
    {

        if( $this->session->userdata('userId') == null)
        {
            $this->loadThis();
        }

        $data['LoginSession'] = $this->session->userdata();
        $this->loadViews("manager/holiday/index", $this->global, $data, NULL);

    }

}

?>