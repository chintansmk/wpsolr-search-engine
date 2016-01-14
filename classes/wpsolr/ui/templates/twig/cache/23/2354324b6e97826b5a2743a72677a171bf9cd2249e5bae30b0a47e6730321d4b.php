<?php

/* generic/facets/css.twig */
class __TwigTemplate_f09df9f0dfc56dfd529fc56bce3c93856a9cccf11596ac1508ba5a6b1f3af218 extends Twig_Template
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

    ul.wpsolr_any_facet_class li {
        padding-left: 10px;
    }

    ul.wpsolr_any_facet_class span {
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
        return "generic/facets/css.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 2,);
    }
}
/* {# Common style used by all facets. Each facet defines also it's own style in it's onwn template. #}*/
/* */
/* <style>*/
/* */
/*     ul.wpsolr_any_facet_class li {*/
/*         padding-left: 10px;*/
/*     }*/
/* */
/*     ul.wpsolr_any_facet_class span {*/
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
