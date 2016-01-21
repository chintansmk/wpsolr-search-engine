<?php

/* generic/facets/radiobox/js.twig */
class __TwigTemplate_0a2fd20d39714720a945aa282cd68488dc157c9b23da046dde38b3f9a79808e5 extends Twig_Template
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

        jQuery(\".wpsolr_any_facet_class li .";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").on(\"click\", function (event) {


            jQuery(\".wpsolr_any_facet_class li .";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").removeClass(\"";
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); // Deactivate all facets
            jQuery(this).addClass(\"";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); // Activate clicked facet

            var facet = jQuery(this).data(\"wpsolr-facet-item-value\"); // facet value stored in html5 data
            //wpsolr_facets.removeFacetValue(facet); // remove all other values
            wpsolr_facets.addFacetValue(facet); // add facet to facets
            wpsolr_facets.create_url();
        });
    });
</script>
";
    }

    public function getTemplateName()
    {
        return "generic/facets/radiobox/js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 8,  30 => 7,  24 => 4,  19 => 1,);
    }
}
/* <script>*/
/*     jQuery(document).ready(function () {*/
/* */
/*         jQuery(".wpsolr_any_facet_class li .{{ facet_selector_class }}").on("click", function (event) {*/
/* */
/* */
/*             jQuery(".wpsolr_any_facet_class li .{{ facet_selector_class }}").removeClass("{{ facet_selected_class }}"); // Deactivate all facets*/
/*             jQuery(this).addClass("{{ facet_selected_class }}"); // Activate clicked facet*/
/* */
/*             var facet = jQuery(this).data("wpsolr-facet-item-value"); // facet value stored in html5 data*/
/*             //wpsolr_facets.removeFacetValue(facet); // remove all other values*/
/*             wpsolr_facets.addFacetValue(facet); // add facet to facets*/
/*             wpsolr_facets.create_url();*/
/*         });*/
/*     });*/
/* </script>*/
/* */
