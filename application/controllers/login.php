<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
/*{{{ index */
    public function index($url = null) {
        $this->load->library("twig");

        $data["captcha"] = "Test login";
        $this->twig->display("login.html", $data);
    }
/*}}}*/
}
