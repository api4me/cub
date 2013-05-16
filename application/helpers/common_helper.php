<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename common_helper.php
* @touch date Thursday, May 16, 2013 PM12:35:58 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*{{{ option_value */
if (!function_exists("option_value")) {
    function option_value($type, $code) {
        $ci =& get_instance();
        $ci->load->library("lcommon");
        if ($val = $ci->lcommon->option($type, $code)) {
            return $val;
        }

        return $code;
    }
}
/*}}}*/
