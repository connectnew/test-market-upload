const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@base': path.resolve(__dirname, 'resources/js/'),
            '@comp': path.resolve(__dirname, 'resources/js/components'),
            '@stor': path.resolve(__dirname, 'resources/js/store'),
        },
        extensions: ['*', '.json', '.js', '.jsx', '.vue', '.ts', '.tsx'],
    },
};
