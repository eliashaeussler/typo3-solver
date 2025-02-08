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

namespace EliasHaeussler\Typo3Solver\Tests\Extension;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Event;
use PHPUnit\Runner;
use PHPUnit\TextUI;

/**
 * ExtensionConfigurationExtension
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class ExtensionConfigurationExtension implements Runner\Extension\Extension, Event\Test\BeforeTestMethodCalledSubscriber
{
    public function bootstrap(
        TextUI\Configuration\Configuration $configuration,
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        $facade->registerSubscriber($this);
    }

    public function notify(Event\Test\BeforeTestMethodCalled $event): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY] = [
            'api' => [
                'key' => 'foo',
            ],
            'attributes' => [
                'maxTokens' => '300',
                'model' => 'gpt-4o-mini',
                'numberOfCompletions' => '1',
                'temperature' => '0.5',
            ],
            'cache' => [
                'lifetime' => '86400',
            ],
            'ignoredCodes' => '',
            'prompt' => Src\ProblemSolving\Solution\Prompt\DefaultPrompt::class,
            'provider' => Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider::class,
        ];
    }
}
