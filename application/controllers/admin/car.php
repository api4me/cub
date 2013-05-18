<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename car.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Car extends Cub_Controller {

    public function __construct() {
        parent::__construct();
    }

/*{{{ index */
    public function index($start = 0) {
        $out = array();

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        if ($this->input->post()) {
            $search["username"] = $this->input->post("username");
            $search["role"] = $this->input->post("role");
            $search["enable"] = $this->input->post("enable");
            $this->lsession->set("user_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("user_search")) {
                $search = $tmp;
            }
        }
        $out["search"] = $search;
        // Param
        $param = array();
        $param["role"] = $this->lcommon->form_option("role", true, array("guest"));
        $param["enable"] = $this->lcommon->form_option("enable");
        $out["param"] = $param;

        // The data of search
        $this->load->model("muser");
        if($data = $this->muser->select($search)) {
            $out["users"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/user/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_car_index.html", $out);
    }
/*}}}*/
/*{{{ prebook */
    public function prebook($start = 0, $func = "index") {
        if (!method_exists($this, "prebook_".$func)) {
            $func = "index";
        }
        $func = "prebook_" . $func;
        $this->$func($start);
    }

/*}}}*/
/*{{{ prebook_index */
    public function prebook_index($start = 0){
        $out = array();

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        if ($this->input->post()) {
            $search["phone"] = $this->input->post("phone");
            $search["status"] = $this->input->post("status");
            $this->lsession->set("car_prebook_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("car_prebook_search")) {
                $search = $tmp;
            }
        }
        $out["search"] = $search;

        // The data of search
        $this->load->model("mprebook");
        if($data = $this->mprebook->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/prebook";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_car_prebook_index.html", $out);
    }
/*}}}*/
/*{{{ prebook_edit */
    public function prebook_edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/car/prebook/");
        }
        // The data of search
        $this->load->model("mprebook");
        if (!$prebook = $this->mprebook->load($id)) {
            redirect("/admin/car/prebook/");
        }
        if ($prebook->status == "valid") {
            redirect("/admin/car/prebook/");
        }

        $out = array();
        $out["prebook"] = $prebook;

        // Param
        $param = array();
        $this->load->model("muser");
        if ($tmp = $this->muser->get_data_by_phone($prebook->phone)) {
            $param["username"] = $tmp->username;
            $out["param"] = $param;
        }

        $this->render("admin_car_prebook_edit.html", $out);
    }
/*}}}*/
/*{{{ prebook_save */
    public function prebook_save($id = 0) {
        $out = array();
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }
        // Invalid when form.status is "invalid"
        if($this->input->post("status")) {
            $this->_prebook_invalid($id);
        } else {
            $this->_prebook_valid($id);
        }

        return true;
    }
/*{{{ _prebook_invalid */
    private function _prebook_invalid($id) {
        $out = array();
        $this->load->model("mprebook");
        if (!$ret = $this->mprebook->invalid($id)) {
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
/*{{{ _prebook_valid */
    private function _prebook_valid($id) {
        $out = array();
        $param = array();
        // Name
        $param["name"] = trim($this->input->post("name"));
        if ($this->lcommon->is_empty($param["name"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写用户名";
            echo json_encode($out);

            return false;
        } else {
            if ($this->lcommon->get_size($param["name"]) > 16) {
                $out["status"] = 1;
                $out["msg"] = "用户在16个字内(包括16)，请调整一下。";
                echo json_encode($out);

                return false;
            }
        }
        // Username
        $param["username_exists"] = $this->input->post("chk-username");
        if (!$param["username_exists"]){
            $param["username"] = trim($this->input->post("username"));
            if ($this->lcommon->is_empty($param["username"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写登录名";
                echo json_encode($out);

                return false;
            } else {
                if ($this->lcommon->get_size($param["username"]) > 10) {
                    $out["status"] = 1;
                    $out["msg"] = "用户在10个字内(包括10)，请调整一下。";
                    echo json_encode($out);

                    return false;
                }
                // Check username is exists
                $this->load->model("muser");
                if ($this->muser->exists_username($param["username"])) {
                    $out["status"] = 1;
                    $out["msg"] = "用户已经存在，请换一个试试。";
                    echo json_encode($out);

                    return false;
                }
            }
        }
        // Email
        $param["email"] = trim($this->input->post("email"));
        if (!$this->lcommon->is_empty($param["email"])) {
            if ($this->lcommon->is_phone($param["email"])) {
                $out["status"] = 1;
                $out["msg"] = "Email输入有误，请检查一下。";
                echo json_encode($out);

                return false;
            }
            // Check email is exists
            $this->load->model("muser");
            if ($this->muser->exists_email($param["email"])) {
                $out["status"] = 1;
                $out["msg"] = "Email已经存在，请换一个试试。";
                echo json_encode($out);

                return false;
            }
        }
        $param["model"] = $this->input->post("brand").$this->input->post("model");
        $param["area"] = $this->input->post("province").$this->input->post("city").$this->input->post("district");
        $param["buy_date"] = $this->input->post("buy_date");
        $param["mileage"] = $this->input->post("mileage");
        $param["remark"] = trim($this->input->post("remark", true));

        $this->load->model("mprebook");
        if (!$ret = $this->mprebook->valid($param, $id)) {
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
/*}}}*/

/*{{{ test */
    public function test($start = 0, $func = "index") {
        if (!method_exists($this, "test_".$func)) {
            $func = "index";
        }
        $func = "test_" . $func;
        $this->$func($start);
    }

/*}}}*/
/*{{{ test_index */
    public function test_index($start = 0){
        $out = array();

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        if ($this->input->post()) {
            $search["phone"] = $this->input->post("phone");
            $search["status"] = $this->input->post("status");
            $this->lsession->set("car_test_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("car_test_search")) {
                $search = $tmp;
            }
        }
        // Only show prebook data
        $search["status"] = "prebook";
        $out["search"] = $search;

        // The data of search
        $this->load->model("mcar");
        if($data = $this->mcar->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/prebook";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_car_test_index.html", $out);
    }
/*}}}*/
/*{{{ test_edit */
    public function test_edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/car/test/");
        }
        // The data of search
        $this->load->model("mcar");
        if (!$car = $this->mcar->load($id)) {
            redirect("/admin/car/test/");
        }
        if ($car->status != "prebook") {
            redirect("/admin/car/test/");
        }

        $out = array();
        $out["car"] = $car;

        $param = array();
        $param["transmission"] = $this->lcommon->form_option("transmission"); 
        $param["fuel"] = $this->lcommon->form_option("fuel"); 
        $param["sale_type"] = $this->lcommon->form_option("sale_type"); 
        $param["condition_score"] = $this->lcommon->form_option("condition_score"); 
        $param["accident_level"] = $this->lcommon->form_option("accident_level"); 

        $out["param"] = $param;


        $this->render("admin_car_test_edit.html", $out);
    }
/*}}}*/
    public function test_upload() {
        $this->load->library("uploadhander");
    }

}
