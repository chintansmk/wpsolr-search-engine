<?php

namespace wpsolr\ui;

class WPSOLR_Data_SortTest extends \PHPUnit_Framework_TestCase {

	public function testEmptyArraySortsReturnsEmptyArray() {

		$result = WPSOLR_Data_Sort::get_data(
			'sort_by_date_desc',
			array(),
			'sort_by_relevancy_desc',
			[ ] );


		$this->assertEquals(
			$result,
			array(
				'items' => array(),
			)
		);

	}

	public function testNulSortsReturnsEmptyArray() {

		$result = WPSOLR_Data_Sort::get_data(
			'sort_by_date_desc',
			null,
			'sort_by_relevancy_desc',
			[ ] );


		$this->assertEquals(
			$result,
			array(
				'items' => array(),
			)
		);

	}

	/**
	 * @group toBeFixed
	 */
	public function XtestNonEmptySelectedSort() {

		$result = WPSOLR_Data_Sort::get_data(
			'sort_by_date_desc',
			array(
				'sort_by_relevancy_desc',
				'sort_by_date_asc',
				'sort_by_date_desc'
			),
			'sort_by_relevancy_desc',
			[ ] );


		$this->assertEquals(
			$result,
			array(
				'items' => array(
					array( 'id' => 'sort_by_relevancy_desc', 'name' => 'sort_by_relevancy_desc', 'selected' => false ),
					array( 'id' => 'sort_by_date_asc', 'name' => 'sort_by_date_asc', 'selected' => false ),
					array( 'id' => 'sort_by_date_desc', 'name' => 'sort_by_date_desc', 'selected' => true ),
				)
			)
		);

	}

	/**
	 * @group toBeFixed
	 */
	public function XtestNullSelectedSortReturnsDefaultSort() {

		$result = WPSOLR_Data_Sort::get_data(
			null,
			array(
				'sort_by_relevancy_desc',
				'sort_by_date_asc',
				'sort_by_date_desc'
			),
			'sort_by_relevancy_desc',
			[ ] );


		$this->assertEquals(
			$result,
			array(
				'items' => array(
					array( 'id' => 'sort_by_relevancy_desc', 'name' => 'sort_by_relevancy_desc', 'selected' => true ),
					array( 'id' => 'sort_by_date_asc', 'name' => 'sort_by_date_asc', 'selected' => false ),
					array( 'id' => 'sort_by_date_desc', 'name' => 'sort_by_date_desc', 'selected' => false ),
				)
			)
		);

	}

	/**
	 * @group toBeFixed
	 */
	public function XtestEmptySelectedSortReturnsDefaultSort() {

		$result = WPSOLR_Data_Sort::get_data(
			'',
			array(
				'sort_by_relevancy_desc',
				'sort_by_date_asc',
				'sort_by_date_desc'
			),
			'sort_by_relevancy_desc',
			[ ] );


		$this->assertEquals(
			$result,
			array(
				'items' => array(
					array( 'id' => 'sort_by_relevancy_desc', 'name' => 'sort_by_relevancy_desc', 'selected' => true ),
					array( 'id' => 'sort_by_date_asc', 'name' => 'sort_by_date_asc', 'selected' => false ),
					array( 'id' => 'sort_by_date_desc', 'name' => 'sort_by_date_desc', 'selected' => false ),
				)
			)
		);

	}

	public function XtestEmptySelectedSortAndEmptyDefaultSort() {

		$result = WPSOLR_Data_Sort::get_data(
			null,
			array(
				'sort_by_relevancy_desc',
				'sort_by_date_asc',
				'sort_by_date_desc'
			),
			null,
			[ ] );


		$this->assertEquals(
			$result,
			array(
				'items' => array(
					array( 'id' => 'sort_by_relevancy_desc', 'name' => 'sort_by_relevancy_desc', 'selected' => false ),
					array( 'id' => 'sort_by_date_asc', 'name' => 'sort_by_date_asc', 'selected' => false ),
					array( 'id' => 'sort_by_date_desc', 'name' => 'sort_by_date_desc', 'selected' => false ),
				)
			)
		);

	}

}
