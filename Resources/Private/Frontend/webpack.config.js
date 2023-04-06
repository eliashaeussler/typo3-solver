'use strict'

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

// noinspection JSUnusedLocalSymbols
const webpack = require('webpack');
const path = require('path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const isDev = process.env.NODE_ENV !== 'production';

module.exports = [
  {
    mode: isDev ? 'development' : 'production',
    devtool: isDev ? 'eval-cheap-module-source-map' : false,
    entry: './src/scripts/main.ts',
    output: {
      path: path.resolve(__dirname, '../../Public/JavaScript'),
    },
    plugins: [
      new CleanWebpackPlugin(),
    ],
    module: {
      rules: [
        {
          test: /\.tsx?$/,
          loader: 'babel-loader',
        },
      ],
    },
    resolve: {
      extensions: ['.ts', '.tsx'],
    },
  },
  {
    mode: isDev ? 'development' : 'production',
    devtool: isDev ? 'eval-cheap-module-source-map' : false,
    output: {
      path: path.resolve(__dirname, '../../Public/Css'),
    },
    entry: './src/styles/main.scss',
    plugins: [
      new CleanWebpackPlugin(),
      new MiniCssExtractPlugin(),
      new IgnoreEmitPlugin(/\.js$/),
    ],
    module: {
      rules: [
        {
          test: /\.scss$/,
          use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
        },
      ],
    },
  },
];
