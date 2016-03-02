<?php

namespace classes\wpsolr\extensions;


use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_ExtensionsTest extends WPSOLR_Unit_Test {

	public function testget_strings_to_translate_level() {

		$extension = new WPSOLR_Options_Facets();

		// Nothing
		$results = [ ];
		$extension->extract_strings_to_translate_for_level( 'wpsolr', [ ], '', '', $results );
		$this->assertEquals( [ ], $results );

		// No fields to translate
		$results = [ ];
		$extension->extract_strings_to_translate_for_level( 'wpsolr', [ ], '', [ 'field1' => 'value1' ], $results );
		$this->assertEquals( [ ], $results );

		// No data to translate
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => 'parent',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			'',
			[ ],
			$results );
		$this->assertEquals( [ ], $results );

		// Translation level 0, 1 field: ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			'',
			[ 'field1' => 'value1' ],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				]
			]
			, $results );

		// Translation level 0, 2 fields: ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				],
				[
					'name'             => 'field2',
					'translation_name' => 'field2 translation name',
					'is_multiline'     => false
				]
			],
			'',
			[ 'field1' => 'value1', 'field2' => 'value2' ],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				],
				[
					'name'          => 'field2 translation name',
					'text'          => 'value2',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				]
			]
			, $results );

		// Translation level 0: ko
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => 'level1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			[ ],
			[ 'field1' => 'value1' ],
			$results );
		$this->assertEquals(
			[ ]
			, $results );

		// Translation level 1: ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => 'level1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			[ ],
			[ 'level1' => [ 'field1' => 'value1' ] ],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				]
			]
			, $results );

		// Translation level 1: ko
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => 'level1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			[ ],
			[ 'level2' => [ 'field1' => 'value1' ] ],
			$results );
		$this->assertEquals(
			[ ]
			, $results );

		// Translation level 2: ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => 'level2',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			[ ],
			[ 'level1' => [ 'level2' => [ 'field1' => 'value1' ] ] ],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				]
			]
			, $results );

		// Translation level 2, not directly under level 0: ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => 'level1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			[ ],
			[ 'level1' => [ 'level2' => [ 'field1' => 'value1' ] ] ],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				]
			]
			, $results );

		// Translation level 2, not directly under level 0, with '*': ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'parent_name'      => '*',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => false
				]
			],
			[ ],
			[ 'level1' => [ 'level2' => [ 'field1' => 'value1' ] ] ],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				]
			]
			, $results );

		// Multi-levels: ok
		$results = [ ];
		$extension->extract_strings_to_translate_for_level(
			'wpsolr',
			[
				[
					'name'             => 'field1',
					'translation_name' => 'field1 translation name',
					'is_multiline'     => true
				],
				[
					'name'             => 'field2',
					'translation_name' => 'field2 translation name',
					'is_multiline'     => true
				],
				[
					'name'             => 'field1',
					'parent_name'      => 'level1',
					'translation_name' => 'field11 translation name',
					'is_multiline'     => false
				],
				[
					'name'             => 'field12',
					'parent_name'      => 'level2',
					'translation_name' => 'field12 translation name',
					'is_multiline'     => false
				],
				[
					'name'             => 'field13',
					'parent_name'      => 'level2',
					'translation_name' => 'field13 translation name',
					'is_multiline'     => true
				],
				[
					'name'             => 'field14',
					'parent_name'      => 'level1', // not direct child
					'translation_name' => 'field14 translation name',
					'is_multiline'     => true
				]
			],
			[ ],
			[
				'field1' => 'value1',
				'field2' => 'value2',
				'level1' => [
					'field1' => 'value11', // same name as level 0
					'level2' => [
						'field12' => 'value12',
						'field13' => 'value13',
						'field14' => 'value14'
					]
				],

			],
			$results );
		$this->assertEquals(
			[
				[
					'name'          => 'field1 translation name',
					'text'          => 'value1',
					'domain'        => 'wpsolr',
					'is_multiligne' => true
				],
				[
					'name'          => 'field2 translation name',
					'text'          => 'value2',
					'domain'        => 'wpsolr',
					'is_multiligne' => true
				],
				[
					'name'          => 'field11 translation name',
					'text'          => 'value11',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				],
				[
					'name'          => 'field12 translation name',
					'text'          => 'value12',
					'domain'        => 'wpsolr',
					'is_multiligne' => false
				],
				[
					'name'          => 'field13 translation name',
					'text'          => 'value13',
					'domain'        => 'wpsolr',
					'is_multiligne' => true
				],
				[
					'name'          => 'field14 translation name',
					'text'          => 'value14',
					'domain'        => 'wpsolr',
					'is_multiligne' => true
				]
			]
			, $results );

	}

}