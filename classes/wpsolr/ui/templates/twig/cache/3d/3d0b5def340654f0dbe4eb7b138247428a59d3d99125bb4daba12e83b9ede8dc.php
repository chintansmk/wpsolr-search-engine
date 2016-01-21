<?php

/* generic/sort_dropdownlist_html.twig */
class __TwigTemplate_521f02df9951b27b3c60f30ff0720864899beefd70748ce8e26832fd8279f3d2 extends Twig_Template
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
<script>
    ";
        // line 5
        echo "</script>

";
        // line 7
        $this->loadTemplate((isset($context["template_css"]) ? $context["template_css"] : null), "generic/sort_dropdownlist_html.twig", 7)->display($context);
        echo " ";
        // line 8
        echo "
";
        // line 9
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "before_widget", array());
        echo " ";
        // line 10
        echo "
";
        // line 11
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "before_title", array());
        echo " ";
        // line 12
        echo "    ";
        echo twig_escape_filter($this->env, (isset($context["sort_header"]) ? $context["sort_header"] : null), "html", null, true);
        echo " ";
        // line 13
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "after_title", array());
        echo " ";
        // line 14
        echo "
<select class=\"select_field\"> ";
        // line 16
        echo "
    ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["sort_list"]) ? $context["sort_list"] : null), "items", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["sort"]) {
            echo " ";
            // line 18
            echo "
        <option value=\"";
            // line 19
            echo twig_escape_filter($this->env, $this->getAttribute($context["sort"], "id", array()), "html", null, true);
            echo "\" ";
            echo (($this->getAttribute($context["sort"], "selected", array())) ? ("selected") : (""));
            echo "> ";
            // line 20
            echo "            ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["sort"], "name", array()), "html", null, true);
            echo " ";
            // line 21
            echo "        </option>

    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sort'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo " ";
        // line 24
        echo "
</select>

";
        // line 27
        echo $this->getAttribute((isset($context["widget_args"]) ? $context["widget_args"] : null), "after_widget", array());
        echo " ";
        // line 28
        echo "
";
    }

    public function getTemplateName()
    {
        return "generic/sort_dropdownlist_html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 28,  87 => 27,  82 => 24,  80 => 23,  72 => 21,  68 => 20,  63 => 19,  60 => 18,  55 => 17,  52 => 16,  49 => 14,  46 => 13,  42 => 12,  39 => 11,  36 => 10,  33 => 9,  30 => 8,  27 => 7,  23 => 5,  19 => 2,);
    }
}
/* {# Shared Sort template #}*/
/* */
/* <script>*/
/*     {# Your custom js here. #}*/
/* </script>*/
/* */
/* {% include template_css %} {# template_css is replaced by the (custom) layout's css selected in the widget. #}*/
/* */
/* {{ widget_args.before_widget| raw }} {# Before widget HTML #}*/
/* */
/* {{ widget_args.before_title| raw }} {# Before title HTML #}*/
/*     {{ sort_header }} {# Title 'Sort by' #}*/
/* {{ widget_args.after_title| raw }} {# After title HTML #}*/
/* */
/* <select class="select_field"> {# Do not remove class "select_field". It is used as JQuery selector. You can override it's CSS however. #}*/
/* */
/*     {% for sort in sort_list.items %} {# Loop on sort items #}*/
/* */
/*         <option value="{{ sort.id }}" {{ sort.selected ? "selected" : "" }}> {# Sort item value #}*/
/*             {{ sort.name }} {# Sort item name #}*/
/*         </option>*/
/* */
/*     {% endfor %} {# Loop on sort items #}*/
/* */
/* </select>*/
/* */
/* {{ widget_args.after_widget| raw }} {# After widget HTML #}*/
/* */
/* */
