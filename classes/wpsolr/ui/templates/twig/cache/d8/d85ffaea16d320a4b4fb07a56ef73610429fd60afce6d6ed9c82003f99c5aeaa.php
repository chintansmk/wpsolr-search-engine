<?php

/* generic/facets/js.twig */
class __TwigTemplate_8fc61a5a79c1f4a36462a5105c7c3f6c8f7d7bcef1a14a16a6d7ca8415d1870e extends Twig_Template
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

    var WPSOLR_Facets = function () {
        console.log(\"Facets constructor\");

        this.clear();
    };

    WPSOLR_Facets.prototype.debug = function (message, object) {
        console.log(\"=> \" + message + \": \" + JSON.stringify(object));
    };

    WPSOLR_Facets.prototype.debugState = function () {
        console.log(\"++ facets: \" + JSON.stringify(this.facets));
        console.log(\"++ url: \" + JSON.stringify(this.url));
    };

    WPSOLR_Facets.prototype.extractUrl = function () {
        this.debug(\"extract url\", \"\");
        this.debugState();

        url1 = new Url(window.location.href);
        this.url = url1.toString();

        // Extract fq parameters
        for (var index = 0; ; index++) {
            var value = url1.query[\"wpsolr_fq\" + \"[\" + index + \"]\"];
            if (undefined === value) {
                break;
            } else {
                this.addFacetValue({'facet_id': value.split(\":\")[0], 'facet_value': value.split(\":\")[1]});
            }
        }

        this.debugState();
    };


    WPSOLR_Facets.prototype.is_exist_facet_id = function (facets, facet) {

        for (var index = 0; index < facets.length; index++) {
            if (facets[index].facet_id == facet.facet_id) {
                return true;
            }
        }

        return false;
    }

    WPSOLR_Facets.prototype.delete_facet_id = function (facets, facet) {

        var len = facets.length;
        for (var index = 0; index < len; index++) {
            if (facets[index].facet_id == facet.facet_id) {
                facets.splice(index, 1);
            }
        }

    }

    WPSOLR_Facets.prototype.clear = function () {

        this.facets = {};
        this.facets.field = [];
        this.facets.range = [];
        this.extractUrl();

    }

    WPSOLR_Facets.prototype.addFacetValue = function (facet) {
        this.debug(\"add facet\", facet);
        this.debugState();

        /*
         if (!this.is_exist_facet_id(this.facets.field, facet)) {
         this.facets.field.push(facet);
         }*/

        // Mono selection: Remove facet(s) with same id
        this.delete_facet_id(this.facets.field, facet);

        // Add facet
        this.facets.field.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.addFacetRangeValue = function (facet) {
        this.debug(\"add facet range\", facet);
        this.debugState();

        // Mono selection: Remove facet(s) with same id
        this.delete_facet_id(this.facets.field, facet);

        // Add facet
        this.facets.range.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.create_url = function () {
        this.debug(\"url\");
        this.debugState();

        var url1 = new Url(this.url);

        // query
        url1.query[\"s\"] = \"\";

        var fq_index = 0;

        // Add field parameters
        var facets = this.facets.field || [];
        for (i = 0; i < facets.length; i++) {
            url1.query[\"wpsolr_fq\" + \"[\" + fq_index + \"]\"] = facets[i].facet_id + \":\" + facets[i].facet_value;
            fq_index++;
        }

        // Add range parameters
        var facets = this.facets.range || [];
        for (i = 0; i < facets.length; i++) {
            url1.query[\"wpsolr_fq\" + \"[\" + fq_index + \"]\"] = facets[i].facet_id + \":\" + \"[\" + facets[i].facet_value + \" TO \" + (facets[i].range_sup) + \"]\";
            fq_index++;
        }


        this.url = url1.toString();

        window.location.href = this.url;

        this.debugState();
    };

    // Create a global WPSOLR facets object
    var wpsolr_facets;
    jQuery(document).ready(function () {
        // Global object used by one widget
        wpsolr_facets = new WPSOLR_Facets();

        jQuery(\"wpsolr_remove_facets\").on(\"click\", function (event) {
            
            //jQuery(\".wpsolr_any_facet_class li .";
        // line 142
        echo twig_escape_filter($this->env, (isset($context["facet_selector_class"]) ? $context["facet_selector_class"] : null), "html", null, true);
        echo "\").removeClass(\"";
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); // Deactivate all facets
            //jQuery(this).addClass(\"";
        // line 143
        echo twig_escape_filter($this->env, (isset($context["facet_selected_class"]) ? $context["facet_selected_class"] : null), "html", null, true);
        echo "\"); // Activate clicked facet

            wpsolr_facets.clear(); // clear all facets
            wpsolr_facets.create_url();
        });

    });

</script>

";
    }

    public function getTemplateName()
    {
        return "generic/facets/js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  168 => 143,  162 => 142,  19 => 1,);
    }
}
/* <script>*/
/* */
/*     var WPSOLR_Facets = function () {*/
/*         console.log("Facets constructor");*/
/* */
/*         this.clear();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.debug = function (message, object) {*/
/*         console.log("=> " + message + ": " + JSON.stringify(object));*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.debugState = function () {*/
/*         console.log("++ facets: " + JSON.stringify(this.facets));*/
/*         console.log("++ url: " + JSON.stringify(this.url));*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.extractUrl = function () {*/
/*         this.debug("extract url", "");*/
/*         this.debugState();*/
/* */
/*         url1 = new Url(window.location.href);*/
/*         this.url = url1.toString();*/
/* */
/*         // Extract fq parameters*/
/*         for (var index = 0; ; index++) {*/
/*             var value = url1.query["wpsolr_fq" + "[" + index + "]"];*/
/*             if (undefined === value) {*/
/*                 break;*/
/*             } else {*/
/*                 this.addFacetValue({'facet_id': value.split(":")[0], 'facet_value': value.split(":")[1]});*/
/*             }*/
/*         }*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/* */
/*     WPSOLR_Facets.prototype.is_exist_facet_id = function (facets, facet) {*/
/* */
/*         for (var index = 0; index < facets.length; index++) {*/
/*             if (facets[index].facet_id == facet.facet_id) {*/
/*                 return true;*/
/*             }*/
/*         }*/
/* */
/*         return false;*/
/*     }*/
/* */
/*     WPSOLR_Facets.prototype.delete_facet_id = function (facets, facet) {*/
/* */
/*         var len = facets.length;*/
/*         for (var index = 0; index < len; index++) {*/
/*             if (facets[index].facet_id == facet.facet_id) {*/
/*                 facets.splice(index, 1);*/
/*             }*/
/*         }*/
/* */
/*     }*/
/* */
/*     WPSOLR_Facets.prototype.clear = function () {*/
/* */
/*         this.facets = {};*/
/*         this.facets.field = [];*/
/*         this.facets.range = [];*/
/*         this.extractUrl();*/
/* */
/*     }*/
/* */
/*     WPSOLR_Facets.prototype.addFacetValue = function (facet) {*/
/*         this.debug("add facet", facet);*/
/*         this.debugState();*/
/* */
/*         /**/
/*          if (!this.is_exist_facet_id(this.facets.field, facet)) {*/
/*          this.facets.field.push(facet);*/
/*          }*//* */
/* */
/*         // Mono selection: Remove facet(s) with same id*/
/*         this.delete_facet_id(this.facets.field, facet);*/
/* */
/*         // Add facet*/
/*         this.facets.field.push(facet);*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.addFacetRangeValue = function (facet) {*/
/*         this.debug("add facet range", facet);*/
/*         this.debugState();*/
/* */
/*         // Mono selection: Remove facet(s) with same id*/
/*         this.delete_facet_id(this.facets.field, facet);*/
/* */
/*         // Add facet*/
/*         this.facets.range.push(facet);*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.create_url = function () {*/
/*         this.debug("url");*/
/*         this.debugState();*/
/* */
/*         var url1 = new Url(this.url);*/
/* */
/*         // query*/
/*         url1.query["s"] = "";*/
/* */
/*         var fq_index = 0;*/
/* */
/*         // Add field parameters*/
/*         var facets = this.facets.field || [];*/
/*         for (i = 0; i < facets.length; i++) {*/
/*             url1.query["wpsolr_fq" + "[" + fq_index + "]"] = facets[i].facet_id + ":" + facets[i].facet_value;*/
/*             fq_index++;*/
/*         }*/
/* */
/*         // Add range parameters*/
/*         var facets = this.facets.range || [];*/
/*         for (i = 0; i < facets.length; i++) {*/
/*             url1.query["wpsolr_fq" + "[" + fq_index + "]"] = facets[i].facet_id + ":" + "[" + facets[i].facet_value + " TO " + (facets[i].range_sup) + "]";*/
/*             fq_index++;*/
/*         }*/
/* */
/* */
/*         this.url = url1.toString();*/
/* */
/*         window.location.href = this.url;*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     // Create a global WPSOLR facets object*/
/*     var wpsolr_facets;*/
/*     jQuery(document).ready(function () {*/
/*         // Global object used by one widget*/
/*         wpsolr_facets = new WPSOLR_Facets();*/
/* */
/*         jQuery("wpsolr_remove_facets").on("click", function (event) {*/
/*             */
/*             //jQuery(".wpsolr_any_facet_class li .{{ facet_selector_class }}").removeClass("{{ facet_selected_class }}"); // Deactivate all facets*/
/*             //jQuery(this).addClass("{{ facet_selected_class }}"); // Activate clicked facet*/
/* */
/*             wpsolr_facets.clear(); // clear all facets*/
/*             wpsolr_facets.create_url();*/
/*         });*/
/* */
/*     });*/
/* */
/* </script>*/
/* */
/* */
