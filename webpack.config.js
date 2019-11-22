const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@styles': path.join(__dirname, 'resources/assets/sass'),
        }
    }
};

