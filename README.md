<div align="center">

![Error page screenshot](Documentation/Images/Screenshots/ErrorPage.png)

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
[![Slack](https://img.shields.io/badge/slack-%23ext--solver-4a154b?logo=slack)](https://typo3.slack.com/archives/C04Q3440HS6)

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
* Compatible with TYPO3 11.5 LTS and 12.3

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

#### `provider`

FQCN of the solution provider to use.

* *Required:* ✅
* *Default:* [`EliasHaeussler\Typo3Solver\Solution\Provider\OpenAISolutionProvider`][3]

#### `prompt`

FQCN of the prompt generator to use.

* *Required:* ✅
* *Default:* [`EliasHaeussler\Typo3Solver\Prompt\DefaultPrompt`][4]

#### `ignoredCodes`

Comma-separated list of exception codes to ignore.

* *Required:* –
* *Default:* –

#### `api.key`

[API key](#api-key) for OpenAI requests.

* *Required:* ✅
* *Default:* –

#### `attributes.model`

[OpenAI model][13] to use (see [List available models](#list-available-models)
to show a list of available models).

* *Required:* ✅
* *Default:* `gpt-3.5-turbo-0301`

#### `attributes.maxTokens`

[Maximum number of tokens][14] to use per request.

* *Required:* ✅
* *Default:* `300`

#### `attributes.temperature`

[Temperature][15] to use for completion requests (must be a value between `0` and `1`).

* *Required:* ✅
* *Default:* `0.5`

#### `attributes.numberOfCompletions`

[Number of completions][16] to generate for each prompt.

* *Required:* ✅
* *Default:* `1`

#### `cache.lifetime`

Lifetime in seconds of the solutions cache (use `0` to disable caching).

* *Required:* ✅
* *Default:* `86400` *(= 1 day)*

## ⚡ Usage

### Exception handler

The extension provides a modified debug exception handler in [`Error/AiSolverExceptionHandler`][5].
It can be activated in the system configuration (formerly `LocalConfiguration.php`):

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
vendor/bin/typo3 solver:solve [<problem>] [options]
```

Problems can be solved in two ways on the command line:

1. Pass the [problem](#problem) (= exception message) and additional metadata
   such as exception [code](#--code--c), [file](#--file--f) and
   [line](#--line--l). By using this way, EXT:solver will create a dummy
   exception and pass it to the solution provider.
2. Pass an exception [cache identifier](#--identifier--i) to solve a cached
   exception. This way is more accurate as it restores the original exception
   and passes it to the solution provider.

💡 You can find the exception cache identifier on exception pages. It is
assigned as `data-exception-id` attribute to the solution container element.

The following input parameters are available:

#### `problem`

The exception message to solve.

> **Note** You must either pass the [`problem`](#problem) argument
or [`--identifier`](#--identifier--i) option.

```bash
vendor/bin/typo3 solver:solve "No TypoScript record found!"
```

#### `--identifier`, `-i`

An alternative cache identifier to load an exception from cache.

> **Note** You must either pass the [`problem`](#problem) argument
  or [`--identifier`](#--identifier--i) option.

```bash
vendor/bin/typo3 solver:solve -i c98d277467ab5da857483dff2b1d267d36c0c24a
```

#### `--code`, `-c`

Optional exception code.

> **Note** This option is only respected in combination with the
  [`problem`](#problem) argument.

```bash
vendor/bin/typo3 solver:solve "No TypoScript record found!" -c 1294587218
```

#### `--file`, `-f`

Optional file where the exception occurs.

> **Note** This option is only respected in combination with the
  [`problem`](#problem) argument.

```bash
vendor/bin/typo3 solver:solve "No TypoScript record found!" -f /var/www/html/vendor/typo3/cms-frontend/Classes/Controller/TypoScriptFrontendController.php
```

#### `--line`, `-l`

Optional line number within the given file.

> **Note** This option is only respected in combination with the
  [`problem`](#problem) argument.

```bash
vendor/bin/typo3 solver:solve "No TypoScript record found!" -l 1190
```

#### `--refresh`, `-r`

Refresh a cached solution (removes the cached solution and requests a new solution).

```bash
vendor/bin/typo3 solver:solve "No TypoScript record found!" --refresh
```

#### `--json`, `-j`

Print solution as JSON.

```bash
vendor/bin/typo3 solver:solve "No TypoScript record found!" --json
```

### List available models

The command `solver:list-models` can be used to list all available models
for the configured OpenAI API key. Note that EXT:solver uses the
[chat completion][20] component to generate solutions. You must select a
model being available with the chat completion component only.

```bash
vendor/bin/typo3 solver:list-models
```

💡 All available models are listed in the [OpenAI documentation][13].

### Flush solution cache

Every solution is cached to reduce the amount of requests sent by
the OpenAI client. In order to flush the solution cache or remove
single cache entries, the command `solver:cache:flush` can be used.

```bash
vendor/bin/typo3 solver:cache:flush [<identifier>]
```

The following input parameters are available:

#### `identifier`

Optional cache identifier to remove a single cache entry.

```bash
vendor/bin/typo3 solver:cache:flush 65e89b311899aa4728a4c1bced1d6f6335674422
```

## 🚧 Migration

### 0.1.x → 0.2.x

#### Chat completion component

The used OpenAI component changed from text completion to chat completion.

* Migrate the used model in your [extension configuration](#attributesmodel).
  The new default model is `gpt-3.5-turbo-0301`.

#### Solution Stream

Solutions are now streamed to exception pages.

* Migrate custom solution providers to implement
  [`ProblemSolving\Solution\Provider\StreamedSolutionProvider`][21].
* Note the modified DOM structure for solutions on exception pages.

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## 💎 Credits

The extension icon ("lightbulb-on") is a modified version of the original
[`actions-lightbulb-on`][6] icon from TYPO3 core. In addition, the icons
[`actions-calendar`][7], [`actions-cpu`][8], [`actions-debug`][17],
[`actions-exclamation-triangle-alt`][18] and [`spinner-circle`][19] from
TYPO3 core are used. All icons are originally licensed under [MIT License][9].

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
[14]: https://platform.openai.com/docs/api-reference/chat/create#chat/create-max_tokens
[15]: https://platform.openai.com/docs/api-reference/chat/create#chat/create-temperature
[16]: https://platform.openai.com/docs/api-reference/chat/create#chat/create-n
[17]: https://typo3.github.io/TYPO3.Icons/icons/actions/actions-debug.html
[18]: https://typo3.github.io/TYPO3.Icons/icons/actions/actions-exclamation-triangle-alt.html
[19]: https://typo3.github.io/TYPO3.Icons/icons/spinner/spinner-circle.html
[20]: https://platform.openai.com/docs/guides/chat
[21]: Classes/ProblemSolving/Solution/Provider/StreamedSolutionProvider.php
