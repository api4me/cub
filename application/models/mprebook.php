<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename mprebook.php
* @touch date Wednesday, May 15, 2013 PM12:21:50 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MPrebook extends CI_Model {

/*{{{ load_all */
    private function _where($param) {
        if (isset($param["phone"]) && $param["phone"]) {
            $this->db->where("phone", $param["phone"]);
        }
        if (isset($param["status"]) && $param["status"] == "invalid") {
            $this->db->where("status", "invalid");
        } else {
            // Where new add
            $this->db->where("status", "add");
        }
    }
    public function load_all($param) {
        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##prebook");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            $query = $this->db->get("##prebook", $param["per_page"], $param["start"]);
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
        $query = $this->db->get("##prebook");
        return $query->row();
    }
/*}}}*/
/*{{{ valid */
    public function valid($param, $id) {
        // TODO procedure
        $prebook = $this->load($id);
        // has been update, return
        if ($prebook->status == 'valid') {
            return false;
        }

        // 1. Update probook
        $data = array();
        $data["status"] = "valid";
        $this->db->set("updated", "now()", false);
        if ($this->db->update("##prebook", $data, array("id"=>$id))) {
            // 2. Insert user
            if ($param["username_exists"]) {
                // Get user via phone
                $this->load->model("muser");
                if (!$tmp = $this->muser->get_data_by_phone($prebook->phone)) {
                    return false;
                }
                $param["user_id"] = $tmp->id;
            } else {
                $data = array();
                $data["name"] = $param["name"];
                $data["username"] = $param["username"];
                $data["pwd"] = md5("888888");
                $data["phone"] = $prebook->phone;
                $data["email"] = $param["email"];
                $data["area"] = $param["area"];
                $data["role"] = "sell";
                $data["enable"] = "Y";
                if (!$tmp = $this->muser->save($data, null)) {
                    return false;
                }
                $param["user_id"] = $tmp;
            }
            // 3. Insert car
            $data = array();
            $data["user_name"] = $param["name"];
            $data["user_phone"] = $prebook->phone;
            $data["model"] = $param["model"];
            $data["area"] = $param["area"];
            $data["buy_date"] = $param["buy_date"];
            $data["mileage"] = $param["mileage"];
            $data["status"] = "prebook";
            $data["user_id"] = $param["user_id"];
            $data["prebook_id"] = $id;
            $data["remark"] = $param["remark"];
            $user = $this->lsession->get("user");
            $data["editor"] = $user->id;

            $this->load->model("mcar");
            if ($this->mcar->save($data, null)) {
                return true;
            }
        }

        return false;
    }
/*}}}*/
/*{{{ invalid */
    public function invalid($id) {
        // invalid
        $data["status"] = "invalid";
        $this->db->set("updated", "now()", false);
        $this->db->update("##prebook", $data, array("id"=>$id));

        return true;
    }
/*}}}*/
/*{{{ count_by_key */
    public function count_by_key($param) {
        if (!is_array($param)) {
            return false;
        }
        foreach ($param as $key => $val) {
            $this->db->where($key, $val);
        }
        $query = $this->db->get("##prebook");
        return $query->num_rows();
    }
/*}}}*/
/*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##prebook", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##prebook", $param, array("id"=>$id));
        }

        return false;
    }
/*}}}*/

}
