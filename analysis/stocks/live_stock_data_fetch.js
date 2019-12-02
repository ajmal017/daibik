var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObj = setTimeout(fetchPrice, 5000);

var jsonObject=JSON.parse(fs.readFileSync('../foSecStockWatch.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content, key) {
  if ( 'data' === key ) {
    _.map( content, function(content_share, key_share) {
      files.push({symbol: content_share.symbol });
    })
  }
})

function fetchPrice() {
  async.each(files, function(file, callback) {
    //var url = 'https://nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuote.jsp?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '%26';})+'&series=EQ&fromDate=undefined&toDate=undefined&datePeriod=1months&hiddDwnld=true';
    var url = 'https://nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuote.jsp?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '%26';});
    var stock = new Mimicry();
    stock.get(url, function(err, data) {
      //console.log(data);
      /*fs.writeFile('downloads/' + file.symbol + '.html', data, (err) => {
        console.log(file.symbol + ' saved!');
    });*/

      var matches = data.match(/<div id="responseDiv" style="display:none">([^<]*)<\/div>/);
      if (matches) {
        fs.writeFile('downloads/' + file.symbol + '.json', matches[1], (err) => {
          console.log(file.symbol + ' saved!');
        });
      }

    });
  });
}

