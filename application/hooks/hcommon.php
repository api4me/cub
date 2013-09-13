<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename hcommon.php
* @touch date Tuesday, May 14, 2013 AM01:20:10 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class HCommon {

/*{{ auth */
    function nocache() {
        // Fix Broswer cache issue
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-control:no-cache,no-store,must-revalidate');
        header("Pragma:no-cache");
        header("Expires:0");
    }
/*}}}*/

}
