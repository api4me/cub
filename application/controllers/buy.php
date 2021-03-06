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
