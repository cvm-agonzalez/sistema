<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        if($_GET['order'] != 'asdqwe'){exit('No Permitido');}
        //$this->load->helper('url');
    }

	public function index()
	{
		$this->load->database();
		$this->load->dbutil();

		// Backup your entire database and assign it to a variable
		$backup =& $this->dbutil->backup(); 

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		write_file('db.bkp/'.date('Y-m-d').'.gz', $backup); 
	}

}

/* End of file backup.php */
/* Location: ./application/controllers/backup.php */