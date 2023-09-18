const path = require('path');

module.exports = {
    mode: 'development', // development = no minify, production = minify
    entry: {
        user_actions: "./js/user_actions.js",
    },
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'bundled'),
    }
}