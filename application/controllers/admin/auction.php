<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename auction.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Auction extends Cub_Controller {

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
//            $search["status"] = $this->input->post("status");
            $search["cert_code"] = $this->input->post("cert_code");
            $this->lsession->set("auction_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("auction_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        // Only show auction data
        $search["status"] = "test";
        $out["search"] = $search;

        // The data of search
        $this->load->model("mcar");
        if($data = $this->mcar->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/auction/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_auction_index.html", $out);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/auction/");
        }
        // The data of search
        $this->load->model("mcar");
        if (!$car = $this->mcar->load($id)) {
            redirect("/admin/auction/");
        }
        if ($car->status != "test") {
            redirect("/admin/auction/");
        }

        $out = array();
        $out["car"] = $car;

        $param = array();
        $param["color"] = $this->lcommon->form_option("color"); 
        $param["transmission"] = $this->lcommon->form_option("transmission"); 
        $param["fuel"] = $this->lcommon->form_option("fuel"); 
        $param["sale_type"] = $this->lcommon->form_option("sale_type"); 
        $param["condition_score"] = $this->lcommon->form_option("condition_score"); 
        $param["accident_level"] = $this->lcommon->form_option("accident_level"); 

        $out["param"] = $param;

        $this->render("admin_auction_edit.html", $out);
    }
/*}}}*/
/*{{{ save */
    public function save($id = 0) {
        $out = array();
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }
        if (!$id || !is_numeric($id)) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }

        $param = array();
        // Rewrite test data
        $param["status"] = "prebook";
        // Auction when form.status is "complete"
        $status = null;
        if($this->input->post("status")) {
            $status = "auction";
            $param["status"] = "auction";
        }

        // sale_start_date
        if ($status) {
            $param["sale_start_date"] = $this->input->post("sale_start_date");
            if ($this->lcommon->is_empty($param["sale_start_date"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写销售开始时间";
                echo json_encode($out);

                return false;
            }
            // sale_end_date
            $param["sale_end_date"] = $this->input->post("sale_end_date");
            if ($this->lcommon->is_empty($param["sale_end_date"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写销售结束时间";
                echo json_encode($out);

                return false;
            }
            if ($param["sale_start_date"] > $param["sale_end_date"]) {
                $out["status"] = 1;
                $out["msg"] = "结束时间应大于开始时间";
                echo json_encode($out);

                return false;
            }
        }

        $this->load->model("mcar");
        if (!$ret = $this->mcar->save($param, $id)) {
            $out["status"] = 1;
            $out["msg"] = "保存失败。";
            echo json_encode($out);

            return false;
        }

        // Success
        $out["status"] = 0;
        $out["msg"] = "保存成功。";
        echo json_encode($out);

        return true;
    }
/*}}}*/

}
