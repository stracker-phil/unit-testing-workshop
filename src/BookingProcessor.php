<?php
/**
 * Booking Processor
 *
 * Processes validated hotel bookings.
 *
 * @package WorkshopPlugin
 */

declare( strict_types = 1 );

namespace WorkshopPlugin;

/**
 * Processes hotel bookings
 */
class BookingProcessor {
	/**
	 * Validator instance
	 *
	 * @var BookingValidator
	 */
	private BookingValidator $validator;

	/**
	 * Stored bookings
	 *
	 * @var array<array<string, mixed>>
	 */
	private array $bookings = [];

	/**
	 * Constructor
	 *
	 * @param BookingValidator $validator Booking validator instance.
	 */
	public function __construct( BookingValidator $validator ) {
		$this->validator = $validator;
	}

	/**
	 * Process a booking
	 *
	 * @param array<string, mixed> $booking Booking data to process.
	 * @return bool True if processed successfully, false if validation failed.
	 */
	public function process( array $booking ): bool {
		if ( ! $this->validator->validate( $booking ) ) {
			return false;
		}

		$booking['id']                = $this->generate_booking_id();
		$booking['processed_at']      = gmdate( 'Y-m-d H:i:s' );
		$booking['confirmation_code'] = $this->generate_confirmation_code( $booking );

		$this->bookings[] = $booking;

		return true;
	}

	/**
	 * Get all processed bookings
	 *
	 * @return array<array<string, mixed>> Processed bookings.
	 */
	public function get_bookings(): array {
		return $this->bookings;
	}

	/**
	 * Get total number of processed bookings
	 *
	 * @return int Booking count.
	 */
	public function get_booking_count(): int {
		return count( $this->bookings );
	}

	/**
	 * Find booking by confirmation code
	 *
	 * @param string $code Confirmation code to search for.
	 * @return array<string, mixed>|null Booking data or null if not found.
	 */
	public function find_by_confirmation_code( string $code ): ?array {
		foreach ( $this->bookings as $booking ) {
			if ( $booking['confirmation_code'] === $code ) {
				return $booking;
			}
		}

		return null;
	}

	/**
	 * Generate unique booking ID
	 *
	 * @return string Unique booking ID.
	 */
	private function generate_booking_id(): string {
		return 'BK' . strtoupper( wp_generate_password( 6, false ) );
	}

	/**
	 * Generate confirmation code based on booking data
	 *
	 * @param array<string, mixed> $booking Booking data.
	 * @return string Confirmation code.
	 */
	private function generate_confirmation_code( array $booking ): string {
		$hash = md5(
			$booking['guest_name'] .
			$booking['room_number'] .
			$booking['check_in'] .
			$booking['guests']
		);

		return strtoupper( substr( $hash, 0, 8 ) );
	}
}
