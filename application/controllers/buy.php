<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Buy extends CI_Controller {

/*{{{ index */
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/article
	 *	- or -  
	 * 		http://example.com/index.php/article/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/article/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($start = 0) {
        $this->auction($start);
	}
/*}}}*/
/*{{{ auction */
    public function auction($start = 0) {
        $this->load->helper("form");
        $this->load->library("twig");
        $out = array();
        $out["title"] = "我要买车 - 竞拍车辆";

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = intval($start);
        $search["per_page"] = 12;
        $out["search"] = $search;
        // Auction
        $this->load->model("mcar");
        if ($data = $this->mcar->load_auction_by_condition($search)) {
            $out["auction"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 3;
            $this->pagination->per_page = $search['per_page'];
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/buy/auction/";
            $this->pagination->full_tag_open = '<div class="pagination pagination-centered"><ul>'; 
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->twig->display("buy_auction.html", $out);
    }
/*}}}*/

/* {{{个人寄售 */
    public function c_consign($start = 0) {
        $param = array();
        $param["title"] = "我要买车 - 寄售车辆（个人）";
        $param["consign_type"] = "C";
        $param["link"] = "/buy/c_consign/";

        $this->common_consign($start, $param);
    }
/* }}} */

/* {{{商家寄售 */
    public function b_consign($start = 0) {
        $param = array();
        $param["title"] = "我要买车 - 寄售车辆（商家）";
        $param["consign_type"] = "B";
        $param["link"] = "/buy/b_consign/";

        $this->common_consign($start, $param);
    }
/* }}} */

    private function common_consign($start, $param) {
        $this->load->library("twig");
        $out = array();
        $out["title"] = $param["title"];

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = intval($start);
        $search["per_page"] = 12;

        $search["model"] = null;
        $search["price_low"] = null;
        $search["price_high"] = null;
        $search["date_start"] = null;
        $search["date_end"] = null;
        $search["mileage_low"] = null;
        $search["mileage_high"] = null;
        $search["gearbox"] = null;
        $search["consign_type"] = $param["consign_type"];

        // model search
        $model = $this->input->post('brand') . $this->input->post('model');
        if ($model != '000001') {
            $search["model"] = $model;
        }

        // price search
        $price_table = array(1 => array(0, 30000),
            2 => array(30000, 50000),
            3 => array(50000, 80000),
            4 => array(80000, 120000),
            5 => array(120000, 180000),
            6 => array(180000, 240000),
            7 => array(240000, 350000),
            8 => array(350000, 600000),
            9 => array(600000, 1000000),
            10 => array(1000000, -1));
        $price_type = intval($this->input->post('consign_price'));
        if ($price_type > 0 && $price_type <= 10) {
            if ($price_table[$price_type][0]) {
                $search["price_low"] = $price_table[$price_type][0];
            }

            if ($price_table[$price_type][1] && $price_table[$price_type][1] != -1) {
                $search["price_high"] = $price_table[$price_type][1];
            }
        }

        // age search
        $age_table = array(1 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 1)), -1 ),
            2 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 3)), date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 1)) ),
            3 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 5)), date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 3)) ),
            4 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 8)), date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 5)) ),
            5 => array(-1, date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 8))) );
        $age_type = intval($this->input->post('consign_age'));
        if ($age_type > 0 && $age_type <= 5) {
            if ($age_table[$age_type][0] && $age_table[$age_type][0] != -1) {
                $search["date_start"] = $age_table[$age_type][0];
            }

            if ($age_table[$age_type][1] && $age_table[$age_type][1] != -1) {
                $search["date_end"] = $age_table[$age_type][1];
            }
        }

        // mileage search
        $mileage_table = array(1 => array(0, 10000),
            2 => array(0, 30000),
            3 => array(0, 50000),
            4 => array(0, 80000),
            5 => array(80000, -1));
        $mileage_type = intval($this->input->post("consign_mileage"));
        if ($mileage_type > 0 && $mileage_type <= 5) {
            if ($mileage_table[$mileage_type][0]) {
                $search["mileage_low"] = $mileage_table[$mileage_type][0];
            }

            if ($mileage_table[$mileage_type][1] && $mileage_table[$mileage_type][1] != -1) {
                $search["mileage_high"] = $mileage_table[$mileage_type][1];
            }
        }

        // gearbox search
        $gearbox_table = array(1 => 'MT', 2 => 'AT', 3 => 'AMT');
        $gearbox_type = intval($this->input->post("consign_gearbox"));
        if ($gearbox_type > 0 && $gearbox_type <= 3) {
            $search["gearbox"] = $gearbox_table[$gearbox_type];
        }

        $out["price_type"] = $price_type;
        $out["age_type"] = $age_type;
        $out["mileage_type"] = $mileage_type;
        $out["gearbox_type"] = $gearbox_type;
        $out["model"] = $model;

        $out["search"] = $search;

        // Consign
        $this->load->model("mcar");
        if ($data = $this->mcar->load_consign_by_condition($search)) {
            $out["consign"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 3;
            $this->pagination->per_page = $search['per_page'];
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . $param["link"];
            $this->pagination->full_tag_open = '<div class="pagination pagination-centered"><ul>';
            $out["pagination"] = $this->pagination->create_links();
        }

        $out["link"] = $param["link"];

        $this->twig->display("buy_consign.html", $out);
    }

