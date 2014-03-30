<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-2-25
 * Time: 下午23:07
 * To change this template use File | Settings | File Templates.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class MAgencyCar extends CI_Model {

    /*{{{ load_all */
    private function _where($param) {
        if (isset($param["user_id"]) && $param["user_id"]) {
            $this->db->where("user_id", $param["user_id"]);
        }

        $this->db->where("status <>", 'del');

    }
    public function load_all($param, $desc = false) {
        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##agency_car");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            if ($desc) {
                $this->db->order_by('ID', 'DESC');
            }
            $query = $this->db->get("##agency_car", $param["per_page"], $param["start"]);
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
        $query = $this->db->get("##agency_car");
        return $query->row();
    }
    /*}}}*/

    /*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##agency_car", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##agency_car", $param, array("id"=>$id));
        }

        return false;
    }
    /*}}}*/
}