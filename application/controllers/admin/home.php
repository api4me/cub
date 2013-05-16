<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public function  __construct(){
        parent::__construct();
        $this->load->helper(array("form"));
        $this->load->library("twig");
    }

    public function index() {
        // Default to car
        $this->car();
    }
    public function car() {
        $this->twig->display("admin_home_index.html");
    }

}
