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

class MCarChart extends CI_Model {

/*{{{ load */
    public function loaddata($carid, $type) {
        $this->db->where("car_id", $carid);
        $this->db->where("type", $type);
        $query = $this->db->get("##car_chart");
        if ($row = $query->row()) {
            return $row->data;
        }

        return false;
    }
/*}}}*/
/*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##car_chart", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##car_chart", $param, array("id"=>$id));
        }

        return false;
    }
/*}}}*/

}
