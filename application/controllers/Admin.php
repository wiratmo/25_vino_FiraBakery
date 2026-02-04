
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct(){
        parent:: __construct();
        if($this->session->userdata('role') !== 'admin'){
            redirect('auth');
        }
    } 
	public function index()
	{
		$this->Template->load('dashboard','home');
	}
}