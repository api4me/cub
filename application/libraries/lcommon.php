<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename common_helper.php
* @touch date Wednesday, May 15, 2013 AM08:56:26 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class LCommon {

/*{{{ variable */
    private $CI;
/*}}}*/
/*{{{ __construct */
    public function __construct() {
        $this->CI =& get_instance();
    }
/*}}}*/
/*{{{ option */
    public function option($type, $code = null, $all = false) {
        static $data;
        if (!isset($data)) {
            $data = array();
            $q = "SELECT * FROM #options";
            $query = $this->CI->db->query($q);
            foreach($query->result() as $row) {
                $data[$row->type][$row->code] = $all ? $row : $row->name;
            }
        }

        if (isset($data[$type])) {
            if ($code) {
                return (isset($data[$type][$code])) ? $data[$type][$code] : false;
            } else {
                return $data[$type];
            }
        }

        return false;
    }
/*}}}*/
/*{{{ form_option */
    public function form_option($type, $blankline = true, $remove = false) {
        if ($data = $this->option($type)) {
            if ($blankline) {
                array_unshift($data, "");
            }

            if (is_array($remove)) {
                foreach ($remove as $val) {
                    if (isset($data[$val])) {
                        unset($data[$val]);
                    }
                }
            }

            return $data;
        }

        return array();
    }
/*}}}*/

/*{{{ validate area */
/*{{{ is_empty */
    public function is_empty($str) {
        if (isset($str) && !empty($str)) {
            return false;
        }

        return true;
    }
/*}}}*/
/*{{{ get_size */
    public function get_size($str) {
        if (isset($str) && !empty($str)) {
            return strlen($str);
        }

        return 0;
    }
/*}}}*/
/*{{{ is_email */
    public function email($str) {
        return (! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
    }
/*}}}*/
/*}}}*/

}
