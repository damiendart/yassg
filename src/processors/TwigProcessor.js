// Copyright (C) 2019-2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

/* eslint-env node */

const { minify } = require('html-minifier');
const {
  TwingEnvironment,
  TwingFilter,
  TwingLoaderFilesystem,
} = require('twing');

const marked = require('marked');
const path = require('path');

class TwigProcessor {
  static get INPUT_EXTENSION() {
    return '.twig';
  }

  static get OUTPUT_EXTENSION() {
    return '';
  }

  // eslint-disable-next-line no-underscore-dangle
  static _initEnvironment(directory) {
    const twingLoader = new TwingLoaderFilesystem(directory);
    const twingEnvironment = new TwingEnvironment(
      twingLoader,
      { strict_variables: true },
    );

    twingLoader.addPath(
      path.resolve(__dirname, '../../node_modules/toolbox-sass/twig'),
      'toolbox-sass',
    );

    twingEnvironment.addFilter(
      new TwingFilter(
        'markdown',
        (markup) => {
          const markdown = markup.toString();
          const indentation = markdown.match(/^\s*/);
          const indentationRegex = new RegExp(
            `^${Array.isArray(indentation) ? indentation[0] : ''}`,
          );

          return Promise.resolve(
            marked(
              markdown.split(/\r?\n/).reduce(
                (carry, line) => `${carry}${line.replace(indentationRegex, '')}\n`,
                '',
              ),
              { headerIds: false, smartypants: true },
            ),
          );
        },
        [],
        { is_safe: ['html'] },
      ),
    );

    twingEnvironment.addFilter(
      new TwingFilter(
        'smartypants',
        // The following is based on Marked's SmartyPants
        // implementation. This implementation is only suitable for
        // processing plain text as it will happily destroy HTML markup.
        (string) => Promise.resolve(
          string
            .replace(/---/g, '—')
            .replace(/--/g, '–')
            .replace(/(^|[-—/([{"\s])'/g, '$1‘')
            .replace(/'/g, '’')
            .replace(/(^|[-—/([{‘\s])"/g, '$1“')
            .replace(/"/g, '”')
            .replace(/\.{3}/g, '…'),
        ),
        [],
        { is_safe: ['html'] },
      ),
    );

    twingEnvironment.addFilter(
      new TwingFilter(
        'widont',
        // The following is based on
        // <http://justinhileman.info/article/a-jquery-widont-snippet/>.
        (string) => {
          if (string.split(' ').filter((n) => n !== '').length <= 2) {
            return Promise.resolve(string);
          }

          return Promise.resolve(
            string.replace(/\s([^\s<]+)\s*$/, '&nbsp;$1'),
          );
        },
        [],
        { is_safe: ['html'] },
      ),
    );

    return twingEnvironment;
  }

  static preprocess(data) {
    return data;
  }

  static process(data) {
    // eslint-disable-next-line no-underscore-dangle
    return TwigProcessor
      ._initEnvironment(data.inputBaseDirectoryPath)
      .render(
        data.inputFilePath.replace(data.inputBaseDirectoryPath, ''),
        data,
      )
      .then((output) => {
        if (data.outputFilePath.match(/(html?|php)$/)) {
          return minify(
            output,
            {
              collapseWhitespace: true,
              decodeEntities: true,
              minifyJS: true,
              removeComments: true,
              removeEmptyAttributes: true,
            },
          );
        }

        return output;
      });
  }
}

module.exports = TwigProcessor;
