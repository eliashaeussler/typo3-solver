<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\Typo3Solver\Tests\Unit\View;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * TemplateRendererTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\View\TemplateRenderer::class)]
final class TemplateRendererTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\View\TemplateRenderer $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\View\TemplateRenderer();
    }

    #[Framework\Attributes\Test]
    public function renderRendersTemplateWithGivenVariables(): void
    {
        $file = __FILE__;
        $lineNumber = __LINE__ + 1;
        $exception = new \Exception('Something went wrong.', 1680791875);

        $expected = <<<TEMPLATE
Hi, I am working with TYPO3 CMS and encountered an exception in my code.
The error message is:

Exception: "Something went wrong."
in {$file}
on line {$lineNumber}

Here is the relevant code snippet:

```php
Hello world!
```

**Environment details:**

* TYPO3 CMS 12.4.0 (composer-managed)
* PHP 8.2.4
* MySQL 8.4.0

**Task:**

Analyze the issue, identify the root cause, and provide a structured solution.
If multiple solutions exist, compare their trade-offs.

**Expected Response Format (Markdown):**

1. Issue Analysis – Explain the root cause.
2. Solution Steps – Provide step-by-step instructions.
3. Code Example – Ensure correctness and maintainability.
4. Additional Considerations – Mention alternative solutions if applicable

The solution should be efficient, maintainable, and production-ready.
Code snippets must be correct, readable, and well-commented.
TEMPLATE;

        $actual = $this->subject->render('Prompt/Default', [
            'exception' => $exception,
            'exceptionClass' => $exception::class,
            'snippet' => 'Hello world!',
            'mode' => 'composer-managed',
            'typo3Version' => '12.4.0',
            'phpVersion' => '8.2.4',
            'dbVersion' => 'MySQL 8.4.0',
        ]);

        self::assertSame(\trim($expected), \trim($actual));
    }
}
