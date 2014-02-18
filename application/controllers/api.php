<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename api.php
* @touch date Wednesday, May 15, 2013 AM01:07:05 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class Api extends CI_Controller {

/*{{{ index */
    public function index() {
        $out = array();
        $out["data"] = "load...";
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($out));
    }
/*}}}*/
/*{{{ area */
    public function area() {
        $out = array();
        $out["data"] = $this->lcommon->area();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($out));
    }
/*}}}*/
/*{{{ model */
    public function model() {
        $out = array();
        $out["data"] = $this->lcommon->model();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($out));
    }
/*}}}*/
/*{{{ prebook */
    public function prebook() {
        $out = array();
        $this->output->set_content_type('application/json');
        $param = array();
        // Name
        $param['name'] = $this->input->post("name");
        if ($this->lcommon->is_empty($param["name"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写用户名";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if ($this->lcommon->get_size($param["name"]) > 16) {
                $out["status"] = 1;
                $out["msg"] = "用户在16个字内(包括16)，请调整一下。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        // Phone
        $param['phone'] = $this->input->post("phone");
        if ($this->lcommon->is_empty($param["phone"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写手机号";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if (!$this->lcommon->is_phone($param["phone"])) {
                $out["status"] = 1;
                $out["msg"] = "手机号输入有误，请检查一下。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        // Brand + Model
        $param["model"] = strval($this->input->post("brand")) . strval($this->input->post("model"));
        if ($this->lcommon->is_empty($param["model"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择品牌车型";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if(!$this->lcommon->model($param["model"])) {
                $out["status"] = 1;
                $out["msg"] = "品牌车型有误";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        // Area
        $param['area'] = strval($this->input->post("province"))
                        . strval($this->input->post("city"))
                        . strval($this->input->post("district"))
                        ;
        if ($this->lcommon->is_empty($param["area"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择地区";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if(!$this->lcommon->area($param["area"])) {
                $out["status"] = 1;
                $out["msg"] = "地区有误";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        // Buy date
        $param['buy_date'] = sprintf("%s-%s-01", $this->input->post("buydate-year"), $this->input->post("buydate-month"));
        if ($param['buy_date'] == '--01') {
            $out["status"] = 1;
            $out["msg"] = "请选择购买时间";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if (!strtotime($param['buy_date'])) {
                $out["status"] = 1;
                $out["msg"] = "购买时间不正确";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        $param['mileage'] = str_replace(array(' ', ','), '', $this->input->post("mileage"));
        if ($this->lcommon->is_empty($param['mileage'])) {
            $out["status"] = 1;
            $out["msg"] = "请填写表盘显示公里数";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if (!is_numeric($param['mileage']) || $param['mileage'] < 1) {
                $out["status"] = 1;
                $out["msg"] = "请正确填写公里数";
                $this->output->set_output(json_encode($out));
            }
        }
        $param['ip'] = $this->input->ip_address();

        $this->load->model('mprebook');
        // Prevent irrigation
        $arr = array(
            'phone' => $param['phone'],
            'model' => $param['model'],
            //'ip' => $param['ip'],
            'status' => 'add',
        );
        if ($this->mprebook->count_by_key($arr) > 0) {
                $out["status"] = 1;
                $out["msg"] = "您的信息已经提交，请保持手机畅通，我们会尽快联系您。";
                $this->output->set_output(json_encode($out));

                return false;
        }

        // Insert in prebook
        $param['status'] = 'add';
        if ($this->mprebook->save($param, 0)) {
            // Success
            $out["status"] = 0;
            $out["msg"] = "提交成功。";
            $this->output->set_output(json_encode($out));

            return true;
        }

        $out["status"] = 1;
        $out["msg"] = "提交失败。";
        $this->output->set_output(json_encode($out));

        return false;
    }
/*}}}*/

    /*{{{ purchase  */
    public function purchase() {
        $out = array();
        $this->output->set_content_type('application/json');
        $param = array();
        // Name
        $param['name'] = $this->input->post("name");
        if ($this->lcommon->is_empty($param["name"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写用户名";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if ($this->lcommon->get_size($param["name"]) > 16) {
                $out["status"] = 1;
                $out["msg"] = "用户名在16个字内(包括16)，请调整一下。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
        // Phone
        $param['phone'] = $this->input->post("phone");
        if ($this->lcommon->is_empty($param["phone"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写手机号";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if (!$this->lcommon->is_phone($param["phone"])) {
                $out["status"] = 1;
                $out["msg"] = "手机号输入有误，请检查一下。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }

        // Content
        $param['content'] = $this->input->post("content");
        if ($this->lcommon->is_empty($param["content"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写代购要求";
            $this->output->set_output(json_encode($out));

            return false;
        } else {
            if (!$this->lcommon->get_size($param["content"]) > 200) {
                $out["status"] = 1;
                $out["msg"] = "代购要求请控制在200字以内。";
                $this->output->set_output(json_encode($out));

                return false;
            }
        }
//        // Brand + Model
//        $param["model"] = strval($this->input->post("brand")) . strval($this->input->post("model"));
//        if ($this->lcommon->is_empty($param["model"])) {
//            $out["status"] = 1;
//            $out["msg"] = "请选择品牌车型";
//            $this->output->set_output(json_encode($out));
//
//            return false;
//        } else {
//            if(!$this->lcommon->model($param["model"])) {
//                $out["status"] = 1;
//                $out["msg"] = "品牌车型有误";
//                $this->output->set_output(json_encode($out));
//
//                return false;
//            }
//        }

        $param['ip'] = $this->input->ip_address();

        $this->load->model('mpurchase');
        // Prevent irrigation
        $arr = array(
            'phone' => $param['phone'],
            'content' => $param['content'],
            'status' => 'add',
        );

        if ($this->mpurchase->count_by_key($arr) > 0) {
            $out["status"] = 1;
            $out["msg"] = "您的信息已经提交，请保持手机畅通，我们会尽快联系您。";
            $this->output->set_output(json_encode($out));

            return false;
        }

        // Insert in purchase
        $param['status'] = 'add';
        if ($this->mpurchase->save($param, 0)) {
            // Success
            $out["status"] = 0;
            $out["msg"] = "提交成功。";
            $this->output->set_output(json_encode($out));

            return true;
        }

        error_log("save after");

        $out["status"] = 1;
        $out["msg"] = "提交失败。";
        $this->output->set_output(json_encode($out));

        return false;
    }
    /*}}}*/
}
