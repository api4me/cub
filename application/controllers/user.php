<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

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
	public function index() {
        // Profile
        $this->load->library("twig");
        $out = array();
        $out['title'] = '我的中心';
        $this->twig->display("user_index.html", $out);
	}
/*}}}*/
/*{{{ buy */
    public function buy($start = 0) {
        $this->load->helper("form");
        $this->load->library("twig");
        $out = array();
        $out["title"] = "我参与的竞拍";

        $this->config->load("pagination");
        $user = $this->lsession->get('user');
        // Search
        $search = array();
        $search["start"] = intval($start);
        $search["per_page"] = 10;
        $search['uid'] = $user->id;
        $out["search"] = $search;
        // Auction
        $this->load->model("mauction");
        if ($data = $this->mauction->load_for_buyer($search)) {
            $out["buy"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 3;
            $this->pagination->per_page = $search['per_page'];
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/user/buy/";
            $this->pagination->full_tag_open = '<div class="pagination pagination-centered"><ul>'; 
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->twig->display("user_buy.html", $out);
    }
/*}}}*/
/*{{{ sell */
    public function sell($id = 0) {
        $this->load->helper("form");
        $this->load->library("twig");
        $out = array();
        $out["title"] = "我的爱车";

        $user = $this->lsession->get('user');
        // Search
        $search = array();
        $search['car_id'] = $id;
        $search['uid'] = $user->id;
        $out["search"] = $search;
        // Auction
        $this->load->model("mauction");
        if ($data = $this->mauction->load_for_sell($search)) {
            $out["car"] = $data[0];
            // Update saw num
            if ($out['car']->status == 'success' && $out['car']->saw_num == 0) {
                $this->load->model('mcar');
                $param['saw_num'] = 1;
                $this->mcar->save($param, $out['car']->id);
            }

            if (count($data) > 1) {
                $out["next"] = $data[1];
            }
        }

        $this->twig->display("user_sell.html", $out);
    }
/*}}}*/
/*{{{ pwd */
	public function pwd() {
        $out = array();
        $this->output->set_content_type('application/json');
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $this->load->model("muser");
        $tmp = $this->lsession->get("user");
        $user = $this->muser->load($tmp->id);
        // Check
        $p = $this->input->post("p");
        $h = $this->input->post("h");
        if (md5($user->pwd . $p) != $h) {
            $out["status"] = 1;
            $out["msg"] = "原密码不正确，请确认。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        $param = array();
        $param['pwd'] = md5($p);
        if (!$ret = $this->muser->save($param, $user->id)) {
            $out["status"] = 1;
            $out["msg"] = "密码修改失败。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Success
        $out["status"] = 0;
        $out["msg"] = "密码修改成功。";
        $this->output->set_output(json_encode($out));

        return true;
	}
/*}}}*/
/*{{{ top */
	public function top($id) {
        $out = array();
        $this->output->set_content_type('application/json');
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$id || !is_numeric($id)) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }
        $user = $this->lsession->get('user');
        if ($user->role != 'buyer') {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Success
        $out["status"] = 0;
        $this->load->model('mauction');
        if ($tmp = $this->mauction->top($id)) {
            $top = array();
            $first = true;
            foreach ($tmp['price'] as $k => $v) {
                if ($first) {
                    $first = false;
                    if ($k != $user->username) {
                        $v = str_repeat('*', strlen($v)); 
                    }
                }
                if (strpos($v, '*') === false) {
                    $v = number_format($v);
                }
                $a = $tmp['area'][$k];
                if ($k != $user->username) {
                    $k = substr($k, 0, 1) . str_repeat('*', strlen($k) - 2) . substr($k, -1);
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