/*{{{ consign */
    public function consign($start = 0) {
        $this->load->library("twig");
        $out = array();
        $out["title"] = "我要买车 - 寄售车辆";

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = intval($start);
        $search["per_page"] = 12;

        $search["model"] = null;
        $search["price_low"] = null;
        $search["price_high"] = null;
        $search["date_start"] = null;
        $search["date_end"] = null;
        $search["mileage_low"] = null;
        $search["mileage_high"] = null;
        $search["gearbox"] = null;

        // model search
        $model = $this->input->post('brand') . $this->input->post('model');
        if ($model != '000001') {
            $search["model"] = $model;
        }

        // price search
        $price_table = array(1 => array(0, 30000),
            2 => array(30000, 50000),
            3 => array(50000, 80000),
            4 => array(80000, 120000),
            5 => array(120000, 180000),
            6 => array(180000, 240000),
            7 => array(240000, 350000),
            8 => array(350000, 600000),
            9 => array(600000, 1000000),
            10 => array(1000000, -1));
        $price_type = intval($this->input->post('consign_price'));
        if ($price_type > 0 && $price_type <= 10) {
            if ($price_table[$price_type][0]) {
                $search["price_low"] = $price_table[$price_type][0];
            }

            if ($price_table[$price_type][1] && $price_table[$price_type][1] != -1) {
                $search["price_high"] = $price_table[$price_type][1];
            }
        }

        // age search
        $age_table = array(1 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 1)), -1 ),
            2 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 3)), date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 1)) ),
            3 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 5)), date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 3)) ),
            4 => array(date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 8)), date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 5)) ),
            5 => array(-1, date("Y-m-d", mktime(0, 0, 0, date("m"),   date("d"),   date("Y") - 8))) );
        $age_type = intval($this->input->post('consign_age'));
        if ($age_type > 0 && $age_type <= 5) {
            if ($age_table[$age_type][0] && $age_table[$age_type][0] != -1) {
                $search["date_start"] = $age_table[$age_type][0];
            }

            if ($age_table[$age_type][1] && $age_table[$age_type][1] != -1) {
                $search["date_end"] = $age_table[$age_type][1];
            }
        }

        // mileage search
        $mileage_table = array(1 => array(0, 10000),
            2 => array(0, 30000),
            3 => array(0, 50000),
            4 => array(0, 80000),
            5 => array(80000, -1));
        $mileage_type = intval($this->input->post("consign_mileage"));
        if ($mileage_type > 0 && $mileage_type <= 5) {
            if ($mileage_table[$mileage_type][0]) {
                $search["mileage_low"] = $mileage_table[$mileage_type][0];
            }

            if ($mileage_table[$mileage_type][1] && $mileage_table[$mileage_type][1] != -1) {
                $search["mileage_high"] = $mileage_table[$mileage_type][1];
            }
        }

        // gearbox search
        $gearbox_table = array(1 => 'MT', 2 => 'AT', 3 => 'AMT');
        $gearbox_type = intval($this->input->post("consign_gearbox"));
        if ($gearbox_type > 0 && $gearbox_type <= 3) {
            $search["gearbox"] = $gearbox_table[$gearbox_type];
        }

        $out["price_type"] = $price_type;
        $out["age_type"] = $age_type;
        $out["mileage_type"] = $mileage_type;
        $out["gearbox_type"] = $gearbox_type;
        $out["model"] = $model;

        $out["search"] = $search;

        // Consign
        $this->load->model("mcar");
        if ($data = $this->mcar->load_consign_by_condition($search)) {
            $out["consign"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 3;
            $this->pagination->per_page = $search['per_page'];
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/buy/consign/";
            $this->pagination->full_tag_open = '<div class="pagination pagination-centered"><ul>'; 
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->twig->display("buy_consign.html", $out);
    }
/*}}}*/
/*{{{ search */
    public function search($start = 0) {
        $this->load->library("twig");
        $out = array();
        $out["title"] = "历史交易查询";

        $search["model_name"] = $this->input->get('q');
        $search["start"] = intval($start);
        $search["per_page"] = 12;
        $out["search"] = $search;

        $this->load->model("mcar");
        if ($data = $this->mcar->load_by_search($search)) {
            $out['car'] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 3;
            $this->pagination->per_page = $search['per_page'];
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/buy/search/";
            $this->pagination->full_tag_open = '<div class="pagination pagination-centered"><ul>'; 
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->twig->display("buy_search.html", $out);
    }
/*}}}*/
/*{{{ car - detail */
    public function car($id = 0) {
        $this->load->library("twig");
        $out = array();
        $out["title"] = "车辆详情";

        $this->load->model("mcar");
        if ($data = $this->mcar->load($id)) {
            $out['car'] = $data;
        }

        $success = $this->mcar->load_recent_by_success($id);
        $out['success'] = $success;

        $this->twig->display("buy_car.html", $out);
    }
/*}}}*/
/*{{{ pay */
	public function pay () {
        $out = array();
        $this->output->set_content_type('application/json');
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // User
        $user = $this->lsession->get('user');
        if (!$user) {
            $out["status"] = 2;
            $out["msg"] = "您重新登录。";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if ($user->role != 'buyer') {
                $out["status"] = 1;
                $out["msg"] = "您无权限拍卖，如有问题请联系我们。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }

        $param = array();
        $param["user_id"] = $user->id;
        $param["car_id"] = trim($this->input->post("id"));
        // Check
        if ($this->lcommon->is_empty($param["car_id"]) || !is_numeric($param["car_id"])) {
            $out["status"] = 1;
            $out["msg"] = "请刷新页面后重试。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // Check car is on sale
        $this->load->model("mcar");
        if (!($car = $this->mcar->load($param['car_id']))
            || $car->status != 'auction' 
            || sale_status($car->sale_start_date, $car->sale_end_date) != 'selling') {

            $out["status"] = 1;
            $out["msg"] = "拍卖已经结束，其他车辆更多精彩。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $param["price"] = str_replace(array(',', ' '), '', trim($this->input->post("p")));
        if ($this->lcommon->is_empty($param["price"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写拍卖价格。";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if (!is_numeric($param['price'])) {
                $out["status"] = 1;
                $out["msg"] = "拍卖价格请以数字填写。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $md5 = $this->input->post("v");
        if (md5($param['car_id'].$param['price']) != $md5) {
            $out["status"] = 1;
            $out["msg"] = "数据校验不一致，请重新拍卖。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        
        // Payment <= market price
        if ($param['price'] > $car->market_price) {
            $out["status"] = 1;
            $out["msg"] = "您的出价超过该车合理价格，请谨慎出价!";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Only pay 3 times
        $max = 3;
        $url = sprintf('http://localhost:6060/pay?uid=%s&cid=%s&max=%s', $param['user_id'], $param['car_id'], $max);
        if (file_get_contents($url) >= $max) {
            $out["status"] = 1;
            $out["msg"] = "您已经出价3次，休息一会吧~";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $extra = array();
        $extra['ip'] = $this->input->ip_address();
        $extra['username'] = $user->username;
        $extra['car'] = $car->model_name;
        $extra['area'] = $user->area;
        $param['extra'] = json_encode($extra);

        $this->load->model('mauction');
        if (!$this->mauction->insert($param)) {
            $out["status"] = 1;
            $out["msg"] = "拍卖失败，请稍后重试。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Get top pay
        $out["status"] = 0;
        $out["msg"] = "出价成功，如果您是出价的领先者将有机会获得此爱车。";
        if ($tmp = $this->mauction->top($param['car_id'])) {
            $top = array();
            foreach ($tmp['price'] as $k => $v) {
                $a = $tmp['area'][$k];
                if ($k != $user->username) {
                    $k = str_repeat('*', strlen($k) - 3) . substr($k, -3, 1) . '**';
                    $v = str_repeat('*', strlen($v));
                }
                $top[] = array('name' => $k, 'price' => $v, 'area' => $a);
            }

            $out['data'] = $top;
        }
        $this->output->set_output(json_encode($out));

        return true;
	}
/*}}}*/

}
