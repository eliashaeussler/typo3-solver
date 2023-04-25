<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\Typo3Solver\Tests\Unit\View;

use EliasHaeussler\Typo3Solver as Src;
use Exception;
use TYPO3\TestingFramework;

use function trim;

/**
 * TemplateRendererTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class TemplateRendererTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\View\TemplateRenderer $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\View\TemplateRenderer();
    }

    /**
     * @test
     */
    public function renderRendersTemplateWithGivenVariables(): void
    {
        $file = __FILE__;
        $lineNumber = __LINE__ + 1;
        $exception = new Exception('Something went wrong.', 1680791875);

        $expected = <<<TEMPLATE
Hi, I am working with TYPO3 CMS, and I have encountered an exception in my code.
The exception message reads as follows:

Exception: "Something went wrong."
in {$file}
on line {$lineNumber}

Here's the code snippet with line numbers where the exception occurred:

Hello world!

Please note that this TYPO3 CMS installation is in composer mode and using
version 12.4.0. The PHP version being used is 8.2.4.

Please provide a solution that is efficient, effective, and robust, enabling the
TYPO3 CMS installation to function smoothly and without errors. Your response
should be written in Markdown format and include a detailed explanation of how
the solution works and why it is the most optimal solution to the problem. Please
ensure that the solution is clear and concise, and highlight relevant aspects of
the solution. Please ensure that any code snippets provided are correct, readable,
and maintainable.
TEMPLATE;

        $actual = $this->subject->render('Prompt/Default', [
            'exception' => $exception,
            'exceptionClass' => $exception::class,
            'snippet' => 'Hello world!',
            'mode' => 'composer',
            'typo3Version' => '12.4.0',
            'phpVersion' => '8.2.4',
        ]);

        self::assertSame(trim($expected), trim($actual));
    }
}
