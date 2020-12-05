<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Common extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global ['pageTitle'] = 'Hackers SCM Portal Admin : Sitemap';
        
        $this->loadViews("sitemap", $this->global, NULL , NULL);
    }

    public function editor_upload(){

        $this->global['pageTitle'] = 'SCM Portal Admin : 이미지 업로드 ';
        return $this->load->view("includes/editor_img_upload",$this->global,null,false);

    }

    public function dd(){

    }
    

}

?>