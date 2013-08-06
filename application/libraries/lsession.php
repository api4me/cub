<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename lsession.php
* @touch date Wednesday, May 15, 2013 PM02:03:13 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class LSession {

/*{{{ __construct */
    public function __construct(){
        $ci =& get_instance();
        $time = $ci->config->item("sess_time_to_update");
      
        session_cache_limiter('private');
        session_cache_expire($time / 60);
        if (!isset($_SESSION)) {
            session_start();
        }
    }
/*}}}*/
/*{{{ set */
    function set($key, $val){
        $_SESSION[$key] = $val;
    }
/*}}}*/
/*{{{ get */
    function get($key){
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }

        return false;
    }
/*}}}*/
/*{{{ del */
    public function del($key){
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }
/*}}}*/
/*{{{ destory */
    public function destory(){
        if(!isset($_SESSION)) return null;
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-1, '/');
        }
        session_unset();
        session_destroy();
    }
/*}}}*/

}
