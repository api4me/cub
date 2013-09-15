<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename prebook.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Prebook extends Cub_Controller {

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
            $search["status"] = $this->input->post("status");
            $this->lsession->set("car_prebook_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("car_prebook_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        $out["search"] = $search;

        // The data of search
        $this->load->model("mprebook");
        if($data = $this->mprebook->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/prebook/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_prebook_index.html", $out);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/prebook/");
        }
        // The data of search
        $this->load->model("mprebook");
        if (!$prebook = $this->mprebook->load($id)) {
            redirect("/admin/prebook/");
        }
        if ($prebook->status == "valid") {
            redirect("/admin/prebook/");
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

        $this->render("admin_prebook_edit.html", $out);
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
        // Invalid when form.status is "invalid"
        if($this->input->post("status")) {
            $this->_invalid($id);
        } else {
            $this->_valid($id);
        }

        return true;
    }
/*{{{ _invalid */
    private function _invalid($id) {
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
/*{{{ _valid */
    private function _valid($id) {
        $out = array();
        $param = array();
        // Name
        $param["name"] = trim($this->input->post("name"));
        if ($this->lcommon->is_empty($param["name"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写姓名";
            echo json_encode($out);

            return false;
        } else {
            if ($this->lcommon->get_size($param["name"]) > 16) {
                $out["status"] = 1;
                $out["msg"] = "姓名在16个字内(包括16)，请调整一下。";
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
                $out["msg"] = "请填写登录ID";
                echo json_encode($out);

                return false;
            } else {
                if ($this->lcommon->get_size($param["username"]) > 16) {
                    $out["status"] = 1;
                    $out["msg"] = "登录ID应在16个字内(包括16)，请调整一下。";
                    echo json_encode($out);

                    return false;
                }
                // Check username is exists
                $this->load->model("muser");
                if ($this->muser->exists_username($param["username"])) {
                    $out["status"] = 1;
                    $out["msg"] = "登录ID已经存在，请换一个试试。";
                    echo json_encode($out);

                    return false;
                }
            }

            // Email
            $param["email"] = trim($this->input->post("email"));
            if (!$this->lcommon->is_empty($param["email"])) {
                if (!$this->lcommon->is_email($param["email"])) {
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
/*{{{ del */
    public function del($id) {
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

        $this->load->model("mprebook");
        $param = array();
        $param['status'] = 'del';
        if (!$ret = $this->mprebook->save($param, $id)) {
            $out["status"] = 1;
            $out["msg"] = "删除失败";
            echo json_encode($out);

            return false;
        }
        $out["status"] = 0;
        $out["msg"] = "删除成功";
        echo json_encode($out);

        return true;
    }
/*}}}i*/
}
