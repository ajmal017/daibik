var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObjCall = setInterval(showPrice, 30000);

var jsonObject=JSON.parse(fs.readFileSync('stocks.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content) {
  files.push({symbol: content.symbol });
})

function showPrice() {
  async.each(files, function(file, callback) {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/stock_price_update.php?symbol='+file.symbol, function(err, data) {
      console.log(data);
    });
  });
}
