<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Homepage extends CI_Controller {

    public function agency($agency_id) {

        if (!is_numeric($agency_id)) {
            redirect("/");
        }

        $this->load->model("muser");
        $agency = $this->muser->load($agency_id);

        $out = array();
        $out["title"] = "诚信经销商-" . $agency->agency_name;

        $out["agency"] = $agency;

        $this->config->load("pagination");
        // Search
        $car_search = array();
        $car_search["start"] = 0;
        $car_search["per_page"] = $this->config->item("per_page");
        $out["search"] = $car_search;
        $car_search["user_id"] = $agency_id;

        // The data of search
        $this->load->model("magencycar");
        if($data = $this->magencycar->load_all($car_search, true)) {
            $out["cars"] = $data["data"];

            // Pagaination
            $this->load->library("pagination");
            $this->pagination->uri_segment = 4;
            $this->pagination->total_rows = $data["num"];
            $this->pagination->base_url = site_url() . "/homepage/agency/" . $agency_id;
            $out["pagination"] = $this->pagination->create_links();
        }

        $promotion_search["start"] = 0;
        $promotion_search["per_page"] = 4;
        $promotion_search["user_id"] = $agency_id;

        $this->load->model("magencyprom");
        if ($data = $this->magencyprom->load_all($promotion_search)) {
            $out["promotions"] = $data["data"];
        }

        $this->load->library("twig");
        $this->twig->display("homepage_agency.html", $out);
    }

    public function agencycar($id) {
        if (!is_numeric($id)) {
            redirect("/");
        }

        $this->load->model("magencycar");
        $car = $this->magencycar->load($id);

        if (!$car) {
            redirect("/");
        }

        $out = array();
        $out["title"] = "车辆详情";
        $out["car"] = $car;

        $this->load->model("muser");
        $agency = $this->muser->load($car->user_id);

        $out["agency"] = $agency;

        $this->load->library("twig");
        $this->twig->display("homepage_agencycar.html", $out);
    }


    public function promotion($id) {
        if (!is_numeric($id)) {
            redirect("/");
        }

        $this->load->model("magencyprom");
        $promotion = $this->magencyprom->load($id);

        if (!$promotion) {
            redirect("/");
        }

        $out = array();

        $out["promotion"] = $promotion;

        $this->load->model("muser");
        $agency = $this->muser->load($promotion->user_id);

        $out["agency"] = $agency;

        $this->load->library("twig");
        $this->twig->display("homepage_promotion.html", $out);
    }

}
