<?php

use Restserver\Libraries\REST_Controller;

if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class User extends CI_Controller
{
	use REST_Controller {
		REST_Controller::__construct as private __resTraitConstruct;
	}

	public function __construct()
	{
		parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model('user_m');
		$this->load->library('encryption');
		$this->load->library('session');
	}

	public function postUser_post()
	{
		$userData = $this->post();
		if (!empty($userData['firstname']) && !empty($userData['lastname']) && !empty($userData['email']) && !empty($userData['phone'])) {
			$insert = $this->user_m->insert($userData);
			if ($insert) {
				$response = array('status' => true, 'code' => 200, 'message' => 'success');
				$this->response($response, 200);
			} else {
				$response = array('status' => false, 'code' => 400, 'message' => 'fail');
				$this->response($response, 400);
			}
		} else {
			$this->response(array('status' => false, 'code' => 400, 'message' => 'No user data were provided'), 400);
		}
	}

	public function user_put()
	{
		$userData = array();
		$id = $this->put('id');
		$userData['first_name'] = $this->put('first_name');
		$userData['last_name'] = $this->put('last_name');
		$userData['email'] = $this->put('email');
		$userData['phone'] = $this->put('phone');
		if (!empty($id) && !empty($userData['first_name']) && !empty($userData['last_name']) && !empty($userData['email']) && !empty($userData['phone'])) {
			$update = $this->user->update($userData, $id);
			if ($update) {
				$this->response(array(
					'status' => TRUE,
					'message' => 'User has been updated successfully.'
				), REST_Controller::HTTP_OK);
			} else {
				$this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response("Provide complete user information to update.", REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function user_delete($id)
	{
		if ($id) {
			$delete = $this->user->delete($id);
			if ($delete) {
				$this->response(array(
					'status' => TRUE,
					'message' => 'User has been removed successfully.'
				), REST_Controller::HTTP_OK);
			} else {
				$this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response(array(
				'status' => FALSE,
				'message' => 'No user were found.'
			), REST_Controller::HTTP_NOT_FOUND);
		}
	}

}
