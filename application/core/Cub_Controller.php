<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename controller.php
* @touch date Friday, May 17, 2013 AM07:16:42 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Cub_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper("form");                                                     
        $this->load->helper("common");                                                     
    }

    protected function render($path, $out) {
        $this->load->library("twig"); 

        if (!isset($out["common"])) {
            // User Data
            $out["common"]["user"] = $this->lsession->get('user');
            // Controller
            $out["common"]["control"] = $this->router->class;
        }
        $this->twig->display($path, $out);
    }
}
