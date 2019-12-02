var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

const intervalObjCall = setTimeout(showPrice, 5000);

var jsonObject=JSON.parse(fs.readFileSync('foSecStockWatch.json', 'utf8'));

var files = [];
_.map( jsonObject, function(content, key) {
  if ( 'data' === key ) {
    _.map( content, function(content_share, key_share) {
      if ( 'null' != content_share.symbol ) {
        //console.log( content_share.symbol );
        files.push({symbol: content_share.symbol });
      }
    })
  }
})

function showPrice() {
  async.each(files, function(file, callback) {
    if ( null !== file.symbol ) {
      console.log( file.symbol );
      var stock = new Mimicry();
      stock.get('http://localhost/daibik/btst_stocks/refractor_stocks.php?symbol=' + file.symbol.replace(/&/gi, function myFunction(x) {
        return '_';
      }), function (err, data) {
        console.log(err);
      });
    }
  });
}
