Date.prototype.toJSON = function() {
    return this.toUTCString();
};