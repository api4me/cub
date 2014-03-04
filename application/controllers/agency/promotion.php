<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-2-13
 * Time: 下午19:04
 * To change this template use File | Settings | File Templates.
 */

class Promotion extends Cub_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index($start = 0)
    {
        $out = array();

        $this->config->load("pagination");
        // Search
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page");
        $user = $this->lsession->get('user');
        $search["user_id"] = $user->id;

        $this->load->model("magencyprom");
        if ($data = $this->magencyprom->load_all($search)) {
            $out["promotions"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/agency/promotion/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("agency_admin_promotion_index.html", $out);
    }

    public function edit($id = 0)
    {
        $out = array();

        if ($id && is_numeric($id)) {
            // The data of search
            $this->load->model("magencyprom");
            if ($promotion = $this->magencyprom->load($id)) {
                $out["promotion"] = $promotion;
            }
        }

        $this->render("agency_admin_promotion_edit.html", $out);
    }

    public function save($id = 0)
    {
        $out = array();
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }

        // Title
        $param = array();
        if ($id == 0) {
            $param["title"] = trim($this->input->post("title"));
            if ($this->lcommon->is_empty($param["title"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写标题";
                echo json_encode($out);

                return false;
            } else {
                if ($this->lcommon->get_size($param["title"]) > 20) {
                    $out["status"] = 1;
                    $out["msg"] = "标题应在20个字内(包括20)，请调整一下。";
                    echo json_encode($out);

                    return false;
                }
            }

            // Content
            $param["content"] = trim($this->input->post("content"));
            if ($this->lcommon->is_empty($param["content"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写内容";
                echo json_encode($out);

                return false;
            }
            // User id
            $user = $this->lsession->get('user');
            $param["user_id"] = $user->id;


        }

        $this->load->model("magencyprom");
        if (!$ret = $this->magencyprom->save($param, $id)) {
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

    public function delete()
    {
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
        $this->load->model("magencyprom");
        if (!$ret = $this->magencyprom->save($param, $id)) {
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
}