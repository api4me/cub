<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename mcar.php
* @touch date Wednesday, May 15, 2013 PM12:21:50 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MCar extends CI_Model {

/*{{{ load_all */
    private function _where($param) {
        if (isset($param["phone"]) && $param["phone"]) {
            $this->db->where("user_phone", $param["phone"]);
        }
        if (isset($param["status"]) && $param["status"]) {
            $this->db->where("status", $param["status"]);
        }
        if (isset($param["cert_code"]) && $param["cert_code"]) {
            $this->db->where("cert_code", $param["cert_code"]);
        }
        $this->db->where("status <>", 'del');
    }
    public function load_all($param, $desc = false) {
        // For search
        $this->_where($param);
        $this->db->select("COUNT(1) AS num");
        $query = $this->db->get("##car");

        if ($num = $query->row(0)->num) {
            $this->db->select();
            $this->_where($param);
            if ($desc) {
                $this->db->order_by('ID', 'DESC');
            }
            $query = $this->db->get("##car", $param["per_page"], $param["start"]);
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
        $query = $this->db->get("##car");
        return $query->row();
    }
/*}}}*/
/*{{{ _sort */
    private function _sort($arr) {
        if (!$arr) {
            return $arr;
        }

        $out = array();
        $sold = array();
        foreach ($arr as $v) {
            switch($v->sale_status) {
                case 'sold':
                    $added = false;
                    if ($sold) {
                        foreach ($sold as $k => $s) {
                            if ($s->sale_end_date < $v->sale_end_date) {
                                array_splice($sold, $k, 0, array($v));
                                $added = true;
                                break;
                            }
                        }
                    }
                    if (!$added) {
                        $sold[] = $v;
                    }
                    break;
                default:
                    $out[] = $v;
                    break;
            }
        }
        array_splice($out, count($out), 0, $sold);

        return $out;
    }
/*}}}*/
/*{{{ load_for_auction */
    public function load_for_auction($count) {
        // Saling + Wait for Auction + sold
        $q = "
            -- Seling
            (SELECT id, model, factory_date, condition_score, appraisal_level, sale_start_date, sale_end_date, bid_num, images, 'selling' AS sale_status
            FROM ##car WHERE status='auction' AND sale_type='auction' AND sale_start_date<=now() AND sale_end_date>=now())
            UNION
            -- Presale, Wait for auction
            (SELECT id, model, factory_date, condition_score, appraisal_level, sale_start_date, sale_end_date, bid_num, images, 'presale' AS sale_status
            FROM ##car WHERE status='auction' AND sale_type='auction' AND sale_start_date>now())
            UNION
            -- Sold
            (SELECT id, model, factory_date, condition_score, appraisal_level, sale_start_date, sale_end_date, bid_num, images, 'sold' AS sale_status
            FROM ##car WHERE status='auction' AND sale_type='auction' AND sale_end_date<now())
            LIMIT ?;
        ";
        $query = $this->db->query($q, array($count));

        return $this->_sort($query->result());
    }
/*}}}*/
/*{{{ load_for_consign */
    public function load_for_consign($count) {
        $this->db->where('status', 'auction');
        $this->db->where('sale_type', 'consign');
        $this->db->where('sale_start_date <=', 'now()', false);
        $this->db->where('sale_end_date >=', 'now()', false);
        $query = $this->db->get("##car", $count);

        return $query->result();
    }
/*}}}*/
/*{{{ load_auction_by_condition */
    public function load_auction_by_condition($param) {
        $q = "
            -- Seling
            (SELECT ?, 'selling' AS sale_status
            FROM ##car WHERE status='auction' AND sale_type='auction' AND sale_start_date <= now() AND sale_end_date >= now())
            UNION
            -- Presale, Wait for auction
            (SELECT ?, 'presale' AS sale_status
            FROM ##car WHERE status='auction' AND sale_type='auction' AND sale_start_date > now())
            UNION
            -- Sold
            (SELECT ?, 'sold' AS sale_status
            FROM ##car WHERE status='auction' AND sale_type='auction' AND sale_end_date < now())
        ";

        $query = $this->db->query(str_replace('?', 'COUNT(1) AS num', $q));
        $num = 0;
        foreach ($query->result() as $row) {
            $num += $row->num;
        }
        if ($num) {
            $select = "id, model, factory_date, condition_score, appraisal_level, sale_start_date, sale_end_date, bid_num, images, sale_price";
            $query = $this->db->query(str_replace('?', $select, $q) . ' LIMIT ?, ?', array($param["start"], $param["per_page"]));
            $data = $this->_sort($query->result());

            return array(
                "num" => $num,
                "data" => $data,
            );
        }

        return false;
    }
/*}}}*/
/*{{{ load_consign_by_condition */
    public function load_consign_by_condition($param) {
        $q = "
            SELECT ? FROM ##car WHERE status='auction' AND sale_type='consign' AND sale_start_date<=now() AND sale_end_date>=now()
        ";

        $query = $this->db->query(str_replace('?', 'COUNT(1) AS num', $q));
        $num = 0;
        foreach ($query->result() as $row) {
            $num += $row->num;
        }
        if ($num) {
            $select = "id, model, factory_date, condition_score, appraisal_level, sale_start_date, sale_end_date, bid_num, images, sale_price";
            $query = $this->db->query(str_replace('?', $select, $q) . ' LIMIT ?, ?', array($param["start"], $param["per_page"]));
            $data = $query->result();

            return array(
                "num" => $num,
                "data" => $data,
            );
        }

        return false;
    }
/*}}}*/
/*{{{ load_recent_by_success */
    public function load_recent_by_success($id) {
        $this->db->where('status', 'success');
        $this->db->where('id <>', intval($id));
        $this->db->order_by('updated', 'desc');
        $query = $this->db->get('##car', 5);

        return $query->result();
    }
/*}}}*/
/*{{{ load_by_search */
    public function load_by_search($param) {
        $q = "
            SELECT ? FROM ##car WHERE status='success'
        ";

        $where = '';
        if ($param['model_name']) {
            $where = ' AND model_name LIKE \'%' . $this->db->escape_like_str($param['model_name']) . '%\'';
        }

        $query = $this->db->query(str_replace('?', 'COUNT(1) AS num', $q) . $where);
        if ($num = $query->row(0)->num) {
            $select = "*";
            $query = $this->db->query(str_replace('?', $select, $q) . $where . ' ORDER BY updated DESC LIMIT ?, ?', array($param["start"], $param["per_page"]));
            $data = $query->result();

            return array(
                "num" => $num,
                "data" => $data,
            );
        }

        return false;
    }
/*}}}*/
/*{{{ save */
    public function save($param, $id) {
        if (!$id) {
            $this->db->set("created", "now()", false);
            $this->db->set("updated", "now()", false);
            if ($this->db->insert("##car", $param)) {
                return $this->db->insert_id();
            }
        } else {
            $this->db->set("updated", "now()", false);
            return $this->db->update("##car", $param, array("id"=>$id));
        }

        return false;
    }
/*}}}*/

}
