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

import del from 'rollup-plugin-delete';
import nodeResolve from '@rollup/plugin-node-resolve';
import noEmit from 'rollup-plugin-no-emit';
import postcss from 'rollup-plugin-postcss';
import terser from '@rollup/plugin-terser';
import typescript from '@rollup/plugin-typescript';

// eslint-disable-next-line no-undef
const isDev = process.env.NODE_ENV !== 'production';

// Options for cssnano on production builds
const minimizeOptions = {
  preset: [
    'default',
    {
      discardComments: {
        removeAll: true,
      },
    },
  ],
};

export default [
  {
    input: 'src/scripts/main.ts',
    output: {
      dir: '../../Resources/Public/JavaScript',
      format: 'esm',
      sourcemap: isDev ? 'inline' : false,
    },
    plugins: [
      del({
        targets: '../../Resources/Public/JavaScript/*',
        force: true,
      }),
      nodeResolve(),
      terser({
        format: {
          comments: false,
        },
      }),
      typescript({
        outputToFilesystem: true,
      }),
    ],
  },
  {
    input: 'src/styles/main.scss',
    output: {
      dir: '../../Resources/Public/Css',
    },
    plugins: [
      del({
        targets: '../../Resources/Public/Css/*',
        force: true,
      }),
      nodeResolve({
        extensions: ['.css'],
      }),
      postcss({
        extract: 'main.css',
        minimize: isDev ? false : minimizeOptions,
        sourceMap: isDev ? 'inline' : false,
        use: ['sass'],
      }),
      noEmit({
        match: (fileName) => fileName.match(/\.js$/),
      }),
    ],
  }
];
