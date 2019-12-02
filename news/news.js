var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var jsonObject=JSON.parse(fs.readFileSync('moneycontrol_url.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content) {
  files.push({symbol: content.script, url: content.url});
})

showPrice();

function showPrice() {
  async.each(files, function(file, callback) {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/news/news.php?symbol='+file.symbol+'&url='+file.url, function(err, data) {
      console.log(data);
    });
  });
}
