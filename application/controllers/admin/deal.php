<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename deal.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Deal extends Cub_Controller {

    public function __construct() {
        parent::__construct();
    }

/*{{{ index */
    public function index($start = 0){
        $out = array();

        $this->config->load("pagination");
        // Search
        $search = array();
        if ($this->input->post()) {
            $search["phone"] = $this->input->post("phone");
            $this->lsession->set("deal_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("deal_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        // Only show auction data
        $search["status"] = "auction";
        $out["search"] = $search;

        // The data of search
        $this->load->model("mcar");
        if($data = $this->mcar->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/deal/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_deal_index.html", $out);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/deal/");
        }
        // The data of search
        $this->load->model("mcar");
        if (!$car = $this->mcar->load($id)) {
            redirect("/admin/deal/");
        }
        if ($car->status != "auction") {
            redirect("/admin/deal/");
        }

        $out = array();
        $out["car"] = $car;

        $out["sale_status"] = sale_status($car->sale_start_date, $car->sale_end_date);
        if ($car->sale_type == "auction") {
            if ($out["sale_status"] == "sold") {
                // Get chart
                $this->load->model("mcarchart");
                if (!$chart = $this->mcarchart->load($id, "auction")) {
                    // Generate to chart table
                    $chart = $this->mcarchart->generate($id);
                }
                $out["chart"] = $chart->data;
                $out["chart_extra"] = json_decode($chart->extra, true);
            } else if ($out['sale_status'] == 'selling'){
                $this->load->model('mauction');
                $chart = $this->mauction->calc($car);
                $out["chart"] = $chart['data'];
                $out["chart_extra"] = json_decode($chart['extra'], true);
            }
        }

        $this->render("admin_deal_edit.html", $out);
    }
/*}}}*/
/*{{{ moreuser */
    public function moreuser() {
        $out = array();
        $this->output->set_content_type('application/json');
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }
        $key = $this->input->post('k');
        if ($this->lcommon->is_empty($key)) {
            $out["status"] = 1;
            $out["msg"] = "请填写查询的内容";
            $this->output->set_output(json_encode($out));

            return false;
        }
        $param = array();
        $param['key'] = $key;
        $this->load->model("muser");
        if (!$ret = $this->muser->load_for_deal($param)) {
            $out["status"] = 1;
            $out["msg"] = "没有找到相应的用户";
            $this->output->set_output(json_encode($out));
            return false;
        }

        $out["status"] = 0;
        $out["user"] = $ret;
        $out["msg"] = "找到相应的用户，已经添加";
        $this->output->set_output(json_encode($out));
        return true;
    }
/*}}}*/
/*{{{ save */
    public function save($id = 0) {
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

        $param = array();
        // Deal when form.status is "complete"
        $status = null;
        if($this->input->post("status")) {
            $status = "success";
            $param["status"] = "success";
        } else {
            $param["status"] = "close";
        }

        if ($status) {
            $param["sale_to"] = $this->input->post("sale_to");
            if ($this->lcommon->is_empty($param["sale_to"])) {
                $out["status"] = 1;
                $out["msg"] = "请选择购车用户。";
                $this->output->set_output(json_encode($out));

                return false;
            }
            $param["final_price"] = $this->input->post("final_price");
            if ($this->lcommon->is_empty($param["final_price"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写成交价。";
                $this->output->set_output(json_encode($out));

                return false;
            }
            $param["fee"] = $this->input->post("fee");
            if ($this->lcommon->is_empty($param["fee"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写服务费用。";
                $this->output->set_output(json_encode($out));

                return false;
            }
            if ($param["fee"] >= $param["final_price"]) {
                $out["status"] = 1;
                $out["msg"] = "貌似服务费用有点大呀^v^";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }

        $this->load->model("mcar");
        if (!$ret = $this->mcar->save($param, $id)) {
            $out["status"] = 1;
            $out["msg"] = "保存失败。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Success
        $out["status"] = 0;
        $out["msg"] = "保存成功。";
        $this->output->set_output(json_encode($out));

        return true;
    }
/*}}}*/

}
