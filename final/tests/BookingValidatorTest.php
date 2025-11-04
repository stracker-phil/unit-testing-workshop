<?php
/**
 * @covers \WorkshopPlugin\BookingValidator
 */

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;
use WorkshopPlugin\BookingValidator;

class BookingValidatorTest extends TestCase {
	private BookingValidator $validator;

	protected function setUp(): void {
		parent::setUp();
		$this->validator = new BookingValidator();
	}

	public function test_validate_accepts_valid_booking(): void {
		$booking = [
			'guest_name'  => 'John Doe',
			'room_number' => '101',
			'check_in'    => '2025-12-25',
			'guests'      => 2,
		];

		$result = $this->validator->validate( $booking );

		$this->assertTrue( $result );
	}
}
