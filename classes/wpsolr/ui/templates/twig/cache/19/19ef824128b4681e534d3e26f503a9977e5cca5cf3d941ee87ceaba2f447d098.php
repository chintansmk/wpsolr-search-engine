<?php

/* wpsolr/facets_html.twig */
class __TwigTemplate_5352f22a50396d2f2262c7ffe3eabb23aa56d1b585051199083dbab90ac64b7d extends Twig_Template
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
<div>

    ";
        // line 6
        echo "    <label class='wdm_label'>";
        echo twig_escape_filter($this->env, (isset($context["facets_header"]) ? $context["facets_header"] : null), "html", null, true);
        echo "</label>
    <input type='hidden' name='sel_fac_field' id='sel_fac_field' data-wpsolr-facets-selected=''>

    <div class='wdm_ul' id='wpsolr_section_facets'>

        <div class='select_opt ";
        // line 11
        echo twig_escape_filter($this->env, (isset($context["facet_class"]) ? $context["facet_class"] : null), "html", null, true);
        echo " ";
        echo (( !$this->getAttribute((isset($context["facets"]) ? $context["facets"] : null), "has_facet_elements_selected", array())) ? ("checked") : (""));
        echo "'
             id='wpsolr_remove_facets'>";
        // line 12
        echo twig_escape_filter($this->env, (isset($context["facets_element_all_results"]) ? $context["facets_element_all_results"] : null), "html", null, true);
        echo "
        </div>

        ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["facets"]) ? $context["facets"] : null), "facets", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["facet"]) {
            echo " ";
            // line 16
            echo "
            ";
            // line 18
            echo "            <lh>";
            echo twig_escape_filter($this->env, sprintf((isset($context["facets_title"]) ? $context["facets_title"] : null), $this->getAttribute($context["facet"], "name", array())), "html", null, true);
            echo "</lh><br>

            ";
            // line 20
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["facet"], "items", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                echo " ";
                // line 21
                echo "
                ";
                // line 23
                echo "                <div class='select_opt ";
                echo twig_escape_filter($this->env, (isset($context["facet_class"]) ? $context["facet_class"] : null), "html", null, true);
                echo " ";
                echo (($this->getAttribute($context["item"], "selected", array())) ? ("checked") : (""));
                echo "'
                     id='";
                // line 24
                echo twig_escape_filter($this->env, $this->getAttribute($context["facet"], "id", array()), "html", null, true);
                echo ":";
                echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "name", array()), "html", null, true);
                echo "'>
                    ";
                // line 25
                echo twig_escape_filter($this->env, sprintf((isset($context["facets_element"]) ? $context["facets_element"] : null), $this->getAttribute($context["item"], "name", array()), $this->getAttribute($context["item"], "count", array())), "html", null, true);
                echo "
                </div>

            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 28
            echo " ";
            // line 29
            echo "
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['facet'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 30
        echo " ";
        // line 31
        echo "
    </div>

</div>";
    }

    public function getTemplateName()
    {
        return "wpsolr/facets_html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 31,  99 => 30,  92 => 29,  90 => 28,  80 => 25,  74 => 24,  67 => 23,  64 => 21,  59 => 20,  53 => 18,  50 => 16,  45 => 15,  39 => 12,  33 => 11,  24 => 6,  19 => 2,);
    }
}
/* {# WPSOLR Facets default template #}*/
/* */
/* <div>*/
/* */
/*     {# Title like 'Filters' #}*/
/*     <label class='wdm_label'>{{ facets_header }}</label>*/
/*     <input type='hidden' name='sel_fac_field' id='sel_fac_field' data-wpsolr-facets-selected=''>*/
/* */
/*     <div class='wdm_ul' id='wpsolr_section_facets'>*/
/* */
/*         <div class='select_opt {{ facet_class }} {{ not facets.has_facet_elements_selected ? "checked" : "" }}'*/
/*              id='wpsolr_remove_facets'>{{ facets_element_all_results }}*/
/*         </div>*/
/* */
/*         {% for facet in facets.facets %} {# Loop on facets #}*/
/* */
/*             {# Current facet name #}*/
/*             <lh>{{ facets_title|format(facet.name) }}</lh><br>*/
/* */
/*             {% for item in facet.items %} {# Loop on current facet items #}*/
/* */
/*                 {# Current facet item #}*/
/*                 <div class='select_opt {{ facet_class }} {{ item.selected ? "checked" : "" }}'*/
/*                      id='{{ facet.id }}:{{ item.name }}'>*/
/*                     {{ facets_element|format( item.name, item.count ) }}*/
/*                 </div>*/
/* */
/*             {% endfor %} {# Loop on current facet items #}*/
/* */
/*         {% endfor %} {# Loop on facets #}*/
/* */
/*     </div>*/
/* */
/* </div>*/
