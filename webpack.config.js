const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    mode: 'development', // development = no minify, production = minify
    entry: {
        user_actions: "./js/user_actions.js",
        user_styles: "./scss/user_styles.scss"
    },
    module: {
        rules: [
            {
                test: /\.scss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'sass-loader'
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin ({filename:"css/[name].css"})
    ],
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'bundled'),
    }
}