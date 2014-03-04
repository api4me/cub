<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-2-25
 * Time: 下午23:06
 * To change this template use File | Settings | File Templates.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class MAgencyProm extends CI_Model {

    private function _where($param) {
        if (isset($param["user_id"]) && $param["user_id"]) {
            $this->db->where("user_id", $param["user_id"]);
        }

        $this->db->where("enable <>", 'D');
    }

    public function load_all($param) {
        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##agency_promotion");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            $query = $this->db->get("##agency_promotion", $param["per_page"], $param["start"]);
            $data = $query->result();

            return array(
                "num" => $num,
                "data" => $data,
            );
        }

        return false;
    }

    public function load($id) {
        $this->db->where("id", $id);
        $query = $this->db->get("##agency_promotion");
        return $query->row();
    }

    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##agency_promotion", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##agency_promotion", $param, array("id"=>$id));
        }

        return false;
    }

}