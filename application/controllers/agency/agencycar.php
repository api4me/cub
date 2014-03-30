<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-2-13
 * Time: 下午18:52
 * To change this template use File | Settings | File Templates.
 */

class AgencyCar extends Cub_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /*{{{ index */
    public function index($start = 0){
        $user = $this->lsession->get('user');
        if (!$user->homepage || $user->homepage != 'Y') {
            redirect("/user/");
        }

        $out = array();
        $out["user"] = $user;

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page");
        $out["search"] = $search;

        $search["user_id"] = $user->id;

        // The data of search
        $this->load->model("magencycar");
        if($data = $this->magencycar->load_all($search, true)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/agency/agencycar/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("agency_agencycar_index.html", $out);
    }
    /*}}}*/
    /*{{{ edit */
    public function edit($id = 0) {
        if (!is_numeric($id)) {
            redirect("/agency/agencycar/");
        }

        $out = array();
        if ($id) {
            $this->load->model("magencycar");
            if (!$car = $this->magencycar->load($id)) {
                redirect("/agency/agencycar/");
            }

            $out["car"] = $car;
//        } else {
//            $car = array();
//            $car["id"] = 0;
//            $out["car"] = $car;
        }

//        error_log("car id " + $out["car"]->id);

        $param = array();
        $param["color"] = $this->lcommon->form_option("color");
        $param["tax_cert"] = $this->lcommon->form_option("exists");
        $param["use_of_nature"] = $this->lcommon->form_option("use_of_nature");
        $param["other_cert_type"] = $this->lcommon->form_option("other_cert_type");

        $param["displacement"] = $this->lcommon->form_option("displacement");
        $param["cylinder"] = $this->lcommon->form_option("cylinder");
        $param["emission_std"] = $this->lcommon->form_option("emission_std");
        $param["transmission"] = $this->lcommon->form_option("transmission");
        $param["air_sac"] = $this->lcommon->form_option("air_sac");
        $param["drive_mode"] = $this->lcommon->form_option("drive_mode");
        $param["fuel"] = $this->lcommon->form_option("fuel");
        $param["sale_type"] = $this->lcommon->form_option("sale_type");
        $param["appraisal_level"] = $this->lcommon->form_option("appraisal_level");

        $param["issue"] = $this->lcommon->form_option("exists");

        $out["param"] = $param;

        $this->render("agency_agencycar_edit.html", $out);
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
        if (!is_numeric($id)) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $param = array();
        // -----------------------------------------------------------
        // 客户资料
        // -----------------------------------------------------------
        // area
        $param["area"] = $this->input->post("province").$this->input->post("city").$this->input->post("district");
        // remark
        $param["remark"] = trim($this->input->post("remark", true));

        // -----------------------------------------------------------
        // 基本信息
        // -----------------------------------------------------------
        // cert_code
        $param["cert_code"] = $this->input->post("cert_code");
        // sale_type
        $param["sale_type"] = $this->input->post("sale_type");

        // model
        $param["model"] = $this->input->post("brand").$this->input->post("model");
        $param["model_name"] = model_value($param["model"]);
        // car_num
        $param["car_num"] = $this->input->post("car_num");
        if (!$this->lcommon->is_empty($param["car_num"])
            && $this->lcommon->get_size($param["car_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写车牌号。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // engine_num
        $param["engine_num"] = $this->input->post("engine_num");
        if (!$this->lcommon->is_empty($param["engine_num"])
            && $this->lcommon->get_size($param["engine_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写发动机号。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // chassis_num
        $param["chassis_num"] = $this->input->post("chassis_num");
        if (!$this->lcommon->is_empty($param["chassis_num"])
            && $this->lcommon->get_size($param["chassis_num"]) > 32) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写车架号。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // factory_date
        $param["factory_date"] = $this->input->post("factory_date");
        // buy_date
        $param["buy_date"] = $this->input->post("buy_date");
        // mileage
        $param["mileage"] = $this->input->post("mileage");
        if (!$this->lcommon->is_empty($param["mileage"])
            && !is_numeric($param["mileage"])) {
            $out["status"] = 1;
            $out["msg"] = "里程是半角数值型，请调整一下。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // color
        $param["color"] = $this->input->post("color");
        // 年检截至年月
        $param["annual_test"] = $this->input->post("annual_test") ? $this->input->post("annual_test"): null;
        // 购置税证书
        $param["tax_cert"] = $this->input->post("tax_cert");
        // 车船税截至年月
        $param["vv_tax"] = $this->input->post("vv_tax") ? $this->input->post("vv_tax") : null;
        // 交强险截至年月
        $param["traffic_insurance"] = $this->input->post("traffic_insurance") ? $this->input->post("traffic_insurance"): null;
        // 使用性质
        $param["use_of_nature"] = $this->input->post("use_of_nature");
        // 其他法定凭证、证明
        $param["other_cert_type"] = $this->input->post("other_cert_type");
        // 其他法定凭证、证明值
        $param["other_cert_value"] = $this->input->post("other_cert_value");
        // 保险/证件信息备注
        $param["cert_remark"] = $this->input->post("cert_remark");
        // 车主名称/姓名
        $param["owner_name"] = $this->input->post("owner_name");
        // 法人证书代码/身份证号码
        $param["idcard_num"] = $this->input->post("idcard_num");

        // -----------------------------------------------------------
        // 重要配置
        // -----------------------------------------------------------
        // 燃料标号
        $param["fuel"] = $this->input->post("fuel");
        // 排量
        $param["displacement"] = $this->input->post("displacement");
        // 缸数
        $param["cylinder"] = $this->input->post("cylinder");
        // 发动机功率
        $param["engine_power"] = $this->input->post("engine_power");
        // 排放标准
        $param["emission_std"] = $this->input->post("emission_std");
        // 变速箱
        $param["transmission"] = $this->input->post("transmission");
        // 气囊
        $param["air_sac"] = $this->input->post("air_sac");
        // 驱动方式
        $param["drive_mode"] = $this->input->post("drive_mode");
        // 其他重要配置
        $param["other_conf"] = $this->input->post("other_conf");

        // -----------------------------------------------------------
        // 技术鉴定
        // -----------------------------------------------------------
        // 是否为事故车
        $extra = array();
        $score = 0;
        // 是否为事故车
        $extra['issue_confirm'] = $this->input->post("extra_issue_confirm");
        $extra['issue_info'] = $this->input->post("extra_issue_info");

        // 市场价
        $param["market_price"] = $this->input->post("market_price");
        if (!$this->lcommon->is_empty($param["market_price"])
            && !is_numeric($param["market_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写市场价。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 评估价
        $param["sale_price"] = $this->input->post("sale_price");
        if (!$this->lcommon->is_empty($param["sale_price"])
            && !is_numeric($param["sale_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写出让价。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $user = $this->lsession->get('user');
        $param["user_id"] = $user->id;

        $this->load->model("magencycar");

        if (!$ret = $this->magencycar->save($param, $id)) {
            $out["status"] = 1;
            $out["msg"] = "保存失败。";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            $id = $ret;
        }

        // Success
        $out["id"] = $id;
        $out["status"] = 0;
        $out["msg"] = "保存成功。";
        $this->output->set_output(json_encode($out));

        return true;
    }
    /*}}}*/
    /*{{{ upload */
    public function upload($id) {
        $this->load->library('limage');
        return $this->limage->upload4agency($id);
    }
    /*}}}*/
    /*{{{ delimage */
    public function delimage($id){
        $this->load->library('limage');
        return $this->limage->delimage4agency($id);
    }
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

        $this->load->model("magencycar");
        $param = array();
        $param['status'] = 'del';
        if (!$ret = $this->magencycar->save($param, $id)) {
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
