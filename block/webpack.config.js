const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    entry: {
        'index': './src/index.js'
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].js',
    },
    mode: 'production',
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            '@babel/preset-env',
                            ['@babel/preset-react', {
                                "pragma": "wp.element.createElement",
                                "pragmaFrag": "wp.element.Fragment"
                            }]
                        ],
                    }
                }
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].css',
        }),
    ],
    externals: {
        '@wordpress/blocks': 'wp.blocks',
        '@wordpress/i18n': 'wp.i18n',
        '@wordpress/editor': 'wp.editor',
        '@wordpress/block-editor': 'wp.blockEditor',
        '@wordpress/components': 'wp.components',
        '@wordpress/element': 'wp.element',
        'react': 'React',
        'react-dom': 'ReactDOM',
    }
};
