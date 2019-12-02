var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObj = setTimeout(updatePriceVolume, 5000);

function updatePriceVolume() {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/analysis/banknifty/banknifty.php', function(err, data) {
      console.log(data);
    });
}
