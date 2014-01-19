<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-1-2
 * Time: 下午19:30
 * To change this template use File | Settings | File Templates.
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MPurchase extends CI_Model {

    /*{{{ load_all */
    private function _where($param) {
        if (isset($param["name"]) && $param["name"]) {
            $this->db->where("name", $param["name"]);
        }
        if (isset($param["phone"]) && $param["phone"]) {
            $this->db->where("phone", $param["phone"]);
        }
        if (isset($param["model"]) && $param["model"]) {
            $this->db->where("model", $param["model"]);
        }
    }

    public function load_all($param) {
        foreach ($param as $key => $val) {
            error_log($key . $val);
        }

        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##purchase");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            $query = $this->db->get("##purchase", $param["per_page"], $param["start"]);
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

    /*{{{ count_by_key */
    public function count_by_key($param) {
        if (!is_array($param)) {
            return false;
        }

        foreach ($param as $key => $val) {
            $this->db->where($key, $val);
        }

        $query = $this->db->get("##purchase");

        return $query->num_rows();
    }
    /*}}}*/
    /*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##purchase", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##purchase", $param, array("id"=>$id));
        }

        return false;
    }
    /*}}}*/

}
