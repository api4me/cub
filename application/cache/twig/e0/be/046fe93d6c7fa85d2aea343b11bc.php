<?php

/* base_admin.html */
class __TwigTemplate_e0be046fe93d6c7fa85d2aea343b11bc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.html");

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_head($context, array $blocks = array())
    {
        // line 3
        echo "    <meta charset=\"utf-8\">
    <title>管理平台 - 我爱我车</title>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta name=\"description\" content=\"\">
    <meta name=\"author\" content=\"\">
    <!-- Le styles -->
    <link href=\"";
        // line 9
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "assets/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link href=\"";
        // line 10
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "assets/css/admin.css\" rel=\"stylesheet\">
    <!--[if lt IE 9]>
        <script src=\"";
        // line 12
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "assets/js/html5shiv.js\"></script>
    <![endif]-->
    <script src=\"";
        // line 14
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "assets/js/jquery.min.js\"></script>
    <script src=\"";
        // line 15
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "assets/bootstrap/js/bootstrap.min.js\"></script>
    <script src=\"";
        // line 16
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "assets/js/admin.min.js\"></script>
";
    }

    // line 18
    public function block_footer($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "base_admin.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 18,  62 => 16,  58 => 15,  54 => 14,  49 => 12,  44 => 10,  40 => 9,  32 => 3,  29 => 2,  39 => 9,  31 => 3,  28 => 2,);
    }
}
