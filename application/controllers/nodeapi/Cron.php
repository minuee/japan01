<?php
//if(!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Class : API ( OrderingController)
 * User Class to control all user related operations.
 * @author : Noh SeongNam
 * @version : 1.1
 * @since : 10 January 2019
 */
class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api_model');
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

}