var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObjCall = setTimeout(showPrice, 5000);


var jsonObject=JSON.parse(fs.readFileSync('../foSecStockWatch.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content, key) {
  if ( 'data' === key ) {
    _.map( content, function(content_share, key_share) {
      files.push({symbol: content_share.symbol });
    })
  }
})

function showPrice() {
  async.each(files, function(file, callback) {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/analysis/future/refractor_future_stocks.php?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '_';}), function(err, data) {
      console.log(data);
    });
  });
}
