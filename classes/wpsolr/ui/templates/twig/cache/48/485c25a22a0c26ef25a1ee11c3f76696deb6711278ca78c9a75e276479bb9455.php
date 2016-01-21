<?php

/* generic/facets/range/css.twig */
class __TwigTemplate_4424131a3bfd9da5e6959f4907221f93ef8fca143ecd3fb9b42184c1a444b3b9 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<style>
    a.";
        // line 2
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo " {
    ";
        // line 3
        echo " background: url(";
        echo twig_escape_filter($this->env, (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "html", null, true);
        echo "/images/1452781040_check-circle-outline-blank.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/326565/blank_check_circle_outline_icon#size=128 */
        background-size: 14px 14px;
        margin-bottom: 4px;
        padding-left: 20px;
        cursor: pointer;
    }

    a.";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo ".checked {
    ";
        // line 11
        echo " background-image: url(";
        echo twig_escape_filter($this->env, (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "html", null, true);
        echo "/images/1452781456_radio-button-on.png); /* https://www.iconfinder.com/icons/326677/on_radio_icon#size=128 */
    }

</style>
";
    }

    public function getTemplateName()
    {
        return "generic/facets/range/css.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 11,  37 => 10,  26 => 3,  22 => 2,  19 => 1,);
    }
}
/* <style>*/
/*     a.{{ facet_selector_class }} {*/
/*     {# Facet unchecked checkbox #} background: url({{ plugin_dir_url }}/images/1452781040_check-circle-outline-blank.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/326565/blank_check_circle_outline_icon#size=128 *//* */
/*         background-size: 14px 14px;*/
/*         margin-bottom: 4px;*/
/*         padding-left: 20px;*/
/*         cursor: pointer;*/
/*     }*/
/* */
/*     a.{{ facet_selector_class }}.checked {*/
/*     {# Facet checked checkbox #} background-image: url({{ plugin_dir_url }}/images/1452781456_radio-button-on.png); /* https://www.iconfinder.com/icons/326677/on_radio_icon#size=128 *//* */
/*     }*/
/* */
/* </style>*/
/* */
