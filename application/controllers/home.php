<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

/*{{{ index */
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {
        $this->load->helper(array("form"));
        $this->load->library("twig");
        $out = array();
        $out["title"] = "首页";

        // 东风风神
        $out["default"]["model"] = "127000";
        // 江苏 南京
        $out["default"]["area"] = "320101";
        // Auction
        $this->load->model("mcar");
        $auction = $this->mcar->load_for_auction(12);
        $out["auction"] = $auction;
        // Consign
        $consign = $this->mcar->load_for_consign(12);
        $out["consign"] = $consign;
        // Article
        $this->load->model("marticle");
        $article = array();
        $article["activity"] = $this->marticle->show_by_tag("activity");
        $article["news"] = $this->marticle->show_by_tag("news");
        $out["article"] = $article;

        $this->twig->display("home_index.html", $out);
	}
/*}}}*/

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
