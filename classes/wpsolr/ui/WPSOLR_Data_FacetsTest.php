<?php

namespace wpsolr\ui;

use Mockery as m;
use wpsolr\WPSOLR_Filters;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Data_FacetsTest extends WPSOLR_Unit_Test {

	public function testAllEmptyArray() {

		$result = WPSOLR_Data_Facets::format_data( array(), array(), array() );

		$this->assertEquals(
			array(
				'facets'                      => array(),
				'has_facet_elements_selected' => false
			),
			$result
		);

	}


	/**
	 * @group toBeFixed
	 */
	public function testFacetIdNotStripped() {

		$this->wpsolr_mock_apply_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_FACET_NAME, m::any(), m::any(), 1, null );

		$result = WPSOLR_Data_Facets::format_data( [ ], [ 'custom1_str' ], [
			'custom1_str' =>
				[
					[
						'1',
						1,
					],
					[
						'2',
						2,
					],
				],
		], array() );


		// 'custom1_str' should not be stripped in 'custom1'
		$this->assertEquals(
			[
				'facets'                      =>
					[
						[
							'items' =>
								[
									[
										'name'     => '1',
										'count'    => 1,
										'selected' => false,
									],
									[
										'name'     => '2',
										'count'    => 2,
										'selected' => false,
									],
								],
							'id'    => 'custom1_str',
							'name'  => 'Custom1',
						],
					],
				'has_facet_elements_selected' => false,
			],
			$result
		);

	}

	/**
	 * @group toBeFixed
	 */
	public function test4facetsCallsFilter4Times() {

		$this->wpsolr_mock_apply_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_FACET_NAME, m::any(), m::any(), 4, null );

		$result = WPSOLR_Data_Facets::format_data( array(),
			[
				'type',
				'author',
				'categories',
				'tags'
			],
			[
				'type'       =>
					[
						[
							'post',
							40,
						],
						[
							'page',
							34,
						],
					],
				'author'     =>
					[
						[
							'admin',
							74,
						],
					],
				'categories' =>
					[
						[
							'Blogs',
							17,
						],
						[
							'Blog',
							13,
						],
						[
							'Uncategorized',
							10,
						],
					],
				'tags'       =>
					[
						[
							'marque blanche Solr',
							1,
						],
						[
							'revendeur Solr',
							1,
						],
						[
							'solrware.com',
							1,
						],
					],
			] );


		$this->assertEquals(
			[
				'facets'                      =>
					[
						[
							'items' =>
								[

									[
										'name'     => 'post',
										'count'    => 40,
										'selected' => false,
									],
									[
										'name'     => 'page',
										'count'    => 34,
										'selected' => false,
									],
								],
							'id'    => 'type',
							'name'  => 'Type',
						],
						[
							'items' =>
								[
									[
										'name'     => 'admin',
										'count'    => 74,
										'selected' => false,
									],
								],
							'id'    => 'author',
							'name'  => 'Author',
						],
						[
							'items' =>
								[
									[
										'name'     => 'Blogs',
										'count'    => 17,
										'selected' => false,
									],
									[
										'name'     => 'Blog',
										'count'    => 13,
										'selected' => false,
									],
									[
										'name'     => 'Uncategorized',
										'count'    => 10,
										'selected' => false,
									],
								],
							'id'    => 'categories',
							'name'  => 'Categories',
						],
						[
							'items' =>
								[
									[
										'name'     => 'marque blanche Solr',
										'count'    => 1,
										'selected' => false,
									],
									[
										'name'     => 'revendeur Solr',
										'count'    => 1,
										'selected' => false,
									],
									[
										'name'     => 'solrware.com',
										'count'    => 1,
										'selected' => false,
									],
								],
							'id'    => 'tags',
							'name'  => 'Tags',
						],
					],
				'has_facet_elements_selected' => false,
			],
			$result
		);

	}

}
