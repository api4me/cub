<?php

/* login.html */
class __TwigTemplate_3e50cbd994a0a47b7573c931c2b26c17 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("site.html");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "site.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = array())
    {
        // line 3
        echo "    <div id=\"login\">
        ";
        // line 4
        echo twig_escape_filter($this->env, validation_errors(), "html", null, true);
        echo "
        ";
        // line 5
        echo form_open("validate", "class=\"form-signin\"");
        echo "
            <h2 class=\"form-signin-heading\">请登录</h2>
            <p id=\"msg\"></p>
            <input type=\"text\" class=\"input-block-level\" name=\"username\" placeholder=\"用户名\">
            <input type=\"password\" class=\"input-block-level\" name=\"pwd\" placeholder=\"密码\">
            <p class=\"help-block\" id=\"p-captcha\">
                <img src=\"";
        // line 11
        echo twig_escape_filter($this->env, site_url(), "html", null, true);
        echo "/captcha/\" id=\"image-captcha\"/><span class=\"btn btn-link\" id=\"span-captcha\" >看不清，换一张</span>
            </p>
            <input type=\"text\" class=\"input-block-level\" name=\"captcha\" placeholder=\"验证码\">
            <label class=\"checkbox\">
                <input type=\"checkbox\" name=\"remember\" value=\"remember-me\"> 记住我
            </label>
            <button class=\"btn btn-large btn-primary\" type=\"submit\">登录</button>
            <input type=\"hidden\" name=\"baseurl\" value=\"";
        // line 18
        echo twig_escape_filter($this->env, site_url(), "html", null, true);
        echo "\" />
        </form>
    </div>
";
    }

    public function getTemplateName()
    {
        return "login.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 18,  47 => 11,  38 => 5,  34 => 4,  31 => 3,  28 => 2,);
    }
}
