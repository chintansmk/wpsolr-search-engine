<?php

namespace wpsolr\extensions;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\acf\WPSOLR_Plugin_Acf;
use wpsolr\extensions\components\WPSOLR_Options_Components;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\groups\WPSOLR_Plugin_Groups;
use wpsolr\extensions\importexport\WPSOLR_Options_ImportExports;
use wpsolr\extensions\indexes\WPSOLR_Options_Indexes;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\extensions\managedservers\WPSOLR_ManagedServers;
use wpsolr\extensions\polylang\WPSOLR_Plugin_Polylang;
use wpsolr\extensions\queries\WPSOLR_Options_Query;
use wpsolr\extensions\resultsheaders\WPSOLR_Options_Result_Header;
use wpsolr\extensions\resultspagenavigations\WPSOLR_Options_Result_Page_Navigation;
use wpsolr\extensions\resultsrows\WPSOLR_Options_Result_Row;
use wpsolr\extensions\s2member\WPSOLR_Plugin_S2member;
use wpsolr\extensions\schemas\WPSOLR_Options_Schemas;
use wpsolr\extensions\searchform\WPSOLR_Options_Search_Form;
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;
use wpsolr\extensions\types\WPSOLR_Plugin_Types;
use wpsolr\extensions\woocommerce\WPSOLR_Plugin_Woocommerce;
use wpsolr\extensions\wpml\WPSOLR_Plugin_Wpml;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\ui\WPSOLR_UI;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Base class for all WPSOLR extensions.
 * An extension is an encapsulation of a plugin that (if configured) might extend some features of WPSOLR.
 */
abstract class WPSOLR_Extensions {

	// Group name in error messages
	const GROUP_NAME = 'Extension';

	// Default admin form file name of the extension
	const CONST_DEFAULT_FORM_FILE = 'admin_options.inc.php';

	// Default groups template file name
	const CONST_DEFAULT_GROUPS_TEMPLATE_FILE = 'groups.inc.php';

	/*
    * Private constants
    */
	const _CONFIG_EXTENSION_DIRECTORY = 'config_extension_directory';
	const _CONFIG_EXTENSION_CLASS_NAME = 'config_extension_class_name';
	const _CONFIG_PLUGIN_CLASS_NAME = 'config_plugin_class_name';
	const _CONFIG_PLUGIN_FUNCTION_NAME = 'config_plugin_function_name';
	const _CONFIG_PLUGIN_CONSTANT_NAME = 'config_plugin_constant_name';
	const _CONFIG_EXTENSION_FILE_PATH = 'config_extension_file_path';
	const _CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH = 'config_extension_admin_options_file_path';
	const _CONFIG_OPTIONS = 'config_extension_options';
	const _CONFIG_OPTIONS_DATA = 'data';
	const _CONFIG_OPTIONS_GROUP = 'options_group'; // Options group settings
	const _CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME = 'is_active_field';
	const _CONFIG_OPTIONS_PLUGIN_VERSION = 'plugin_version';
	const _CONFIG_OPTIONS_PLUGIN_NAME = 'plugin_name';
	const _CONFIG_OPTIONS_PLUGIN_LINK = 'plugin_link';
	const _CONFIG_OPTIONS_PLUGIN_TITLE = 'plugin_title';
	const _CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN = 'translation_domain';
	const _CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS = 'translation_fields';


	const _SOLR_OR_OPERATOR = ' OR ';
	const _SOLR_AND_OPERATOR = ' AND ';

	const _METHOD_CUSTOM_QUERY = 'set_custom_query';

	/*
	 * Public constants
	 */

	// Option: localization
	const EXTENSION_INDEXES = WPSOLR_Options_Indexes::class;

	// Option: localization
	const OPTION_LOCALIZATION = 'Localization';

	// Extension: Groups
	const EXTENSION_GROUPS = 'Groups';

	// Extension: s2member
	const EXTENSION_S2MEMBER = 'S2Member';

	// Extension: WPML
	const EXTENSION_WPML = 'WPML';

	// Extension: POLYLANG
	const EXTENSION_POLYLANG = 'Polylang';

	// Extension: qTranslate X
	const EXTENSION_QTRANSLATEX = 'qTranslate X';

	// Extension: WooCommerce
	const EXTENSION_WOOCOMMERCE = 'WooCommerce';

