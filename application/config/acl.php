<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename acl.php
* @touch date Tuesday, May 14, 2013 AM01:55:52 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0'
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
|     'user type' => array(
|         'control1' => array('*'),
|         'control2' => array('action1', 'action2', ...)
| If prevent all of controller, please add the '*' in array of controller.
|
| Role: super, admin, leader, appraiser, waiter, buyer, sell, guest
|
*/
$config['role'] = array(
    'super' => array(
    //    'admin' => array('index', 'logout'),
        'user' => array('*'),
    ),
    'admin' => array(
        'admin' => array('prebook', 'purchase', 'test', 'auction', 'deal', 'car', 'stat'),
        'user' => array('*'),
    ),
    'leader' => array(
        'admin' => array('prebook', 'purchase', 'test', 'stat', 'user'),
        'user' => array('*'),
    ),
    'appraiser' => array(
        'admin' => array('prebook', 'purchase', 'auction', 'deal', 'car', 'stat', 'user'),
        'user' => array('*'),
    ),
    'waiter' => array(
        'admin' => array('test', 'auction', 'deal', 'car', 'stat', 'user'),
        'user' => array('*'),
    ),
    'buyer' => array(
        'admin' => array('*'),
        'user' => array('sell'),
    ),
    'sell' => array(
        'admin' => array('*'),
        'user' => array('buy'),
    ),
    'guest' => array(
        'admin' => array('*'),
        'user' => array('*'),
    ),
);
