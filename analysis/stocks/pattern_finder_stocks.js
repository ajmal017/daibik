var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObjCall = setTimeout(showPrice, 5000);
console.log('Countdown Starts');

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
  var count=1;
  async.each(files, function(file, callback) {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/analysis/stocks/pattern_finder_stocks.php?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '_';}), function(err, data) {
      if ( data != '' ) {
        console.log(count + '   ' + data);
        count++;
      }
    });
  });
}
