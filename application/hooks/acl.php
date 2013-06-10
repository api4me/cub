<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename Init.php
* @touch date Tuesday, May 14, 2013 AM01:20:10 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Acl {

/*{{{ variable */
    private $control;
    private $action;
    private $CI;
 /*}}}*/
/*{{{ __construct*/
    function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');

        $this->control = $this->CI->uri->segment(1);
        $this->action = $this->CI->uri->segment(2);
    }
/*}}}*/
/*{{ auth */
    function auth() {
        $user = $this->CI->lsession->get('user');
        if(empty($user)) {
            $user->role = "guest";
        }

        $this->CI->load->config('acl');
        $role = $this->CI->config->item('role');
        if (isset($role[$user->role])) {
            $controllers = $role[$user->role];
            
            if (isset($controllers[$this->control])) {
                $actions = $controllers[$this->control];
                if (in_array("*", $actions) || in_array($this->action, $actions)) {
                    redirect("/login");
                    //show_404('您无权访问该功能，该错误已经被记录！点击<a href="'. site_url('login') .'">返回</a>');
                }
            }
        } else {
            show_404('错误的用户类型，该错误已经被记录！点击<a href="'. site_url('login') .'">返回</a>');
        }
    }
/*}}}*/

}
