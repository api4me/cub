<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Article extends CI_Controller {

/*{{{ index */
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/article
	 *	- or -  
	 * 		http://example.com/index.php/article/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/article/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($tag = null, $title = null) {
        $this->load->library("twig");
        $out = array();
        $out["title"] = "资讯信息";
        if (!$tag || !$this->lcommon->option("tag", $tag)) {
            $tag = 'news';
        }
        // Article
        $this->load->model("marticle");
        $list = array();
        $list = $this->marticle->show_by_tag($tag);
        $out["list"] = $list;
        foreach ($out["list"] as $val) {
            if (is_numeric($title)) {
                if ($val->id == $title) {
                    $out["article"] = $val;
                    break;
                }
            } else {
                if ($val->title == urldecode($title)) {
                    $out["article"] = $val;
                    break;
                }
            }
        }
        if (!isset($out["article"])) {
            $out["article"] = $out["list"] ? $out["list"][0]: "";
        }
        $out["tag"] = $tag;

        $this->twig->display("article.html", $out);
	}
/*}}}*/
/*{{{ news */
	public function news($title = null) {
        $this->index("news", $title);
	}
/*}}}*/
/*{{{ activity */
	public function activity($title = null) {
        $this->index("activity", $title);
	}
/*}}}*/
/*{{{ state */
	public function state($id = null) {
        $this->load->library("twig");
        $out = array();
        $out["title"] = "资讯信息";

        $this->load->model("marticle");
        $row = $this->marticle->show($id);

        if ($row) {
            $out['article'] = $row;
        }

        $this->twig->display("article_state.html", $out);
	}
/*}}}*/
}
