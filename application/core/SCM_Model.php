<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SCM_Model extends CI_Model {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public $_limit_cnt = 10;
	public $_limit_offset = 15;

    public $secret_key = "hackers2018";
    public $secret_iv = "#@$%^&*()_+=-";
    
	/**
	 * Cross-site Scripting clean
	 * insert 혹은 update 시의 데이터를(배열) 통째로 넣어 사용
	 * ex) $insertData = $this->xssClean($insertData);
	 * 
	 * @param array $data
	 * @return unknown
	 */
	public function xssClean($data = []){
	    $this->load->helper('security');
	    return $this->security->xss_clean($data);
	}
	
	// 
	public function __limit()
	{
		$_limit = array();
		$_limit['cnt'] = $_limit_cnt;
		$_limit['offset'] = $_limit_offset;
	}

	/**
	 * where 조건이 형식정보와 함께 입력 된경우 where문을 생성 해 줌
	 * 배열의 형식 : [호출할 함수][컬럼명][값]
	 * 	ex> 
	 * 1. 일반적인 where 조건 추가 
	 * 	$where['like']['colName'] = 'data';
	 * 
	 * 2. 괄호 포함(group) where 조건 추가
	 * $innerArray = array();
	 * $innerArray['like']['colName'] = 'data';
	 * $innerArray['or_where']['colName1'] = 'data';
	 * 
	 * $where['where'][] = $innerArray ;
	 * => 그룹조건과 이전 조건과의 연결을 and로 하는 경우에는 'where'로, or로 하는 경우에는 'or_where'로 호출함수를 설정
	 * 
	 * 실제 호출되는 CI함수 형태
	 * 1. 컬럼명 존재 : [호출할 함수]([컬럼명], [값]);
	 * 2. 컬럼명 미입력(배열의 key가 String이 아닌 경우) : [호출할 함수]([값]);
	 */
	public function setWhere($where = array(), $_dataBaseName = "" ){
	    $database = $this->db;
	    if(!empty($_dataBaseName)){
	        $database = $this->$_dataBaseName;
	    }
		if ( is_array($where) && count($where) ) {
			foreach ((array)$where as $key=>$_where ){ //함수명 key
				foreach ((array)$_where as $_key=>$_val ){ //실제 where 값
					if(is_array($_val) && strpos($key,'_in') === false){ //배열인 경우 그룹핑(where_in, where_not_in 제외)
						if(strpos($key,'or') === 0){
						    $database->or_group_start();
						}else{
						    $database->group_start();
						}
						//재귀함수 호출
						$this->setWhere((array)$_val, $_dataBaseName);
						$database->group_end();
					}else{
						if(is_string($_key)){
							if(strpos($_key,':') !== false){
								$colName = str_replace(':', '.', $_key);
								$database->{$key}($colName, $_val);
							}else{
							    $database->{$key}($_key, $_val);
							}
						}else{
						    $database->{$key}($_val);
						}
					}
				}
			}
		}
	}
	
	/**
	 * 배열 내 공백 데이터가 있는 경우 해당 배열 요소를 null로 변경해주는 함수
	 * DB에 데이터 입력 시 날짜데이터가 잘못 입력되는 문제가 발생하여 생성
	 */
	public function setEmpty($value = array()){
		$returnValue = $value;
		if ( is_array($value) && count($value) ) {
			foreach((array)$value as $key=>$val ) {
				if(gettype($val) == 'array' ){ //배열인 경우 재호출
					$returnValue[$key] = $this->setEmpty($val);
				} elseif(empty($val)){
					if(!is_numeric($val)){
						$returnValue[$key] = null;
					}
				}
			}
		}elseif(empty($returnValue)){
			$returnValue = null;
		}
		return $returnValue;
    }
	
	// 쿼리의 결과에 레코드번호를 붙여준다.
	public function setRowNum( $_tcount=0, $_dataBaseName = "" ) {
        
	    if(!empty($_dataBaseName)){
	        // $_dataBaseName setRowNum을 사용한 곳에서 사용 하고 있는 databasename을 그대로 사용 
	        // ex) $this->"db"->setRowNum(); "" 사이에있는게 dbname ex2) $this->db_intra->setRowNum() ...
	        // 이 안에서 새로 선언하면 내용이 초기화 되어 아래 함수들을 사용할수 없으니 기존에 선언된 디비를 그대로 사용한다
	        $_sql = $this->$_dataBaseName->get_compiled_select();
	    }else{
	        $_sql = $this->db->get_compiled_select();
	    }
		
		$sql = '
		SELECT
			@ROWNUM := @ROWNUM - 1 AS ROWNUM ,
			A.*
		FROM
			('.$_sql.') A, (SELECT @ROWNUM := '.($_tcount+1).') R
		';
		
		if(!empty($_dataBaseName)){
		    $query = $this->$_dataBaseName->query($sql);
		}else{
		    $query = $this->db->query($sql);
		}

		return $query;
	}
	
    public function getInsertId($tableName = false,$fieldName=false) {
        $returnValue = false;
        if ( $tableName && $fieldName ) {
            $this->db->select_max($fieldName);
            $query = $this->db->get($tableName);
            if ( $query->num_rows() ) {
                $row = $query->row_array();
                $returnValue = (int)$row[$fieldName] + 1;
            } else {
                $returnValue = 1;
            }
        }
        return $returnValue;
    }
    
    /**
     * $param 데이터 검증
     * $inculde_keys에 table의 column데이터를 넣으면 $param에서 해당하는 값만 사용
     * 아래 function과 같이 씀 
     * ex) 
     * $updateData = $this->filter_keys($updateOriginData, $this->getColumnData("User"));
     * $this->db->update("User", $updateData);
     * 
     * @param array $params
     * @param array $include_keys
     * @return mixed[]
     */
    public function filter_keys($params = [], $include_keys = []) {
        $new_arr = [];
        $params = (array)$params;
        foreach ($params as $key => $val) {
            if ( in_array($key, $include_keys) ) {
                $new_arr[$key] = $params[$key];
            }
        }
        return $new_arr;
    }
    
    /**
     * 테이블명으로 테이블의 column을 가져온다
     * 
     * @param string $tableName
     * @return boolean|columnData
     */
    public function getColumnData($tableName = ""){
        if(empty($tableName)){
            return false;
        }
        return $this->db->list_fields($tableName);
    }
    
    //bit 타입으로 변환
	public function str2bit($value = array(), $prefix= 'Is', $isNull2zero = false){
		$returnValue = false;
    	if ( is_array($value)  && count($value) ) {
    		foreach((array)$value as $key=>$val ) {
    			if(strncmp($key, $prefix, strlen($prefix)) === 0
    				&& (!is_null($val) || $isNull2zero)) {
    					$returnValue[$key] = (boolean)$val;
    			} else {
    				$returnValue[$key] = $val;
    			}
    		}
    	} else {
    		if(!is_null($value) || $isNull2zero) {
    			$returnValue = (int) $value;
    		}else {
    			$returnValue = $value;
    		}
    	}
    	return $returnValue;
    }


    function Encrypt($str, $secret_key='secret key', $secret_iv='secret iv')
    {
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16)    ;

        return str_replace("=", "", base64_encode(
                openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv))
        );
    }


    function Decrypt($str, $secret_key='secret key', $secret_iv='secret iv')
    {
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_decrypt(
            base64_decode($str), "AES-256-CBC", $key, 0, $iv
        );
    }

}
