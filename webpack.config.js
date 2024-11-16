const path = require('path');

module.exports = {
    mode: 'development',
    entry: {
        "app": './public/js/index.js',
        "editor": './public/js/utils/textEditor.js'
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'dist'),
        clean: true,
    },
};