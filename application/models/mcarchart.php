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
    public function load($carid, $type) {
        $this->db->where("car_id", $carid);
        $this->db->where("type", $type);
        $query = $this->db->get("##car_chart");
        if ($row = $query->row()) {
            return $row;
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
/*{{{ generate */
    public function generate($carid) {
        // 1. Check car data
        $this->db->where("id", $carid);
        $this->db->where("status", 'auction');
        $this->db->where("sale_type", 'auction');
        $query = $this->db->get("##car");
        // Only auction car can generate auction
        if (!$car = $query->row()) {
            return false;
        }
        // Only sold can do
        if (sale_status($car->sale_start_date, $car->sale_end_date) != 'sold') {
            return false;
        }
        // 2. Get auction data
        $this->db->where('car_id', $carid);
        $this->db->order_by('created', 'ASC');
        $query = $this->db->get('##auction');
        $auction = $query->result();

        // 3. Generate chart data
        $extra = array();
        $extra['bid'] = 0;
        $extra['top'] = array();
        $extra['user'] = array();

        $chart = array();
        $chart['title'] = array('text' => '竞拍回放', 'x' => -20);
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

        $param = array();
        $param['car_id'] = $carid;
        $param['type'] = 'auction';
        $param['data'] = json_encode($chart);

        asort($extra['top']);
        $param['extra'] = json_encode($extra);


        if($this->save($param, 0)) {
            return $this->load($carid, 'auction');
        }
        return false;
    }
/*}}}*/
/*{{{ del */
    public function del($carid, $type) {
        $this->db->where("car_id", $carid);
        $this->db->where("type", $type);

        return $this->db->delete("##car_chart");
    }
/*}}}*/

}
