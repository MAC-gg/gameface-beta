const path = require('path');

module.exports = {
    mode: 'development', // development = no minify, production = minify
    entry: {
        regApprovalActions: "./js/regApprovalActions.js",
        validation: "./js/validation.js",
    },
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'bundled'),
    }
}