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

import {fileURLToPath} from 'url';
import path from 'path';
import {readFileSync} from 'fs';

const licenseFile = `${path.dirname(fileURLToPath(import.meta.url))}/license-header.txt`;
const currentYear = new Date().getFullYear().toString();
const lines = readFileSync(licenseFile).toString()
  .replace('<%= YEAR %>', currentYear)
  .trim()
  .split('\n')
;

const licenseHeader = [
  '/*',
  ...lines.map((line) => ` * ${line}`.trimEnd()),
  ' */'
];

export default licenseHeader;
