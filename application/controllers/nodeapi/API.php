<?php
//if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : API ( OrderingController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class API extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api_model');
        $this->load->model('login_model');
    }

    /**
     * 채널 정보
     */
    public function information( $_channel_id =  null)
    {

        if ($_channel_id === null) {
            return $this->apiresult(201, '잘못된 접근입니다', 'Channel');
        }
        $channelPoolID = $_channel_id;
        // 채널 정보
        $channelInfo =  array();;//$this->channelPoolRepository->selectChannelPool($channelPoolID)->first();
        $_select = array(
            "A.ProjectNo as ChannelPoolID",
            "A.ProjectTitle as ChannelTitle",
            "1 as IsUse","'Y' as IsUseName",
            "A.ProjectMode",
            "A.ProjectIdx as ChatRoomIdx"
        );
        $_dbresult = $this->api_model->getDetailInfo($_select, $channelPoolID);
        if (empty($_dbresult['row'])) {
            return $this->apiresult(201, '잘못된 접근입니다', 'Channel');
        }

        foreach($_dbresult['row'] as $key => $val) {
            $channelInfo[0]['ChannelPoolID'] = $val['ChannelPoolID'];
            $channelInfo[0]['ChannelName'] = $val['ChannelTitle'];
            $channelInfo[0]['IsUse'] = true;
            $channelInfo[0]['IsUseName'] = 'Y';
            $channelInfo[0]['ServiceID'] = ($val['ProjectMode'] == 1 ? 'PROJECT' : 'MAINTENANCE');
            $channelInfo[0]['ChatRoomIdx'] = $val['ChatRoomIdx'];
            $channelInfo[0]['IsUseChat'] = true;
        }

        // 유저 참여 여부 업데이트
        //$this->chatMemberRepository->updateChatMemberIsParticipation($channelInfo['row'][0]['ChatRoomIdx'], $channelInfo['row'][0]['UserID'], 1);

        $data = [
            'ChannelInfo'                   => $channelInfo
        ];


        return $this->apiresult(200, '채널 공지 정보', 'success', $data);
    }

    /**
     * 채널 정보
     */
    public function informationteam( $_channel_id =  null)
    {

        if ($_channel_id === null) {
            return $this->apiresult(201, '잘못된 접근입니다', 'Channel');
        }
        $channelPoolID = $_channel_id;
        // 채널 정보
        $channelInfo =  array();;//$this->channelPoolRepository->selectChannelPool($channelPoolID)->first();
        /*$_select = array(
            "A.ProjectNo as ChannelPoolID",
            "A.ProjectTitle as ChannelTitle",
            "1 as IsUse","'Y' as IsUseName",
            "A.ProjectMode",
            "A.ProjectIdx as ChatRoomIdx"
        );
        $_dbresult = $this->api_model->getDetailInfo($_select, $channelPoolID);
        if (empty($_dbresult['row'])) {
            return $this->apiresult(201, '잘못된 접근입니다', 'Channel');
        }

        foreach($_dbresult['row'] as $key => $val) {
            $channelInfo[0]['ChannelPoolID'] = $val['ChannelPoolID'];
            $channelInfo[0]['ChannelName'] = $val['ChannelTitle'];
            $channelInfo[0]['IsUse'] = true;
            $channelInfo[0]['IsUseName'] = 'Y';
            $channelInfo[0]['ServiceID'] = ($val['ProjectMode'] == 1 ? 'PROJECT' : 'MAINTENANCE');
            $channelInfo[0]['ChatRoomIdx'] = $val['ChatRoomIdx'];
            $channelInfo[0]['IsUseChat'] = true;
        }*/

        // 유저 참여 여부 업데이트
        //$this->chatMemberRepository->updateChatMemberIsParticipation($channelInfo['row'][0]['ChatRoomIdx'], $channelInfo['row'][0]['UserID'], 1);

        $_TeamCode = 'Team001';
        $_TeamName = '모듈혁신2팀';
        $serviceID = 'TEAM';
        $channelInfo[0]['ChannelPoolID'] = $_TeamCode;
        $channelInfo[0]['ChannelName'] = $_TeamName;
        $channelInfo[0]['IsUse'] = true;
        $channelInfo[0]['IsUseName'] = 'Y';
        $channelInfo[0]['ServiceID'] = $serviceID;
        $channelInfo[0]['ChatRoomIdx'] = '1'; // teamcode  idx
        $channelInfo[0]['IsUseChat'] = true;



        $data = [
            'ChannelInfo'                   => $channelInfo,
            'ServiceNoticeList'             => null,
            'FixVideoChannelNoticeInfo'     => null,
            'LoopVideoChannelNoticeInfo'    => null,
        ];


        return $this->apiresult(200, '채널 공지 정보', 'success', $data);
    }

    public function msginsert(){

        $PostData = $this->input->post();
        $dbresult = $this->api_model->messageinsert($PostData);
        echo json_encode($dbresult);
        exit;

    }

    public function apiresult($code, $message, $desc = null, $data = array() )
    {
        $result = [
            'code'      => $code,
            'message'   => $message,
            'desc'      => $desc,
        ];
        if (!empty($data)) {
            $result['data'] = $data;
        }

        echo json_encode($result);
        //return $result;
    }


    public function tmpinsertmember(){

        $_select_array = array(453,532,185);
        $_MemberInfo = $this->api_model->getLocalHackersInfo2($_select_array);
        $_NUM=1;
        foreach ( (array)$_MemberInfo['row'] as $key=>$row ) {

            $tokenResult = $this->curl_post("http://hac.educamp.org/admin/external_site/api_user_picture.php?act=userid&vals=".$row['USER_ID']);
            if ( empty($tokenResult['error']) ) {
                $_faceinfo =   json_decode($tokenResult['response'],true);
                $_facedata = $_faceinfo[$row['USER_ID']];

            }else{
                $_facedata = null;
            }
            $basedata = array();
            $basedata['hackersidx'] = $row['MEMBER_IDX'];
            $basedata['hackersid'] = $row['USER_ID'];
            $basedata['email'] = $row['USER_ID']."@hackers.com";
            $basedata['password'] = getHashedPassword("hackers".$row['USER_ID']);
            $basedata['name'] = $row['USER_NAME'];
            $basedata['nickname'] = substr($row['USER_NAME'],3,9);
            $basedata['roleId'] = $row['HR_VAR_9']==147?ROLE_MANAGER:ROLE_EMPLOYEE;
            $basedata['face'] = $_facedata;
            $basedata['isDeleted'] = 0;
            $basedata['createdBy'] = 1;

            if ( empty($row['roleId']) ) {
                $_resullt = $this->api_model->insert_origin($basedata);
                if ($_resullt['result'] == false) {
                    echo "fail";
                    exit;
                }
                $_NUM++;
            }

        }
        $_resullt = array();
        $_resullt['result'] = true;
        $_resullt['count'] = $_NUM;
        echo json_encode($_resullt);
        exit;

    }

    public function getintra1(){

        if($this->isAdmin() == TRUE)
        {
            $_resullt = array();
            $_resullt['result'] = true;
            $_resullt['message'] = "접근권한이 없습니다.";
            echo json_encode($_resullt);
            exit;
        }
        else {
            $_MemberInfo = $this->api_model->getHackersInfo("ADMIN_MEMBER", "MEMBER_IDX");
            print_r($_MemberInfo['row']);exit;

            foreach ((array)$_MemberInfo['row'] as $key => $row) {

                $tokenResult = $this->curl_post("http://hac.educamp.org/admin/external_site/api_user_picture.php?act=userid&vals=" . $row['USER_ID']);
                if (empty($tokenResult['error'])) {
                    $_faceinfo = json_decode($tokenResult['response'], true);
                    $_facedata = $_faceinfo[$row['USER_ID']];

                } else {
                    $_facedata = null;
                }

                $basedata = array(
                    'MEMBER_IDX' => $row['MEMBER_IDX'],
                    'USER_ID' => $row['USER_ID'],
                    'USER_NAME' => $row['USER_NAME'],
                    'USER_NAME_SUFFIX' => $row['USER_NAME_SUFFIX'],
                    'POSITION_IDX' => empty($row['POSITION_IDX']) ? 0 : $row['POSITION_IDX'],
                    'CLASS_IDX' => empty($row['CLASS_IDX']) ? 0 : $row['CLASS_IDX'],
                    'GROUP_IDX' => empty($row['GROUP_IDX']) ? 0 : $row['GROUP_IDX'],
                    'DENIED' => $row['DENIED'],
                    'HR_VAR_9' => empty($row['HR_VAR_9']) ? 0 : $row['HR_VAR_9'],
                    'LIMIT_DATE' => $row['LIMIT_DATE'],
                    'LAST_LOGIN' => $row['LAST_LOGIN'],
                    'FACE_URL' => $_facedata,
                    'REGDATE' => $row['REGDATE']
                );

                $updatedata = $basedata;
                unset($updatedata['MEMBER_IDX'], $updatedata['USER_ID'], $updatedata['RegDatetime'], $updatedata['REGDATE']);

                $_resullt = $this->api_model->insert_on_duplicate_update_batch("ADMIN_MEMBER", $basedata, $updatedata);
                if ($_resullt['result'] == false) {
                    echo json_encode($_resullt);
                    exit;
                }

                if ($row['DENIED'] == 'Y') {
                    $_resullt = $this->api_model->denied_update_batch($row['USER_ID']);
                    if ($_resullt['result'] == false) {
                        echo json_encode($_resullt);
                        exit;
                    }
                }
            }
            $_resullt = array();
            $_resullt['result'] = true;
            echo json_encode($_resullt);
            exit;
        }

    }

    public function getintra2(){

        if($this->isAdmin() == TRUE)
        {
            $_resullt = array();
            $_resullt['result'] = true;
            $_resullt['message'] = "접근권한이 없습니다.";
            echo json_encode($_resullt);
            exit;
        }
        else {

            $_array_base = array();
            $_array_base[0]['TABLE_NAME'] = "ADMIN_MEMBER_CLASS";
            $_array_base[0]['ORDERBY'] = "IDX";
            $_array_base[1]['TABLE_NAME'] = "ADMIN_MEMBER_GROUP";
            $_array_base[1]['ORDERBY'] = "IDX";
            $_array_base[2]['TABLE_NAME'] = "ADMIN_MEMBER_POSITION";
            $_array_base[2]['ORDERBY'] = "IDX";
            $_array_base[3]['TABLE_NAME'] = "ADMIN_VARS";
            $_array_base[3]['ORDERBY'] = "IDX";

            foreach ($_array_base as $tkey => $tval) {

                $_Info = $this->api_model->getHackersInfo($tval['TABLE_NAME'], $tval['ORDERBY']);
                $_CNT2 = 0;
                foreach ((array)$_Info['row'] as $key => $row) {
                    if ($tkey == 0) {
                        $basedata = array(
                            'IDX' => $row['IDX'],
                            'D_IDX' => empty($row['D_IDX']) ? 0 : $row['D_IDX'],
                            'CODE' => $row['CODE'],
                            'NAME' => $row['NAME'],
                            'SORT' => empty($row['SORT']) ? 0 : $row['SORT'],
                            'GW_CODE' => $row['GW_CODE']
                        );
                        $updatedata = $basedata;
                        unset($updatedata['IDX']);

                        $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                        if ($_resullt['result'] === false) {
                            echo json_encode($_resullt);
                            exit;
                        }
                    } else if ($tkey == 1) {
                        $basedata = array(
                            'IDX' => $row['IDX'],
                            'D_IDX' => empty($row['D_IDX']) ? 0 : $row['D_IDX'],
                            'PARENT' => empty($row['PARENT']) ? 0 : $row['PARENT'],
                            'DEPTH' => empty($row['DEPTH']) ? 0 : $row['DEPTH'],
                            'SORT' => empty($row['SORT']) ? 0 : $row['SORT'],
                            'NAME' => $row['NAME'],
                            'PRIVATE_NAME' => $row['PRIVATE_NAME'],
                            'ACCESS_AUTH_LEVEL' => empty($row['ACCESS_AUTH_LEVEL']) ? 0 : $row['ACCESS_AUTH_LEVEL'],
                            'CONFIRM_NAME' => $row['CONFIRM_NAME'],
                            'CONFIRM_USERID' => $row['CONFIRM_USERID'],
                            'CONFIRM_E_NAME' => $row['CONFIRM_E_NAME'],
                            'CONFIRM_E_USERID' => $row['CONFIRM_E_USERID'],
                            'CONFIRM_ALL_AUTH' => $row['CONFIRM_ALL_AUTH'],
                            'CONFIRM_ALL_USERID' => $row['CONFIRM_ALL_USERID'],
                            'CONFIRM_ALL_NAME' => $row['CONFIRM_ALL_NAME']

                        );
                        $updatedata = $basedata;
                        unset($updatedata['IDX']);

                        $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                        if ($_resullt['result'] === false) {
                            echo json_encode($_resullt);
                            exit;
                        }
                    } else if ($tkey == 2) {
                        $basedata = array(
                            'IDX' => $row['IDX'],
                            'NAME' => $row['NAME'],
                            'NAME_ENG' => $row['NAME_ENG'],
                            'SORT' => empty($row['SORT']) ? 0 : $row['SORT']);
                        $updatedata = $basedata;
                        unset($updatedata['IDX']);

                        $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                        if ($_resullt['result'] == false) {
                            echo json_encode($_resullt);
                            exit;
                        }
                    } else {
                        $basedata = array(
                            'IDX' => $row['IDX'],
                            'D_IDX' => empty($row['D_IDX']) ? 0 : $row['D_IDX'],
                            'PARENT' => empty($row['PARENT']) ? 0 : $row['PARENT'],
                            'DEPTH' => empty($row['DEPTH']) ? 0 : $row['DEPTH'],
                            'SORT' => empty($row['SORT']) ? 0 : $row['SORT'],
                            'NAME' => $row['NAME'],
                            'PRIVATE_NAME' => $row['PRIVATE_NAME'],
                            'ACCESS_AUTH_LEVEL' => empty($row['ACCESS_AUTH_LEVEL']) ? 0 : $row['ACCESS_AUTH_LEVEL'],
                            'IS_DELETED' => empty($row['IS_DELETED']) ? 0 : $row['IS_DELETED'],
                            'GW_CODE' => $row['GW_CODE']
                        );
                        $updatedata = $basedata;
                        unset($updatedata['IDX']);

                        $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                        if ($_resullt['result'] === false) {
                            echo json_encode($_resullt);
                            exit;
                        }

                    }
                    $_CNT2++;
                }

            }
            $_resullt = array();
            $_resullt['result'] = true;
            echo json_encode($_resullt);
            exit;
        }

    }


    public function cron_1(){

        echo "ADMIN_MEMBER UPDATE START : ".date("Y-m-d H:i:s");
        echo "\n";
        $_MemberInfo = $this->api_model->getHackersInfo("ADMIN_MEMBER","MEMBER_IDX");
        $_cnt = 1;
        foreach ( (array)$_MemberInfo['row'] as $key=>$row ) {

            $tokenResult = $this->curl_post("http://hac.educamp.org/admin/external_site/api_user_picture.php?act=userid&vals=".$row['USER_ID']);
            if ( empty($tokenResult['error']) ) {
                $_faceinfo =   json_decode($tokenResult['response'],true);
                $_facedata = isset($_faceinfo[$row['USER_ID']])?$_faceinfo[$row['USER_ID']]:null;

            }else{
                $_facedata = null;
            }

            $basedata = array(
                'MEMBER_IDX' => $row['MEMBER_IDX'],
                'USER_ID' => $row['USER_ID'],
                'USER_NAME' => $row['USER_NAME'],
                'USER_NAME_SUFFIX' => $row['USER_NAME_SUFFIX'],
                'POSITION_IDX' => empty($row['POSITION_IDX'])?0:$row['POSITION_IDX'],
                'CLASS_IDX' => empty($row['CLASS_IDX'])?0:$row['CLASS_IDX'],
                'GROUP_IDX' => empty($row['GROUP_IDX'])?0:$row['GROUP_IDX'],
                'DENIED' => $row['DENIED'],
                'HR_VAR_9' => empty($row['HR_VAR_9'])?0:$row['HR_VAR_9'],
                'LIMIT_DATE' => $row['LIMIT_DATE'],
                'LAST_LOGIN' => $row['LAST_LOGIN'],
                'FACE_URL' => $_facedata,
                'REGDATE' => $row['REGDATE']
            );

            echo $_cnt." : USER_ID : ".$row['USER_ID']."USER_NAME : ".$row['USER_NAME'];
            echo "\n";

            $updatedata = $basedata;
            unset($updatedata['MEMBER_IDX'],$updatedata['USER_ID'], $updatedata['RegDatetime'],$updatedata['REGDATE']);

            $_resullt = $this->api_model->insert_on_duplicate_update_batch("ADMIN_MEMBER", $basedata, $updatedata);
            if ($_resullt['result'] == false) {
                echo "ADMIN_MEMBER UPDATE FAIL : ".date("Y-m-d H:i:s");
                echo "\n";
                exit;
            }

            if ($row['DENIED'] == 'Y') {
                $_resullt2 = $this->api_model->denied_update_batch($row['USER_ID']);
                if ($_resullt2['result'] == false) {
                    echo "ADMIN_MEMBER DENIED UPDATE FAIL : ".date("Y-m-d H:i:s");
                    echo "\n";
                    exit;
                }
            }

            $_cnt++;
        }
        echo "ADMIN_MEMBER UPDATE SUCCES, 건수 :".number_format($_cnt)." 작업시간 : ".date("Y-m-d H:i:s");
        echo "\n";
        exit;

    }

    public function cron_2(){
        echo "ADMIN_MEMBER_* ETC UPDATE START : ".date("Y-m-d H:i:s"); echo "\n";
        $_array_base = array();
        $_array_base[0]['TABLE_NAME'] = "ADMIN_MEMBER_CLASS";
        $_array_base[0]['ORDERBY'] = "IDX";
        $_array_base[1]['TABLE_NAME'] = "ADMIN_MEMBER_GROUP";
        $_array_base[1]['ORDERBY'] = "IDX";
        $_array_base[2]['TABLE_NAME'] = "ADMIN_MEMBER_POSITION";
        $_array_base[2]['ORDERBY'] = "IDX";
        $_array_base[3]['TABLE_NAME'] = "ADMIN_VARS";
        $_array_base[3]['ORDERBY'] = "IDX";

        foreach ( $_array_base as $tkey => $tval) {

            $_Info = $this->api_model->getHackersInfo($tval['TABLE_NAME'],$tval['ORDERBY']);
            $_CNT2 = 0;
            foreach ( (array)$_Info['row'] as $key=>$row ) {
                if ($tkey == 0) {
                    $basedata = array(
                        'IDX' => $row['IDX'],
                        'D_IDX' => empty($row['D_IDX']) ? 0 : $row['D_IDX'],
                        'CODE' => $row['CODE'],
                        'NAME' => $row['NAME'],
                        'SORT' => empty($row['SORT']) ? 0 : $row['SORT'],
                        'GW_CODE' => $row['GW_CODE']
                    );
                    $updatedata = $basedata;
                    unset($updatedata['IDX']);

                    $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                    if ($_resullt['result'] ===  false) {
                        echo "ADMIN_MEMBER_CLASS UPDATE FAIL : ".date("Y-m-d H:i:s");echo "\n";
                        if ( isset($_resullt['message']) ){
                            echo "ADMIN_MEMBER_CLASS UPDATE ERROR ".$_resullt['message'];
                            echo "\n";
                        }
                        exit;
                    }
                } else if ($tkey == 1) {
                    $basedata = array(
                        'IDX' => $row['IDX'],
                        'D_IDX' => empty($row['D_IDX']) ? 0 : $row['D_IDX'],
                        'PARENT' => empty($row['PARENT']) ? 0 : $row['PARENT'],
                        'DEPTH' => empty($row['DEPTH']) ? 0 : $row['DEPTH'],
                        'SORT' => empty($row['SORT']) ? 0 : $row['SORT'],
                        'NAME' => $row['NAME'],
                        'PRIVATE_NAME' => $row['PRIVATE_NAME'],
                        'ACCESS_AUTH_LEVEL' => empty($row['ACCESS_AUTH_LEVEL']) ? 0 : $row['ACCESS_AUTH_LEVEL'],
                        'CONFIRM_NAME' => $row['CONFIRM_NAME'],
                        'CONFIRM_USERID' => $row['CONFIRM_USERID'],
                        'CONFIRM_E_NAME' => $row['CONFIRM_E_NAME'],
                        'CONFIRM_E_USERID' => $row['CONFIRM_E_USERID'],
                        'CONFIRM_ALL_AUTH' => $row['CONFIRM_ALL_AUTH'],
                        'CONFIRM_ALL_USERID' => $row['CONFIRM_ALL_USERID'],
                        'CONFIRM_ALL_NAME' => $row['CONFIRM_ALL_NAME']

                    );
                    $updatedata = $basedata;
                    unset($updatedata['IDX']);

                    $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                    if ($_resullt['result'] === false) {
                        echo "ADMIN_MEMBER_GROUP UPDATE FAIL : ".date("Y-m-d H:i:s");echo "\n";
                        if ( isset($_resullt['message']) ) {
                            echo "ADMIN_MEMBER_CLASS UPDATE ERROR ".$_resullt['message'];echo "\n";
                        }
                        exit;
                    }
                } else if ($tkey == 2) {
                    $basedata = array(
                        'IDX' => $row['IDX'],
                        'NAME' => $row['NAME'],
                        'NAME_ENG' => $row['NAME_ENG'],
                        'SORT' => empty($row['SORT']) ? 0 : $row['SORT']  );
                    $updatedata = $basedata;
                    unset($updatedata['IDX']);

                    $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                    if ($_resullt['result'] == false) {
                        echo "ADMIN_MEMBER_POSITION UPDATE Fail : ".date("Y-m-d H:i:s");echo "\n";
                        if ( isset($_resullt['message']) ) {
                            echo "ADMIN_MEMBER_CLASS UPDATE ERROR ".$_resullt['message'];echo "\n";
                        }
                        exit;
                    }
                } else {
                    $basedata = array(
                        'IDX' => $row['IDX'],
                        'D_IDX' => empty($row['D_IDX']) ? 0 : $row['D_IDX'],
                        'PARENT' => empty($row['PARENT']) ? 0 : $row['PARENT'],
                        'DEPTH' => empty($row['DEPTH']) ? 0 : $row['DEPTH'],
                        'SORT' => empty($row['SORT']) ? 0 : $row['SORT'],
                        'NAME' => $row['NAME'],
                        'PRIVATE_NAME' => $row['PRIVATE_NAME'],
                        'ACCESS_AUTH_LEVEL' => empty($row['ACCESS_AUTH_LEVEL']) ? 0 : $row['ACCESS_AUTH_LEVEL'],
                        'IS_DELETED' => empty($row['IS_DELETED']) ? 0 : $row['IS_DELETED'],
                        'GW_CODE' => $row['GW_CODE']
                    );
                    $updatedata = $basedata;
                    unset($updatedata['IDX']);

                    $_resullt = $this->api_model->insert_on_duplicate_update_batch($tval['TABLE_NAME'], $basedata, $updatedata);
                    if ($_resullt['result'] === false) {
                        echo "ADMIN_VARS UPDATE FAIL : ".date("Y-m-d H:i:s");echo "\n";
                        if ( isset($_resullt['message']) ) {
                            echo "ADMIN_MEMBER_CLASS UPDATE ERROR ".$_resullt['message'];echo "\n";
                        }
                        exit;
                    }

                }

            }

        }
        echo "ADMIN_MEMBER_* ETC UPDATE UPDATE SUCCESS, 작업시간 : ".date("Y-m-d H:i:s"); echo "\n";
        exit;
    }


    /* hackers intranet todo regist API */
    public function intratodo(){

        $PostData = $this->input->post();
        $result = array();
        if ( $PostData['USERID']) {
            $result = $this->api_model->intranettodoinsert($PostData);
        }

        echo json_encode($result);
    }



    /* hackers intranet todo regist API */
    public function intra_team (){

        $PostData = $this->input->get();
        $authcode=  $PostData['authcode'];
        $intranet = '&intra=HAC';

        $site_url = 'http://222.122.234.15/_include/goAuthLinker.php?authcode='.$authcode.'&act=auth_data'.$intranet;
        $_authresult = json_decode(file_get_contents($site_url), true);

        if ( $_authresult['result'] !== 'success' ) {
            //print_r($result);
            alert_back('인증되지 않은 접근방법입니다.');
            redirect('main/home');
            exit;
        }


        if ( $PostData['USERID'] && $PostData['GIDX'] ) {
            /* 등록여부 확인 */
            $_is_hackers = $this->api_model->getUserInfoByUserID($PostData['USERID']);


            if (!isset($_is_hackers->userId)) {
                alert_back('등록되지 않은 직원입니다.');
                redirect('main/home');
                exit;
            }

            if ($PostData['USERID'] && $PostData['GIDX']) {
                $PostData['USERIDX'] = $_is_hackers->userId;
                $result1 = $this->api_model->intranteamviewinsert($PostData);
                if ( $result1['result']) {

                    $isLoggedIn = $this->session->userdata('isLoggedIn');
                    if(!isset($isLoggedIn) || $isLoggedIn != TRUE)
                    {

                        $email = strtolower($this->security->xss_clean($PostData['USERID']."@hackers.com"));
                        $password = "hackers".$PostData['USERID'];
                        $loginresult = $this->login_model->loginMe($email, $password);
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

                    }

                    redirect("manager/monitor/".$PostData['GIDX']);
                    exit;
                }else{
                    alert_back($result1['message']);
                    redirect('main/home');
                    exit;
                }
            } else {
                alert_back('필수항목이 없습니다.');
                redirect('main/home');
                exit;
            }
        }


    }


    public function  testsend(){

       /* $postData = array();
        array_push($data, array(
            "DATE" => "20190803",
            "USER_ID" => "jinwonkim",
            "WORKTYPE" => 1
        ));
        array_push($data, array(
            "DATE" => "20190804",
            "USER_ID" => "khkim9",
            "WORKTYPE" => 4
        ));

        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query(json_decode($postData))
            )
        ));
        $result = file_get_contents('http://project.hackers.com/api/intraworkday', false, $context);
        $result = mb_convert_encoding($result, 'UTF-8', mb_detect_encoding($result, 'UTF-8, ISO-8859-1', true));

        print_r($result);exit;*/


        //$hp = preg_replace("/[^0-9]/", "", "20190731");
        //$rehp = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "$1-$2-$3", $hp);

       /* $url = "http://project.hackers.com/api/intraworkday";
        $postData = array();
        array_push($postData, array(
            "DATE" => "20190803",
            "USER_ID" => "jinwonkim",
            "WORKTYPE" => 1
        ));
        array_push($postData, array(
            "DATE" => "20190804",
            "USER_ID" => "khkim9",
            "WORKTYPE" => 4
        ));
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $result = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($result);
        print_r($json);
        exit;*/


    }

    /* hackers intranet 특근 regist API */
    public function intraworkday(){

        $PostData = json_decode(file_get_contents('php://input'), true);
        //print_r($PostData);exit;

        $_succent_cnt = 0;
        $_dup_cnt = 0;
        foreach($PostData as $key => $val) {

              if ( $val['DATE'] ){
                  $ReDate = preg_replace("/[^0-9]/", "", $val['DATE']);
                  $val["DATE"] = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "$1-$2-$3", $ReDate);
              }else{
                  $_return = array();
                  $_return['result'] = false;
                  $_return['message'] = $val["USER_ID"]."의 날짜 정보가 누락되었습니다.";
                  echo json_encode($_return);
                  exit;

              }
             $dbresult = $this->api_model->intranetworkinsert($val);
             if ($dbresult['result'] == 2 ) {
                 $_dup_cnt++;
             }else if( !$dbresult['result'] ) {
                 echo json_encode($dbresult);
                 exit;
             }else{
                 $_succent_cnt++;
             }
        }
        $_return = array();
        $_return['result'] = true;
        echo json_encode($_return);
        exit;
    }

    public function stringmatch(){

        $PostData = json_decode(file_get_contents('php://input'), true);
        $sim = similar_text(strtolower($PostData['target']), strtolower($PostData['myvoice']), $percentage);
        $_return = array();
        $_return['result'] = $percentage;
        echo json_encode($percentage);
        exit;

    }

    public function imgupload(){

        $PostData = json_decode(file_get_contents('php://input'), true);


    }

    /*
    public function stringmatch(Request $request)
    {
        $target = json_decode($request->input('target'));
        $myvoice = json_decode($request->input('myvoice'));

        $sim = similar_text(strtolower($target), strtolower($myvoice), $percentage);
        $_return = array();
        $_return['result'] = $percentage;
        echo json_encode($percentage);
        exit;
    }
    */

}
