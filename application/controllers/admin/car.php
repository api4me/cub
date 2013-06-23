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
            $this->pagination->base_url = site_url() . "/admin/car/prebook";
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
            if ($this->lcommon->is_mail($param["email"])) {
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
            $this->pagination->base_url = site_url() . "/admin/car/test";
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
        $param["color"] = $this->lcommon->form_option("color"); 
        $param["transmission"] = $this->lcommon->form_option("transmission"); 
        $param["fuel"] = $this->lcommon->form_option("fuel"); 
        $param["sale_type"] = $this->lcommon->form_option("sale_type"); 
        $param["condition_score"] = $this->lcommon->form_option("condition_score"); 
        $param["accident_level"] = $this->lcommon->form_option("accident_level"); 

        $out["param"] = $param;

        $this->render("admin_car_test_edit.html", $out);
    }
/*}}}*/
/*{{{ test_save */
    public function test_save($id = 0) {
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

        $param = array();
        // Test when form.status is "complete"
        $status = null;
        if($this->input->post("status")) {
            $status = "test";
            $param["status"] = "test";
        }

        // model
        $param["model"] = $this->input->post("brand").$this->input->post("model");
        // area
        $param["area"] = $this->input->post("province").$this->input->post("city").$this->input->post("district");
        // remark
        $param["remark"] = trim($this->input->post("remark", true));
        // mileage
        $param["mileage"] = $this->input->post("mileage");
        if ($status && $this->lcommon->is_empty($param["mileage"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写里程";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["mileage"]) 
            && !is_numeric($param["mileage"])) {
            $out["status"] = 1;
            $out["msg"] = "里程是半角数值型，请调整一下。";
            echo json_encode($out);

            return false;
        }

        // car_num
        $param["car_num"] = $this->input->post("car_num");
        if ($status && $this->lcommon->is_empty($param["car_num"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写车牌号";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["car_num"]) 
            && $this->lcommon->get_size($param["car_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写车牌号。";
            echo json_encode($out);

            return false;
        }
        // factory_date
        $param["factory_date"] = $this->input->post("factory_date");
        if ($status && $this->lcommon->is_empty($param["factory_date"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写出厂日期。";
            echo json_encode($out);

            return false;
        }
        // engine_num
        $param["engine_num"] = $this->input->post("engine_num");
        if ($status && $this->lcommon->is_empty($param["engine_num"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写发动机号";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["engine_num"]) 
            && $this->lcommon->get_size($param["car_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写发动机号。";
            echo json_encode($out);

            return false;
        }
        // chassis_num
        $param["chassis_num"] = $this->input->post("chassis_num");
        if ($status && $this->lcommon->is_empty($param["chassis_num"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写车架号";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["chassis_num"]) 
            && $this->lcommon->get_size($param["chassis_num"]) > 16) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写手架号。";
            echo json_encode($out);

            return false;
        }
        // color
        $param["color"] = $this->input->post("color");
        if ($status && $this->lcommon->is_empty($param["color"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择车身颜色";
            echo json_encode($out);

            return false;
        }
        // displacement
        $param["displacement"] = $this->input->post("displacement");
        if ($status && $this->lcommon->is_empty($param["displacement"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写排量";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["displacement"]) 
            && !is_numeric($param["displacement"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写排量。";
            echo json_encode($out);

            return false;
        }
        // transmission
        $param["transmission"] = $this->input->post("transmission");
        if ($status && $this->lcommon->is_empty($param["transmission"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择变速箱种类";
            echo json_encode($out);

            return false;
        }
        // fuel
        $param["fuel"] = $this->input->post("fuel");
        if ($status && $this->lcommon->is_empty($param["fuel"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择燃料种类";
            echo json_encode($out);

            return false;
        }

        // sale_type
        $param["sale_type"] = $this->input->post("sale_type");
        if ($status && $this->lcommon->is_empty($param["sale_type"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择销售类型";
            echo json_encode($out);

            return false;
        }
        // market_price
        $param["market_price"] = $this->input->post("market_price");
        if ($status && $this->lcommon->is_empty($param["market_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写市场价";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["market_price"]) 
            && !is_numeric($param["market_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写市场价。";
            echo json_encode($out);

            return false;
        }
        // condition_score
        $param["condition_score"] = $this->input->post("condition_score");
        if ($status && $this->lcommon->is_empty($param["condition_score"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择车况级别评分";
            echo json_encode($out);

            return false;
        }
        // accident_level
        $param["accident_level"] = $this->input->post("accident_level");
        if ($status && $this->lcommon->is_empty($param["accident_level"])) {
            $out["status"] = 1;
            $out["msg"] = "请选择事故等级";
            echo json_encode($out);

            return false;
        }
        // sale_price
        $param["sale_price"] = $this->input->post("sale_price");
        if ($status && $this->lcommon->is_empty($param["sale_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请填写出让价";
            echo json_encode($out);

            return false;
        }
        if (!$this->lcommon->is_empty($param["sale_price"]) 
            && !is_numeric($param["sale_price"])) {
            $out["status"] = 1;
            $out["msg"] = "请正确填写出让价。";
            echo json_encode($out);

            return false;
        }

        $this->load->model("mcar");
        // images
        if ($status) {
            $car = $this->mcar->load($id);
            if (!$car->images) {
                $out["status"] = 1;
                $out["msg"] = "请上传车辆相关图片";
                echo json_encode($out);

                return false;
            }
        }

        if (!$ret = $this->mcar->save($param, $id)) {
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
/*{{{ upload */
    public function upload($id) {
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

        // File upload
        $config = array();
        $config["upload_path"] = FCPATH . "assets/upload";
        $config["allowed_types"] = "gif|jpg|png";
        $config["max_size"] = 2*1024*1024;
        $config["max_width"] = "1200";
        $config["max_height"] = "800";
        $config["remove_spaces"] = true;
        $config["encrypt_name"] = true;
        $this->load->library("upload", $config);
        if (!$this->upload->do_upload("upload")) {
            $out["status"] = 1;
            $out['msg'] = $this->upload->display_errors();
            echo json_encode($out);

            return false;
        }
        $upload = $this->upload->data();

        // Thumb
        $config = array();
        $config["source_image"] = $upload["full_path"];
        $config["create_thumb"] = true;
        $config["maintain_ratio"] = true;
        $config["width"] = 150;
        $config["height"] = 100;
        $config["master_dim"] = (($upload["image_width"]/$upload["image_height"]) > ($config["width"]/$config["height"])) ? "width" : "height";
        $config["thumb_marker"] = "_i";
        $this->load->library("image_lib", $config);
        $this->image_lib->resize();

        // Update in DB
        $this->load->model("mcar");
        $car = $this->mcar->load($id);
        $images = json_decode($car->images, true);
        if (!$images) {
            $images = array();
        }
        $image_name = $upload["raw_name"] . "_i" . $upload["file_ext"];
        $images[] = $image_name;
        $this->mcar->save(array("images"=>json_encode($images)), $id);

        $out["status"] = 0;
        $out['name'] = $image_name;
        $out['msg'] = "上传成功";
        echo json_encode($out);

        return true;
    }
/*}}}*/
/*{{{ delimage */
    public function delimage($id){
        $out = array();
        if (!$this->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }
        $name = $this->input->post("name");
        if (!$id || !is_numeric($id) || !$name || !strpos($name, '.')) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }

        // File delete
        $file = array();
        $file["upload_path"] = FCPATH . "assets/upload/";
        list($file["raw_name"], $file["file_ext"]) = explode('.', $name);
        if (substr($file["raw_name"], -2) == "_i") {
            $file["thumb_name"] = $file["raw_name"] . "." . $file["file_ext"];
            $file["file_name"] = substr($file["raw_name"], 0, -2) . "." . $file["file_ext"];
        } else {
            $file["thumb_name"] = $file["raw_name"] . "_i." . $file["file_ext"];
            $file["file_name"] = $file["raw_name"] . "." . $file["file_ext"];
        }

        $this->load->model("mcar");
        $car = $this->mcar->load($id);
        $images = json_decode($car->images, true);
        if (is_array($images)) {
            foreach ($images as $k => $v) {
                if ($v == $file["thumb_name"]) {
                    unlink($file["upload_path"] . $file["thumb_name"]);
                    unlink($file["upload_path"] . $file["file_name"]);
                    unset($images[$k]);
                    $this->mcar->save(array("images"=>json_encode($images)), $id);
                }
            }
        }

        $out["status"] = 0;
        $out['msg'] = "删除成功";
        echo json_encode($out);

        return true;
    }
/*}}}*/

/*{{{ auction */
    public function auction($start = 0, $func = "index") {
        if (!method_exists($this, "auction_".$func)) {
            $func = "index";
        }
        $func = "auction_" . $func;
        $this->$func($start);
    }
/*}}}*/
/*{{{ auction_index */
    public function auction_index($start = 0){
        $out = array();

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        if ($this->input->post()) {
            $search["phone"] = $this->input->post("phone");
            $search["status"] = $this->input->post("status");
            $this->lsession->set("car_auction_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("car_auction_search")) {
                $search = $tmp;
            }
        }
        // Only show auction data
        $search["status"] = "test";
        $out["search"] = $search;

        // The data of search
        $this->load->model("mcar");
        if($data = $this->mcar->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/car/auction";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_car_auction_index.html", $out);
    }
/*}}}*/
/*{{{ auction_edit */
    public function auction_edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/car/auction/");
        }
        // The data of search
        $this->load->model("mcar");
        if (!$car = $this->mcar->load($id)) {
            redirect("/admin/car/auction/");
        }
        if ($car->status != "test") {
            redirect("/admin/car/auction/");
        }

        $out = array();
        $out["car"] = $car;

        $param = array();
        $param["color"] = $this->lcommon->form_option("color"); 
        $param["transmission"] = $this->lcommon->form_option("transmission"); 
        $param["fuel"] = $this->lcommon->form_option("fuel"); 
        $param["sale_type"] = $this->lcommon->form_option("sale_type"); 
        $param["condition_score"] = $this->lcommon->form_option("condition_score"); 
        $param["accident_level"] = $this->lcommon->form_option("accident_level"); 

        $out["param"] = $param;

        $this->render("admin_car_auction_edit.html", $out);
    }
/*}}}*/
/*{{{ auction_save */
    public function auction_save($id = 0) {
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

        $param = array();
        // Rewrite test data
        $param["status"] = "prebook";
        // Auction when form.status is "complete"
        $status = null;
        if($this->input->post("status")) {
            $status = "auction";
            $param["status"] = "auction";
        }

        // sale_start_date
        if ($status) {
            $param["sale_start_date"] = $this->input->post("sale_start_date");
            if ($this->lcommon->is_empty($param["sale_start_date"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写销售开始时间";
                echo json_encode($out);

                return false;
            }
            // sale_end_date
            $param["sale_end_date"] = $this->input->post("sale_end_date");
            if ($this->lcommon->is_empty($param["sale_end_date"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写销售结束时间";
                echo json_encode($out);

                return false;
            }
            if ($param["sale_start_date"] > $param["sale_end_date"]) {
                $out["status"] = 1;
                $out["msg"] = "结束时间应大于开始时间";
                echo json_encode($out);

                return false;
            }
        }

        $this->load->model("mcar");
        if (!$ret = $this->mcar->save($param, $id)) {
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

/*{{{ deal */
    public function deal($start = 0, $func = "index") {
        if (!method_exists($this, "deal_".$func)) {
            $func = "index";
        }
        $func = "deal_" . $func;
        $this->$func($start);
    }
/*}}}*/
/*{{{ deal_index */
    public function deal_index($start = 0){
        $out = array();

        $this->config->load("pagination");
        // Search
        $search = array();
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page"); 
        if ($this->input->post()) {
            $search["phone"] = $this->input->post("phone");
            $this->lsession->set("car_deal_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("car_deal_search")) {
                $search = $tmp;
            }
        }
        // Only show auction data
        $search["status"] = "auction";
        $out["search"] = $search;

        // The data of search
        $this->load->model("mcar");
        if($data = $this->mcar->load_all($search)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/car/deal";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_car_deal_index.html", $out);
    }
/*}}}*/
/*{{{ deal_edit */
    public function deal_edit($id = 0) {
        if (!$id || !is_numeric($id)) {
            redirect("/admin/car/deal/");
        }
        // The data of search
        $this->load->model("mcar");
        if (!$car = $this->mcar->load($id)) {
            redirect("/admin/car/deal/");
        }
        if ($car->status != "auction") {
            redirect("/admin/car/deal/");
        }

        $out = array();
        $out["car"] = $car;

        $out["sale_status"] = sale_status($car->sale_start_date, $car->sale_end_date);
        if ($car->sale_type == "auction" && $out["sale_status"] == "sold") {
            $chart = array();
            // Generate to chart table
            // Get chart
            $this->load->model("mcarchart");
            $chart["auction"] = $this->mcarchart->loaddata($id, "auction");
            $out["chart"] = $chart;
        }

        $this->render("admin_car_deal_edit.html", $out);
    }
/*}}}*/
/*{{{ deal_save */
    public function deal_save($id = 0) {
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

        $param = array();
        // Rewrite test data
        $param["status"] = "prebook";
        // Auction when form.status is "complete"
        $status = null;
        if($this->input->post("status")) {
            $status = "auction";
            $param["status"] = "auction";
        }

        // sale_start_date
        if ($status) {
            $param["sale_start_date"] = $this->input->post("sale_start_date");
            if ($this->lcommon->is_empty($param["sale_start_date"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写销售开始时间";
                echo json_encode($out);

                return false;
            }
            // sale_end_date
            $param["sale_end_date"] = $this->input->post("sale_end_date");
            if ($this->lcommon->is_empty($param["sale_end_date"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写销售结束时间";
                echo json_encode($out);

                return false;
            }
            if ($param["sale_start_date"] > $param["sale_end_date"]) {
                $out["status"] = 1;
                $out["msg"] = "结束时间应大于开始时间";
                echo json_encode($out);

                return false;
            }
        }

        $this->load->model("mcar");
        if (!$ret = $this->mcar->save($param, $id)) {
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
