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

import eslint from '@eslint/js';
import licenseHeader from 'eslint-plugin-license-header';
import licenseText from './res/license-header.js';
import tseslint from 'typescript-eslint';

export default [
  eslint.configs.recommended,
  ...tseslint.configs.recommended,
  {
    languageOptions: {
      globals: {
        browser: true,
        es2021: true,
        node: true,
      },
      parserOptions: {
        ecmaFeatures: {
          impliedStrict: true,
        },
        ecmaVersion: 2021,
        sourceType: 'module',
      },
    },
    plugins: {
      'license-header': licenseHeader,
    },
    rules: {
      'license-header/header': ['error', licenseText],
      '@typescript-eslint/no-unused-vars': 'off',
    },
  },
];
