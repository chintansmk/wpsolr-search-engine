<?php

/* generic/facet_checkbox.twig */
class __TwigTemplate_0326175ce6b6f53dbf02b2a42305714c69310dedf4efb17ec7b6b9e2a3d00131 extends Twig_Template
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
";
        // line 3
        $context["facet_selector_class"] = $this->getAttribute((isset($context["facet"]) ? $context["facet"] : null), "id", array());
        // line 4
        $context["facet_selected_class"] = "checked";
        // line 5
        echo "
<style>
    a.";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo " {
    ";
        // line 8
        echo " background: url(";
        echo twig_escape_filter($this->env, (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "html", null, true);
        echo "/images/1449252372_unchecked_checkbox.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/175466/checkbox_unchecked_icon#size=128 */
        background-size: 14px 14px;
        margin-bottom: 4px;
        padding-left: 20px;
        cursor: pointer;
    }

    a.";
        // line 15
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo ".checked {
    ";
        // line 16
        echo " background-image: url(";
        echo twig_escape_filter($this->env, (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "html", null, true);
        echo "/images/1449252361_checked_checkbox.png); /* https://www.iconfinder.com/icons/175220/checkbox_checked_icon#size=128 */
    }

</style>

<script>
    jQuery(document).ready(function () {

        jQuery(\".";
        // line 24
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").on(\"click\", function (event) {

            jQuery(this).toggleClass(\"";
        // line 26
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); ";
        // line 27
        echo "
            ";
        // line 29
        echo "            console.log(jQuery(this).data(\"wpsolr-facet\"));
        });
    });
</script>

<ul class=\"wpsolr_any_facet_class ";
        // line 34
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\">
    <span>";
        // line 35
        echo twig_escape_filter($this->env, sprintf((isset($context["facets_title"]) ? $context["facets_title"] : null), $this->getAttribute((isset($context["facet"]) ? $context["facet"] : null), "name", array())), "html", null, true);
        echo "</span> ";
        // line 36
        echo "
    ";
        // line 37
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["items"]) ? $context["items"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            echo " ";
            // line 38
            echo "
        ";
            // line 39
            $context["data_wpsolr_facet"] = array("facet_id" => $this->getAttribute((isset($context["facet"]) ? $context["facet"] : null), "id", array()), "facet_value" => $this->getAttribute($context["item"], "name", array()));
            // line 40
            echo "
        <li>

            <a class=\"";
            // line 43
            echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, (($this->getAttribute($context["item"], "selected", array())) ? ((isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null)) : ("")), "html", null, true);
            echo "\"
               data-wpsolr-facet=\"";
            // line 44
            echo twig_escape_filter($this->env, twig_jsonencode_filter((isset($context["data_wpsolr_facet"]) ? $context["data_wpsolr_facet"] : null)), "html", null, true);
            echo "\">
                ";
            // line 45
            echo twig_escape_filter($this->env, twig_capitalize_string_filter($this->env, sprintf((isset($context["facets_element"]) ? $context["facets_element"] : null), $this->getAttribute($context["item"], "name", array()), $this->getAttribute($context["item"], "count", array()))), "html", null, true);
            echo "
            </a> ";
            // line 47
            echo "
        </li>

    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 50
        echo " ";
        // line 51
        echo "
</ul>

";
    }

    public function getTemplateName()
    {
        return "generic/facet_checkbox.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  129 => 51,  127 => 50,  118 => 47,  114 => 45,  110 => 44,  104 => 43,  99 => 40,  97 => 39,  94 => 38,  89 => 37,  86 => 36,  83 => 35,  79 => 34,  72 => 29,  69 => 27,  66 => 26,  61 => 24,  49 => 16,  45 => 15,  34 => 8,  30 => 7,  26 => 5,  24 => 4,  22 => 3,  19 => 2,);
    }
}
/* {# Display a facet elements as checkboxes #}*/
/* */
/* {% set facet_selector_class = facet.id %}*/
/* {% set facet_selected_class = "checked" %}*/
/* */
/* <style>*/
/*     a.{{ facet_selector_class }} {*/
/*     {# Facet unchecked checkbox #} background: url({{ plugin_dir_url }}/images/1449252372_unchecked_checkbox.png) 0 50% no-repeat; /* https://www.iconfinder.com/icons/175466/checkbox_unchecked_icon#size=128 *//* */
/*         background-size: 14px 14px;*/
/*         margin-bottom: 4px;*/
/*         padding-left: 20px;*/
/*         cursor: pointer;*/
/*     }*/
/* */
/*     a.{{ facet_selector_class }}.checked {*/
/*     {# Facet checked checkbox #} background-image: url({{ plugin_dir_url }}/images/1449252361_checked_checkbox.png); /* https://www.iconfinder.com/icons/175220/checkbox_checked_icon#size=128 *//* */
/*     }*/
/* */
/* </style>*/
/* */
/* <script>*/
/*     jQuery(document).ready(function () {*/
/* */
/*         jQuery(".{{ facet_selector_class }}").on("click", function (event) {*/
/* */
/*             jQuery(this).toggleClass("{{ facet_selected_class }}"); {# check/uncheck clicked facet #}*/
/* */
/*             {# Update url #}*/
/*             console.log(jQuery(this).data("wpsolr-facet"));*/
/*         });*/
/*     });*/
/* </script>*/
/* */
/* <ul class="wpsolr_any_facet_class {{ facet_selector_class }}">*/
/*     <span>{{ facets_title|format(facet.name) }}</span> {# Facet name #}*/
/* */
/*     {% for item in items %} {# Loop on facet items #}*/
/* */
/*         {% set data_wpsolr_facet = {'facet_id': facet.id, 'facet_value': item.name} %}*/
/* */
/*         <li>*/
/* */
/*             <a class="{{ facet_selector_class }} {{ item.selected ? facet_selected_class : "" }}"*/
/*                data-wpsolr-facet="{{ data_wpsolr_facet|json_encode }}">*/
/*                 {{ facets_element|format( item.name, item.count )|capitalize }}*/
/*             </a> {# Current facet item #}*/
/* */
/*         </li>*/
/* */
/*     {% endfor %} {# Loop on facet items #}*/
/* */
/* </ul>*/
/* */
/* */
