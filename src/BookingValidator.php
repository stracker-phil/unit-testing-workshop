<?php
/**
 * Booking Validator
 *
 * Validates hotel booking data before processing.
 *
 * @package WorkshopPlugin
 */

declare( strict_types = 1 );

namespace WorkshopPlugin;

/**
 * Validates booking data
 */
class BookingValidator {
	/**
	 * Available room numbers
	 *
	 * @var array<string>
	 */
	private array $available_rooms = [ '101', '102', '103', '201', '202', '203' ];

	/**
	 * Validate booking data
	 *
	 * @param array<string, mixed> $booking Booking data to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( array $booking ): bool {
		if ( ! $this->has_required_fields( $booking ) ) {
			return false;
		}

		if ( ! $this->is_valid_room( $booking['room_number'] ) ) {
			return false;
		}

		if ( ! $this->is_valid_date( $booking['check_in'] ) ) {
			return false;
		}

		if ( ! $this->is_valid_guest_count( $booking['guests'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if all required fields are present and not empty
	 *
	 * @param array<string, mixed> $booking Booking data.
	 * @return bool True if all required fields present.
	 */
	private function has_required_fields( array $booking ): bool {
		$required = [ 'guest_name', 'room_number', 'check_in', 'guests' ];

		foreach ( $required as $field ) {
			if ( empty( $booking[ $field ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if room number exists and is available
	 *
	 * @param mixed $room_number Room number to check.
	 * @return bool True if valid room.
	 */
	private function is_valid_room( $room_number ): bool {
		if ( ! is_string( $room_number ) ) {
			return false;
		}

		return in_array( $room_number, $this->available_rooms, true );
	}

	/**
	 * Check if date is valid format (Y-m-d) and not in the past
	 *
	 * @param mixed $date Date to validate.
	 * @return bool True if valid date.
	 */
	private function is_valid_date( $date ): bool {
		if ( ! is_string( $date ) ) {
			return false;
		}

		$parsed = \DateTime::createFromFormat( 'Y-m-d', $date );

		if ( ! $parsed || $parsed->format( 'Y-m-d' ) !== $date ) {
			return false;
		}

		// Check if date is not in the past
		$today = new \DateTime( 'today' );

		return $parsed >= $today;
	}

	/**
	 * Check if guest count is valid (1-4 guests)
	 *
	 * @param mixed $guests Guest count to validate.
	 * @return bool True if valid guest count.
	 */
	private function is_valid_guest_count( $guests ): bool {
		if ( ! is_int( $guests ) && ! is_string( $guests ) ) {
			return false;
		}

		$count = (int) $guests;

		return $count >= 1 && $count <= 4;
	}

	/**
	 * Get list of available rooms
	 *
	 * @return array<string> Available room numbers.
	 */
	public function get_available_rooms(): array {
		return $this->available_rooms;
	}
}
