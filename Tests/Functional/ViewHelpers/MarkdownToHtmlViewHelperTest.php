<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Functional\ViewHelpers;

use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * MarkdownToHtmlViewHelperTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class MarkdownToHtmlViewHelperTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    use Tests\DOMDocumentTrait;
    use Tests\ViewTrait;

    protected array $testExtensionsToLoad = [
        'solver',
    ];

    protected bool $initializeDatabase = false;

    private \Parsedown $parsedown;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parsedown = new \Parsedown();
    }

    #[Framework\Attributes\Test]
    public function renderStaticConvertsMarkdownToHtml(): void
    {
        $markdown = <<<'MARKDOWN'
# Hello world!

Nice to *meet* you :)
MARKDOWN;

        $view = $this->createView('{s:markdownToHtml(markdown: markdown)}');
        $view->assign('markdown', $markdown);

        self::assertSame(
            $this->parsedown->text($markdown),
            \trim($view->render()),
        );
    }

    #[Framework\Attributes\Test]
    public function renderStaticCanBeUsedWithContentArgument(): void
    {
        $markdown = <<<'MARKDOWN'
# Hello world!

Nice to *meet* you :)
MARKDOWN;

        $view = $this->createView('{markdown -> s:markdownToHtml()}');
        $view->assign('markdown', $markdown);

        self::assertSame(
            $this->parsedown->text($markdown),
            \trim($view->render()),
        );
    }

    #[Framework\Attributes\Test]
    public function renderStaticReplacesLinNumbersInCodeSnippets(): void
    {
        $markdown = <<<'MARKDOWN'
* Code with line numbers:
  ```
  23     $result = $this->call(
  24         'foo',
  25         'baz',
  26     );
  ```
* Code without line numbers:
  ```
  $result = $this->call(
      'foo',
      'baz',
  );
  ```
MARKDOWN;

        $view = $this->createView('{s:markdownToHtml(markdown: markdown, replaceLineNumbersInCodeSnippets: 1)}');
        $view->assign('markdown', $markdown);

        $actual = $view->render();
        $xpath = self::createDOMXPath($actual);

        self::assertNodeContentEqualsString('has-line-numbers', '//ul/li[1]/pre[1]/@class', $xpath);
        self::assertNodeContentEqualsString('23', '//ul/li[1]/pre[1]/span[1]/@data-line', $xpath);
        self::assertNodeContentEqualsString('24', '//ul/li[1]/pre[1]/span[2]/@data-line', $xpath);
        self::assertNodeContentEqualsString('25', '//ul/li[1]/pre[1]/span[3]/@data-line', $xpath);
        self::assertNodeContentEqualsString('26', '//ul/li[1]/pre[1]/span[4]/@data-line', $xpath);
        self::assertNodeListIsEmpty('//ul/li[2]/pre[1]/@class', $xpath);
    }
}
