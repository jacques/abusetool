
/**
 * Module dependencies.
 */

var mod_assert = require('assert');
var mod_bunyan = require('bunyan');

var express = require('express');
var routes = require('./routes');
var user = require('./routes/user');
var http = require('http');
var path = require('path');

var LOG = mod_bunyan.createLogger({
  name: 'abusetool',
  stream: process.stdout,
  level: 'info'
});

var app = express();

app.set('logger', LOG);

// all environments
//app.set('host', process.env.HOST || '127.0.0.1');
app.set('port', process.env.PORT || 3000);
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');
app.use(express.favicon());
// bunyan logging
app.use(function(req, res, next) {
    var end = res.end;
    req._startTime = new Date();
    res.end = function(chunk, encoding){
        res.end = end;
        res.end(chunk, encoding);
        LOG.info({req: req, res: res, total_time: new Date() - req._startTime}, 'handled request/response');
    };

    next();
});
app.use(express.json());
app.use(express.urlencoded());
app.use(express.methodOverride());
app.use(express.cookieParser('your secret here'));
app.use(express.session());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

// development only
if ('development' === app.get('env')) {
  app.use(express.errorHandler());
}

app.get('/', routes.index);
app.get('/whois/:ip', routes.whois);
app.get('/users', user.list);

http.createServer(app).listen(app.get('port'), function(){
  LOG.info('Express server listening on port ' + app.get('port'));
});
