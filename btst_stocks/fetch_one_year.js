var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

/*var jsonObject=JSON.parse(fs.readFileSync('foSecStockWatch.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content, key) {
  if ( 'data' === key ) {
    _.map( content, function(content_share, key_share) {
      files.push({symbol: content_share.symbol });
    })
  }
})


var timeperiod = '1month';

async.each(files, function(file, callback) {
  //var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/getHistoricalData.jsp?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '%26';})+'&series=EQ&fromDate=undefined&toDate=undefined&datePeriod='+timeperiod+'&hiddDwnld=true';
  var url = 'https://nseindia.com/products/dynaContent/common/productsSymbolMapping.jsp?symbol='+ file.symbol.replace(/&/gi, function myFunction(x){return '%26';}) +'&segmentLink=3&symbolCount=2&series=ALL&dateRange=12month&fromDate=&toDate=&dataType=PRICEVOLUMEDELIVERABLE';
  console.log(url);

  var stock = new Mimicry();
  stock.get(url, function(err, data) {
    fs.writeFile('downloads_one_year/' + file.symbol + '.html', data, (err) => {
      console.log(file.symbol + ' saved!');
  });
  });
});*/

var url = 'https://nseindia.com/products/dynaContent/common/productsSymbolMapping.jsp?symbol=ALBK&segmentLink=3&symbolCount=2&series=ALL&dateRange=12month&fromDate=&toDate=&dataType=PRICEVOLUMEDELIVERABLE';
console.log(url);

var stock = new Mimicry();
stock.get(url, function(err, data) {
  fs.writeFile('downloads_one_year/ALBK.html', data, (err) => {
    console.log('ALBK saved!');
});
});
