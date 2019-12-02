var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var jsonObject=JSON.parse(fs.readFileSync('fo_stocks.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content) {
  files.push({symbol: content.symbol });
})

async.each(files, function(file, callback) {
  var url = 'http://localhost/daibik/update_fo_stocks_data.php?symbol='+file.symbol;
  var stock = new Mimicry();
  stock.get(url, function(err, data) {
    //console.log(data);
    fs.writeFile('./FO_CSV/' + file.symbol + '.csv', data, (err) => {
      console.log(file.symbol + ' updated!');
  });
  });
});
