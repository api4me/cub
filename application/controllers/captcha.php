<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename captcha.php
* @touch date Wednesday, May 15, 2013 AM01:26:39 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Captcha extends CI_Controller {
    private $captcha;

    public function __construct() {
        parent::__construct();
        $this->load->library("lcaptcha");
        $this->captcha = new Lcaptcha();
    }
    public function index() {
        $this->captcha->show();
    }
}
