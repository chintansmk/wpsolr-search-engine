<?php

namespace wpsolr\solr;

class WPSOLR_AbstractSolrClient {

	public $solarium_client;
	protected $solarium_config;

	// Indice of the Solr index configuration in admin options
	protected $index_indice;


	// Array of active extension objects
	protected $wpsolr_extensions;

}
