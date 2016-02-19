/**
 * Class WPSOLR_Sorts used by WPSOLR Widgets
 */

var WPSOLR_Sorts = function (groups_sort_id) {
    console.log("Sorts constructor");

    this.groups_sort_id = groups_sort_id;
    this.sort = "";
};

WPSOLR_Sorts.prototype.debug = function (message, object) {
    console.log("=> " + message + ": " + JSON.stringify(object));
};

WPSOLR_Sorts.prototype.debugState = function () {
    console.log("  ++ sort: " + JSON.stringify(this.sort));
    console.log("  ++ url: " + JSON.stringify(this.url));
};

WPSOLR_Sorts.prototype.clear = function () {
    this.debug("clear", '');
    this.debugState();

    this.sort = "";
    var url1 = new Url(window.location.href);
    this.debug("toto", url1);
    //delete url1.query["wpsolr_sort"];
    this.url = url1.toString();

    this.debugState();
}

WPSOLR_Sorts.prototype.addValue = function (sort) {
    this.debug("add sort", sort);
    this.debugState();

    // Add sort
    this.sort = sort

    this.debugState();
};

WPSOLR_Sorts.prototype.removeValue = function (sort) {
    this.debug("remove sort", sort);
    this.debugState();

    this.sort = "";

    this.debugState();
};

WPSOLR_Sorts.prototype.create_url = function (external_parameters) {
    this.debug("url", '');
    this.debugState();

    var url1 = new Url(this.url);

    // query: keep it, or add one empty to go to search page on click
    url1.query["s"] = url1.query["s"] || '';


    if (this.sort) {
        url1.query["wpsolr_sort"] = this.sort;
    } else {
        delete url1.query["wpsolr_sort"];
    }

    url1.query["wpsolr_sorts_group"] = this.groups_sort_id;

    // External parameters
    if (external_parameters) {
        url1.query[external_parameters.name] = external_parameters.value;
    }

    this.url = url1.toString();

    window.location.href = this.url;

    this.debugState();
};

/**
 * Class WPSOLR_Facets used by WPSOLR Widgets
 */

var WPSOLR_Facets = function () {
    console.log("Facets constructor");

    this.groups_facet_id = "";
    this.lastFacetSelected = {};
    this.facets = {};
    this.facets.field = [];
    this.facets.range = [];
    this.ui_id = "";
    //this.extractUrl(); // Done by the widget js calling this api
};

WPSOLR_Facets.prototype.set_groups_facet_id = function (groups_facet_id) {
    this.groups_facet_id = groups_facet_id;
};

WPSOLR_Facets.prototype.set_ui_id = function (ui_id) {
    this.ui_id = ui_id;
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
                this.addFacetValue({'facet_id': value.split(":")[0], 'facet_value': value.split(":")[1]});
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
    this.debug("addFacetAnyValue", facet);
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

WPSOLR_Facets.prototype.getFacetsByType = function (facet) {
    this.debug("getFacetsByType", facet);
    this.debugState();

    var result = this.facets.field;

    switch (facet.facet_type) {
        case "facet_range":
        case "facet_range_custom":
            this.debug("getFacetsByType result:", "range");
            result = this.facets.range;
            break;

        default:
            this.debug("getFacetsByType result:", "field");
            result = this.facets.field;
            break;
    }


    return result;
}

WPSOLR_Facets.prototype.toggleFacetValue = function (facet) {
    this.debug("toggleFacetValue", facet);
    this.debugState();

    this.toggleFacetAnyValue(this.getFacetsByType(facet), facet);

    this.debugState();
};

WPSOLR_Facets.prototype.addFacetValue = function (facet) {
    this.debug("addFacetValue", facet);
    this.debugState();

    this.addFacetAnyValue(this.getFacetsByType(facet), facet);

    this.debugState();
};

WPSOLR_Facets.prototype.removeFacetValue = function (facet) {
    this.debug("removeFacetValue", facet);
    this.debugState();

    this.removeFacetAnyValue(this.getFacetsByType(facet), facet);

    this.debugState();
};

WPSOLR_Facets.prototype.updateLastFacetSelected = function (facet) {
    this.debug("updateLastFacetSelected", facet);
    this.debugState();

    // Update
    this.lastFacetSelected = facet;

    this.debugState();
};

WPSOLR_Facets.prototype.create_url = function (delay_in_ms) {
    this.debug("create_url", '');
    this.debugState();

    // Init the delay if undefined
    this.delay_in_ms = delay_in_ms || 0;

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
    if (this.lastFacetSelected['facet_id'] != undefined) {
        url1.query["wpsolr_last_facet_selected"] = this.lastFacetSelected['facet_id'];
    }


    this.url = url1.toString();

    // Load the url with a delay. Reset the timer first.
    if (this.timer_handle) {
        clearTimeout(this.timer_handle);
    }
    this.timer_handle = setTimeout(this.timer.bind(this), this.delay_in_ms);


    this.debugState();
};

WPSOLR_Facets.prototype.timer = function () {
    this.debug("timer", this.delay_in_ms);

    // Display loaders on each facet
    jQuery("." + this.ui_id + " " + ".wpsolr_any_facet_class li ul").addClass("wpsolr_loader");

    window.location.href = this.url;

}


// Globals array
var wpsolr_facets = [];
var wpsolr_sorts = [];
