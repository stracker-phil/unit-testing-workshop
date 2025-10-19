# Unit Testing Workshop

> We can either write working code, or clean code, but not both at the same time.
>
> Testing allows us to make things work, and ensure it stays working as we make it clean.
>
> -- _Summary of TDD philosophy by Kent Beck_

## Workshop Agenda

### Setup Phase

- [ ] Launch DDEV and verify the site works
    - `ddev start`
- [ ] Review the sample code we'll test
- [ ] Install PHPUnit
    - `composer require --dev phpunit/phpunit:~9.6.0`

### Manual Testing Basics

- [ ] Write and run first test
    - `tests/BookingValidatorTest.php`
    - `ddev exec vendor/bin/phpunit`
- [ ] Add composer script
    - `"test": "vendor/bin/phpunit"`
- [ ] **Discussion: What makes a good unit test?**
    - Isolation (no database, no WordPress functions)
    - Fast execution
    - Stubs vs Mocks (when to use each)
    - Descriptive test names
    - One assertion per test (or use dataProviders)

### Code Coverage

- [ ] Install coverage tool
    - `composer require --dev phpmetrics/phpmetrics:^2.9`
- [ ] Configure coverage in `phpunit.xml`
- [ ] Generate coverage report
    - `ddev exec vendor/bin/phpunit --coverage-html coverage/`
- [ ] Open `coverage/index.html` in browser
- [ ] Identify untested code paths (red/yellow indicators)
- [ ] Write one more test to improve coverage

### AI-Assisted Testing

- [ ] Use Claude to generate remaining tests
- [ ] Review AI-generated code quality
- [ ] Iterate: Ask Claude to refactor/improve
- [ ] Run full suite → achieve high coverage
- [ ] **Discussion: AI workflow patterns**
    - What prompts work well
    - When to accept AI suggestions
    - When to refine manually

## Sample Code Structure

```
workshop-plugin/
├── plugin.php                   # Plugin header only
├── src/
│   ├── BookingValidator.php     # Validates booking data
│   └── BookingProcessor.php     # Processes validated bookings
├── tests/
│   ├── phpunit.xml
│   └── (we'll create these together)
├── composer.json
├── PROMPTS.md                    # AI prompts
└── README.md                     # This file
```
