<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename test.php
* @touch date Thursday, May 16, 2013 AM03:55:20 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Test extends Cub_Controller {

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
            $this->lsession->set("test_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("test_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
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
            $this->pagination->base_url = site_url() . "/admin/test/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_test_index.html", $out);
    }
/*}}}*/
/*{{{ edit */
    public function edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/test/");
        }
        // The data of search
        $this->load->model("mcar");
        if (!$car = $this->mcar->load($id)) {
            redirect("/admin/test/");
        }
        if ($car->status != "prebook") {
            redirect("/admin/test/");
        }

        $out = array();
        $out["car"] = $car;

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

        $this->render("admin_test_edit.html", $out);
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
        // Test when form.status is "complete"
        $status = null;
        if($this->input->post("status")) {
            $status = "test";
            $param["status"] = "test";
        }

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
        if ($status && $this->lcommon->is_empty($param["sale_type"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择销售类型";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // model
        $param["model"] = $this->input->post("brand").$this->input->post("model");
        $param["model_name"] = model_value($param["model"]);
        // car_num
        $param["car_num"] = $this->input->post("car_num");
        if ($status && $this->lcommon->is_empty($param["car_num"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写车牌号";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($param["car_num"]) 
            && $this->lcommon->get_size($param["car_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写车牌号。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // engine_num
        $param["engine_num"] = $this->input->post("engine_num");
        if ($status && $this->lcommon->is_empty($param["engine_num"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写发动机号";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($param["engine_num"]) 
            && $this->lcommon->get_size($param["car_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写发动机号。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // chassis_num
        $param["chassis_num"] = $this->input->post("chassis_num");
        if ($status && $this->lcommon->is_empty($param["chassis_num"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写车架号";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($param["chassis_num"]) 
            && $this->lcommon->get_size($param["chassis_num"]) > 32) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写车架号。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // factory_date
        $param["factory_date"] = $this->input->post("factory_date");
        if ($status && $this->lcommon->is_empty($param["factory_date"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写出厂日期。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // buy_date
        $param["buy_date"] = $this->input->post("buy_date");
        if ($status && $this->lcommon->is_empty($param["buy_date"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写初次登记日期。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // mileage
        $param["mileage"] = $this->input->post("mileage");
        if ($status && $this->lcommon->is_empty($param["mileage"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写里程。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($param["mileage"]) 
            && !is_numeric($param["mileage"])) {
            $out["status"] = 1;
            $out["msg"] = "里程是半角数值型，请调整一下。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // color
        $param["color"] = $this->input->post("color");
        if ($status && $this->lcommon->is_empty($param["color"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择车身颜色。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 年检截至年月
        $param["annual_test"] = $this->input->post("annual_test");
        // 购置税证书
        $param["tax_cert"] = $this->input->post("tax_cert");
        // 车船税截至年月
        $param["vv_tax"] = $this->input->post("vv_tax");
        // 交强险截至年月
        $param["traffic_insurance"] = $this->input->post("traffic_insurance");
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
        if ($status && $this->lcommon->is_empty($param["fuel"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择燃料种类";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 排量
        $param["displacement"] = $this->input->post("displacement");
        if ($status && $this->lcommon->is_empty($param["displacement"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择排量";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 缸数
        $param["cylinder"] = $this->input->post("cylinder");
        if ($status && $this->lcommon->is_empty($param["cylinder"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择缸数";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 发动机功率
        $param["engine_power"] = $this->input->post("engine_power");
        // 排放标准
        $param["emission_std"] = $this->input->post("emission_std");
        if ($status && $this->lcommon->is_empty($param["emission_std"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择排放标准";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 变速箱
        $param["transmission"] = $this->input->post("transmission");
        if ($status && $this->lcommon->is_empty($param["transmission"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择变速箱种类";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 气囊
        $param["air_sac"] = $this->input->post("air_sac");
        if ($status && $this->lcommon->is_empty($param["air_sac"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择气囊";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 驱动方式
        $param["drive_mode"] = $this->input->post("drive_mode");
        if ($status && $this->lcommon->is_empty($param["drive_mode"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择驱动方式";
            $this->output->set_output(json_encode($out));

            return false;
        }
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
        if ($status && $this->lcommon->is_empty($extra["issue_confirm"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择是否为事故车";
            $this->output->set_output(json_encode($out));

            return false;
        }
        $extra['issue_info'] = $this->input->post("extra_issue_info");
        // 车身检查
        $extra['appraisal_body_score'] = $this->input->post("extra_appraisal_body_score");
        if ($status && $this->lcommon->is_empty($extra["appraisal_body_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写车身检查得分";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($extra["appraisal_body_score"])) {
            if (!is_numeric($extra["appraisal_body_score"])) {
                $out["status"] = 1;
                $out["msg"] = "请正确车身检查得分。";
                $this->output->set_output(json_encode($out));

                return false;
            } else if(intval($extra["appraisal_body_score"]) > 20) {
                $out["status"] = 1;
                $out["msg"] = "车身检查得分不能大于20分。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $score += intval($extra["appraisal_body_score"]);
        $extra['appraisal_body_comment'] = $this->input->post("extra_appraisal_body_comment");
        // 发动机检查
        $extra['appraisal_engine_score'] = $this->input->post("extra_appraisal_engine_score");
        if ($status && $this->lcommon->is_empty($extra["appraisal_engine_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写发动机检查得分";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($extra["appraisal_engine_score"])) {
            if (!is_numeric($extra["appraisal_engine_score"])) {
                $out["status"] = 1;
                $out["msg"] = "请正确发动机检查得分。";
                $this->output->set_output(json_encode($out));

                return false;
            } else if (intval($extra["appraisal_engine_score"]) > 20) {
                $out["status"] = 1;
                $out["msg"] = "发动机检查得分不能大于20分。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $score += intval($extra["appraisal_engine_score"]);
        $extra['appraisal_engine_comment'] = $this->input->post("extra_appraisal_engine_comment");
        // 车内检查
        $extra['appraisal_inner_score'] = $this->input->post("extra_appraisal_inner_score");
        if ($status && $this->lcommon->is_empty($extra["appraisal_inner_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写车内检查得分";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($extra["appraisal_inner_score"])) {
            if (!is_numeric($extra["appraisal_inner_score"])) {
                $out["status"] = 1;
                $out["msg"] = "请正确车内检查得分。";
                $this->output->set_output(json_encode($out));

                return false;
            } else if (intval($extra["appraisal_inner_score"]) > 10) {
                $out["status"] = 1;
                $out["msg"] = "车内检查得分不能大于10分。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $score += intval($extra["appraisal_inner_score"]);
        $extra['appraisal_inner_comment'] = $this->input->post("extra_appraisal_inner_comment");
        // 启动检查
        $extra['appraisal_start_score'] = $this->input->post("extra_appraisal_start_score");
        if ($status && $this->lcommon->is_empty($extra["appraisal_start_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写启动检查得分";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($extra["appraisal_start_score"])) {
            if (!is_numeric($extra["appraisal_start_score"])) {
                $out["status"] = 1;
                $out["msg"] = "请正确启动检查得分。";
                $this->output->set_output(json_encode($out));

                return false;
            } else if (intval($extra["appraisal_start_score"]) > 20) {
                $out["status"] = 1;
                $out["msg"] = "启动检查得分不能大于10分。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $score += intval($extra["appraisal_start_score"]);
        $extra['appraisal_start_comment'] = $this->input->post("extra_appraisal_start_comment");
        // 路试检查
        $extra['appraisal_road_score'] = $this->input->post("extra_appraisal_road_score");
        if ($status && $this->lcommon->is_empty($extra["appraisal_road_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写路试检查得分";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($extra["appraisal_road_score"])) {
            if (!is_numeric($extra["appraisal_road_score"])) {
                $out["status"] = 1;
                $out["msg"] = "请正确路试检查得分。";
                $this->output->set_output(json_encode($out));

                return false;
            } else if (intval($extra["appraisal_road_score"]) > 15) {
                $out["status"] = 1;
                $out["msg"] = "路试检查得分不能大于15分。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $score += intval($extra["appraisal_road_score"]);
        $extra['appraisal_road_comment'] = $this->input->post("extra_appraisal_road_comment");
        // 底盘检查
        $extra['appraisal_chassis_score'] = $this->input->post("extra_appraisal_chassis_score");
        if ($status && $this->lcommon->is_empty($extra["appraisal_chassis_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写底盘检查得分";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($extra["appraisal_chassis_score"])) {
            if (!is_numeric($extra["appraisal_chassis_score"])) {
                $out["status"] = 1;
                $out["msg"] = "请正确底盘检查得分。";
                $this->output->set_output(json_encode($out));

                return false;
            } else if (intval($extra["appraisal_chassis_score"]) > 15) {
                $out["status"] = 1;
                $out["msg"] = "底盘检查得分不能大于15分。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $score += intval($extra["appraisal_chassis_score"]);
        $extra['appraisal_chassis_comment'] = $this->input->post("extra_appraisal_chassis_comment");
        // 技术鉴定信息备注
        $extra['remark'] = $this->input->post("extra_remark");
        $param['extra'] = json_encode($extra);

        // 车况级别评分
        $param["condition_score"] = $score;
        if (!$param["appraisal_level"] = $this->input->post("appraisal_level")) {
            $param["appraisal_level"] = get_appraisal($extra['issue_confirm'], $score);
        }
        // 市场价
        $param["market_price"] = $this->input->post("market_price");
        if ($status && $this->lcommon->is_empty($param["market_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写市场价";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($param["market_price"]) 
            && !is_numeric($param["market_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写市场价。";
            $this->output->set_output(json_encode($out));

            return false;
        }
        // 评估价
        $param["sale_price"] = $this->input->post("sale_price");
        if ($status && $this->lcommon->is_empty($param["sale_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写出让价";
            $this->output->set_output(json_encode($out));

            return false;
        }
        if (!$this->lcommon->is_empty($param["sale_price"]) 
            && !is_numeric($param["sale_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写出让价。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        $this->load->model("mcar");
        // images
        if ($status) {
            $car = $this->mcar->load($id);
            if (!$car->images) {
                $out["status"] = 1;
                $out["msg"] = "请上传车辆相关图片";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }

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
/*{{{ upload */
    public function upload($id) {
        $this->load->library('limage');
        return $this->limage->upload($id);
    }
/*}}}*/
/*{{{ delimage */
    public function delimage($id){
        $this->load->library('limage');
        return $this->limage->delimage($id);
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

        $this->load->model("mcar");
        $param = array();
        $param['status'] = 'del';
        if (!$ret = $this->mcar->save($param, $id)) {
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
