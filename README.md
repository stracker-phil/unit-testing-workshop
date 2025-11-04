# Unit Testing Workshop

> We can either write working code, or clean code, but not both at the same time.
>
> Testing allows us to make things work, and ensure it stays working as we make it clean.
>
> -- _Summary of TDD philosophy by Kent Beck_

## Workshop Agenda

### Preparation

- [ ] Launch DDEV and verify the site works (before the workshop)
    - `ddev start && ddev orchestrate -f`
- [ ] Ensure the website works
    - https://unit-testing-workshop.ddev.site/
- [ ] Share code-with-me access in Slack

### Setup Phase

- [ ] Review the sample code we'll test
- [ ] Install PHPUnit
    - `composer require --dev phpunit/phpunit:~9.6.0`
- [ ] Setup & confirm it works:
    - Create (copy) the `phpunit.xml` config file
    - `ddev exec vendor/bin/phpunit`

### Manual Testing Basics

- [ ] Write and run first test
    - `tests/BookingValidatorTest.php`
    - `ddev exec vendor/bin/phpunit`
- [ ] Add composer script
    - `"test": "vendor/bin/phpunit"`
- [ ] **Discussion: What makes a good unit test?**
    - **Isolation** (mock everything except the tested class)
    - **Stubs vs Mocks** (when to use each)
    - **One assertion** per test (or use dataProviders)
    - Descriptive test names (i.e. documentation)
    - Avoid DRY/abstractions (linear and transparent behavior)

### Code Coverage

- [ ] Install coverage tool
    - `composer require --dev phpmetrics/phpmetrics:^2.9`
- [ ] Configure coverage in `phpunit.xml`
- [ ] Generate coverage report
    - `ddev exec vendor/bin/phpunit --coverage-html coverage/`
- [ ] Open `coverage/index.html` in browser
    - https://unit-testing-workshop.ddev.site/coverage/
- [ ] Identify untested code paths
- [ ] Write one more test to improve coverage

### AI-Assisted Testing

- [ ] Use Claude to generate remaining tests, using MCP
- [ ] Review AI-generated code quality
- [ ] Iterate: Ask Claude to refactor/improve handwritten tests
- [ ] Run full suite → achieve high coverage

### Discussion

- Or look at the Claude project at the bottom

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
└── README.md                     # This file
```

## Claude Project

https://claude.ai/project/01987bbe-7203-7080-a49b-4517ec5a1440

Has a clear understanding of good testing philosophy, and includes capabilities for

- Interactive coach ("What is a good unit test?")
- Create uniform test suites for uploaded/copy-pasted code
- Identify testing gaps (provide test file + covered class)
- Refactor/improve existing tests ("Review and improve the following test suite...")
- TDD without MCP (AI writes test in chat, you copy-paste tests and write code)
- TDD with MCP ("Let's start a TDD session", AI writes test files, you write code)
