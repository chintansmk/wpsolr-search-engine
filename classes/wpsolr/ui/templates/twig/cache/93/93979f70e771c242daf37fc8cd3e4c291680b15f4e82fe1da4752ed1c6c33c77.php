<?php

/* generic/facets/range/html.twig */
class __TwigTemplate_0e2d919f052e649fe6144088c8fe2b6978898a7a895b79046e8b09bc762ac5da extends Twig_Template
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
";
        // line 6
        $this->loadTemplate((isset($context["template_css"]) ? $context["template_css"] : null), "generic/facets/range/html.twig", 6)->display(array_merge($context, array("plugin_dir_url" => (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null))));
        // line 7
        $this->loadTemplate((isset($context["template_js"]) ? $context["template_js"] : null), "generic/facets/range/html.twig", 7)->display(array_merge($context, array("plugin_dir_url" => (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "facet_selector_class" => (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null))));
        // line 8
        echo "

<ul class=\"wpsolr_any_facet_class ";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\">

    <li>
        ??????
        <span>";
        // line 14
        echo twig_escape_filter($this->env, sprintf((isset($context["facets_title"]) ? $context["facets_title"] : null), $this->getAttribute((isset($context["facet"]) ? $context["facet"] : null), "name", array())), "html", null, true);
        echo "</span> ";
        // line 15
        echo "
        <ul>

            ";
        // line 18
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["items"]) ? $context["items"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            echo " ";
            // line 19
            echo "
                ";
            // line 20
            $context["data_wpsolr_facet"] = array("facet_id" => $this->getAttribute((isset($context["facet"]) ? $context["facet"] : null), "id", array()), "facet_value" => $this->getAttribute($context["item"], "name", array()));
            // line 21
            echo "
                <li>

                    <a class=\"";
            // line 24
            echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, (($this->getAttribute($context["item"], "selected", array())) ? ((isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null)) : ("")), "html", null, true);
            echo "\"
                       data-wpsolr-facet=\"";
            // line 25
            echo twig_escape_filter($this->env, twig_jsonencode_filter((isset($context["data_wpsolr_facet"]) ? $context["data_wpsolr_facet"] : null)), "html", null, true);
            echo "\">
                        ";
            // line 26
            echo twig_escape_filter($this->env, twig_capitalize_string_filter($this->env, sprintf((isset($context["facets_element"]) ? $context["facets_element"] : null), (($this->getAttribute($context["item"], "name", array()) . "-") . ($this->getAttribute($context["item"], "name", array()) + $this->getAttribute($this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "range", array()), "gap", array()))), $this->getAttribute($context["item"], "count", array()))), "html", null, true);
            echo "
                    </a> ";
            // line 28
            echo "
                </li>

            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo " ";
        // line 32
        echo "
        </ul>

    </li>

</ul>

";
    }

    public function getTemplateName()
    {
        return "generic/facets/range/html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 32,  90 => 31,  81 => 28,  77 => 26,  73 => 25,  67 => 24,  62 => 21,  60 => 20,  57 => 19,  52 => 18,  47 => 15,  44 => 14,  37 => 10,  33 => 8,  31 => 7,  29 => 6,  26 => 5,  24 => 4,  22 => 3,  19 => 2,);
    }
}
/* {# Display a facet elements as checkboxes #}*/
/* */
/* {% set facet_selector_class = facet.id %}*/
/* {% set facet_selected_class = "checked" %}*/
/* */
/* {% include template_css with {'plugin_dir_url': plugin_dir_url} %}*/
/* {% include template_js with {'plugin_dir_url': plugin_dir_url, 'facet_selector_class': facet_selector_class} %}*/
/* */
/* */
/* <ul class="wpsolr_any_facet_class {{ facet_selector_class }}">*/
/* */
/*     <li>*/
/*         ??????*/
/*         <span>{{ facets_title|format(facet.name) }}</span> {# Facet name #}*/
/* */
/*         <ul>*/
/* */
/*             {% for item in items %} {# Loop on facet items #}*/
/* */
/*                 {% set data_wpsolr_facet = {'facet_id': facet.id, 'facet_value': item.name} %}*/
/* */
/*                 <li>*/
/* */
/*                     <a class="{{ facet_selector_class }} {{ item.selected ? facet_selected_class : "" }}"*/
/*                        data-wpsolr-facet="{{ data_wpsolr_facet|json_encode }}">*/
/*                         {{ facets_element|format( (item.name ~ "-" ~  (item.name + definition.range.gap)), item.count )|capitalize }}*/
/*                     </a> {# Current facet item #}*/
/* */
/*                 </li>*/
/* */
/*             {% endfor %} {# Loop on facet items #}*/
/* */
/*         </ul>*/
/* */
/*     </li>*/
/* */
/* </ul>*/
/* */
/* */