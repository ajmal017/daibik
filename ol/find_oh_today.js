var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObjCall = setTimeout(showPrice, 8000);

var jsonObject=JSON.parse(fs.readFileSync('oh_not_come_yet.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content) {
  files.push({symbol: content.symbol });
})

function showPrice() {
  async.each(files, function(file, callback) {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/ol/find_oh_today.php?symbol='+file.symbol, function(err, data) {
      console.log(data);
    });
  });
}
