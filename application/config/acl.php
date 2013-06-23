<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename acl.php
* @touch date Tuesday, May 14, 2013 AM01:55:52 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/


/*
|--------------------------------------------------------------------------
| Acl setting
|--------------------------------------------------------------------------
|
| Prevent user to access the controller or action of controller.
| The struct if below:
| $config['role'] = array(
|     "user type" => array(
|         "control1" => array("*"),
|         "control2" => array("action1", "action2", ...)
| If prevent all of controller, please add the "*" in array of controller.
|
| Role: super, admin, user, guest
|
*/
$config['role'] = array(
    "super" => array(
    //    "admin" => array("index", "logout"),
    ),
    "admin" => array(
        "admin" => array("user"),
    ),
    "buyer" => array(
        "admin" => array("*"),
    ),
    "sell" => array(
        "admin" => array("*"),
    ),
    "guest" => array(
        "admin" => array("*"),
        "user" => array("*"),
    ),
);
