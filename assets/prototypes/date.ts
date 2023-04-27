Date.prototype.toJSON = function() {
    console.log(this);
    console.log(this.toDateString());
    return this.toDateString();
};