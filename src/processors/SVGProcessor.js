// Copyright (C) 2019-2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

/* eslint-env node */

const SVGO = require('svgo');

class SVGProcessor {
  static get INPUT_EXTENSION() {
    return '.svg';
  }

  static get OUTPUT_EXTENSION() {
    return this.INPUT_EXTENSION;
  }

  static preprocess(data) {
    return data;
  }

  static process(data) {
    return new SVGO().optimize(data.content).then((output) => output.data);
  }
}

module.exports = SVGProcessor;
