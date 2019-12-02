var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObj = setTimeout(fetchPrice, 8000);

var jsonObject=JSON.parse(fs.readFileSync('oh_not_come_yet.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content) {
  files.push({symbol: content.symbol });
})

var expiry = '26JUL2018';

function fetchPrice() {
  async.each(files, function(file, callback) {
    var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuoteFO.jsp?underlying='+file.symbol+'&instrument=FUTSTK&expiry='+expiry+'&type=-&strike=-#';
    var stock = new Mimicry();
    stock.get(url, function(err, data) {
      //console.log(data);
      fs.writeFile('DOWNLOADS_OPEN_HIGH/' + file.symbol + '.html', data, (err) => {
        console.log(file.symbol + ' saved!');
    });
    });
  });
}

