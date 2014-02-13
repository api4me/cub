<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-1-21
 * Time: 下午19:17
 * To change this template use File | Settings | File Templates.
 */

class Carousel extends Cub_Controller {

    private $CI;

    public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
    }

    public function index() {
        $out = array();
        $out["images"] = array("carousel-01.jpg", "carousel-02.jpg", "carousel-03.jpg", "carousel-04.jpg");
        $this->render("admin_carousel_index.html", $out);
    }

    public function upload($name) {
        $out = array();

        // File upload
        $config = array();
        $config["upload_path"] = FCPATH . "assets/upload/img/carousel/";
        $config["file_name"] = $name;
        $config["allowed_types"] = "jpg";
        $config["max_size"] = 2*1024*1024;
        $config["max_width"] = "1200";
        $config["max_height"] = "800";

        $this->CI->load->library("upload", $config);
        $this->CI->upload->overwrite = true;
        if (!$this->CI->upload->do_upload("upload")) {
            $out["status"] = 1;
            $out['msg'] = $this->CI->upload->display_errors();
            echo json_encode($out);

            return false;
        }

        $out["status"] = 0;
        $out['name'] = $name;
        $out['msg'] = "上传成功";
        echo json_encode($out);

        return true;
    }
}