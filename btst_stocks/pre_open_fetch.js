var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var stock = new Mimicry();
stock.get('https://nseindia.com/live_market/dynaContent/live_analysis/pre_open/fo.json', function(err, data) {
    fs.writeFile('fo.json', data, (err) => {
        console.log(' saved!');
});
});
