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

        jQuery(\".";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").on(\"click\", function (event) {

            jQuery(\".";
        // line 6
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").removeClass(\"";
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); // Deactivate all other facets

            jQuery(this).addClass(\"";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\");

            ";
        // line 11
        echo "            console.log(jQuery(this).data(\"wpsolr-facet\"));
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
        return array (  41 => 11,  36 => 8,  29 => 6,  24 => 4,  19 => 1,);
    }
}
/* <script>*/
/*     jQuery(document).ready(function () {*/
/* */
/*         jQuery(".{{ facet_selector_class }}").on("click", function (event) {*/
/* */
/*             jQuery(".{{ facet_selector_class }}").removeClass("{{ facet_selected_class }}"); // Deactivate all other facets*/
/* */
/*             jQuery(this).addClass("{{ facet_selected_class }}");*/
/* */
/*             {# Update url #}*/
/*             console.log(jQuery(this).data("wpsolr-facet"));*/
/*         });*/
/*     });*/
/* </script>*/
/* */
