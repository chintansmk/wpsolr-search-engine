<?php

/* {# Shared Facets css template #}

<style>
    .wpsolr_facet_checkbox {
    {# Facet unchecked checkbox #} background: url({{ plugin_dir_url }}/images/1449252372_unchecked_checkbox.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/175466/checkbox_unchecked_icon#size=128 * /
        background-size: 14px 14px;
        margin-bottom: 4px;
        padding-left: 20px;
        cursor: pointer;
    }

    .wpsolr_facet_checkbox.checked {
    {# Facet checked checkbox #} background-image: url({{ plugin_dir_url }}/images/1449252361_checked_checkbox.png); /* https://www.iconfinder.com/icons/175220/checkbox_checked_icon#size=128 * /
    }

    .widget_wpsolr_widget_facets ul li {
        padding-left: 10px;
    }

    .widget_wpsolr_widget_facets ul span {
        text-align: left;
        padding-left: 10px;
        border-bottom: 1px solid #e4e4e4;
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        #font-size: 15px;
        font-weight: 400;
        #text-transform: uppercase;
    }

</style>
 */
class __TwigTemplate_be50dc3a2e8c963346124fe46864d55a83506f244f7b1ef61df50d3645555e03 extends Twig_Template
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
        // line 2
        echo "
<style>
    .wpsolr_facet_checkbox {
    ";
        // line 5
        echo " background: url(";
        echo twig_escape_filter($this->env, (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "html", null, true);
        echo "/images/1449252372_unchecked_checkbox.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/175466/checkbox_unchecked_icon#size=128 */
        background-size: 14px 14px;
        margin-bottom: 4px;
        padding-left: 20px;
        cursor: pointer;
    }

    .wpsolr_facet_checkbox.checked {
    ";
        // line 13
        echo " background-image: url(";
        echo twig_escape_filter($this->env, (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "html", null, true);
        echo "/images/1449252361_checked_checkbox.png); /* https://www.iconfinder.com/icons/175220/checkbox_checked_icon#size=128 */
    }

    .widget_wpsolr_widget_facets ul li {
        padding-left: 10px;
    }

    .widget_wpsolr_widget_facets ul span {
        text-align: left;
        padding-left: 10px;
        border-bottom: 1px solid #e4e4e4;
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        #font-size: 15px;
        font-weight: 400;
        #text-transform: uppercase;
    }

</style>
";
    }

    public function getTemplateName()
    {
        return "{# Shared Facets css template #}

<style>
    .wpsolr_facet_checkbox {
    {# Facet unchecked checkbox #} background: url({{ plugin_dir_url }}/images/1449252372_unchecked_checkbox.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/175466/checkbox_unchecked_icon#size=128 */
        background-size: 14px 14px;
        margin-bottom: 4px;
        padding-left: 20px;
        cursor: pointer;
    }

    .wpsolr_facet_checkbox.checked {
    {# Facet checked checkbox #} background-image: url({{ plugin_dir_url }}/images/1449252361_checked_checkbox.png); /* https://www.iconfinder.com/icons/175220/checkbox_checked_icon#size=128 */
    }

    .widget_wpsolr_widget_facets ul li {
        padding-left: 10px;
    }

    .widget_wpsolr_widget_facets ul span {
        text-align: left;
        padding-left: 10px;
        border-bottom: 1px solid #e4e4e4;
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        #font-size: 15px;
        font-weight: 400;
        #text-transform: uppercase;
    }

</style>
";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 13,  56 => 5,  51 => 2,);
    }
}
/* {# Shared Facets css template #}*/
/* */
/* <style>*/
/*     .wpsolr_facet_checkbox {*/
/*     {# Facet unchecked checkbox #} background: url({{ plugin_dir_url }}/images/1449252372_unchecked_checkbox.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/175466/checkbox_unchecked_icon#size=128 *//* */
/*         background-size: 14px 14px;*/
/*         margin-bottom: 4px;*/
/*         padding-left: 20px;*/
/*         cursor: pointer;*/
/*     }*/
/* */
/*     .wpsolr_facet_checkbox.checked {*/
/*     {# Facet checked checkbox #} background-image: url({{ plugin_dir_url }}/images/1449252361_checked_checkbox.png); /* https://www.iconfinder.com/icons/175220/checkbox_checked_icon#size=128 *//* */
/*     }*/
/* */
/*     .widget_wpsolr_widget_facets ul li {*/
/*         padding-left: 10px;*/
/*     }*/
/* */
/*     .widget_wpsolr_widget_facets ul span {*/
/*         text-align: left;*/
/*         padding-left: 10px;*/
/*         border-bottom: 1px solid #e4e4e4;*/
/*         display: block;*/
/*         margin-top: 15px;*/
/*         margin-bottom: 5px;*/
/*         #font-size: 15px;*/
/*         font-weight: 400;*/
/*         #text-transform: uppercase;*/
/*     }*/
/* */
/* </style>*/
/* */
