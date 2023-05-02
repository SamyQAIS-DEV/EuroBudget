Date.prototype.toJSON = function() {
    return this.toDateString();
};