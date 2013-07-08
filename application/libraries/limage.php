<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename LImage.php
* @touch date Wednesday, May 15, 2013 AM08:56:26 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/

class LImage {

/*{{{ variable */
    private $CI;
/*}}}*/
/*{{{ __construct */
    public function __construct() {
        $this->CI =& get_instance();
    }
/*}}}*/
/*{{{ upload */
    public function upload($id) {
        $out = array();
        // Fix IE issue, fileupload jquery plug is not send "HTTP_X_REQUESTED_WITH"
        /*
        if (!$this->CI->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }
        */
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
        $this->CI->load->library("upload", $config);
        if (!$this->CI->upload->do_upload("upload")) {
            $out["status"] = 1;
            $out['msg'] = $this->CI->upload->display_errors();
            echo json_encode($out);

            return false;
        }
        $upload = $this->CI->upload->data();

        // Thumb
        $config = array();
        $config["source_image"] = $upload["full_path"];
        $config["create_thumb"] = true;
        $config["maintain_ratio"] = true;
        $config["width"] = 150;
        $config["height"] = 100;
        $config["master_dim"] = (($upload["image_width"]/$upload["image_height"]) > ($config["width"]/$config["height"])) ? "width" : "height";
        $config["thumb_marker"] = "_i";
        $this->CI->load->library("image_lib", $config);
        $this->CI->image_lib->resize();

        // Update in DB
        $this->CI->load->model("mcar");
        $car = $this->CI->mcar->load($id);
        $images = json_decode($car->images, true);
        if (!$images) {
            $images = array();
        }
        $image_name = $upload["raw_name"] . "_i" . $upload["file_ext"];
        $images[] = $image_name;
        $this->CI->mcar->save(array("images"=>json_encode($images)), $id);

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
        if (!$this->CI->input->is_ajax_request()) {
            $out["status"] = 1;
            $out["msg"] = "系统忙，请稍后...";
            echo json_encode($out);

            return false;
        }
        $name = $this->CI->input->post("name");
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

        $this->CI->load->model("mcar");
        $car = $this->CI->mcar->load($id);
        $images = json_decode($car->images, true);
        if (is_array($images)) {
            foreach ($images as $k => $v) {
                if ($v == $file["thumb_name"]) {
                    unlink($file["upload_path"] . $file["thumb_name"]);
                    unlink($file["upload_path"] . $file["file_name"]);
                    unset($images[$k]);
                    $this->CI->mcar->save(array("images"=>json_encode($images)), $id);
                }
            }
        }

        $out["status"] = 0;
        $out['msg'] = "删除成功";
        echo json_encode($out);

        return true;
    }
/*}}}*/

}
