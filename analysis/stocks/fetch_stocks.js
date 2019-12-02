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


/*var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/getHistoricalData.jsp?symbol=VEDL&series=EQ&fromDate=undefined&toDate=undefined&datePeriod=1month&hiddDwnld=true';
var stock = new Mimicry();
stock.get(url, function(err, data) {
  //console.log(data);
  //fs.writeFile('DUMP/' + file.symbol + '.csv', data, (err) => {
  fs.writeFile('DUMP/VEDL.csv', data, (err) => {
    console.log('VEDL saved!');
});
});*/


var timeperiod = '1month';

async.each(files, function(file, callback) {
  //var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/getHistoricalData.jsp?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '%26';})+'&series=EQ&fromDate=undefined&toDate=undefined&datePeriod='+timeperiod+'&hiddDwnld=true';
  var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/getHistoricalData.jsp?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '%26';})+'&series=EQ&fromDate=undefined&toDate=undefined&datePeriod='+timeperiod+'&hiddDwnld=true';
  var stock = new Mimicry();
  stock.get(url, function(err, data) {
    fs.writeFile('DUMP/' + file.symbol + '.csv', data, (err) => {
      console.log(file.symbol + ' saved!');
  });
  });
});
