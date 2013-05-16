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
        $this->db->from("#user");
        $this->db->where("username", $param["username"]);
        $this->db->where("pwd", $param["pwd"]);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        }

        return false;
    }
/*}}}*/
/*{{{ select */
    public function select($param) {
        // Search
        if (isset($param["username"]) && $param["username"]) {
            $this->db->like("username", $param["username"]);
        }
        if (isset($param["role"]) && $param["role"]) {
            $this->db->where("role", $param["role"]);
        }
        if (isset($param["enable"]) && $param["enable"]) {
            $this->db->where("enable", $param["enable"]);
        }

        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("#user");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $query = $this->db->get("#user", $param["per_page"], $param["start"]);
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
        $query = $this->db->get("#user");
        return $query->row();
    }
/*}}}*/

}
