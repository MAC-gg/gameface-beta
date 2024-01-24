const path = require('path');

module.exports = {
    mode: 'development', // development = no minify, production = minify
    entry: {
        // regApprovalActions: "./js/regApprovalActions.js",
        validation: "./js/validation.js",
        omedacitySandbox: "./js/omedacitySandbox.js",
    },
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'bundled'),
    },
    module: {
        rules: [
            {
                test: /\.m?js/,
                type: "javascript/auto",
            },
            {
                test: /\.m?js/,
                resolve: {
                    fullySpecified: false,
                },
            },
        ]
    }
}