<?php
/**
 * @covers \WorkshopPlugin\BookingProcessor
 */

declare( strict_types = 1 );

namespace tests;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use WorkshopPlugin\BookingProcessor;
use WorkshopPlugin\BookingValidator;

class BookingProcessorTest extends TestCase {
	use MockeryPHPUnitIntegration;

	private BookingProcessor $processor;
	private BookingValidator $validator;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		
		// Mock WordPress function used in generate_booking_id()
		Monkey\Functions\when( 'wp_generate_password' )
			->justReturn( 'ABC123' );
		
		$this->validator = \Mockery::mock( BookingValidator::class );
		$this->processor = new BookingProcessor( $this->validator );
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * GIVEN a valid booking
	 * WHEN processing the booking
	 * THEN it should return true
	 */
	public function test_process_returns_true_for_valid_booking(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );

		// Act
		$result = $this->processor->process( $this->valid_booking() );

		// Assert
		$this->assertTrue( $result );
	}

	/**
	 * GIVEN a valid booking
	 * WHEN processing the booking
	 * THEN it should store the booking with generated fields
	 */
	public function test_process_adds_generated_fields_to_booking(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );

		// Act
		$this->processor->process( $this->valid_booking() );
		$stored_bookings = $this->processor->get_bookings();

		// Assert
		$this->assertCount( 1, $stored_bookings );
		$stored_booking = $stored_bookings[0];
		$this->assertArrayHasKey( 'id', $stored_booking );
		$this->assertArrayHasKey( 'processed_at', $stored_booking );
		$this->assertArrayHasKey( 'confirmation_code', $stored_booking );
	}

	/**
	 * GIVEN an invalid booking
	 * WHEN processing the booking
	 * THEN it should return false and not store the booking
	 */
	public function test_process_returns_false_for_invalid_booking(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( false );

		// Act
		$result = $this->processor->process( $this->valid_booking() );

		// Assert
		$this->assertFalse( $result );
		$this->assertCount( 0, $this->processor->get_bookings() );
	}

	/**
	 * GIVEN multiple valid bookings
	 * WHEN processing them
	 * THEN all bookings should be stored and retrievable
	 */
	public function test_get_bookings_returns_all_processed_bookings(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );
		$booking1 = $this->valid_booking();
		$booking2 = $this->valid_booking( [ 'guest_name' => 'Jane Smith', 'room_number' => '102' ] );

		// Act
		$this->processor->process( $booking1 );
		$this->processor->process( $booking2 );
		$bookings = $this->processor->get_bookings();

		// Assert
		$this->assertCount( 2, $bookings );
		$this->assertSame( 'John Doe', $bookings[0]['guest_name'] );
		$this->assertSame( 'Jane Smith', $bookings[1]['guest_name'] );
	}

	/**
	 * GIVEN processed bookings
	 * WHEN getting booking count
	 * THEN it should return the correct number
	 */
	public function test_get_booking_count_returns_correct_number(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );

		// Act
		$this->processor->process( $this->valid_booking() );
		$this->processor->process( $this->valid_booking() );
		$this->processor->process( $this->valid_booking() );

		// Assert
		$this->assertSame( 3, $this->processor->get_booking_count() );
	}

	/**
	 * GIVEN no processed bookings
	 * WHEN querying empty state
	 * THEN appropriate empty values should be returned
	 * 
	 * @dataProvider empty_state_provider
	 */
	public function test_empty_state_returns_expected_values(
		string $method,
		$expected_value
	): void {
		// Act
		$result = $this->processor->$method();

		// Assert
		$this->assertSame( $expected_value, $result );
	}

	public function empty_state_provider(): array {
		return [
			'get_bookings returns empty array'       => [ 'get_bookings', [] ],
			'get_booking_count returns zero'         => [ 'get_booking_count', 0 ],
		];
	}

	/**
	 * GIVEN a processed booking
	 * WHEN finding by confirmation code
	 * THEN it should return the matching booking
	 */
	public function test_find_by_confirmation_code_returns_matching_booking(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );
		$this->processor->process( $this->valid_booking() );
		$stored = $this->processor->get_bookings()[0];
		$confirmation_code = $stored['confirmation_code'];

		// Act
		$found = $this->processor->find_by_confirmation_code( $confirmation_code );

		// Assert
		$this->assertNotNull( $found );
		$this->assertSame( $confirmation_code, $found['confirmation_code'] );
		$this->assertSame( 'John Doe', $found['guest_name'] );
	}

	/**
	 * GIVEN various search scenarios
	 * WHEN searching by confirmation code
	 * THEN it should return null when not found
	 * 
	 * @dataProvider not_found_scenarios_provider
	 */
	public function test_find_by_confirmation_code_returns_null_when_not_found(
		string $scenario,
		bool $process_booking,
		string $search_code
	): void {
		// Arrange
		if ( $process_booking ) {
			$this->validator->shouldReceive( 'validate' )->andReturn( true );
			$this->processor->process( $this->valid_booking() );
		}

		// Act
		$found = $this->processor->find_by_confirmation_code( $search_code );

		// Assert
		$this->assertNull( $found, $scenario );
	}

	public function not_found_scenarios_provider(): array {
		return [
			'non-existent code with bookings' => [ 'searching for non-existent code', true, 'NONEXIST' ],
			'any code without bookings'       => [ 'searching in empty state', false, 'ANYCODE' ],
		];
	}

	/**
	 * GIVEN multiple bookings with different data
	 * WHEN processing them
	 * THEN each should have a unique confirmation code
	 */
	public function test_confirmation_codes_are_unique_for_different_bookings(): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );
		$booking1 = $this->valid_booking( [ 'room_number' => '101' ] );
		$booking2 = $this->valid_booking( [ 'room_number' => '102' ] );

		// Act
		$this->processor->process( $booking1 );
		$this->processor->process( $booking2 );
		$bookings = $this->processor->get_bookings();

		// Assert
		$this->assertNotSame(
			$bookings[0]['confirmation_code'],
			$bookings[1]['confirmation_code']
		);
	}

	/**
	 * GIVEN a processed booking
	 * WHEN checking generated fields
	 * THEN they should have the correct format
	 * 
	 * @dataProvider generated_field_format_provider
	 */
	public function test_generated_fields_have_correct_format(
		string $field,
		callable $assertion
	): void {
		// Arrange
		$this->validator->shouldReceive( 'validate' )->andReturn( true );
		$this->processor->process( $this->valid_booking() );
		$stored = $this->processor->get_bookings()[0];

		// Act & Assert
		$assertion( $this, $stored[$field] );
	}

	public function generated_field_format_provider(): array {
		return [
			'booking ID format' => [
				'id',
				function ( TestCase $test, string $value ): void {
					$test->assertStringStartsWith( 'BK', $value );
					$test->assertSame( strtoupper( $value ), $value );
					$test->assertSame( 8, strlen( $value ) );
				},
			],
			'processed_at timestamp format' => [
				'processed_at',
				function ( TestCase $test, string $value ): void {
					$test->assertMatchesRegularExpression(
						'/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
						$value
					);
					$before = gmdate( 'Y-m-d H:i:s', strtotime( '-1 minute' ) );
					$after = gmdate( 'Y-m-d H:i:s', strtotime( '+1 minute' ) );
					$test->assertGreaterThanOrEqual( $before, $value );
					$test->assertLessThanOrEqual( $after, $value );
				},
			],
			'confirmation code format' => [
				'confirmation_code',
				function ( TestCase $test, string $value ): void {
					$test->assertSame( 8, strlen( $value ) );
					$test->assertSame( strtoupper( $value ), $value );
					$test->assertMatchesRegularExpression( '/^[A-F0-9]{8}$/', $value );
				},
			],
		];
	}

	/**
	 * Create a valid booking array for testing
	 * 
	 * @param array<string, mixed> $overrides Optional field overrides.
	 * @return array<string, mixed>
	 */
	private function valid_booking( array $overrides = [] ): array {
		return array_merge(
			[
				'guest_name'  => 'John Doe',
				'room_number' => '101',
				'check_in'    => '2025-12-25',
				'guests'      => 2,
			],
			$overrides
		);
	}
}
