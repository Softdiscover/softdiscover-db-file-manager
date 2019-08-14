// SuppressEntryChunksPlugin.js

function SuppressEntryChunksPlugin(options) {
  if (typeof options === 'string') {
    this.options = {skip: [options]};
  } else if (Array.isArray(options)) {
    this.options = {skip: options};
  } else {
    throw new Error("SuppressEntryChunksPlugin requires an array of entry names to strip");
  }
}

SuppressEntryChunksPlugin.prototype.apply = function(compiler) {
  var options = this.options;

  // just before webpack is about to emit the chunks,
  // strip out primary file assets (but not additional assets)
  // for entry chunks we've been asked to suppress
  compiler.plugin('emit', function(compilation, callback) {
    compilation.chunks.forEach(function(chunk) {
      if (options.skip.indexOf(chunk.name) >= 0) {
        chunk.files.forEach(function(file) {
          // delete only js files.
          if (file.match(/.*\.js$/)) {
            delete compilation.assets[file];
          }
        });
      }
    });
    callback();
  });
};

module.exports = SuppressEntryChunksPlugin;