var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

//datainfo();
const intervalObj = setInterval(datainfo, 6000);

function datainfo() {
  var fnoGainersCall = new Mimicry();

  var fnoGainers = 'https://www.nseindia.com/live_market/dynaContent/live_analysis/gainers/fnoGainers1.json';
  fnoGainersCall.get(fnoGainers, function(err, data) {
    //console.log(data);
    var fnoGainersUpdate = new Mimicry();
    fnoGainersUpdate.get('http://localhost/daibik/gainers_loosers/toppers.php?type=fnoGainers&data='+data, function(err, data) {
      console.log(data);
    });
  });


  var fnoLosersCall = new Mimicry();

  var fnoLosers = 'https://www.nseindia.com/live_market/dynaContent/live_analysis/losers/fnoLosers1.json';
  fnoLosersCall.get(fnoLosers, function(err, data) {
    //console.log(data);
    var fnoLosersUpdate = new Mimicry();
    fnoLosersUpdate.get('http://localhost/daibik/gainers_loosers/toppers.php?type=fnoLosers&data='+data, function(err, data) {
      console.log(data);
    });
  });
}


