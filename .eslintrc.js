module.exports = {
    "env": {
        "browser": true,
        "es6": true,
        "node": true,
    },
    "extends": [
        "eslint:recommended",
        "plugin:vue/essential"
    ],
    "parserOptions": {
        "ecmaVersion": 12,
        "sourceType": "module",
        "skipTemplates": true,
        "skipRegExps": true,
        "skipComments": true,
    },
    "plugins": [
        "vue"
    ],
    settings: {
        "import/resolver": {
            webpack: {
                config: "webpack.config.js"
            }
        }
    },
    "globals": {
        "_": false,
        "axios": false,
    },
    "rules": {
        "indent": ["error", 4],
        "vue/html-indent": ["error", 4],
        "no-console": "off",
    }
};
