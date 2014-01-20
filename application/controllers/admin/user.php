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
        if ($this->input->post()) {
            $search["username"] = $this->input->post("username");
            $search["role"] = $this->input->post("role");
            $search["enable"] = $this->input->post("enable");
            $search["phone"] = $this->input->post("phone");
            $search["startdate"] = $this->input->post("startdate");
            $search["enddate"] = $this->input->post("enddate");
            $this->lsession->set("user_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("user_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 

        $out["search"] = $search;
        // Param
        $param = array();
        $user = $this->lsession->get('user');

        if ($user->role != 'super') {
            $param["role"] = $this->lcommon->form_option("role", true, array('super', 'admin', 'guest'));
        } else {
            $param["role"] = $this->lcommon->form_option("role", true, array('guest'));
        }
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
        $user = $this->lsession->get('user');
        if ($user->role != 'super') {
            $param["role"] = $this->lcommon->form_option("role", true, array('super', 'admin', 'guest'));
        } else {
            $param["role"] = $this->lcommon->form_option("role", true, array('guest'));
        }
        $param["enable"] = $this->lcommon->form_option("enable");
        $out["param"] = $param;

        if ($id && is_numeric($id)) {
            // The data of search
            $this->load->model("muser");
            if ($data = $this->muser->load($id)) {
                $out["user"] = $data;
                if ($user->role != 'super') {
                    if (in_array($data->role, array('super', 'admin'))) {
                        redirect('/admin/user/');
                    }
                }
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
        if ($id == 0) {
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
                if ($this->muser->exists_username($param["username"], $id)) {
                    $out["status"] = 1;
                    $out["msg"] = "登录ID已经存在，请换一个试试。";
                    echo json_encode($out);

                    return false;
                }
            }
        }
        // Name
        $param["name"] = trim($this->input->post("name"));
        if ($this->lcommon->is_empty($param["name"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写显示名";
            echo json_encode($out);

            return false;
        } else {
            if ($this->lcommon->get_size($param["name"]) > 16) {
                $out["status"] = 1;
                $out["msg"] = "显示名应在16个字内(包括16)，请调整一下。";
                echo json_encode($out);

                return false;
            }
        }
        // Pwd
        if ($id == 0) {
            $param['pwd'] = $this->input->post("pwd");
            if ($this->lcommon->is_empty($param["pwd"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写密码";
                echo json_encode($out);

                return false;
            }
            $param['pwd'] = md5($param['pwd']);
        }
        // Phone
        $param["phone"] = trim($this->input->post("phone"));
        if (!$this->lcommon->is_empty($param["phone"])
            && !$this->lcommon->is_phone($param["phone"])) {
            $out["status"] = 1;
            $out["msg"] = "手机号输入有误，请检查一下。";
            echo json_encode($out);

            return false;
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
            if ($this->muser->exists_email($param["email"], $id)) {
                $out["status"] = 1;
                $out["msg"] = "Email已经存在，请换一个试试。";
                echo json_encode($out);

                return false;
            }
        }
        $param["area"] = $this->input->post("province").$this->input->post("city").$this->input->post("district");
        // 用户类型
        $user = $this->lsession->get('user');
        if ($id != $user->id) {
            $param["role"] = trim($this->input->post("role"));
            if ($this->lcommon->is_empty($param["role"])) {
                $out["status"] = 1;
                $out["msg"] = "请选择用户类型。";
                echo json_encode($out);

                return false;
            } else {
                $user = $this->lsession->get('user');
                if ($user->role != 'super') {
                    if (in_array($param['role'], array('super', 'admin', 'guest'))) {
                        $out["status"] = 1;
                        $out["msg"] = "用户类型有误。";
                        echo json_encode($out);

                        return false;
                    }
                }
            }
            // 是否可用
            $param["enable"] = trim($this->input->post("enable"));
            if ($this->lcommon->is_empty($param["enable"])) {
                $out["status"] = 1;
                $out["msg"] = "请选择是否可用。";
                echo json_encode($out);

                return false;
            }
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

        $id = $this->input->post("id");
        if (!$id) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }
        $param = array();
        $param['pwd'] = trim($this->input->post("p"));
        if ($this->lcommon->is_empty($param['pwd'])) {
            $out["status"] = 1;
            $out["msg"] = "请输入新密码。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $param['pwd'] = md5($param['pwd']);
        $this->load->model("muser");
        $user = $this->lsession->get('user');
        if ($user->role != 'super') {
            $data = $this->muser->load($id);
            if (in_array($data->role, array('super', 'admin'))){
                $out["status"] = 1;
                $out["msg"] = "您没有权限修改此用户的密码。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        if (!$ret = $this->muser->save($param, $id)) {
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
/*{{{ delete */
	public function delete() {
        $out = array();
        $this->output->set_content_type('application/json');
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $id = $this->input->post("id");
        if (!$id) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $param = array();
        // Delete
        $param['enable'] = 'D';
        $this->load->model("muser");
        if (!$ret = $this->muser->save($param, $id)) {
            $out["status"] = 1;
            $out["msg"] = "删除失败。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Success
        $out["status"] = 0;
        $out["msg"] = "删除成功。";
        $this->output->set_output(json_encode($out));

        return true;
	}
/*}}}*/

}
