<?php 
class ManagedRouterAPI {
	protected $url;
	protected $login_url;
	protected $search_url;
	protected $add_url;
	protected $update_url;
	protected $delete_url;
	protected $undelete_url;
	protected $username;
	protected $password;
	protected $display_errors;
	protected $token;
	protected $logged_in;
	public $response;

	/**
	     * Makes an HTTP POST request to the login url with 3 variables username and password.
	     *Also, 
	     *
	     * @param string $url
	     * @param string $username 
	     * @param string $password 
	     * @param boolean $display_errors 
	     * @return boolean
    **/

	function __construct($url, $username, $password, $display_errors = false) {

		$this->url = $url;
		$this->login_url = $url."/users/login";
		$this->search_url = $url."/mr/search";
		$this->add_url = $url."/mr/add";
		$this->update_url = $url."/mr/update";
		$this->delete_url = $url."/mr/delete";
		$this->undelete_url = $url."/mr/undelete";
		$this->username = $username;
		$this->password = $password;
		$this->display_errors = $display_errors;
		$this->logged_in = $this->login();
		if($this->logged_in){
			return true;
		}else {
			return false;
		}
	}
	/**
	     * Makes an HTTP POST request to the login url with 3 variables from constructor.
	     *Also, 
	     * 
	     *Sets token needed for Authorization: in Header
	     *
	     * @return boolean
    **/
	public function login(){

		$data = array("username" => $this->username, "password" => $this->password);
		$data_string = json_encode($data);                                                                                       
		$ch = curl_init($this->login_url);    
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(        
		    'Content-Type: application/json',                  
		    'Content-Length: ' . strlen($data_string))         
		);                                                 
		$result_string = curl_exec($ch);
		$this->response = $result_string;
		$result = json_decode($result_string);
		
		if(property_exists($result,'error')){
			curl_close($ch);
			return false;
		} else{
			$this->token = $result->token;
			curl_close($ch);	
			return true;
		}

	}
	/**
	     * Makes an HTTP POST request to the url with 1 variable.
	     *
	     * @param string $url
	     * @param string $param 
	     * @return string $result
	     *
    **/

	public function startCurl($url, $params = ''){
		if($params != '')
		$params = json_encode($params);
	
		$header = array();
		$header[] = 'Content-Type: application/json';
		$header[] = 'Content-Length: ' . strlen($params);
		$header[] = 'Authorization: Bearer '.$this->token;

		$ch = curl_init($url);    
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                 
		$result = curl_exec($ch);
		$this->response = $result;
		curl_close($ch);
		return $result;
	}

	/**
	     * Makes an HTTP POST request to the search url with 2 variables.
	     *
	     * @param string $params
	     * @param integer $limit 
	     * @return array $result
	     *
    **/

	public function search($params = '',$limit = 0){
		if(($params != '') && ($limit == 0 )) {
			if(is_string($params)){
				$params = array($params);
				$result_string = $this->startCurl($this->search_url,$params);
			}
		} else if(($params != '') && ($limit != 0 )) {
			$query = array(
				"q" => $params,
				"limit" => $limit
				);
			$result_string = $this->startCurl($this->search_url,$query);	

		} else {
			$result_string = $this->startCurl($this->search_url);	
		}

		$routerList = json_decode($result_string);
		

		if(is_array($routerList)){
			return $routerList;
		} else {
			return false;
		}
		
	}

	/**
	     * Makes an HTTP POST request to the add url with 3 variables and returns
	     *router array if update is successful.
	     *
	     * @param string $serial
	     * @param string $mac
	     * @param string $name
	     * @return array $router 
	     *
    **/

	public function add($serial, $mac, $name){
		$params = array(
  			"serial"=>$serial,
  			"mac"=>$mac,
  			"name"=>$name
		);

		$result_string = $this->startCurl($this->add_url,$params);

		$router = json_decode($result_string);
		
		if(is_array($router)){
			return $router;
		} else {
			return false;
		}
	}

	/**
	     * Makes an HTTP POST request to update url with 4 variable.
	     *
	     * @param integer $routerId
	     * @param string $serial
	     * @param string $field
	     * @param string $value
	     * @return object $result
	     *
    **/

	public function update($routerId, $serial, $field, $value){
		if($field === "mac"){
			$params = array(
				'id' => $routerId,
				'serial'=> $serial,
				'new' => array('mac' => $value)
				);	
		} else if($field === "name") {
			$params = array(
				'id' => $routerId,
				'serial'=> $serial,
				'new' => array('name' => $value)
				);	
		}

		$result_string = $this->startCurl($this->update_url,$params);

		$router = json_decode($result_string);
		
		if(is_object($router)){
			return $router;
		} else {
			return false;
		}
	}

	/**
	     * Makes an HTTP POST request to delete url with 2 variable.
	     *
	     * @param integer $routerId
	     * @param string $serial
	     * @return array $result
	     *
    **/
	public function delete($routerId, $serial) {
		$params = array(
				'id'=> $routerId,
				'serial' => $serial
				);
		$result_string = $this->startCurl($this->delete_url,$params);
		
		$status = json_decode($result_string);
		if(is_array($status)){
			return $status;
		}else {
			return false;
		}
	}

	/**
	     * Makes an HTTP POST request to undelete url with 2 variable.
	     *
	     * @param integer $routerId
	     * @param string $serial
	     * @return object $router
	     *
    **/

	public function undelete($routerId, $serial) {
		$params = array(
				'id'=> $routerId,
				'serial' => $serial
				);
		$result_string = $this->startCurl($this->undelete_url,$params);

		$router = json_decode($result_string);
		if(is_array($router)){
			return $router[0];	
		} else {
			return false;
		}
	}

	/**
    	*Returns the json string from the API after an action is performed. 
    **/
	public function getResponse(){
		return $this->response;
	}
	

	
}
?>