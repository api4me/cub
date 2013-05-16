<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename user.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper("form");
        $this->load->helper("common");
        $this->load->library('encrypt');
        $this->load->library("lcommon");
        $this->load->library("twig");
    }

/*{{{ index */
    public function index($start = 0) {
        $output = array();
        $this->config->load("pagination");
        // Search
        $search = array();
        $search["username"] = $this->input->post("username");
        $search["role"] = $this->input->post("role");
        $search["enable"] = $this->input->post("enable");
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page");
        $output["search"] = $search;
        // Param
        $param = array();
        $param["role"] = $this->lcommon->form_option("role", true, array("guest"));
        $param["enable"] = $this->lcommon->form_option("enable");
        $output["param"] = $param;

        // The data of search
        $this->load->model("muser");
        if($data = $this->muser->select($search)) {
            $output["users"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/user/index";
            $output["pagination"] = $this->pagination->create_links();
        }

        $this->twig->display("admin_user_index.html", $output);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id) {
        $output = array();

        // Param
        $param = array();
        $param["role"] = $this->lcommon->form_option("role");
        $param["enable"] = $this->lcommon->form_option("enable");
        $output["param"] = $param;

        if ($id && is_numeric($id)) {
            // The data of search
            $this->load->model("muser");
            if ($user = $this->muser->load($id)) {
                $output["user"] = $user;
            }
        }

        $this->twig->display("admin_user_edit.html", $output);
    }
/*}}}*/
/*{{{ save */
    public function save($id) {
        $output = array();
        if (!$this->input->is_ajax_request()) {
            $output["status"] = 1;
            $output["msg"] = "系统忙，请稍后...";
            echo json_encode($output);

            return false;
        }

        // Username
        $param = array();
        $param["username"] = trim($this->input->post("username"));
        if ($this->lcommon->is_empty($param["username"])) {
            $output["status"] = 1;
            $output["msg"] = "请填写登录名";
            echo json_encode($output);

            return false;
        } else {
            if ($this->lcommon->get_size($param["username"]) > 10) {
                $output["status"] = 1;
                $output["msg"] = "用户在10个字内(包括10)，请调整一下。";
                echo json_encode($output);

                return false;
            }
        }

        // Name

    }
/*}}}*/
/*{{{ del */
    public function del($id) {
    }
/*}}}*/

}
