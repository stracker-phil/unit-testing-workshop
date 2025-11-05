# Unit Testing - Quick Reference

## Commands

```bash
# Run all tests
ddev exec phpunit

# Run tests with verbose output
ddev exec phpunit --testdox

# Run specific test file
ddev exec phpunit tests/BookingValidatorTest.php

# Run a subset of tests based on path filter
ddev exec phpunit --filter Booking

# "TDD" approach, stop on first failure
ddev exec phpunit --stop-on-failure --filter Booking

# Generate terminal coverage
ddev exec XDEBUG_MODE=coverage phpunit --coverage-text

# Generate HTML coverage report and open it
ddev exec XDEBUG_MODE=coverage phpunit --coverage-html coverage/

open https://unit-testing-workshop.ddev.site/coverage/
```

Extra convenience using composer:

```
"scripts": {
  "test": "vendor/bin/phpunit",
  "coverage": "XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text --coverage-html coverage/"
},
```

Run via `ddev composer coverage`

## Test Structure Template

```php
<?php
declare(strict_types=1);

namespace WorkshopPlugin\Tests;

use PHPUnit\Framework\TestCase;
use WorkshopPlugin\YourClass;

class YourClassTest extends TestCase {
    // Test subject
    private YourClass $instance;

    protected function setUp(): void {
        parent::setUp();
        $this->instance = new YourClass();
    }

    public function test_method_does_something(): void {
        // Arrange
        $input = 'test';

        // Act
        $result = $this->instance->method($input);

        // Assert
        $this->assertEquals('expected', $result);
    }
}
```

## DataProvider Template

```php
/**
 * @dataProvider invalid_input_provider
 */
public function test_rejects_invalid_input($input, $reason): void {
    $result = $this->validator->validate($input);
    
    $this->assertFalse($result, "Failed: $reason");
}

public function invalid_input_provider(): array {
    return [
        'empty string' => ['', 'should reject empty'],
        'null value'   => [null, 'should reject null'],
        'wrong type'   => [123, 'should reject integer'],
    ];
}

// Alternative: Named array syntax
// Keys match parameter names, making intent clearer 
public function invalid_input_provider(): array {
    return [
        'empty string' => [
            'input'  => '',
            'reason' => 'should reject empty',
        ],
        'null value'   => [
            'input'  => null,
            'reason' => 'should reject null',
        ],
        'wrong type'   => [
            'input'  => 123,
            'reason' => 'should reject integer',
        ],
    ];
}
```

## Stub vs Mock

```php
// STUB - very simple. Just returns values, no verifications
$stub = $this->createStub(Dependency::class);
$stub->method('getValue')->willReturn(42);

// MOCK - More complex. Can verify behavior via "expect"
$mock = $this->createMock(Dependency::class);
$mock->expects($this->once())
     ->method('save')
     ->with($this->equalTo('data'));
```

## Common Assertions

```php
// Boolean checks
$this->assertTrue($value);
$this->assertFalse($value);

// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual);  // Strict type check

// Null checks
$this->assertNull($value);
$this->assertNotNull($value);

// Array checks
$this->assertArrayHasKey('key', $array);
$this->assertCount(3, $array);
$this->assertEmpty($array);

// String checks
$this->assertStringContainsString('needle', $haystack);
$this->assertStringStartsWith('prefix', $string);

// Type checks
$this->assertIsString($value);
$this->assertIsInt($value);
$this->assertInstanceOf(Class::class, $object);
```

## AI Prompt Quick Reference

### Generate tests

```
Create a full phpunit test suite for the following class:
src/BookingProcessor.php
```

### Improve coverage based on gaps

```
Coverage report shows lines 42-45 are untested (date validation logic).
Add test cases to cover these scenarios.
```

### Refactor with dataProvider

```
Refactor the test suite, reducing duplicates; use dataProvider where meaningful:
tests/BookingProcessorTest.php
```

## Testing Best Practices

✅ **DO:**

- Test **behavior**, i.e. return values and Exceptions
- Keep tests **focused** and simple
- Use **dataProviders** for similar cases
- Use descriptive test **names**
- **Prefer stubs** over mocks, i.e. `allows()` over `expect()`
- Verify **test coverage** of business logic

❌ **DON'T:**

- Don't test private methods
- Don't test/expect internal behavior (unless critical)
- Don't skip edge cases (null, empty, boundaries)
- Don't overengineer tests; it's okay if test code is repetitive
- You don't need 100% test coverage, 60%-80% is usually good enough
