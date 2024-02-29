const path = require('path');
// const HtmlWebpackPlugin = require('html-webpack-plugin');
const fs = require('fs');

// Dynamically find all .js files in src/pages
const pages = fs.readdirSync(path.resolve(__dirname, 'src/pages'))
    .filter(file => file.endsWith('.js'))
    .map(file => file.replace('.js', ''));

// Create an entry object with a key for each page
const entry = pages.reduce((entry, page) => {
    entry[page] = `./src/pages/${page}.js`;
    return entry;
}, {});

// // Generate an HtmlWebpackPlugin instance for each page
// const htmlPlugins = pages.map(page => new HtmlWebpackPlugin({
//     inject: true,
//     chunks: [page],
//     filename: `${page}.html`,
//     template: 'src/index.html' // You would need a generic template file here
// }));

module.exports = {
    entry: entry,
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].bundle.js',
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                },
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
    plugins: [
        // ...htmlPlugins,
        // Add any additional plugins here
    ],
};