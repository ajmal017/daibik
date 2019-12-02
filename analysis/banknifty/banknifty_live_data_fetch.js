var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObj =  setInterval(fetchPrice, 120000);

//fetchPrice();
var expiry = '29NOV2018';

function fetchPrice() {
  var url = 'https://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/ajaxFOGetQuoteJSON.jsp?underlying=BANKNIFTY&instrument=FUTIDX&expiry=29NOV2018&type=SELECT&strike=SELECT';
  var stock = new Mimicry();
  stock.get(url, function(err, data) {
    fs.writeFile('downloads/banknifty.json', data, (err) => {
      console.log('saved!');
    });
  });
}


const intervalObjUpdate =  setInterval(updatePriceVolume, 110000);

function updatePriceVolume() {
  var stock = new Mimicry();
  stock.get('http://localhost/daibik/analysis/banknifty/banknifty.php', function(err, data) {
    console.log(data);
  });
}

