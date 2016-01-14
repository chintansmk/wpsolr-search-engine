<?php

namespace wpsolr\solr;

class WPSOLR_AbstractSolrClient {

	// Timeout in seconds when calling Solr
	const DEFAULT_SOLR_TIMEOUT_IN_SECOND = 30;

	// Solr operators
	const QUERY_OPERATOR_AND = 'AND';
	const QUERY_OPERATOR_OR = 'OR';

	public $solarium_client;
	protected $solarium_config;

	// Indice of the Solr index configuration in admin options
	protected $index_indice;


	// Array of active extension objects
	protected $wpsolr_extensions;

}
