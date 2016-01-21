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

        this.facets = [];
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
        this.debug(\"add facets\", facet);
        this.debugState();

        this.facets.push(facet);

        this.debugState();
    };

    WPSOLR_Facets.prototype.create_url = function () {
        this.debug(\"url\");
        this.debugState();

        var url1 = new Url(window.location.href);

        // 2nd, add parameters
        var fields_query = this.facets || [];
        for (i = 0; i < fields_query.length; i++) {
            url1.query['wpsolr_fq' + '[' + i + ']'] = fields_query[i].facet_id + \":\" + fields_query[i].facet_value;
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
/*         this.facets = [];*/
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
/*         this.debug("add facets", facet);*/
/*         this.debugState();*/
/* */
/*         this.facets.push(facet);*/
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
/*         // 2nd, add parameters*/
/*         var fields_query = this.facets || [];*/
/*         for (i = 0; i < fields_query.length; i++) {*/
/*             url1.query['wpsolr_fq' + '[' + i + ']'] = fields_query[i].facet_id + ":" + fields_query[i].facet_value;*/
/*         }*/
/* */
/* */
/*         this.url = url1.toString();*/
/* */
/*        window.location.href = this.url;*/
/* */
/*         this.debugState();*/
/*     };*/
/* */
/* */
/*     // Global object used by one widget*/
/*     var wpsolr_facets = new WPSOLR_Facets();*/
/* */
/* </script>*/
