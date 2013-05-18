<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename base.php
* @touch date Friday, May 17, 2013 AM06:43:48 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
class Base extends CI_Controller{
    public function __construct(){
        parent::__construct();
        echo "base";
    }
}
