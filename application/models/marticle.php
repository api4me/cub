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

class MArticle extends CI_Model {

/*{{{ load_all */
    private function _where($param) {
        if (isset($param["title"]) && $param["title"]) {
            $this->db->like("title", $param["title"]);
        }
        if (isset($param["tag"]) && $param["tag"]) {
            $this->db->where("tag", $param["tag"]);
        }
    }
    public function load_all($param) {
        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##article");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            $query = $this->db->get("##article", $param["per_page"], $param["start"]);
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
        $query = $this->db->get("##article");
        return $query->row();
    }
/*}}}*/
/*{{{ load_by_tag */
    public function load_by_tag($tag) {
        $this->db->where("tag", $tag);
        $this->db->order_by("sort", "asc");
        $this->db->order_by("created", "desc");
        $query = $this->db->get("##article");

        return $query->result();
    }
/*}}}*/
/*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##article", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##article", $param, array("id"=>$id));
        }

        return false;
    }
/*}}}*/
/*{{{ show */
    public function show($id) {
        $this->db->where("id", $id);
        $this->db->where("enable", "Y");
        $query = $this->db->get("##article");

        return $query->row();
    }
/*}}}*/
/*{{{ show_by_tag */
    public function show_by_tag($tag) {
        $this->db->where("tag", $tag);
        $this->db->where("enable", "Y");
        $this->db->order_by("sort", "asc");
        $this->db->order_by("created", "desc");
        $query = $this->db->get("##article");

        return $query->result();
    }
/*}}}*/

}
