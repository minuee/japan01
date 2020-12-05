<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = "login";
$route['404_override'] = 'error_404';

/*********** USER DEFINED ROUTES *******************/
$route['loginMe'] = 'login/loginMe';
$route['dashboard'] = 'user';
$route['myjobs'] = 'user';
$route['logout'] = 'user/logout';
$route['userListing'] = 'user/userListing';
$route['userListing/(:num)'] = "user/userListing/$1";
$route['addNew'] = "user/addNew";
$route['addNewUser'] = "user/addNewUser";
$route['editOld'] = "user/editOld";
$route['editOld/(:num)'] = "user/editOld/$1";
$route['editUser'] = "user/editUser";
$route['deleteUser'] = "user/deleteUser";
$route['profile'] = "user/profile";
$route['profile/(:any)'] = "user/profile/$1";
$route['profileUpdate'] = "user/profileUpdate";
$route['profileUpdate/(:any)'] = "user/profileUpdate/$1";
$route['intra_login'] = 'login/intra_login';
$route['manual'] = "manual/index";
$route['loadChangePass'] = "user/loadChangePass";
$route['changePassword'] = "user/changePassword";
$route['changePassword/(:any)'] = "user/changePassword/$1";
$route['pageNotFound'] = "user/pageNotFound";
$route['checkEmailExists'] = "user/checkEmailExists";
$route['login-history'] = "user/loginHistoy";
$route['login-history/(:num)'] = "user/loginHistoy/$1";
$route['login-history/(:num)/(:num)'] = "user/loginHistoy/$1/$2";

$route['forgotPassword'] = "login/forgotPassword";
$route['resetPasswordUser'] = "login/resetPasswordUser";
$route['resetPasswordConfirmUser'] = "login/resetPasswordConfirmUser";
$route['resetPasswordConfirmUser/(:any)'] = "login/resetPasswordConfirmUser/$1";
$route['resetPasswordConfirmUser/(:any)/(:any)'] = "login/resetPasswordConfirmUser/$1/$2";
$route['createPasswordUser'] = "login/createPasswordUser";

$route['statics'] = 'manager/Statics';

/*********** CUSTMIZE  ROUTES *******************/
$route['sitemap'] = 'sitemap';

$route['manager/group'] = 'manager/group/index';
$route['manager/group/develope'] = 'manager/group/develope';
$route['manager/group/design'] = 'manager/group/design';
$route['manager/group/planning'] = 'manager/group/planning';
$route['manager/group/view/(:num)/(:num)'] = 'manager/group/view/$1/$2';
$route['manager/group/depth3'] = 'manager/group/depth3';



$route['account'] = 'account';
$route['account/(:num)'] = "account/list/$1";
$route['manager/condition'] = 'manager/condition/index';
$route['manager/condition/(:num)'] = "manager/condition/index/$1";

$route['manager/income'] = 'manager/income/index';
$route['manager/income/update'] = 'manager/income/update';
$route['manager/income/ajax_list'] = 'manager/income/ajax_list';
$route['manager/income/popview/(:num)'] = "manager/income/popview/$1";
$route['manager/income/popdetail/(:num)'] = "manager/income/popdetail/$1";

//프로젝트관리
$route['manager/project'] = 'manager/project/index';
$route['manager/project/popreg'] = 'manager/project/popreg';
$route['manager/project/popmodify'] = 'manager/project/popmodify';
$route['manager/project/ajax_list'] = 'manager/project/ajax_list';
$route['manager/project/view/(:any)/(:any)'] = "manager/project/view/$1/$1";
$route['manager/project/dailyview/(:any)'] = "manager/project/dailyview/$1";
$route['manager/project/allview/(:any)'] = "manager/project/allview/$1";
$route['manager/project/allview/(:any)/(:any)'] = "manager/project/allview/$1/$2";
$route['manager/project/myview/(:num)'] = "manager/project/myview/$1";
$route['manager/project/update'] = "manager/project/update";
$route['manager/project/insert'] = "manager/project/insert";
$route['manager/project/delete'] = "manager/project/delete";
$route['manager/project/workupdate'] = "manager/project/workupdate";
$route['manager/project/workdateupdate'] = "manager/project/workdateupdate";
$route['manager/project/workinsert'] = "manager/project/workinsert";
$route['manager/project/getChatMsg/(:num)'] = "manager/project/getChatMsg/$1";
$route['manager/project/getReadyWorks'] = "manager/project/getReadyWorks";
$route['manager/project/popuserinfo/(:num)'] = "manager/project/popuserinfo/$1";
$route['manager/project/popteaminfo/(:num)'] = "manager/project/popteaminfo/$1";

$route['manager/kanban'] = "manager/kanban/index";
$route['manager/kanban/(:any)'] = "manager/kanban/index/$1";
$route['manager/kanban/(:any)/(:any)'] = "manager/kanban/index/$1/$2";
$route['manager/kanban/view/(:num)'] = "manager/kanban/view/$1";
$route['manager/monitor/'] = "manager/monitor/index";
$route['manager/monitor/(:num)'] = "manager/monitor/index/$1";


//메일
$route['manager/email'] = 'manager/email/index';
$route['manager/email/send'] = 'manager/email/send';
$route['manager/email/view'] = 'manager/email/view';

//게시판
$route['manager/board'] = 'manager/board/index';
$route['manager/board/imgupload'] = 'manager/board/imgupload';
$route['manager/board/ajax_list'] = 'manager/board/ajax_list';
$route['manager/board/detail/(:num)'] = "manager/board/detail/$1";
$route['manager/board/insert'] = 'manager/board/insert';

//보고
$route['manager/report/'] = 'manager/report/index';
$route['manager/report/view/(:num)'] = 'manager/report/view/$1';

//휴일관리
$route['manager/schedule'] = 'manager/schedule/index';
$route['manager/schedule/popdetail/(:num)'] = "manager/schedule/popdetail/$1";


$route['manager/statics'] = 'manager/statics/index';

//fa icons
$route['manager/icon'] = 'manager/icon/index';

/*********** COMMON  ROUTES *******************/
$route['common/editor/upload/photo/(:any)'] = "common/editor_upload";


/*********** API  ROUTES *******************/
$route['nodeapi/API/information/(:any)'] = "nodeapi/API/information/$1";
$route['api/getintra1'] = "nodeapi/API/getintra1";
$route['api/getintra2'] = "nodeapi/API/getintra2";
$route['api/cron_1'] = "nodeapi/API/cron_1";
$route['api/cron_2'] = "nodeapi/API/cron_2";

$route['cron/cron_1'] = "nodeapi/Cron/cron_1";
$route['cron/cron_2'] = "nodeapi/Cron/cron_2";

$route['api/intratodo'] = "nodeapi/API/intratodo";
$route['api/intraworkday'] = "nodeapi/API/intraworkday";
$route['api/tmpinsertmember'] = "nodeapi/API/tmpinsertmember";
$route['api/intra_team'] = "nodeapi/API/intra_team";


/*********** React Native Test API ROUTES *******************/
$route['api/match'] = "nodeapi/API/stringmatch";
$route['api/react_upload'] = "nodeapi/API/imgupload";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
