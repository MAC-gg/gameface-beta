const path = require('path');

module.exports = {
    entry: {
        user_actions: "./js/user_actions.js"
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'bundled/js'),
    },
    mode: 'development', // development = no minify, production = minify
}