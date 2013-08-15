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
    public function load_for_buyer($param) {
        // For search
        $q = 'SELECT DISTINCT car_id FROM ##auction WHERE user_id=?';
        $query = $this->db->query($q, array($param['uid']));

        $num = array();
        foreach($query->result() as $row) {
            if (!in_array($row->car_id, $num)) {
                $num[] = $row->car_id;
            }
        }
        if ($num) {
            $q = 'SELECT * FROM (SELECT A.*, C.images FROM `##auction` A 
            LEFT JOIN `##car` AS C ON A.car_id=C.id
            where A.user_id=? order by A.car_id desc, A.price desc) AS T GROUP BY car_id LIMIT ?, ?';
            $query = $this->db->query($q, array($param['uid'], $param["start"], $param["per_page"]));
            $data = $query->result();

            return array(
                "num" => count($num),
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
/*{{{ calc */
    public function calc($car) {
        $carid = $car->id;
        // 1. Get auction data
        $this->db->where('car_id', $carid);
        $this->db->order_by('created', 'ASC');
        $query = $this->db->get('##auction');
        $auction = $query->result();

        // 2. Generate chart data
        $extra = array();
        $extra['bid'] = 0;
        $extra['top'] = array();
        $extra['user'] = array();

        $chart = array();
        $chart['title'] = array('text' => '竞拍跟踪', 'x' => -20);
        $chart['yAxis'] = array('title' => array('text' => '拍价(人民币)'));
        $chart['tooltip'] = array('valueSuffix' => '元', 'headerFormat' => '', 'pointFormat' => '<i style="color:{series.color}">{point.name}</i>: {point.y}');
        $series = array();
        $series['name'] = model_value($car->model);
        if ($auction) {
            foreach ($auction as $val) {
                $k = $val->user_id;
                $v = intval($val->price);
                $e = json_decode($val->extra, true);
                $u = @$e['username'];
                $series['data'][] = array('name' => $u, 'y' => $v);
                if (!isset($extra['top'][$k]) || ($extra['top'][$k] < $v)) {
                    $extra['top'][$k] = $v;
                    $extra['user'][$k] = $e;
                }
            }
            $extra['bid'] = count($auction);
        }
        $chart['series'][] = $series;

        $out = array();
        $out['car_id'] = $carid;
        $out['type'] = 'auction';
        $out['data'] = json_encode($chart);

        asort($extra['top']);
        $out['extra'] = json_encode($extra);

        return $out;
    }
/*}}}*/
/*{{{ top */
    public function top($cid) {
        // 1. Get auction data
        $this->db->where('car_id', $cid);
        $this->db->order_by('created', 'ASC');
        $query = $this->db->get('##auction');
        $auction = $query->result();

        // 2. Generate top auction data
        $out = array();
        if ($auction) {
            $out['price'] = array();
            $out['area'] = array();
            foreach ($auction as $val) {
                $v = intval($val->price);
                $e = json_decode($val->extra, true);
                $k = @$e['username'];
                if (!isset($out['price'][$k]) || ($out['price'][$k] < $v)) {
                    $out['price'][$k] = $v;
                    $out['area'][$k] = $e['area'] ? area_value($e['area']): '--';
                }
            }

            asort($out['price']);
            $out['price'] = array_slice(array_reverse($out['price'], true), 0, 10, true);
        }

        return $out;
    }
/*}}}*/

}
