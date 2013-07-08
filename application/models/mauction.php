<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename mauction.php
* @touch date Wednesday, May 15, 2013 PM12:21:50 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MAuction extends CI_Model {

/*{{{ load_for_buyer */
    private function _where_for_buyer($param) {
        $this->db->where("user_id", $param['uid']);
    }
    public function load_for_buyer($param) {
        // For search
        $this->_where_for_buyer($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##auction");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where_for_buyer($param);
            $this->db->order_by("created", 'DESC');
            $query = $this->db->get("##auction", $param["per_page"], $param["start"]);
            $data = $query->result();

            return array(
                "num" => $num,
                "data" => $data,
            );
        }

        return false;
    }
/*}}}*/
/*{{{ load_for_sell */
    public function load_for_sell($param) {
        // For search
        $this->db->select("id");
        $query = $this->db->get("##car");
        $this->db->order_by("id", 'DESC');
        $this->db->where("user_id", $param['uid']);
        if ($param['car_id']) {
            $this->db->where("id <=", $param['car_id']);
        }
        $query = $this->db->get("##car", 2);

        return $query->result();
    }
/*}}}*/
/*{{{ insert */
    public function insert($param) {
        $this->db->set("created", "now()", false);
        $this->db->set("updated", "now()", false);
        if ($this->db->insert("##auction", $param)) {
            return $this->db->insert_id();
        }

        return false;
    }
/*}}}*/
/*{{{ stat */
    public function stat($param) {

        return false;
    }
/*}}}*/


}
