<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Userlogin_m extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	function loginUser($username, $password)
	{
		log_message("debug", "onlyUsername".json_encode($username));
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		$user = $query->row();
		if (isset($user)) {
			$store_password = $this->passDecrypt($user->password);
			if ($password == $store_password) {
				return $this->session->set_userdata(array(
					'id' => $user->id,
					'username' => $user->username,
					'firstname' => $user->firstname,
					'lastname' => $user->lastname,
					'email' => $user->email,
					'phone' => $user->phone));
			} else {
				return 'Wrong Password';
			}
		} else {
			return 'Wrong Username';
		}
	}
	function passDecrypt($jsonString){
		$passphrase = "6be5aee122ff6537f28b0c1792c83e286f44656213caa72c58c828c37fdfa4fd2a2f4d5e21e8e8f7ee18795307027efff5f536007ed93a920d52d3cb20b87208R0bJvHMqqbPjVFaWt3VlOxby";
		$jsonData = explode("__",$jsonString);
		try {
			$salt = hex2bin($jsonData[2]);
			$iv  = hex2bin($jsonData[1]);
		} catch(Exception $e) { return null; }
		$ciphertext = base64_decode($jsonData[0]);
		$iterations = 999;
		$key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);
		$decrypted= openssl_decrypt($ciphertext , 'AES-256-CBC', hex2bin($key), OPENSSL_RAW_DATA, $iv);
		return $decrypted;

	}
}
