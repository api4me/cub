<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename api.php
* @touch date Wednesday, May 15, 2013 AM01:07:05 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Api extends CI_Controller {
    public function index() {
        $data = new stdClass();
        $data->a = "aa";
        echo json_encode($data);
    }
}
