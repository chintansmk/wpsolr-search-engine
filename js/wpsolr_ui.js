/**
 * Class WPSOLR_UI (from which others inherit)
 */
var WPSOLR_UI = function () {
    console.log("WPSOLR_UI constructor");

    this.url = "";
    this.ui_id = "";
    this.query_page = "";
    this.query_parameter_name = "";
};

WPSOLR_UI.prototype.debug = function (message, object) {
    console.log("=> " + message + ": " + JSON.stringify(object));
};

WPSOLR_UI.prototype.debugState = function () {
    console.log("  ++ url: " + JSON.stringify(this.url));
    console.log("  ++ ui_id: " + JSON.stringify(this.ui_id));
    console.log("  ++ query_page: " + JSON.stringify(this.query_page));
    console.log("  ++ query_parameter_name: " + JSON.stringify(this.query_parameter_name));
};

WPSOLR_UI.prototype.get_url_query = function () {
    var current_url = new Url(this.url);
    return current_url.query[wp_localize_script_wpsolr_ui.SEARCH_PARAMETER_Q] || current_url.query[wp_localize_script_wpsolr_ui.SEARCH_PARAMETER_S] || '';
}

WPSOLR_UI.prototype.clear = function () {
    this.debug("clear", "");
    this.debugState();

    // Clear url parameters, but keep query parameter
    var current_url = new Url(window.location.href);
    var current_query = this.get_url_query();
    current_url.query.clear();
    current_url.query[this.query_parameter_name] = current_query;

    this.url = current_url.toString();

    // Call child
    this._clear();

    this.debugState();
}

WPSOLR_UI.prototype.set_ui_id = function (ui_id) {
    this.debug("ui_id", ui_id);
    this.ui_id = ui_id;
};

WPSOLR_UI.prototype.set_query_page = function (query_page) {
    this.debug("query_page", query_page);
    this.query_page = query_page;
};

WPSOLR_UI.prototype.set_query_parameter_name = function (query_parameter_name) {
    this.debug("query_parameter_name", query_parameter_name);
    this.query_parameter_name = query_parameter_name;
};

WPSOLR_UI.prototype.set_url_query = function () {

    // query: keep it, or add one empty to go to search page on click
    var current_url = new Url(this.url);

    var current_query = this.get_url_query(this.url);
    delete current_url.query[wp_localize_script_wpsolr_ui.SEARCH_PARAMETER_Q];
    delete current_url.query[wp_localize_script_wpsolr_ui.SEARCH_PARAMETER_S];
    current_url.query[this.query_parameter_name] = current_query;

    return current_url;
};


WPSOLR_UI.prototype.create_url = function (delay_in_ms) {
    this.debug("create_url", '');
    this.debugState();

    // Init the delay if undefined
    this.delay_in_ms = delay_in_ms || 0;

    var current_url = this.set_url_query();

    // Call child
    this._create_url(current_url);

    // Swap current base url with target base url
    target_url = new Url(this.query_page);
    target_url.query = current_url.query;
    this.url = target_url.toString();

    // Load the url with a delay. Reset the timer first.
    if (this.timer_handle) {
        clearTimeout(this.timer_handle);
    }
    this.timer_handle = setTimeout(this.timer.bind(this), this.delay_in_ms);


    this.debugState();
};

WPSOLR_UI.prototype.timer = function () {
    this.debug("timer", this.delay_in_ms);

    // Display loaders on each facet
    jQuery("." + this.ui_id + " ul").addClass("wpsolr_loader");
    //jQuery("select." + this.ui_id).addClass("wpsolr_loader");

    window.location.href = this.url;
}

/**
 * Class WPSOLR_Sorts used by WPSOLR Widgets
 */
function WPSOLR_Sorts(groups_sort_id) {
    console.log("WPSOLR_Sorts constructor");

    this.groups_sort_id = groups_sort_id;
    this.sort = "";
}

WPSOLR_Sorts.prototype = new WPSOLR_UI();
WPSOLR_Sorts.prototype.constructor = WPSOLR_Sorts;


WPSOLR_Sorts.prototype.debugState = function () {
    WPSOLR_UI.prototype.debugState.call(this);

    console.log("  ++ sort: " + JSON.stringify(this.sort));
    console.log("  ++ url: " + JSON.stringify(this.url));
};

WPSOLR_Sorts.prototype._clear = function () {
    this.debug("_clear", '');
    this.debugState();

    this.sort = "";

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

WPSOLR_Sorts.prototype._create_url = function (current_url) {

    if (this.sort) {
        current_url.query["wpsolr_sort"] = this.sort;
    } else {
        delete current_url.query["wpsolr_sort"];
    }

    current_url.query["wpsolr_sorts_group"] = this.groups_sort_id;

};

/**
 * Class WPSOLR_Facets used by WPSOLR Widgets
 */
function WPSOLR_Facets() {
    console.log("WPSOLR_Facets constructor");

    this.groups_facet_id = "";
    this.lastFacetSelected = {};
    this.facets = {};
    this.facets.field = [];
    this.facets.range = [];
}

WPSOLR_Facets.prototype = new WPSOLR_UI();
WPSOLR_Facets.prototype.constructor = WPSOLR_Facets;

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

WPSOLR_Facets.prototype.debugState = function () {
    WPSOLR_UI.prototype.debugState.call(this)

    console.log("  ++ facets: " + JSON.stringify(this.facets));
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

WPSOLR_Facets.prototype._clear = function () {

    this.facets = {};
    this.facets.field = [];
    this.facets.range = [];

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

WPSOLR_Facets.prototype._create_url = function (current_url) {

    var fq_index = 0;

    // delete all wpsolr_fq parameters
    for (i = 0; ; i++) {
        if (current_url.query["wpsolr_fq" + "[" + i + "]"]) {
            delete current_url.query["wpsolr_fq" + "[" + i + "]"];
            this.debug('delete from url', "wpsolr_fq" + "[" + i + "]");
        } else {
            break;
        }
    }

    // Add field parameters
    var facets = this.facets.field || [];
    for (i = 0; i < facets.length; i++) {
        current_url.query["wpsolr_fq" + "[" + fq_index + "]"] = facets[i].facet_id + ":" + facets[i].facet_value;
        fq_index++;
    }

    // Add range parameters
    var facets = this.facets.range || [];
    for (i = 0; i < facets.length; i++) {
        current_url.query["wpsolr_fq" + "[" + fq_index + "]"] = facets[i].facet_id + ":" + "[" + facets[i].facet_value + " TO " + (facets[i].range_sup) + "]";
        fq_index++;
    }


    current_url.query["wpsolr_facets_group"] = this.groups_facet_id;

    // Last facet selected
    if (this.lastFacetSelected['facet_id'] != undefined) {
        current_url.query["wpsolr_last_facet_selected"] = this.lastFacetSelected['facet_id'];
    }

};


// Global array of WPSOLR UIs
var wpsolr_facets = [];
var wpsolr_sorts = [];
