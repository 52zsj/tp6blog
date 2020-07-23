const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
module.exports = {
    context: path.resolve(__dirname, 'public/static'),
    entry: {
        login: './source/js/login.js',
        index: './source/js/index.js',
        "auth.admin": './source/js/auth.admin.js',
        "auth.rule": './source/js/auth.rule.js'
    },
    optimization: {
        usedExports: true,
        splitChunks: {
            cacheGroups: {
                styles: {
                    name: 'styles',
                    test: /\.css$/,
                    chunks: 'all',
                    enforce: true,
                },
            },
        },
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            axios: 'axios',
        }),
        new MiniCssExtractPlugin({
            // Options similar to the same options in webpackOptions.output
            // both options are optional
            filename: "../css/[name].css",
            chunkFilename: '[id].css',
        }),
        new CleanWebpackPlugin(),
    ],
    module: {
        rules: [
            {
                test: /\.(css|scss|less)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            publicPath: '../css/',
                            reloadAll: true,
                        },
                    },
                    'css-loader',
                    'less-loader',
                ],
            },
            {
                test: /\.(svg|jpg|png|jpeg|gif)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 10240,
                            outputPath: '../img/',
                            useRelativePath: true
                        },

                    }
                ]
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            outputPath: '../font/',
                            useRelativePath: true
                        }
                    }
                ]
            },
        ],
    },
    output: {
        filename: "[name].bundle.js",
        path: path.resolve(__dirname, 'public/static/backend/js'),
    },
    mode: 'development',
    devtool: 'cheap-module-eval-source-map',//eval
};