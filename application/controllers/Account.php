<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Account extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('condition_model');
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */

    function index()
    {
        $this->list();
    }

    function list()
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

            $count = $this->condition_model->userListingCount($searchText);


            $returns = $this->paginationCompress( "account/list", $count, 1 );


            $data['userRecords'] = $this->condition_model->userListing($searchText, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'SCM Portal Admin : 가격결정';

            $this->loadViews("account", $this->global, $data, NULL);
        }
    }
    

}

?>