<?php

/* generic/facets/checkbox/js.twig */
class __TwigTemplate_3927621baad880af4280492f34324383d65c00ca6c4731a77acce9e5341c4626 extends Twig_Template
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
        echo "<script>
    jQuery(document).ready(function () {

        jQuery(\".";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").on(\"click\", function (event) {

            alert(\".";
        // line 6
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\");

            jQuery(this).toggleClass(\"";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); ";
        // line 9
        echo "
            ";
        // line 11
        echo "            console.log(jQuery(this).data(\"wpsolr-facet\"));
        });
    });
</script>";
    }

    public function getTemplateName()
    {
        return "generic/facets/checkbox/js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 11,  37 => 9,  34 => 8,  29 => 6,  24 => 4,  19 => 1,);
    }
}
/* <script>*/
/*     jQuery(document).ready(function () {*/
/* */
/*         jQuery(".{{ facet_selector_class }}").on("click", function (event) {*/
/* */
/*             alert(".{{ facet_selector_class }}");*/
/* */
/*             jQuery(this).toggleClass("{{ facet_selected_class }}"); {# check/uncheck clicked facet #}*/
/* */
/*             {# Update url #}*/
/*             console.log(jQuery(this).data("wpsolr-facet"));*/
/*         });*/
/*     });*/
/* </script>*/
