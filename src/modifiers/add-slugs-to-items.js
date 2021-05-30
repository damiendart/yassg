// Copyright (C) 2019-2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

function addSlugsToItems(globalData, items) {
  return [
    globalData,
    items.map((item) => {
      if (item.data.outputFilePath.match(/(html?|php)/) === null) {
        return item;
      }

      const updatedItem = item;

      updatedItem.data.slug = item.data.outputFilePath.replace(
        new RegExp(
          `(${globalData.outputBaseDirectoryPath}/|(index)?.(html?|php)$)`,
          'g',
        ),
        '',
      );

      return updatedItem;
    }),
  ];
}

module.exports = addSlugsToItems;
