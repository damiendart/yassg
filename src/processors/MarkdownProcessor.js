// Copyright (C) 2019-2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

/* eslint-env node */

const grayMatter = require('gray-matter');
const marked = require('marked');
const { minify } = require('html-minifier');
const TwigProcessor = require('./TwigProcessor');

class MarkdownProcessor {
  static get INPUT_EXTENSION() {
    return '.md';
  }

  static get OUTPUT_EXTENSION() {
    return '.html';
  }

  static preprocess(data) {
    return {
      ...Object.assign(
        data,
        grayMatter(data.content, { delimiters: ['<!--', '-->'] }).data,
      ),
      renderedMarkdown: minify(
        marked(
          data.content,
          { headerIds: false, smartypants: true },
        ),
        {
          collapseWhitespace: true,
          decodeEntities: true,
          minifyJS: true,
          removeComments: true,
          removeEmptyAttributes: true,
        },
      ),
    };
  }

  static process(data) {
    if ('twigTemplate' in data) {
      return TwigProcessor.process(
        { ...data, inputFilePath: data.twigTemplate },
      );
    }

    return Promise.resolve(data.renderedMarkdown);
  }
}

module.exports = MarkdownProcessor;
