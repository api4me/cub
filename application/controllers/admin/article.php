<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename article.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Article extends Cub_Controller {

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
            $search["title"] = $this->input->post("title");
            $search["tag"] = $this->input->post("tag");
            $this->lsession->set("article_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("article_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        $out["search"] = $search;
        // Param
        $param = array();
        $param["tag"] = $this->lcommon->form_option("tag");
        $out["param"] = $param;

        // The data of search
        $this->load->model("marticle");
        if($data = $this->marticle->load_all($search)) {
            $out["article"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/article/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_article_index.html", $out);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id = 0) {
        $out = array();

        // Param
        $param = array();
        $param["tag"] = $this->lcommon->form_option("tag");
        $param["enable"] = $this->lcommon->form_option("enable");
        $out["param"] = $param;

        if ($id && is_numeric($id)) {
            // The data of search
            $this->load->model("marticle");
            if ($article = $this->marticle->load($id)) {
                $out["article"] = $article;
            }
        }

        $this->render("admin_article_edit.html", $out);
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

        // Title
        $param = array();
        $param["title"] = trim($this->input->post("title"));
        if ($this->lcommon->is_empty($param["title"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写名称";
            echo json_encode($out);

            return false;
        } else {
            if ($this->lcommon->get_size($param["title"]) > 128) {
                $out["status"] = 1;
                $out["msg"] = "用户在128个字内(包括128)，请调整一下。";
                echo json_encode($out);

                return false;
            }
        }
        // 文章类型
        $param["tag"] = trim($this->input->post("tag"));
        if ($this->lcommon->is_empty($param["tag"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择文章类型。";
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
        $param["content"] = trim($this->input->post("content"));

        $this->load->model("marticle");
        if (!$ret = $this->marticle->save($param, $id)) {
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
        $this->load->model("marticle");
        if (!$ret = $this->marticle->save($param, $id)) {
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
