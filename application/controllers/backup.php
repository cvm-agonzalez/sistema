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

var_dump("Cargue backup");
var_dump($backup);
		// Si es el primer backup despues de facturacion le cambio el nombre
		$dia=date('d');
		$hora=date('H');
		if ( $dia == 1 && $hora < 10 ) {
			$extra="postfact";
		} else {
			$extra="";
		}
		// Load the file helper and write the file to your server
		$this->load->helper('file');
		if ( $extra != "" ) {
			write_file('db_bkp/'.date('Y-m-d').'_postfact.gz', $backup); 
		} else {
			write_file('db_bkp/'.date('Y-m-d').'.gz', $backup); 
var_dump("grabe");
		}
	}

}

/* End of file backup.php */
/* Location: ./application/controllers/backup.php */
