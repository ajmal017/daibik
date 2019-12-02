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
    var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/getHistoricalData.jsp?symbol='+file.symbol+'&series=EQ&fromDate=undefined&toDate=undefined&datePeriod=3months&hiddDwnld=true';
    var stock = new Mimicry();
    stock.get(url, function(err, data) {
      //console.log(data);
      fs.writeFile('./FO_CSV/' + file.symbol + '.csv', data, (err) => {
        console.log(file.symbol + ' saved!');
    });
    });
});
