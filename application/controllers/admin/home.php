<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index() {
        $user = $this->lsession->get("user");
        if ($user->role == "super") {
            redirect("/admin/user");
        } else {
            redirect("/admin/car");
        }
    }
}
