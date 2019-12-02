var _=require("underscore");
var fs=require("fs");
var async   = require('async');
var Mimicry = require('mimicry');

var stock = new Mimicry();
stock.get('http://localhost/daibik/ol/oh_not_come_yet.php', function(err, data) {
  console.log(data);
});
