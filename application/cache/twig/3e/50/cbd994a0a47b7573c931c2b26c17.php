<?php

/* login.html */
class __TwigTemplate_3e50cbd994a0a47b7573c931c2b26c17 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base_admin.html");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base_admin.html";
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
        <form class=\"form-signin\">
            <h2 class=\"form-signin-heading\">请登录</h2>
            <input type=\"text\" class=\"input-block-level\" placeholder=\"用户名\">
            <input type=\"password\" class=\"input-block-level\" placeholder=\"密码\">
            <img src=\"";
        // line 8
        echo twig_escape_filter($this->env, site_url(), "html", null, true);
        echo "/captcha/\" />
            <input type=\"text\" class=\"input-block-level\" placeholder=\"验证码\">
            <label class=\"checkbox\">
                <input type=\"checkbox\" value=\"remember-me\"> 记住我
            </label>
            <button class=\"btn btn-large btn-primary\" type=\"submit\">登录</button>
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
        return array (  38 => 8,  31 => 3,  28 => 2,);
    }
}
