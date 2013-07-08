<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index() {
        $user = $this->lsession->get("user");
        switch ($user->role) {
            case 'super':
                redirect("/admin/user");
                break;
            case 'admin':
                redirect("/admin/article");
                break;
            case 'leader':
                redirect("/admin/auction");
                break;
            case 'appraiser':
                redirect("/admin/test");
                break;
            case 'waiter':
                redirect("/admin/prebook");
                break;
        }
    }
}
