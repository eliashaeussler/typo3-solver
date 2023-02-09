<div align="center">

![Extension icon](Resources/Public/Icons/Extension.svg)

# TYPO3 extension `solver`

[![Coverage](https://codecov.io/gh/eliashaeussler/typo3-solver/branch/main/graph/badge.svg?token=fj60tJlnHW)](https://codecov.io/gh/eliashaeussler/typo3-solver)
[![Maintainability](https://api.codeclimate.com/v1/badges/1dd3e21a767e5ffb03cf/maintainability)](https://codeclimate.com/github/eliashaeussler/typo3-solver/maintainability)
[![Tests](https://github.com/eliashaeussler/typo3-solver/actions/workflows/tests.yaml/badge.svg)](https://github.com/eliashaeussler/typo3-solver/actions/workflows/tests.yaml)
[![CGL](https://github.com/eliashaeussler/typo3-solver/actions/workflows/cgl.yaml/badge.svg)](https://github.com/eliashaeussler/typo3-solver/actions/workflows/cgl.yaml)
[![Release](https://github.com/eliashaeussler/typo3-solver/actions/workflows/release.yaml/badge.svg)](https://github.com/eliashaeussler/typo3-solver/actions/workflows/release.yaml)
[![License](http://poser.pugx.org/eliashaeussler/typo3-solver/license)](LICENSE.md)\
[![Version](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/solver/version/shields)](https://extensions.typo3.org/extension/solver)
[![Downloads](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/solver/downloads/shields)](https://extensions.typo3.org/extension/solver)
[![Supported TYPO3 versions](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/solver/typo3/shields)](https://extensions.typo3.org/extension/solver)
[![Extension stability](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/solver/stability/shields)](https://extensions.typo3.org/extension/solver)

📦&nbsp;[Packagist](https://packagist.org/packages/eliashaeussler/typo3-solver) |
🐥&nbsp;[TYPO3 extension repository](https://extensions.typo3.org/extension/solver) |
💾&nbsp;[Repository](https://github.com/eliashaeussler/typo3-solver) |
🐛&nbsp;[Issue tracker](https://github.com/eliashaeussler/typo3-solver/issues)

</div>

An extension for TYPO3 CMS to solve exceptions with AI generated solutions.
It uses the [OpenAI PHP client][1] to send a prompt to a configured model
and uses the responded completion as solution. Several completion attributes
(model, tokens, temperature, number of completions) are configurable. It also
provides a console command to solve problems from command line.

## 🚀 Features

* Extended exception handler with AI generated solutions
* Configurable AI completion attributes (model, tokens, temperature, number of completions)
* Caching integration for solved problems
* Command to solve problems from command line
* Customizable solution providers and prompts
* Compatible with TYPO3 11.5 LTS and 12.2

## 🔥 Installation

Via Composer:

```bash
composer require eliashaeussler/typo3-solver
```

Or download the zip file from
[TYPO3 extension repository (TER)](https://extensions.typo3.org/extension/solver).

## 📂 Configuration

### API key

You need an [API key][2] to perform requests at OpenAI. Once generated,
the key must be configured in the extension configuration.

### Extension configuration

The following extension configuration is available:

| Configuration                    | Description                                                                                                        | Default value                                                              |
|----------------------------------|--------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------|
| `provider`                       | FQCN of the solution provider                                                                                      | [`EliasHaeussler\Typo3Solver\Solution\Provider\OpenAISolutionProvider`][3] |
| `prompt`                         | FQCN of the prompt generator                                                                                       | [`EliasHaeussler\Typo3Solver\Prompt\DefaultPrompt`][4]                     |
| `ignoredCodes`                   | Comma-separated list of exception codes to ignore                                                                  | –                                                                          |
| `api.key`                        | [API key](#api-key) for OpenAI requests                                                                            | –                                                                          |
| `attributes.model`               | [OpenAI model][13] to use (see [List available models](#list-available-models) to show a list of available models) | `text-davinci-003`                                                         |
| `attributes.maxTokens`           | [Maximum number of tokens][14] to use per request                                                                  | `300`                                                                      |
| `attributes.temperature`         | [Temperature][15] to use for completion requests (must be a value between `0` and `1`)                             | `0.5`                                                                      |
| `attributes.numberOfCompletions` | [Number of completions][16] to generate for each prompt                                                            | `1`                                                                        |
| `cache.lifetime`                 | Lifetime in seconds of the solutions cache (use `0` to disable caching)                                            | `86400` (1 day)                                                            |

## ⚡ Usage

### Exception handler

The extension provides a modified debug exception handler in [`Error/AiSolverExceptionHandler`][5].
It can be activated in the system configuration (aka `LocalConfiguration.php`):

```php
# config/system/settings.php

return [
    'SYS' => [
        'debugExceptionHandler' => 'EliasHaeussler\\Typo3Solver\\Error\\AiSolverExceptionHandler',
    ],
];
```

Once configured, the exception handler tries to provide a solution for the
current exception and shows it on the exception page.

### Solve a problem on the command line

Next to the exception handler integration, one can also explicitly solve
problems using the provided console command `solver:solve`.

```bash
vendor/bin/typo3 solver:solve <problem> [--code=CODE] [--file=FILE] [--line=LINE] [--refresh] [--json]
```

The following input parameters are available:

| Parameter         | Description                                                                         |
|-------------------|-------------------------------------------------------------------------------------|
| `problem`         | The exception message to solve                                                      |
| `--code`, `-c`    | Optional exception code                                                             |
| `--file`, `-f`    | Optional file where the exception occurs                                            |
| `--line`, `-l`    | Optional line number within the given file                                          |
| `--refresh`, `-r` | Refresh a cached solution (requests a new solution and ignores the cached solution) |
| `--json`, `-j`    | Print solution as JSON                                                              |

### List available models

The command `solver:list-models` can be used to list all available models
for the configured OpenAI API key.

```bash
vendor/bin/typo3 solver:list-models
```

💡 All available models are listed in the [OpenAI documentation][13].

### Flush solution cache

Every solution is cached to reduce the amount of requests sent by
the OpenAI client. In order to flush the solution cache or remove
single cache entries, the command `solver:cache:flush` cam be used.

```bash
vendor/bin/typo3 solver:cache:flush [<identifier>]
```

The following input parameters are available:

| Parameter    | Description                                              |
|--------------|----------------------------------------------------------|
| `identifier` | Optional cache identifier to remove a single cache entry |

## 💎 Credits

The extension icon ("lightbulb-on") is a modified version of the original
[`actions-lightbulb-on`][6] icon from TYPO3 core. In addition, the icons
[`actions-calendar`][7] and [`actions-cpu`][8] from TYPO3 core are used.
All icons are originally licensed under [MIT License][9].

This project is highly inspired by the article [`Fix your Laravel exceptions with AI`][10]
by [Marcel Pociot][11].

In addition, I'd like to thank [Nuno Maduro][12] and all contributors
of the [`openai-php/client`][1] library for this beautiful piece of code.

## ⭐ License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).

[1]: https://github.com/openai-php/client
[2]: https://platform.openai.com/account/api-keys
[3]: Classes/ProblemSolving/Solution/Provider/OpenAISolutionProvider.php
[4]: Classes/ProblemSolving/Solution/Prompt/DefaultPrompt.php
[5]: Classes/Error/AiSolverExceptionHandler.php
[6]: https://typo3.github.io/TYPO3.Icons/icons/actions/actions-lightbulb-on.html
[7]: https://typo3.github.io/TYPO3.Icons/icons/actions/actions-calendar.html
[8]: https://typo3.github.io/TYPO3.Icons/icons/actions/actions-cpu.html
[9]: https://github.com/TYPO3/TYPO3.Icons/blob/main/LICENSE
[10]: https://beyondco.de/blog/ai-powered-error-solutions-for-laravel
[11]: https://pociot.dev/
[12]: https://nunomaduro.com/
[13]: https://platform.openai.com/docs/models
[14]: https://platform.openai.com/docs/api-reference/completions/create#completions/create-max_tokens
[15]: https://platform.openai.com/docs/api-reference/completions/create#completions/create-temperature
[16]: https://platform.openai.com/docs/api-reference/completions/create#completions/create-n
