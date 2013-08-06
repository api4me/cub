<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

/*{{{ index */
    public function index($url = null) {
        $this->lsession->destory();
        // Back to home
        redirect();
    }
/*}}}*/
    
}
