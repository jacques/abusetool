
/*
 * GET home page.
 */

exports.index = function(req, res){
  res.render('index', { title: 'Abuse Tool' });
};

/*
 * GET whois page.
 */
exports.whois = function(req, res){
  var mod_whois = require('node-whois');

  mod_whois.lookup(req.params.ip, function(err, data) {
    console.log(err, data);

    res.render('whois', { title: 'Whois', ip: req.params.ip, data: data });
  });
};
