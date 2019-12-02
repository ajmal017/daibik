var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var jsonObject=JSON.parse(fs.readFileSync('stocks.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content) {
  files.push({symbol: content.symbol });
})

var expiry = '26APR2018';

const intervalObj = setInterval(fetchPrice, 30000);

function fetchPrice() {
  async.each(files, function(file, callback) {
    var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuoteFO.jsp?underlying='+file.symbol+'&instrument=FUTSTK&expiry='+expiry+'&type=-&strike=-#';
    var stock = new Mimicry();
    stock.get(url, function(err, data) {
      //console.log(data);
      fs.writeFile('./downloads/' + file.symbol + '.html', data, (err) => {
        console.log(file.symbol + ' saved!');
      });
    });
  });
}

