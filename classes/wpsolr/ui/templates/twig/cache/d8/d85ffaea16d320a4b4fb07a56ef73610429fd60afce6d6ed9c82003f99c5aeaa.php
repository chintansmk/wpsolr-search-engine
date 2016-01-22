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

        this.facets = {};
        this.facets.field = [];
        this.facets.range = [];
        //this.extractUrl(); // Done by the widget js calling this api
    };

    WPSOLR_Facets.prototype.is_pattern_range = function (parameter) {

        var pattern_range = /\\[.* TO .*\\]/;

        return pattern_range.test(parameter);
    };

    WPSOLR_Facets.prototype.debug = function (message, object) {
        console.log(\"=> \" + message + \": \" + JSON.stringify(object));
    };

    WPSOLR_Facets.prototype.debugState = function () {
        console.log(\"  ++ facets: \" + JSON.stringify(this.facets));
        console.log(\"  ++ url: \" + JSON.stringify(this.url));
    };

    WPSOLR_Facets.prototype.extractUrl = function () {
        this.debug(\"extract url\", \"\");
        this.debugState();

        url1 = new Url(this.url);
        this.url = url1.toString();

        // Extract fq parameters
        for (var index = 0; ; index++) {
            var value = url1.query[\"wpsolr_fq\" + \"[\" + index + \"]\"];
            if (undefined === value) {
                break;
            } else {
                if (this.is_pattern_range(value)) {
                    this.addFacetRangeValue({'facet_id': value.split(\":\")[0], 'facet_value': value.split(\":\")[1]});
                } else {
                    this.addFacetValue({'facet_id': value.split(\":\")[0], 'facet_value': value.split(\":\")[1]});
                }
            }
        }

        this.debugState();
    };


    WPSOLR_Facets.prototype.is_exist_facet_id = function (facets, facet, is_compare_value) {

        for (var index = 0; index < facets.length; index++) {
            if (facets[index].facet_id == facet.facet_id) {
                return !is_compare_value || (facets[index].facet_value == facet.facet_value);
            }
        }

        return false;
    }

    WPSOLR_Facets.prototype.delete_facet_id = function (facets, facet) {
        this.debug(\"delete facet id\", facet);
        this.debugState();

        var len = facets.length;
        for (var index = 0; index < len; index++) {
            if (facets[index].facet_id == facet.facet_id) {
                facets.splice(index, 1);
            }
        }

        this.debugState();
    }

    WPSOLR_Facets.prototype.clear = function () {
        this.debug(\"clear\", '');
        this.debugState();

        this.facets = {};
        this.facets.field = [];
        this.facets.range = [];

        var url1 = new Url(window.location.href);
        url1.query.clear();
        this.url = url1.toString();

        this.debugState();
    }

    WPSOLR_Facets.prototype.addFacetAnyValue = function (facets, facet) {
        this.debug(\"add facet any\", facet);
        this.debugState();


        if (this.is_exist_facet_id(this.facets.field, facet, false)) {
        }

        // Add facet
        facets.field.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.addFacetValue = function (facet) {
        this.debug(\"add facet\", facet);
        this.debugState();


        if (this.is_exist_facet_id(this.facets.field, facet, false)) {
        }

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
        this.delete_facet_id(this.facets.range, facet);

        // Add facet
        this.facets.range.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.create_url = function () {
        this.debug(\"url\", '');
        this.debugState();

        var url1 = new Url(this.url);

        // query: keep it, or add one empty to go to search page on click
        url1.query[\"s\"] = url1.query[\"s\"] || '';

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

        jQuery(\".wpsolr_remove_facets\").on(\"click\", function (event) {

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

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
/* <script>*/
/* */
/*     var WPSOLR_Facets = function () {*/
/*         console.log("Facets constructor");*/
/* */
/*         this.facets = {};*/
/*         this.facets.field = [];*/
/*         this.facets.range = [];*/
/*         //this.extractUrl(); // Done by the widget js calling this api*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.is_pattern_range = function (parameter) {*/
/* */
/*         var pattern_range = /\[.* TO .*\]/;*/
/* */
/*         return pattern_range.test(parameter);*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.debug = function (message, object) {*/
/*         console.log("=> " + message + ": " + JSON.stringify(object));*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.debugState = function () {*/
/*         console.log("  ++ facets: " + JSON.stringify(this.facets));*/
/*         console.log("  ++ url: " + JSON.stringify(this.url));*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.extractUrl = function () {*/
/*         this.debug("extract url", "");*/
/*         this.debugState();*/
/* */
/*         url1 = new Url(this.url);*/
/*         this.url = url1.toString();*/
/* */
/*         // Extract fq parameters*/
/*         for (var index = 0; ; index++) {*/
/*             var value = url1.query["wpsolr_fq" + "[" + index + "]"];*/
/*             if (undefined === value) {*/
/*                 break;*/
/*             } else {*/
/*                 if (this.is_pattern_range(value)) {*/
/*                     this.addFacetRangeValue({'facet_id': value.split(":")[0], 'facet_value': value.split(":")[1]});*/
/*                 } else {*/
/*                     this.addFacetValue({'facet_id': value.split(":")[0], 'facet_value': value.split(":")[1]});*/
/*                 }*/
/*             }*/
/*         }*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/* */
/*     WPSOLR_Facets.prototype.is_exist_facet_id = function (facets, facet, is_compare_value) {*/
/* */
/*         for (var index = 0; index < facets.length; index++) {*/
/*             if (facets[index].facet_id == facet.facet_id) {*/
/*                 return !is_compare_value || (facets[index].facet_value == facet.facet_value);*/
/*             }*/
/*         }*/
/* */
/*         return false;*/
/*     }*/
/* */
/*     WPSOLR_Facets.prototype.delete_facet_id = function (facets, facet) {*/
/*         this.debug("delete facet id", facet);*/
/*         this.debugState();*/
/* */
/*         var len = facets.length;*/
/*         for (var index = 0; index < len; index++) {*/
/*             if (facets[index].facet_id == facet.facet_id) {*/
/*                 facets.splice(index, 1);*/
/*             }*/
/*         }*/
/* */
/*         this.debugState();*/
/*     }*/
/* */
/*     WPSOLR_Facets.prototype.clear = function () {*/
/*         this.debug("clear", '');*/
/*         this.debugState();*/
/* */
/*         this.facets = {};*/
/*         this.facets.field = [];*/
/*         this.facets.range = [];*/
/* */
/*         var url1 = new Url(window.location.href);*/
/*         url1.query.clear();*/
/*         this.url = url1.toString();*/
/* */
/*         this.debugState();*/
/*     }*/
/* */
/*     WPSOLR_Facets.prototype.addFacetAnyValue = function (facets, facet) {*/
/*         this.debug("add facet any", facet);*/
/*         this.debugState();*/
/* */
/* */
/*         if (this.is_exist_facet_id(this.facets.field, facet, false)) {*/
/*         }*/
/* */
/*         // Add facet*/
/*         facets.field.push(facet);*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.addFacetValue = function (facet) {*/
/*         this.debug("add facet", facet);*/
/*         this.debugState();*/
/* */
/* */
/*         if (this.is_exist_facet_id(this.facets.field, facet, false)) {*/
/*         }*/
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
/*         this.delete_facet_id(this.facets.range, facet);*/
/* */
/*         // Add facet*/
/*         this.facets.range.push(facet);*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.create_url = function () {*/
/*         this.debug("url", '');*/
/*         this.debugState();*/
/* */
/*         var url1 = new Url(this.url);*/
/* */
/*         // query: keep it, or add one empty to go to search page on click*/
/*         url1.query["s"] = url1.query["s"] || '';*/
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
/*         jQuery(".wpsolr_remove_facets").on("click", function (event) {*/
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
