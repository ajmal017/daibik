var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

//const intervalObjFetchPriceRepeat = setInterval(fetchPrice, 60000);
const intervalObjUpdateVolumeRepeat = setTimeout(updatePriceVolume, 5000);


var jsonObject=JSON.parse(fs.readFileSync('foSecStockWatch.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content, key) {
  if ( 'data' === key ) {
    _.map( content, function(content_share, key_share) {
      files.push({symbol: content_share.symbol });
    })
  }
})

function fetchPrice() {
  var d = new Date(2018, 11, 24, 10, 33, 30, 0);
  console.log('fetchPrice = ' + d);

  async.each(files, function(file, callback) {
    var url = 'https://nseindia.com/live_market/dynaContent/live_watch/get_quote/GetQuote.jsp?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '%26';})+'&series=EQ&fromDate=undefined&toDate=undefined&datePeriod=3months&hiddDwnld=true';
    var stock = new Mimicry();
    stock.get(url, function(err, data) {
      var matches = data.match(/<div id="responseDiv" style="display:none">([^<]*)<\/div>/);
      if (matches) {
        fs.writeFile('downloads/' + file.symbol + '.json', matches[1], (err) => {
          console.log(file.symbol + ' saved!');
      });
      }

    });
  });
}

function updatePriceVolume() {
  var d = new Date(2018, 11, 24, 10, 33, 30, 0);
  console.log('updatePriceVolume = ' + d);

  async.each(files, function(file, callback) {
    var stock = new Mimicry();
    stock.get('http://localhost/daibik/btst_stocks/stock_selection.php?symbol='+file.symbol.replace(/&/gi, function myFunction(x){return '_';}), function(err, data) {
      console.log(data);
    });
  });
}
