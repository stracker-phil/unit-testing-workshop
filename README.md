# Unit Testing Workshop

> We can either write working code, or clean code, but not both at the same time.
>
> Testing allows us to make things work, and ensure it stays working as we make it clean.
>
> -- _Summary of TDD philosophy by Kent Beck_

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
├── CHEATSHEET.md                 # Quick reference (commands, concepts)
└── README.md                     # This file
```

## Workshop Agenda

### Preparation

1. Launch DDEV and verify the site works (before the workshop)
    - `ddev start && ddev orchestrate -f`
2. Ensure the website works
    - https://unit-testing-workshop.ddev.site/
3. Share code-with-me access in Slack

### Setup Phase

1. Review the sample code we'll test
2. Install PHPUnit
    - `composer require --dev phpunit/phpunit:~9.6.0`
3. Setup & confirm it works:
    - Create (copy) the `phpunit.xml` config file
    - `ddev exec vendor/bin/phpunit`

### Manual Testing Basics

1. Write and run first test
    - `tests/BookingValidatorTest.php`
    - `ddev exec vendor/bin/phpunit`
2. Add composer script
    - `"test": "vendor/bin/phpunit"`
3. 💬 What makes a good unit test?
    - **Isolation** (mock everything except the tested class)
    - **Stubs vs Mocks** (when to use each)
    - **One assertion** per test (or use dataProviders)
    - Descriptive test names (i.e. documentation)
    - Avoid DRY/abstractions (linear and transparent behavior)

### Code Coverage

1. 💬 Who is using coverage reports already?
2. Install coverage tool
    - `composer require --dev phpmetrics/phpmetrics:^2.9`
3. Configure coverage in `phpunit.xml`
4. Generate coverage report
    - `ddev exec vendor/bin/phpunit --coverage-html coverage/`
    - Add composer script for convenience
5. Open `coverage/index.html` in browser
    - https://unit-testing-workshop.ddev.site/coverage/
6. Identify untested code paths
7. Write one more test to improve coverage

### AI-Assisted Testing

1. Use Claude to generate remaining tests, using MCP
2. 💬 Review AI-generated code quality together
    - Focus on: Which tests are useless, what can we delete?
3. Iterate: Ask Claude to refactor/improve the tests
4. Run full suite → confirm high coverage

### Discussion

💬 / review the Claude project at the bottom

## Key Takeaways

**Three-Step Workflow:**

1. **Manual** - Establish pattern (1-2 tests)
2. **AI Generate** - Bulk structure (10-15 tests in seconds)
3. **Human Refine** - Delete trivia, consolidate, focus on behavior

**Test coverage reveals risk areas**

---

## Claude Project

https://claude.ai/project/01987bbe-7203-7080-a49b-4517ec5a1440

Has a clear understanding of good testing philosophy, and includes capabilities for

- Interactive coach ("What is a good unit test?")
- Create uniform test suites for uploaded/copy-pasted code
- Identify testing gaps (provide test file + covered class)
- Refactor/improve existing tests ("Review and improve the following test suite...")
- TDD without MCP (AI writes test in chat, you copy-paste tests and write code)
- TDD with MCP ("Let's start a TDD session", AI writes test files, you write code)