	// Extension: Advanced Custom Fields
	const EXTENSION_ACF = 'ACF';

	// Extension: Types
	const EXTENSION_TYPES = 'Types';

	// Extension: Gotosolr hosting
	const OPTION_MANAGED_SOLR_SERVERS = 'Managed Solr Servers';

	// Extension: Facets
	const OPTION_FACETS = WPSOLR_Options_Facets::class;

	// Extension: Solr Fields
	const OPTION_SCHEMAS = 'schemas';

	// Extension: Sort
	const OPTION_SORTS = WPSOLR_Options_Sorts::class;

	// Extension: Import/Export
	const OPTION_IMPORTEXPORT = 'import_export';

	// Extension: layouts
	const OPTION_LAYOUTS = 'layouts';

	// Extension: components
	const OPTION_COMPONENTS = WPSOLR_Options_Components::class;

	// Extension: Results rows
	const OPTION_RESULTS_ROWS = 'results_rows';

	// Extension: Results headers
	const OPTION_RESULTS_HEADERS = 'results_headers';

	// Extension: Results page navigation
	const OPTION_RESULTS_PAGE_NAVIGATIONS = 'results_page_navigations';

	// Extension: Results headers
	const OPTION_SEARCH_FORMS = 'search_forms';

	// Extension: queries
	const OPTION_QUERIES = 'queries';

