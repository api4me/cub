<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

/*{{{ index */
    public function index($url = null) {
        $this->load->helper(array("form"));
        $this->load->library("twig");

        $out = array();

        if ($this->lsession->get("user")) {
            redirect();
            return false;
        }

        $out["title"] = "登录";
        if ($this->lsession->get('login_err_num')) {
            $out['has_captcha'] = true;
        }
        $this->twig->display("login.html", $out);
    }
/*}}}*/
/*{{{ validate */
    public function validate(){
        $param = array();
        $param["username"] = $this->input->post("username");
        $param["pwd"] = md5($this->input->post("pwd"));
        $param["captcha"] = $this->input->post("captcha");
        $param["remember"] = $this->input->post("remember");

        $out = array();
        if ($this->lsession->get("login_err_num")) {
            if (empty($param["captcha"])) {
                $out["status"] = 1;
                $out["msg"] = "请填写验证码";
                echo json_encode($out);

                return false;
            } else if (strtolower($this->lsession->get("captcha")) != strtolower($param["captcha"])) {
                $out["status"] = 1;
                $out["msg"] = "验证码有误";
                echo json_encode($out);

                return false;
            }
        }

        $this->load->model("muser");
        if (!$data = $this->muser->login($param)) {
            $out["status"] = 1;
            $out["msg"] = "用户名或密码不正确";
            echo json_encode($out);

            $err_num = $this->lsession->get('login_err_num');
            $err_num = $err_num? $err_num + 1 : 1;
            $this->lsession->set('login_err_num', $err_num);

            return false;
        }
        $this->load->helper("common");
        $this->lsession->set("user", $data);
        $out["status"] = 0;
        $out["msg"] = "登录成功";
        echo json_encode($out);

        // Del captcha for login success
        $this->lsession->del('login_err_num');

        return true;
    }
/*}}}*/
    
}
