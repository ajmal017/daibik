var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var jsonObject=JSON.parse(fs.readFileSync('../foSecStockWatch.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content, key) {
  if ( 'data' === key ) {
    _.map( content, function(content_share, key_share) {
      files.push({symbol: content_share.symbol });
    })
  }
})

//var expiry = '26JUL2018';
//var stock_symbol = 'APOLLOHOSP';

/*async.each(files, function(file, callback) {
  var url = 'https://nseindia.com/products/dynaContent/common/productsSymbolMapping.jsp?symbol='+file.symbol+'&segmentLink=3&symbolCount=2&series=ALL&dateRange=12month&fromDate=&toDate=&dataType=PRICEVOLUMEDELIVERABLE';
  var stock = new Mimicry();
  stock.get(url, function(err, data) {
    //console.log(data);
    fs.writeFile('DUMP/' + file.symbol + '.csv', data, (err) => {
      console.log(file.symbol + ' saved!');
  });
  });
});*/

var url = 'https://nseindia.com/products/dynaContent/common/productsSymbolMapping.jsp?symbol=TCS&segmentLink=3&symbolCount=2&series=ALL&dateRange=12month&fromDate=&toDate=&dataType=PRICEVOLUMEDELIVERABLE';
var stock = new Mimicry();
stock.get(url, function(err, data) {
  //console.log(data);
  fs.writeFile('DUMP/TCS.csv', data, (err) => {
    console.log('TCS saved!');
});
});
