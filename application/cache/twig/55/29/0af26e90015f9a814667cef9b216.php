<?php

/* base.html */
class __TwigTemplate_55290af26e90015f9a814667cef9b216 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
    ";
        // line 4
        $this->displayBlock('head', $context, $blocks);
        // line 7
        echo "    </head>
    <body>
        <div id=\"content\">";
        // line 9
        $this->displayBlock('content', $context, $blocks);
        echo "</div>
        <div id=\"footer\">
        ";
        // line 11
        $this->displayBlock('footer', $context, $blocks);
        // line 14
        echo "        </div>
    </body>
</html>
";
    }

    // line 4
    public function block_head($context, array $blocks = array())
    {
        // line 5
        echo "        <title>";
        $this->displayBlock('title', $context, $blocks);
        echo " - 我爱我车</title>
    ";
    }

    public function block_title($context, array $blocks = array())
    {
    }

    // line 9
    public function block_content($context, array $blocks = array())
    {
    }

    // line 11
    public function block_footer($context, array $blocks = array())
    {
        // line 12
        echo "        &copy; Copyright 2013 by <a href=\"";
        echo twig_escape_filter($this->env, base_url(), "html", null, true);
        echo "\">我爱我车</a>.
        ";
    }

    public function getTemplateName()
    {
        return "base.html";
    }

    public function getDebugInfo()
    {
        return array (  70 => 12,  67 => 11,  62 => 9,  51 => 5,  48 => 4,  41 => 14,  39 => 11,  34 => 9,  30 => 7,  28 => 4,  23 => 1,);
    }
}
