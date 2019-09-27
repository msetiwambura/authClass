<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_m extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('encryption');
	}

	function getRows($username, $password)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		$user = $query->row();
		if (isset($user)) {
			$store_password = $this->encryption->decrypt($user->password);
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
	public function insert($data = array())
	{
		if (!array_key_exists('created', $data)) {
			$data['created'] = date("Y-m-d H:i:s");
		}
		if (!array_key_exists('modified', $data)) {
			$data['modified'] = date("Y-m-d H:i:s");
		}
		unset($data['id']);
		$insert = $this->db->insert('users', $data);
		if ($insert) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	public function update($data, $id)
	{
		if (!empty($data) && !empty($id)) {
			if (!array_key_exists('modified', $data)) {
				$data['modified'] = date("Y-m-d H:i:s");
			}
			$update = $this->db->update('users', $data, array('id' => $id));
			return $update ? true : false;
		} else {
			return false;
		}
	}

	public function delete($id)
	{
		$delete = $this->db->delete('users', array('id' => $id));
		return $delete ? true : false;
	}

}
