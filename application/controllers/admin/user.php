<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename user.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class User extends Cub_Controller {

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

        $this->render("admin_user_index.html", $out);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id = 0) {
        $out = array();

        // Param
        $param = array();
        $param["role"] = $this->lcommon->form_option("role");
        $param["enable"] = $this->lcommon->form_option("enable");
        $out["param"] = $param;

        if ($id && is_numeric($id)) {
            // The data of search
            $this->load->model("muser");
            if ($user = $this->muser->load($id)) {
                $out["user"] = $user;
            }
        }

        $this->render("admin_user_edit.html", $out);
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

        // TODO password char validate.
        // Username
        $param = array();
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
            if ($this->muser->exists_username($param["username"], $id)) {
                $out["status"] = 1;
                $out["msg"] = "登录名已经存在，请换一个试试。";
                echo json_encode($out);

                return false;
            }
        }
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
        // Phone
        $param["phone"] = trim($this->input->post("phone"));
        if ($this->lcommon->is_empty($param["phone"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写手机号";
            echo json_encode($out);

            return false;
        } else {
            if (!$this->lcommon->is_phone($param["phone"])) {
                $out["status"] = 1;
                $out["msg"] = "手机号输入有误，请检查一下。";
                echo json_encode($out);

                return false;
            }
            // Check phone is exists
            $this->load->model("muser");
            if ($this->muser->exists_phone($param["phone"], $id)) {
                $out["status"] = 1;
                $out["msg"] = "手机号已经存在，请换一个试试。";
                echo json_encode($out);

                return false;
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
            if ($this->muser->exists_email($param["email"], $id)) {
                $out["status"] = 1;
                $out["msg"] = "Email已经存在，请换一个试试。";
                echo json_encode($out);

                return false;
            }
        }
        // 用户类型
        $param["role"] = trim($this->input->post("role"));
        if ($this->lcommon->is_empty($param["role"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择用户类型。";
            echo json_encode($out);

            return false;
        }
        // 是否可用
        $param["enable"] = trim($this->input->post("enable"));
        if ($this->lcommon->is_empty($param["enable"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择是否可用。";
            echo json_encode($out);

            return false;
        }
        $param["remark"] = trim($this->input->post("remark"));

        $this->load->model("muser");
        if (!$ret = $this->muser->save($param, $id)) {
            $out["status"] = 1;
            $out["msg"] = "保存失败。";
            echo json_encode($out);

            return false;
        }

        // Success
        $out["status"] = 0;
        if (!$id) {
            $out["id"] = $ret;
        } else {
            $out["id"] = $id;
        }
        $out["msg"] = "保存成功。";
        echo json_encode($out);

        return true;
    }
/*}}}*/
/*{{{ enable */
    public function enable($id) {
        $out = array();
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }

        $param = array();
        $param["enable"] = trim($this->input->post("enable"));

        $this->load->model("muser");
        if (!$ret = $this->muser->change_enable($param, $id)) {
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
