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

namespace EliasHaeussler\Typo3Solver\Tests;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function trim;

/**
 * DOMDocumentTrait
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
trait DOMDocumentTrait
{
    private static function createDOMXPath(string $html): DOMXPath
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

        return new DOMXPath($document);
    }

    private static function assertNodeContentEqualsString(string $string, string $query, DOMXPath $xpath): void
    {
        self::assertNotFalse($nodeList = $xpath->query($query));
        self::assertInstanceOf(DOMNode::class, $firstNode = $nodeList->item(0));
        self::assertSame(trim($string), trim($firstNode->textContent));
    }

    private static function assertNodeListIsEmpty(string $query, DOMXPath $xpath): void
    {
        self::assertInstanceOf(DOMNodeList::class, $nodeList = $xpath->query($query));
        self::assertSame(0, $nodeList->length);
    }
}
