<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename pagination.php
* @touch date Thursday, May 16, 2013 AM05:27:27 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0'
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['per_page'] = 10; 

$config['full_tag_open'] = '<div class="pagination pagination-right"><ul>';
$config['full_tag_close'] = '</ul></div>';

// First
$config['first_link'] = '首页';
$config['first_tag_open'] = '<li>';
$config['first_tag_close'] = '</li>';

// Last
$config['last_link'] = '最后';
$config['last_tag_open'] = '<li>';
$config['last_tag_close'] = '</li>';

// Next
$config['next_link'] = '&gt;';
$config['next_tag_open'] = '<li>';
$config['next_tag_close'] = '</li>';

// Prev
$config['prev_link'] = '&lt;';
$config['prev_tag_open'] = '<li>';
$config['prev_tag_close'] = '</li>';

// Current
$config['cur_tag_open'] = '<li class="active"><a href="#">';
$config['cur_tag_close'] = '</a></li>';

// Number
$config['num_tag_open'] = '<li>';
$config['num_tag_close'] = '</li>';
