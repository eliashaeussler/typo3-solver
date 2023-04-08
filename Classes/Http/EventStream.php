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

namespace EliasHaeussler\Typo3Solver\Http;

use EliasHaeussler\Typo3Solver\Exception;

use function header;
use function headers_sent;
use function sprintf;
use function uniqid;

/**
 * EventStream
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class EventStream
{
    private bool $closed = false;

    /**
     * @throws Exception\EventStreamException
     */
    private function __construct(
        private readonly string $id,
        private readonly int $retry,
    ) {
        if (headers_sent()) {
            throw Exception\EventStreamException::forActiveResponse();
        }

        $this->sendHeader('Content-Type', 'text/event-stream');
        $this->sendHeader('Cache-Control', 'no-cache');
        $this->sendHeader('Connection', 'keep-alive');
        $this->sendHeader('X-Accel-Buffering', 'no');
    }

    /**
     * @throws Exception\EventStreamException
     */
    public static function create(string $id = null, int $retry = 50): self
    {
        return new self($id ?? uniqid(), $retry);
    }

    /**
     * @throws Exception\EventStreamException
     */
    public function sendMessage(string $name = 'message', bool|float|int|string $data = null): void
    {
        if ($this->closed) {
            throw Exception\EventStreamException::forClosedStream();
        }

        // Send event data
        $this->sendStreamData('id', $this->id);
        $this->sendStreamData('event', $name);
        $this->sendStreamData('data', $data);
        $this->sendStreamData('retry', $this->retry);
        $this->sendDelimiter();

        // Flush output buffer
        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }

    /**
     * @throws Exception\EventStreamException
     */
    public function close(string $eventName = 'done'): void
    {
        if ($this->closed) {
            throw Exception\EventStreamException::forClosedStream();
        }

        $this->sendMessage($eventName);
        $this->closed = true;
    }

    private function sendHeader(string $name, string $value): void
    {
        header(sprintf('%s: %s', $name, $value));
    }

    private function sendStreamData(string $name, bool|float|int|string|null $value): void
    {
        echo sprintf('%s: %s', $name, $value);

        $this->sendDelimiter();
    }

    private function sendDelimiter(): void
    {
        echo PHP_EOL;
    }
}
