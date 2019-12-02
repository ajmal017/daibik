var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const fetchIntervalObj = setInterval(fetchJson, 180000);
const intervalObj = setInterval(datainfo, 190000);

//fetchJson();

function fetchJson() {
  var fnoListCall = new Mimicry();

  fnoListCall.get('https://www.nseindia.com/live_market/dynaContent/live_watch/stock_watch/foSecStockWatch.json', function(err, data) {
    //console.log(data);

    fs.writeFile('foSecStockWatch.json', data, (err) => {
      // success case, the file was saved
      console.log(' downloaded!');
    });

  });

}

function datainfo() {
  var fnoListUpdate = new Mimicry();
  fnoListUpdate.get('http://localhost/daibik/all_fo/fetch_fo_list.php', function(err, data) {
    console.log(data);
  });
}
