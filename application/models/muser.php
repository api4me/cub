<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename ../models/user.php
* @touch date Wednesday, May 15, 2013 PM12:21:50 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MUser extends CI_Model {

/*{{{ login */
    /**
     * @param param["name"]: user name
     * @param param["pwd"]: password of user, the md5 value
     */
    public function login($param) {
        $this->db->from("##user");
        $this->db->where("username", $param["username"]);
        $this->db->where("enable", 'Y');
        $this->db->where("pwd", $param["pwd"]);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            if ($out = $query->row()) {
                // Set login time
                $param = array();
                if (!$out->first_logged) {
                    $this->db->set("first_logged", "now()", false);
                }
                $this->db->set("last_logged", "now()", false);
                $this->save($param, $out->id);
            }
            return $out;
        }

        return false;
    }
/*}}}*/
/*{{{ select */
    private function _where($param) {
        if (isset($param["username"]) && $param["username"]) {
            $this->db->like("username", $param["username"]);
        }
        $user = $this->lsession->get('user');
        if ($user->role != 'super') {
            $this->db->where_not_in('role', array('super', 'admin'));
        }
        if (isset($param["role"]) && $param["role"]) {
            $this->db->where("role", $param["role"]);
        }
        if (isset($param["enable"]) && $param["enable"]) {
            $this->db->where("enable", $param["enable"]);
        }
        $this->db->where("enable <>", 'D');
    }
    public function select($param) {
        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##user");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            $query = $this->db->get("##user", $param["per_page"], $param["start"]);
            $data = $query->result();

            return array(
                "num" => $num,
                "data" => $data,
            );
        }

        return false;
    }
/*}}}*/
/*{{{ load */
    public function load($id) {
        $this->db->where("id", $id);
        $this->db->where("enable <>", 'D');
        $query = $this->db->get("##user");
        return $query->row();
    }
/*}}}*/
/*{{{ load_for_deal */
    public function load_for_deal($param) {
        $this->db->select('id, username');
        $key = '(username=\'' . $this->db->escape_str($param['key']) 
            . '\' OR phone=\'' . $this->db->escape_str($param['key'])
            . '\')';
        $this->db->where($key);
        $this->db->where("role", 'buyer');
        $this->db->where("enable <>", 'D');
        $query = $this->db->get("##user");
        return $query->row();
    }
/*}}}*/
/*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##user", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##user", $param, array("id"=>$id));
        }

        return false;
    }
/*}}}*/
/*{{{ change_enable */
    public function change_enable($param, $id) {
        if (!$id) {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##user", $param, array("id"=>$id));
        }

        return false;
    }
/*}}}*/
/*{{{ get_data_by_phone */
    public function get_data_by_phone($phone) {
        $this->db->where("phone", $phone);
        $this->db->where("enable <>", 'D');
        $query = $this->db->get("##user");
        return $query->row();
    }
/*}}}*/
/*{{{ exists_username */
    public function exists_username($str, $id = 0) {
        if ($id) {
            $this->db->where('id <>', $id);
        }
        $this->db->where("username", $str);
        $this->db->where("enable <>", 'D');
        $query = $this->db->get("##user");
        return ($query->row()) ? true : false;
    }
/*}}}*/
/*{{{ exists_phone */
    public function exists_phone($str, $id = 0) {
        if ($id) {
            $this->db->where('id <>', $id);
        }
        $this->db->where("phone", $str);
        $this->db->where("enable <>", 'D');
        $query = $this->db->get("##user");
        return ($query->row()) ? true : false;
    }
/*}}}*/
/*{{{ exists_email */
    public function exists_email($str, $id = 0) {
        if ($id) {
            $this->db->where('id <>', $id);
        }
        $this->db->where("email", $str);
        $this->db->where("enable <>", 'D');
        $query = $this->db->get("##user");
        return ($query->row()) ? true : false;
    }
/*}}}*/

}
