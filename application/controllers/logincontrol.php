<?php

class logincontrol extends CI_Controller {
	
	public function __construct() {
		parent::__construct();

	// Load form helper library
		$this->load->helper('form');

	// Load form validation library
		$this->load->library('form_validation');

	// Load session library
		$this->load->library('session');

	// Load database
		$this->load->model('loginmodel');
		$this->load->database();
	}

	public function index() {
		$this->load->helper('url');
		$this->load->view('login');
	}

	public function login_authentication(){

		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			if(isset($this->session->userdata['logged_in'])){
				$this->load->view('dashboard');
			}else{
				$this->load->view('login');
			}
		} else {
			$data = array(
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password')
				);
			$result = $this->loginmodel->login($data);
			if ($result == TRUE) {
				$email = $this->input->post('email');
				$result = $this->loginmodel->read_user_information($email);
				if ($result != false) {
					$session_data = array(
						'id' => $result[0]->id,
						'nama' => $result[0]->nama,
						'email' => $result[0]->email,
						'tanggal'=>$result[0]->tanggal_lahir
						);
					// Add user data in session
					$this->session->set_userdata('logged_in', $session_data);
					$this->load->view('dashboard');
				}
			} else {
				$data = array(
					'error_message' => 'Invalid email or Password'
					);
				$this->load->view('login', $data);
			}
		}
	}


	public function register_page(){
		
// Check validation for user input in SignUp form
		$this->form_validation->set_rules('nama', 'Nama', 'required');
		$this->form_validation->set_rules('alamat', 'Alamat', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('register');
		} else {
			$data = array(
				'nama' => $this->input->post('nama'),
				'alamat' => $this->input->post('alamat'),
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password')
				);
			$result = $this->login_database->registration_insert($data);
			if ($result == TRUE) {
				$data['message_display'] = 'Registration Successfully !';
				$this->load->view('login', $data);
			} else {
				$data['message_display'] = 'Username already exist!';
				$this->load->view('register', $data);
			}
		}
	}

	public function logout() {

	// Removing session data
		$sess_array = array(
			'email' => ''
			);
		$this->session->unset_userdata('logged_in', $sess_array);
		$data['message_display'] = 'Successfully Logout';
		$this->load->view('login', $data);
	}
}
