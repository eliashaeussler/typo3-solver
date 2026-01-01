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

namespace EliasHaeussler\Typo3Solver\Http;

use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use OpenAI\Client;

/**
 * ClientFactory
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final readonly class ClientFactory
{
    public function __construct(
        private Configuration\Configuration $configuration,
    ) {}

    /**
     * @throws Exception\ApiKeyMissingException
     */
    public function get(): Client
    {
        $apiKey = $this->configuration->getApiKey();

        if ($apiKey === null || \trim($apiKey) === '') {
            throw Exception\ApiKeyMissingException::create();
        }

        return \OpenAI::client($apiKey);
    }
}
