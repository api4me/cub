<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vincent
 * Date: 14-1-16
 * Time: 下午22:41
 * To change this template use File | Settings | File Templates.
 */

class Purchase extends Cub_Controller {

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
            $search["name"] = $this->input->post("name");
            $search["phone"] = $this->input->post("phone");
            if ($this->input->post("brand") != "000") {
                $search["model"] = $this->input->post("brand") . $this->input->post("model");
            }
            $this->lsession->set("purchase_search", $search);
        } else {
            // Get search data from session
            if ($tmp = $this->lsession->get("purchase_search")) {
                $search = $tmp;
            }
        }
        $search["start"] = $start;
        $search["per_page"] = $this->config->item("per_page");
        $out["search"] = $search;

        // The data of search
        $this->load->model("mpurchase");
        if($data = $this->mpurchase->load_all($search)) {
            $out["purchase"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/admin/purchase/index";
            $out["pagination"] = $this->pagination->create_links();
        }

        $this->render("admin_purchase_index.html", $out);
    }
    /*}}}*/
}
