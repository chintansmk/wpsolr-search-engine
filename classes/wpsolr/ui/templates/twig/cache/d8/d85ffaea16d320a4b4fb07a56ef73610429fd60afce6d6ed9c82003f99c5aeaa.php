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
        this.url = \"\";
    };

    WPSOLR_Facets.prototype.debug = function (message, object) {
        console.log(\"=> \" + message + \": \" + JSON.stringify(object));
    };

    WPSOLR_Facets.prototype.debugState = function () {
        console.log(\"++ facets: \" + JSON.stringify(this.facets));
        console.log(\"++ url: \" + JSON.stringify(this.url));
    };

    WPSOLR_Facets.prototype.addFacetValue = function (facet) {
        this.debug(\"add facet\", facet);
        this.debugState();

        this.facets.field.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.addFacetRangeValue = function (facet) {
        this.debug(\"add facet range\", facet);
        this.debugState();

        this.facets.range.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.create_url = function () {
        this.debug(\"url\");
        this.debugState();

        var url1 = new Url(window.location.href);

        // query
        url1.query['s'] = '';


        // Add field parameters
        var facets = this.facets.field || [];
        for (i = 0; i < facets.length; i++) {
            url1.query['wpsolr_fq' + '[' + i + ']'] = facets[i].facet_id + \":\" + facets[i].facet_value;
        }

        // Add range parameters
        var facets = this.facets.range || [];
        for (i = 0; i < facets.length; i++) {
            url1.query['wpsolr_fq' + '[' + i + ']'] = facets[i].facet_id + \":\" + \"[\" + facets[i].facet_value + \" TO \" + (facets[i].range_sup) + \"]\";
        }


        this.url = url1.toString();

        window.location.href = this.url;

        this.debugState();
    };


    // Global object used by one widget
    var wpsolr_facets = new WPSOLR_Facets();

</script>";
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
/*         this.url = "";*/
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
/*     WPSOLR_Facets.prototype.addFacetValue = function (facet) {*/
/*         this.debug("add facet", facet);*/
/*         this.debugState();*/
/* */
/*         this.facets.field.push(facet);*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.addFacetRangeValue = function (facet) {*/
/*         this.debug("add facet range", facet);*/
/*         this.debugState();*/
/* */
/*         this.facets.range.push(facet);*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/*     WPSOLR_Facets.prototype.create_url = function () {*/
/*         this.debug("url");*/
/*         this.debugState();*/
/* */
/*         var url1 = new Url(window.location.href);*/
/* */
/*         // query*/
/*         url1.query['s'] = '';*/
/* */
/* */
/*         // Add field parameters*/
/*         var facets = this.facets.field || [];*/
/*         for (i = 0; i < facets.length; i++) {*/
/*             url1.query['wpsolr_fq' + '[' + i + ']'] = facets[i].facet_id + ":" + facets[i].facet_value;*/
/*         }*/
/* */
/*         // Add range parameters*/
/*         var facets = this.facets.range || [];*/
/*         for (i = 0; i < facets.length; i++) {*/
/*             url1.query['wpsolr_fq' + '[' + i + ']'] = facets[i].facet_id + ":" + "[" + facets[i].facet_value + " TO " + (facets[i].range_sup) + "]";*/
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
/* */
/*     // Global object used by one widget*/
/*     var wpsolr_facets = new WPSOLR_Facets();*/
/* */
/* </script>*/
