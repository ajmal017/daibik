var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var stock = new Mimicry();
stock.get(url, function(err, data) {
  //console.log(data);
  fs.writeFile('./moving_average/' + file.symbol + '.csv', data, (err) => {
    console.log(file.symbol + ' saved!');
});
});
