<?php

/* generic/facets_html.twig */
class __TwigTemplate_0052e98f554a5ccd504d141a71e0a63a4ffb3bb49c516bde869e75c175397ebb extends Twig_Template
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
        $this->loadTemplate((isset($context["template_css"]) ? $context["template_css"] : null), "generic/facets_html.twig", 3)->display($context);
        echo " ";
        // line 4
        echo "
";
        // line 5
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "before_widget", array());
        echo " ";
        // line 6
        echo "
";
        // line 7
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "before_title", array());
        echo " ";
        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, (isset($context["facets_header"]) ? $context["facets_header"] : null), "html", null, true);
        echo " ";
        // line 9
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "after_title", array());
        echo " ";
        // line 10
        echo "
<div id=\"res_facets\">

    <ul>
        <li>
            <a id=\"wpsolr_remove_facets\" ";
        // line 16
        echo "               class=\"select_opt wpsolr_facet_checkbox ";
        echo (( !$this->getAttribute((isset($context["facets"]) ? $context["facets"] : null), "has_facet_elements_selected", array())) ? ("checked") : (""));
        echo "\" ";
        // line 17
        echo "               href=\"#wpsolr_facets\"
            >
                ";
        // line 19
        echo twig_escape_filter($this->env, (isset($context["facets_element_all_results"]) ? $context["facets_element_all_results"] : null), "html", null, true);
        echo "
            </a>
        </li>
    </ul>

    ";
        // line 24
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["facets"]) ? $context["facets"] : null), "facets", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["facet"]) {
            echo " ";
            // line 25
            echo "
        ";
            // line 26
            $this->loadTemplate($this->getAttribute($context["facet"], "template_html", array()), "generic/facets_html.twig", 26)->display(array_merge($context, array("facets_element" => (isset($context["facets_element"]) ? $context["facets_element"] : null), "facets_title" => (isset($context["facets_title"]) ? $context["facets_title"] : null), "items" => $this->getAttribute($context["facet"], "items", array()), "plugin_dir_url" => (isset($context["plugin_dir_url"]) ? $context["plugin_dir_url"] : null), "template_css" => $this->getAttribute($context["facet"], "template_css", array()))));
            // line 27
            echo "
    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['facet'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 28
        echo " ";
        // line 29
        echo "
</div>

";
        // line 32
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "after_widget", array());
        echo " ";
    }

    public function getTemplateName()
    {
        return "generic/facets_html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  112 => 32,  107 => 29,  105 => 28,  90 => 27,  88 => 26,  85 => 25,  67 => 24,  59 => 19,  55 => 17,  51 => 16,  44 => 10,  41 => 9,  37 => 8,  34 => 7,  31 => 6,  28 => 5,  25 => 4,  22 => 3,  19 => 2,);
    }
}
/* {# Shared Facets template #}*/
/* */
/* {% include template_css %} {# template_css is replaced by the (custom) layout's css selected in the widget. #}*/
/* */
/* {{ widget_args.before_widget| raw }} {# Before widget HTML #}*/
/* */
/* {{ widget_args.before_title| raw }} {# Before title HTML #}*/
/*     {{ facets_header }} {# Title 'Filters' #}*/
/* {{ widget_args.after_title| raw }} {# After title HTML #}*/
/* */
/* <div id="res_facets">*/
/* */
/*     <ul>*/
/*         <li>*/
/*             <a id="wpsolr_remove_facets" {# Do not remove id "wpsolr_remove_facets". It is used as JQuery selector. You can override it's CSS however. #}*/
/*                class="select_opt wpsolr_facet_checkbox {{ not facets.has_facet_elements_selected ? "checked" : "" }}" {# Do not remove class "select_opt". It is used as JQuery selector. You can override it's CSS however. #}*/
/*                href="#wpsolr_facets"*/
/*             >*/
/*                 {{ facets_element_all_results }}*/
/*             </a>*/
/*         </li>*/
/*     </ul>*/
/* */
/*     {% for facet in facets.facets %} {# Loop on facets #}*/
/* */
/*         {% include facet.template_html with {'facets_element': facets_element, 'facets_title': facets_title, 'items': facet.items, 'plugin_dir_url': plugin_dir_url, 'template_css': facet.template_css} %}*/
/* */
/*     {% endfor %} {# Loop on facets #}*/
/* */
/* </div>*/
/* */
/* {{ widget_args.after_widget| raw }} {# After widget HTML #}*/
