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
            $q = "SELECT * FROM ##options ORDER BY id";
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
            if (is_array($remove)) {
                foreach ($remove as $val) {
                    if (isset($data[$val])) {
                        unset($data[$val]);
                    }
                }
            }

            if ($blankline) {
                //array_unshift($data, "");
                $tmp = array("0" => "--");
                foreach ($data as $key => $val) {
                    $tmp[$key] = $val;
                }
                $data = $tmp;
            }

            return $data;
        }

        return array();
    }
/*}}}*/
/*{{{ model */
    /**
     * Data struct: brand+model, 101+001
     * Get all data: model()
     * Get name of code: model("101001")
     *
     */
    public function model($code = null) {
        static $data;
        if (!isset($data)) {
            $data = array();
            $q = "SELECT * FROM ##car_model ORDER BY sort";
            $query = $this->CI->db->query($q);
            foreach($query->result() as $row) {
                list($brand, $model) = str_split($row->code, 3);
                $data[$brand][$model] = $row->name;
            }
        }

        if (is_array($data)) {
            // Get all data
            if (!isset($code)) {
                return $data;
            }

            // Get model full name
            list($brand, $model) = str_split($code, 3);
            $name = array();
            if (isset($data[$brand])) {
                $name[] = $data[$brand]["000"];
                if (isset($data[$brand][$model])) {
                    $name[] = $data[$brand][$model];
                }
            }

            return implode('>', $name);
        }

        return false;
    }
/*}}}*/
/*{{{ area */
    /**
     * Data struct: province+city+district, 32+01+04
     * Get all data: area()
     * Get name of code: area("320104")
     *
     */
    public function area($code = null) {
        static $data;
        if (!isset($data)) {
            $data = array();
            $q = "SELECT * FROM ##area ORDER BY sort";
            $query = $this->CI->db->query($q);
            foreach($query->result() as $row) {
                list($province, $city, $district) = str_split($row->code, 2);
                $data[$province][$city][$district] = $row->name;
            }
        }

        if (is_array($data)) {
            // Get all data
            if (!isset($code)) {
                return $data;
            }

            // Get model full name
            list($province, $city, $district) = str_split($code, 2);
            $name = array();
            if (isset($data[$province])) {
                $name[] = $data[$province]["00"]["00"];
                if (isset($data[$province][$city])) {
                    $name[] = $data[$province][$city]["00"];
                    if (isset($data[$province][$city][$district])) {
                        $name[] = $data[$province][$city][$district];
                    }
                }
            }

            return implode(">", $name);
        }

        return false;
    }
/*}}}*/

/*{{{ validate func */
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
            return mb_strlen($str);
        }

        return 0;
    }
/*}}}*/
/*{{{ is_email */
    public function is_email($str) {
        return (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? true : false;
    }
/*}}}*/
/*{{{ is_phone */
    public function is_phone($str) {
        if (preg_match("/^1[3458]{1}[0-9]{1}[0-9]{8}$/", $str)) { // Phone
           // || preg_match("/^0[1-9]{2,3}-[0-9]{7,8}$/", $str)) { // Tel

            return true;
        }

        return false;
    }
/*}}}*/
/*}}}*/


}
