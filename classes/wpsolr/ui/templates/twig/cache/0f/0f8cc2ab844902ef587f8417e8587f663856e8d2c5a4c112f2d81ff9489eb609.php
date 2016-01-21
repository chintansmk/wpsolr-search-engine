<?php

/* generic/facets/range/js.twig */
class __TwigTemplate_324ec559ab400df5bcbf5fd8c1df51a7891e700204d999b2569f62861ffbd03e extends Twig_Template
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

            jQuery(\".";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").removeClass(\"";
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); // Deactivate all other facets

            jQuery(this).addClass(\"";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\");

            ";
        // line 13
        echo "            console.log(jQuery(this).data(\"wpsolr-facet\"));
        });
    });
</script>
";
    }

    public function getTemplateName()
    {
        return "generic/facets/range/js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 13,  41 => 10,  34 => 8,  29 => 6,  24 => 4,  19 => 1,);
    }
}
/* <script>*/
/*     jQuery(document).ready(function () {*/
/* */
/*         jQuery(".{{ facet_selector_class }}").on("click", function (event) {*/
/* */
/*             alert(".{{ facet_selector_class }}");*/
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
