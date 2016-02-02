/**
 * Class WPSOLR_Facets used by WPSOLR Widgets
 */

var WPSOLR_Facets = function (groups_facet_id) {
    console.log("Facets constructor");

    this.groups_facet_id = "";
    this.lastFacetSelected = {};
    this.facets = {};
    this.facets.field = [];
    this.facets.range = [];
    //this.extractUrl(); // Done by the widget js calling this api
};

WPSOLR_Facets.prototype.set_groups_facet_id = function (groups_facet_id) {
    this.groups_facet_id = groups_facet_id;
};

WPSOLR_Facets.prototype.get_parameter_group_id = function () {

    return {'name': 'wpsolr_facets_group', 'value': this.groups_facet_id};
};

WPSOLR_Facets.prototype.is_pattern_range = function (parameter) {

    var pattern_range = /\[.* TO .*\]/;

    return pattern_range.test(parameter);
};

WPSOLR_Facets.prototype.debug = function (message, object) {
    console.log("=> " + message + ": " + JSON.stringify(object));
};

WPSOLR_Facets.prototype.debugState = function () {

    console.log("  ++ facets: " + JSON.stringify(this.facets));
    console.log("  ++ url: " + JSON.stringify(this.url));
    console.log("  ++ last selection: " + JSON.stringify(this.lastFacetSelected));
    console.log("  ++ facets group: " + JSON.stringify(this.groups_facet_id));
};

WPSOLR_Facets.prototype.extractUrl = function () {
    this.debug("extract url", "");
    this.debugState();

    url1 = new Url(this.url);
    this.url = url1.toString();
    //this.lastFacetSelected = url1.query['wpsolr_last_facet_selected'];
    //this.groups_facet_id = url1.query["wpsolr_facets_group"];


    // Extract fq parameters
    for (var index = 0; ; index++) {
        var value = url1.query["wpsolr_fq" + "[" + index + "]"];
        if (undefined === value) {
            break;
        } else {
            if (this.is_pattern_range(value)) {
                this.addFacetRangeValue({'facet_id': value.split(":")[0], 'facet_value': value.split(":")[1]});
            } else {
                this.addFacetValue({'facet_id': value.split(":")[0], 'facet_value': value.split(":")[1]});
            }
        }
    }

    this.debugState();
};


WPSOLR_Facets.prototype.is_exist_facet_id = function (facets, facet, is_compare_value) {
    this.debug("is_exist_facet_id", facet);
    this.debugState();

    var result = false;
    for (var index = 0; index < facets.length; index++) {
        if (facets[index].facet_id == facet.facet_id) {

            if (!is_compare_value || (facets[index].facet_value == facet.facet_value)) {
                result = true;
                break;
            }
        }
    }

    this.debug("is_exist_facet_id found", result);

    return result;
}

WPSOLR_Facets.prototype.clear = function () {
    this.debug("clear", '');
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
    this.debug("add facet any", facet);
    this.debugState();

    // Add facet
    facets.push(facet);

    this.debugState();
};

WPSOLR_Facets.prototype.removeFacetAnyValue = function (facets, facet) {
    this.debug("removeFacetAnyValue", facet);
    this.debugState();

    for (index = facets.length - 1; index >= 0; --index) {
        if (facets[index].facet_id == facet.facet_id) {
            if ((facet.facet_value == undefined) || (facets[index].facet_value == facet.facet_value)) {
                facets.splice(index, 1);
            }
        }
    }

    this.debugState();
};

WPSOLR_Facets.prototype.toggleFacetAnyValue = function (facets, facet) {
    this.debug("toggleFacetAnyValue", facet);
    this.debugState();

    if (this.is_exist_facet_id(facets, facet, true)) {

        this.removeFacetAnyValue(facets, facet);
    } else {

        this.addFacetAnyValue(facets, facet);
    }


    this.debugState();
};

WPSOLR_Facets.prototype.toggleFacetValue = function (facet) {
    this.debug("toggleFacetValue", facet);
    this.debugState();

    this.toggleFacetAnyValue(this.facets.field, facet);

    this.debugState();
};

WPSOLR_Facets.prototype.addFacetValue = function (facet) {
    this.debug("add facet", facet);
    this.debugState();

    this.addFacetAnyValue(this.facets.field, facet);

    this.debugState();
};

WPSOLR_Facets.prototype.removeFacetValue = function (facet) {
    this.debug("removeFacetValue", facet);
    this.debugState();

    this.removeFacetAnyValue(this.facets.field, facet);
    this.removeFacetAnyValue(this.facets.range, facet);

    this.debugState();
};

WPSOLR_Facets.prototype.addFacetRangeValue = function (facet) {
    this.debug("add facet range", facet);
    this.debugState();

    // Add facet
    this.addFacetAnyValue(this.facets.range, facet);

    this.debugState();
};


WPSOLR_Facets.prototype.updateLastFacetSelected = function (facet) {
    this.debug("updateLastFacetSelected", facet);
    this.debugState();

    // Update
    this.lastFacetSelected = facet;

    this.debugState();
};

WPSOLR_Facets.prototype.create_url = function () {
    this.debug("create_url", '');
    this.debugState();

    var url1 = new Url(this.url);

    // query: keep it, or add one empty to go to search page on click
    url1.query["s"] = url1.query["s"] || '';

    var fq_index = 0;

    // delete all wpsolr_fq parameters
    for (i = 0; ; i++) {
        if (url1.query["wpsolr_fq" + "[" + i + "]"]) {
            delete url1.query["wpsolr_fq" + "[" + i + "]"];
            this.debug('delete from url', "wpsolr_fq" + "[" + i + "]");
        } else {
            break;
        }
    }

    // Add field parameters
    var facets = this.facets.field || [];
    for (i = 0; i < facets.length; i++) {
        url1.query["wpsolr_fq" + "[" + fq_index + "]"] = facets[i].facet_id + ":" + facets[i].facet_value;
        fq_index++;
    }

    // Add range parameters
    var facets = this.facets.range || [];
    for (i = 0; i < facets.length; i++) {
        url1.query["wpsolr_fq" + "[" + fq_index + "]"] = facets[i].facet_id + ":" + "[" + facets[i].facet_value + " TO " + (facets[i].range_sup) + "]";
        fq_index++;
    }


    url1.query["wpsolr_facets_group"] = this.groups_facet_id;

    // Last facet selected
    url1.query["wpsolr_last_facet_selected"] = this.lastFacetSelected['facet_id'];


    this.url = url1.toString();

    // Display loaders on each facet
    jQuery(".wpsolr_any_facet_class li ul").addClass("wpsolr_loader");

    window.location.href = this.url;

    this.debugState();
};


// Global object used by one widget
var wpsolr_facets = new WPSOLR_Facets();
