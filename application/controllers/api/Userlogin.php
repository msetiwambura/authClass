<?php

use Restserver\Libraries\REST_Controller;

if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Userlogin extends CI_Controller
{
	use REST_Controller {
		REST_Controller::__construct as private __resTraitConstruct;
	}

	public function __construct()
	{
		parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model('userlogin_m');
		$this->load->library('session');
	}
	public function loginUser_post()
	{
		$userData = $this->post();
		$pass = $userData['password'];
		$password = $this->passDecrypt($pass);
		if (empty($userData['username']) || empty($password)){
			$this->response(array('status'=>'fail','code'=>400,'message'=>'Username or password was not provided'),400);
		}else {
			$users = $this->userlogin_m->loginUser($userData['username'],$password);
			if (empty($users)) {
				$response = array('results' =>  $this->session->userdata(), 'code' => 200, 'message' => 'success');
				$this->response($response, 200);
			} else {
				$response = array('results' =>  $users, 'code' => 404, 'message' => 'fail');
				$this->response($response, 200);
			}
		}
	}
	function passDecrypt($jsonString){
		$passphrase = "6be5aee122ff6537f28b0c1792c83e286f44656213caa72c58c828c37fdfa4fd2a2f4d5e21e8e8f7ee18795307027efff5f536007ed93a920d52d3cb20b87208R0bJvHMqqbPjVFaWt3VlOxby";
		try {
			$salt = hex2bin($jsonString["salt"]);
			$iv  = hex2bin($jsonString["iv"]);
		} catch(Exception $e) { return null; }
		$ciphertext = base64_decode($jsonString["ciphertext"]);
		$iterations = 999;
		$key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);
		$decrypted= openssl_decrypt($ciphertext , 'AES-256-CBC', hex2bin($key), OPENSSL_RAW_DATA, $iv);
		return $decrypted;

	}
}