	/*
	 * Extensions configuration
	 */
	private static $extensions_array = [
		self::EXTENSION_WOOCOMMERCE           =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Woocommerce::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => 'WooCommerce',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'woocommerce/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'woocommerce/WPSOLR_Plugin_Woocommerce.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'woocommerce/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'WooCommerce',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(>= 2.4.10)',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://wordpress.org/plugins/woocommerce/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_WPML                  =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Wpml::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => 'SitePress',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'wpml/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'wpml/WPSOLR_Plugin_Wpml.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'wpml/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'WPML',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(WPML Multilingual CMS > 3.1.6) ',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://wpml.org/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_PLUGIN_WPML,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_POLYLANG              =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Polylang::CLASS,
				self::_CONFIG_PLUGIN_FUNCTION_NAME              => 'pll_get_post_language',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'polylang/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'polylang/WPSOLR_Plugin_Polylang.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'polylang/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Polylang',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(>= 1.8.1)',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://polylang.wordpress.com/documentation/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_PLUGIN_POLYLANG,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_ACF                   =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Acf::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => 'acf',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'acf/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'acf/WPSOLR_Plugin_Acf.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'acf/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Advanced Custom Fields',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(>= 4.4.3)',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://wordpress.org/plugins/advanced-custom-fields/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_PLUGIN_ACF,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_TYPES                 =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Types::CLASS,
				self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'WPCF_VERSION',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'types/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'types/WPSOLR_Plugin_Types.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'types/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Types',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(>= 1.8.10)',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://wordpress.org/plugins/types/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_PLUGIN_TYPES,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_INDEXES               =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Indexes::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Indexes::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'indexes/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'indexes/WPSOLR_Options_Indexes.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'indexes/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_INDEXES,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_LOCALIZATION             =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Localization::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Localization::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'localization/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'localization/WPSOLR_Localization.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'localization/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_LOCALIZATION,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_GROUPS                =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Groups::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => 'Groups_WordPress',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'groups/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'groups/WPSOLR_Plugin_Groups.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'groups/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Groups',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(>= 1.4.13)',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://wordpress.org/plugins/groups/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_PLUGIN_GROUPS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::EXTENSION_S2MEMBER              =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_S2member::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => 'c_ws_plugin__s2member_utils_s2o',
				self::_CONFIG_EXTENSION_DIRECTORY               => 's2member/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 's2member/WPSOLR_Plugin_S2member.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 's2member/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 's2Member',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '(>= 150203)',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => 'https://wordpress.org/plugins/s2member/',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_OPTION::OPTION_PLUGIN_S2MEMBER,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		/*
		self::EXTENSION_QTRANSLATEX       =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Qtranslatex::CLASS,
				self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'QTRANSLATE_FILE',
				self::_CONFIG_EXTENSION_DIRECTORY               => 'qtranslate-x/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'qtranslate-x/WPSOLR_Plugin_Qtranslatex.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'qtranslate-x/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => 'wdm_solr_extension_qtranslatex_data',
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		*/
		self::OPTION_MANAGED_SOLR_SERVERS     =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_ManagedServers::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_ManagedServers::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'managedservers/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'managedservers/WPSOLR_ManagedServers.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'managedservers/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => 'wdm_solr_extension_managed_solr_servers_data',
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_FACETS                   =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Facets::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Facets::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'facets/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'facets/WPSOLR_Options_Facets.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'facets/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Facet',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => 'Manage your facets',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => 'wpsolr facets',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [
					[
						'name'             => WPSOLR_Options_Facets::FACET_FIELD_LABEL_FRONT_END,
						'parent_name'      => WPSOLR_Options_Facets::FACET_FIELD_FACETS,
						'translation_name' => 'Facet Label on front-end',
						'is_multiline'     => false
					],
					[
						'name'             => WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST,
						'parent_name'      => WPSOLR_Options_Facets::FACET_FIELD_FACETS,
						'translation_name' => 'First facet Label',
						'is_multiline'     => false
					],
					[
						'name'             => WPSOLR_Options_Facets::FACET_FIELD_LABEL,
						'parent_name'      => WPSOLR_Options_Facets::FACET_FIELD_FACETS,
						'translation_name' => 'Middle facet Label',
						'is_multiline'     => false
					],
					[
						'name'             => WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST,
						'parent_name'      => WPSOLR_Options_Facets::FACET_FIELD_FACETS,
						'translation_name' => 'Last facet Label',
						'is_multiline'     => false
					],
					[
						'name'             => WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_RANGES,
						'parent_name'      => WPSOLR_Options_Facets::FACET_FIELD_FACETS,
						'translation_name' => 'Uneven Range facet Labels',
						'is_multiline'     => true
					]
				],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_FACETS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_SCHEMAS                  =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Schemas::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Schemas::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'schemas/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'schemas/WPSOLR_Options_Schemas.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'schemas/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Schema',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => 'Manage schemas used by your Solr indexes',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_SCHEMAS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_SORTS                    =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Sorts::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Sorts::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'sorts/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'sorts/WPSOLR_Options_Sorts.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'sorts/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Sort',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => 'Manage sorts',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => 'wpsolr sorts',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [
					[
						'name'             => WPSOLR_Options_Sorts::SORT_FIELD_LABEL,
						'parent_name'      => WPSOLR_Options_Sorts::FORM_FIELD_SORTS,
						'translation_name' => 'Sort Label',
						'is_multiline'     => false
					]
				],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_SORTS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_IMPORTEXPORT             =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_ImportExports::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_ImportExports::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'importexport/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'importexport/WPSOLR_Options_ImportExports.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'importexport/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_IMPORTEXPORT,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_LAYOUTS                  =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Layouts::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Layouts::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'layouts/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'layouts/WPSOLR_Options_Layouts.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'layouts/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_LAYOUTS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_COMPONENTS               =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Components::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Components::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'components/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'components/WPSOLR_Options_Components.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'components/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => 'wpsolr components',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [
					[
						'name'             => WPSOLR_UI::FORM_FIELD_TITLE,
						'translation_name' => 'Component title',
						'parent_name'      => '*',
						'is_multiline'     => false
					]
				],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_COMPONENTS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_RESULTS_ROWS             =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Result_Row::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Result_Row::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'resultsrows/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'resultsrows/WPSOLR_Options_Result_Row.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'resultsrows/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_RESULTS_ROWS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_RESULTS_HEADERS          =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Result_Header::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Result_Header::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'resultsheaders/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'resultsheaders/WPSOLR_Options_Result_Header.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'resultsheaders/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_RESULTS_HEADERS,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_RESULTS_PAGE_NAVIGATIONS =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Result_Page_Navigation::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Result_Page_Navigation::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'resultspagenavigations/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'resultspagenavigations/WPSOLR_Options_Result_Page_Navigation.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'resultspagenavigations/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_RESULTS_PAGE_NAVIGATION,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_SEARCH_FORMS             =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Search_Form::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Search_Form::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'searchform/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'searchform/WPSOLR_Options_Search_Form.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'searchform/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => '',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_SEARCH_FORM,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			],
		self::OPTION_QUERIES                  =>
			[
				self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Options_Query::CLASS,
				self::_CONFIG_PLUGIN_CLASS_NAME                 => WPSOLR_Options_Query::CLASS,
				self::_CONFIG_EXTENSION_DIRECTORY               => 'queries/',
				self::_CONFIG_EXTENSION_FILE_PATH               => 'queries/WPSOLR_Options_Query.php',
				self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'queries/admin_options.inc.php',
				self::_CONFIG_OPTIONS_PLUGIN_NAME               => 'Query',
				self::_CONFIG_OPTIONS_PLUGIN_TITLE              => 'Manage your Solr queries',
				self::_CONFIG_OPTIONS_PLUGIN_VERSION            => '',
				self::_CONFIG_OPTIONS_PLUGIN_LINK               => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => '',
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS => [ ],
				self::_CONFIG_OPTIONS                           => [
					self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_QUERIES,
					self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
				]
			]
	];

	// Current extension name
	protected $extension;

	/*
	 * Array of active extension objects
	 */
	private $extension_objects = array();

	/**
	 * Factory to load extensions
	 * @return WPSOLR_Extensions
	 */
	static function global_object( $extension ) {

		$result            = new self::$extensions_array[ $extension ][ self::_CONFIG_EXTENSION_CLASS_NAME ]( $extension );
		$result->extension = $extension;

		if ( empty( $result->extension ) ) {
			throw new \Exception( sprintf( 'Extension %s constructor did not set the extension property.', self::$extensions_array[ $extension ][ self::_CONFIG_EXTENSION_CLASS_NAME ] ) );
		}

		$result->post_constructor();

		return $result;
	}

	/**
	 * Post constructor.
	 */
	abstract protected function post_constructor();

	/**
	 * Include a file with a set of parameters.
	 * All other parameters are not passed, because they are out of the function scope.
	 *
	 * @param $pg File to include
	 * @param $vars Parameters to pass to the file
	 */
	public static function require_with( $pg, $vars = null ) {

		if ( isset( $vars ) ) {
			extract( $vars );
		}

		require $pg;
	}

	/**
	 * Returns all extensions.
	 *
	 * @return array[string] [['id' => 'extension1', 'is_active' => true], ['id' => 'extension2', 'is_active' => false]]
	 */
	public static function get_extensions() {
		$results = array();

		foreach ( self::$extensions_array as $key => $class ) {

			$results[] = [
				'id'                                            => $key,
				'name'                                          => $class[ self::_CONFIG_OPTIONS_PLUGIN_NAME ],
				'is_active'                                     => self::is_extension_to_be_loaded( $key, false ),
				self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN => $class[ self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN ]
			];
		}

		return $results;
	}

	/**
	 * Include the admin options extension file.
	 *
	 * @param string $extension
	 *
	 * @return bool
	 */
	public static function require_once_wpsolr_extension_admin_options( $extension ) {

		require_once self::get_extension_admin_form_file( $extension );
	}

	/**
	 * Include the admin options extension file.
	 *
	 * @param string $extension
	 *
	 * @return bool
	 */
	public static function get_extension_admin_form_file( $extension ) {

		// Configuration array of $extension
		$extension_config_array = self::$extensions_array[ $extension ];

		return plugin_dir_path( __FILE__ ) . $extension_config_array[ self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH ];
	}

	/**
	 * Is the extension's plugin active ?
	 *
	 * @param $extension
	 *
	 * @return bool
	 */
	public static function is_plugin_activated( $extension ) {

		// Configuration array of $extension
		$extension_config_array = self::$extensions_array[ $extension ];

		// Is extension's plugin installed and activated ?
		if ( isset( $extension_config_array[ self::_CONFIG_PLUGIN_CLASS_NAME ] ) ) {

			return class_exists( $extension_config_array[ self::_CONFIG_PLUGIN_CLASS_NAME ] );

		} else if ( isset( $extension_config_array[ self::_CONFIG_PLUGIN_FUNCTION_NAME ] ) ) {

			return function_exists( $extension_config_array[ self::_CONFIG_PLUGIN_FUNCTION_NAME ] );

		} else if ( isset( $extension_config_array[ self::_CONFIG_PLUGIN_CONSTANT_NAME ] ) ) {

			return defined( $extension_config_array[ self::_CONFIG_PLUGIN_CONSTANT_NAME ] );
		}

		return false;
	}

	public static function update_custom_field_capabilities( $custom_field_name ) {

		// Get options contening custom fields
		$array_wdm_solr_form_data = WPSOLR_Service_Wordpress::get_option( 'wdm_solr_form_data', null );

		// is extension active checked in options ?
		$extension_is_active = self::is_extension_option_activate( self::EXTENSION_GROUPS );


		if ( $extension_is_active
		     && ! self::get_custom_field_capabilities( $custom_field_name )
		     && isset( $array_wdm_solr_form_data )
		     && isset( $array_wdm_solr_form_data['cust_fields'] )
		) {

			$custom_fields = explode( ',', $array_wdm_solr_form_data['cust_fields'] );

			if ( ! isset( $custom_fields[ $custom_field_name ] ) ) {

				$custom_fields[ $custom_field_name ] = $custom_field_name;

				$custom_fields_str = implode( ',', $custom_fields );

				$array_wdm_solr_form_data['cust_fields'] = $custom_fields_str;

				update_option( 'wdm_solr_form_data', $array_wdm_solr_form_data );
			}
		}
	}

	/**
	 * Is the extension activated ?
	 *
	 * @param string $extension
	 *
	 * @return bool
	 */
	public static function is_extension_option_activate( $extension ) {

		// Configuration array of $extension
		$extension_config_array = self::$extensions_array[ $extension ];

		// Configuration not set, return
		if ( ! is_array( $extension_config_array ) ) {
			return false;
		}

		// Configuration options array: setup in extension options tab admin
		$extension_options_array = WPSOLR_Service_Wordpress::get_option( $extension_config_array[ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_DATA ], null );

		// Configuration option says that user did not choose to active this extension: return
		if ( isset( $extension_options_array ) && isset( $extension_options_array[ $extension_config_array[ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME ] ] ) ) {
			return true;
		}

		return false;
	}

	public
	static function get_custom_field_capabilities(
		$custom_field_name
	) {

		// Get custom fields selected for indexing
		$array_options     = WPSOLR_Service_Wordpress::get_option( 'wdm_solr_form_data', null );
		$array_cust_fields = explode( ',', $array_options['cust_fields'] );

		if ( ! is_array( $array_cust_fields ) ) {
			return false;
		}

		return false !== array_search( $custom_field_name, $array_cust_fields );
	}


	/*
	 * If extension is active, check its custom field in indexing options
	 */

	/**
	 * Include the extension file.
	 * If called from admin, always do.
	 * Else, do it if the extension options say so, and the extension's plugin is activated.
	 *
	 * @param string $extension
	 * @param bool $is_admin
	 *
	 * @return bool
	 */
	public static function is_extension_to_be_loaded( $extension, $is_admin = false ) {

		// Configuration array of $extension
		$extension_config_array = self::$extensions_array[ $extension ];

		if ( $is_admin ) {
			return true;
		}

		// Configuration not set, return
		if ( ! is_array( $extension_config_array ) ) {
			return false;
		}

		// Configuration options array: setup in extension options tab admin
		$extension_options_array = get_option( $extension_config_array[ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_DATA ] );

		// Configuration option says that user did not choose to active this extension: return
		if ( ! isset( $extension_options_array ) || ! isset( $extension_options_array[ $extension_config_array[ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME ] ] ) ) {
			return false;
		}

		// Is extension's plugin installed and activated ?
		$result = self::is_plugin_activated( $extension );

		return $result;
	}

	/**
	 * Get the option data of an extension
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_data( $extension, $default = false ) {

		return WPSOLR_Service_Wordpress::get_option( self::get_option_name( $extension ), $default );
	}


	/**
	 * Get the option name of an extension
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_name( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_DATA ];
	}

	/**
	 * Get the version of a plugin
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_plugin_version( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS_PLUGIN_VERSION ];
	}

	/**
	 * Get the title of a plugin
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_plugin_title( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS_PLUGIN_TITLE ];
	}

	/**
	 * Get the name of a plugin
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_plugin_name( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS_PLUGIN_NAME ];
	}

	/**
	 * Get the url of a plugin website
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_plugin_link( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS_PLUGIN_VERSION ];
	}

	/**
	 * Get the settings name of an extension
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_options_group_name( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_GROUP ];
	}

	/**
	 * Set the option value of an extension
	 *
	 * @param $extension
	 * @param $option_value
	 *
	 * @return mixed
	 */
	public static function set_option_data( $extension, $option_value ) {

		return update_option( self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS ][ self::_CONFIG_OPTIONS_DATA ], $option_value );
	}

	/**
	 * Get the extension template path
	 *
	 * @param $extension
	 *
	 * @param $template_file_name
	 *
	 * @return string Template file path
	 *
	 */
	public static function get_option_template_file( $extension, $template_file_name ) {

		return plugin_dir_path( __FILE__ ) . self::$extensions_array[ $extension ][ self::_CONFIG_EXTENSION_DIRECTORY ] . 'templates/' . $template_file_name;
	}

	/**
	 * Get the extension file
	 *
	 * @param $extension
	 *
	 * @param $file_name
	 *
	 * @return string File path
	 *
	 */
	public static function get_option_file( $extension, $file_name ) {

		return plugin_dir_path( __FILE__ ) . self::$extensions_array[ $extension ][ self::_CONFIG_EXTENSION_DIRECTORY ] . $file_name;
	}

	/*
	 * Templates methods
	 */

	public static function extract_form_data( $is_submit, $fields ) {

		$form_data = array();

		$is_error = false;

		foreach ( $fields as $key => $field ) {

			$value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : $field['default_value'];
			$error = '';

			// Check format errors id it is a form post (submit)
			if ( $is_submit ) {

				$error = '';

				if ( isset( $field['can_be_empty'] ) && ! $field['can_be_empty'] ) {
					$error = empty( $value ) ? 'This field cannot be empty.' : '';
				}

				if ( isset( $field['is_email'] ) ) {
					$error = is_email( $value ) ? '' : 'This does not look like an email address.';
				}
			}

			$is_error = $is_error || ( '' != $error );

			$form_data[ $key ] = array( 'value' => $value, 'error' => $error );
		}

		// Is there an error in any field ?
		$form_data['is_error'] = $is_error;

		return $form_data;
	}


	public static function register_settings() {

		foreach ( self::$extensions_array as $key => $class ) {

			$extension_name = self::get_option_name( $key );

			register_setting( $extension_name, $extension_name );
		}

	}


	/**
	 * Display admin form.
	 * Override in children
	 *
	 * @param array $plugin_parameters Parameters set by the plugin
	 */
	public function output_form( $form_file, $plugin_parameters = [ ]
	) {

		self::require_with(
			empty( $form_file ) ? $this->get_default_admin_form_file() : $form_file,
			array_merge(
				[
					'options_name'     => self::get_option_name( $this->extension ),
					'is_plugin_active' => self::is_plugin_activated( $this->extension ),
					'plugin_name'      => self::get_option_plugin_name( $this->extension ),
					'plugin_link'      => self::get_option_plugin_link( $this->extension ),
					'plugin_version'   => self::get_option_plugin_version( $this->extension ),
					'plugin_title'     => self::get_option_plugin_title( $this->extension )
				],
				$plugin_parameters
			)
		);
	}

	/**
	 *    Absolute default admin form file of the extension
	 */
	protected function get_default_admin_form_file() {

		$class_info               = new \ReflectionClass( $this );
		$directory_of_child_class = dirname( $class_info->getFileName() );

		$file = $directory_of_child_class . '/' . self::CONST_DEFAULT_FORM_FILE;

		return $file;
	}

	/**
	 *    Absolute groups template file
	 */
	protected function get_groups_template_file() {

		$directory = __DIR__;

		$file = $directory . '/' . self::CONST_DEFAULT_GROUPS_TEMPLATE_FILE;

		return $file;
	}


	/**
	 *    Get path of a class
	 */
	public static function get_class_path( $class ) {

		$class_info               = new \ReflectionClass( $class );
		$directory_of_child_class = dirname( $class_info->getFileName() );

		return $directory_of_child_class;
	}

	/**
	 * Get groups
	 *
	 * @return array Groups
	 */
	public function get_groups() {
		die( 'get_groups() not implemented.' );
	}

	/**
	 * Get group
	 *
	 * @@param string $group_id
	 * @return array Group
	 */
	public function get_group( $group_id ) {

		$groups = $this->get_groups();

		if ( empty( $groups ) || empty( $groups[ $group_id ] ) ) {
			throw new WPSOLR_Exception( sprintf( '%s \'%s\' is unknown.', static::GROUP_NAME, $group_id ) );
		}

		return $groups[ $group_id ];
	}

	/**
	 * Clone the groups marked.
	 *
	 * @param $groups
	 */
	public function clone_some_groups() {

		$groups = $this->get_groups();

		foreach ( $groups as $group_uuid => &$group ) {

			if ( ! empty( $group['is_to_be_cloned'] ) ) {

				unset( $group['is_to_be_cloned'] );

				// Clone the group
				$clone              = $group;
				$result_cloned_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
				$clone['name']      = 'Clone of ' . $clone['name'];

				$groups[ $result_cloned_uuid ] = $clone;
			}
		}

		return $groups;
	}

	/**
	 * Format a string translation
	 *
	 * @param $field_name
	 * @param $text
	 * @param $domain
	 * @param $is_multiligne
	 *
	 * @param $name
	 *
	 * @return array
	 */
	protected function get_string_to_translate( $field_name, $text, $domain, $is_multiligne, $name ) {

		return [
			'name'          => $name,
			'text'          => $text,
			'domain'        => $domain,
			'is_multiligne' => $is_multiligne
		];
	}

	/**
	 * Get the translation domain of the plugin
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_plugin_translation_domain( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_DOMAIN ];
	}


	/**
	 * Get the translatable fields of the plugin
	 *
	 * @param $extension
	 *
	 * @return mixed
	 */
	public static function get_option_plugin_translated_fields( $extension ) {

		return self::$extensions_array[ $extension ][ self::_CONFIG_OPTIONS_PLUGIN_TRANSLATION_FIELDS ];
	}


	/**
	 * Get the strings to translate among the group data
	 * @return array
	 */
	public function extract_strings_to_translate() {

		$results = [ ];
		$domain  = self::get_option_plugin_translation_domain( get_class( $this ) );

		// Fields that can be translated and their definition
		$translated_fields = self::get_option_plugin_translated_fields( get_class( $this ) );

		if ( ! empty( $domain ) && count( $translated_fields ) > 0 ) {

			foreach ( $this->get_groups() as $group_name => $group ) {

				$this->extract_strings_to_translate_for_level( $domain, $translated_fields, [ ], $group, $results );
			}
		}

		return $results;
	}


	/**
	 * Get the strings to translate among the data
	 *
	 * @param $domain
	 * @param $translated_fields
	 * @param $level_names
	 * @param $level_value
	 * @param $results
	 *
	 * @return array
	 */
	public function extract_strings_to_translate_for_level( $domain, $translated_fields, $level_names, $level_value, &$results ) {

		if ( ! is_array( $level_value ) || count( $level_value ) <= 0 ) {
			// Level must be a not empty array
			return;
		}

		foreach ( $level_value as $field_name => $field_value ) {

			if ( ! is_array( $field_value ) ) {

				foreach ( $translated_fields as $translatable ) {

					if ( ( $translatable['name'] == $field_name ) &&
					     ( ( empty( $translatable['parent_name'] ) && empty( $level_names ) ) ||
					       ( ! empty( $translatable['parent_name'] ) && ( '*' == $translatable['parent_name'] || in_array( $translatable['parent_name'], $level_names ) ) ) )
					) {

						$results[] = $this->get_string_to_translate(
							$translatable['name'], $field_value, $domain, $translatable['is_multiline'], $translatable['translation_name']
						);

					}

				}

			} else {

				// Add next level to already visited levels
				array_push( $level_names, $field_name );

				// Call level below
				$this->extract_strings_to_translate_for_level( $domain, $translated_fields, $level_names, $field_value, $results );
			}

		}

	}

	/**
	 * Get the strings to translate among the group data of all extensions translatable.
	 *
	 * @return array Translations
	 */
	public static function extract_strings_to_translate_for_all_extensions() {

		$translations = [ ];
		foreach ( WPSOLR_Global::getTranslatedExtensions() as $extension ) {

			$translation = $extension->extract_strings_to_translate();
			if ( count( $translation ) > 0 ) {
				$translations = array_merge( $translations, $translation );
			}

		}

		if ( count( $translations ) > 0 ) {

			// Translate
			do_action( WPSOLR_Filters::WPSOLR_ACTION_TRANSLATION_REGISTER_STRINGS,
				[
					'translations' => $translations
				]
			);
		}

	}
}