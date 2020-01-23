// This originated from https://gist.github.com/CalebEverett/bed94582b437ffe88f650819d772b682
// and was modified to suite our needs
const fs = require('fs'),
    WebSocket = require('ws'),
    express = require('express'),
    http = require("http"),
    https = require('https'),
    mysql = require('mysql'),
    expressWs = require('express-ws'),
    path = require('path'),
    cors = require('cors'),
    request = require("request");

const envImportResult = require('dotenv').config({
    path: "/var/www/LxdMosaic/.env"
});

if (envImportResult.error) {
    throw envImportResult.error
}

// Https certificate and key file location for secure websockets + https server
var privateKey = fs.readFileSync(process.env.CERT_PRIVATE_KEY, 'utf8'),
    certificate = fs.readFileSync(process.env.CERT_PATH, 'utf8');
    certDir = "/var/www/LxdMosaic/src/sensitiveData/certs/",
    lxdConsoles = [],
    credentials = {
        key: privateKey,
        cert: certificate
    },
    app = express();

app.use(cors());
var bodyParser = require('body-parser')
app.use( bodyParser.json() );       // to support JSON-encoded bodies
app.use(bodyParser.urlencoded({     // to support URL-encoded bodies
  extended: true
}));

var httpServer = http.createServer(app).listen(8001);
var httpsServer = https.createServer(credentials, app);
var io = require('socket.io')(httpsServer);

var operationSocket = io.of("/operations")
// expressWs(app, httpsServer);

var con = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME
});

var hostDetails = {};

function createExecOptions(host, container) {
    return {
        method: 'POST',
        host: hostDetails[host].hostWithOutProtoOrPort,
        port: hostDetails[host].port,
        path: '/1.0/containers/' + container + '/exec',
        cert: fs.readFileSync(hostDetails[host].cert),
        key: fs.readFileSync(hostDetails[host].key),
        rejectUnauthorized: false
    }
}

const lxdExecBody = {
    "command": ["bash"],
    "environment": {
        "HOME": "/root",
        "TERM": "xterm",
        "USER": "root"
    },
    "wait-for-websocket": true,
    "interactive": true,
}


con.connect(function(err) {
    if (err) {
        throw err;
    }
});

function createWebSockets() {
    con.query("SELECT * FROM Hosts", function(err, result, fields) {
        if (err) {
            throw err;
        }
        // Clear host details on reload of hosts to prevent deleted hosts
        // persisting
        hostDetails = {};

        for (i = 0; i < result.length; i++) {
            let lxdClientCert = certDir + result[i].Host_Cert_Only_File
            let lxdClientKey = certDir + result[i].Host_Key_File

            if(result[i].Host_Online == 0){
                continue;
            }

            // Connecting to the lxd server/s
            const wsoptions = {
                cert: fs.readFileSync(lxdClientCert),
                key: fs.readFileSync(lxdClientKey),
                rejectUnauthorized: false,
            }

            var portRegex = /:[0-9]+/;

            let stringUrl = result[i].Host_Url_And_Port;
            let urlURL = new URL(result[i].Host_Url_And_Port);

            let hostWithOutProto = stringUrl.replace("https://", "");
            let hostWithOutProtoOrPort = hostWithOutProto.replace(portRegex, "");

            hostDetails[result[i].Host_ID] = {
                cert: lxdClientCert,
                key: lxdClientKey,
                hostWithOutProtoOrPort: hostWithOutProtoOrPort,
                port: urlURL.port
            };

            var ws = new WebSocket('wss://' + hostWithOutProto + '/1.0/events?type=operation', wsoptions);

            ws.on('message', function(data, flags) {
                var buf = Buffer.from(data)
                let message = JSON.parse(data.toString());
                message.host = hostWithOutProtoOrPort;
                operationSocket.emit('operationUpdate', message);
            });
        }
    });
}


httpsServer.listen(3000, function() {});


app.get('/hosts/reload/', function(req, res) {
    createWebSockets();
    res.send({
        success: "reloaded"
    });
});

app.post('/hosts/message/', function(req, res) {
    operationSocket.emit(req.body.type, req.body.data);
    res.send({
        success: "delivered"
    });
});

app.get('/', function(req, res) {
    res.sendFile(path.join(__dirname + '/index.html'));
});

var terminalsIo = io.of("/terminals");

terminalsIo.on("connect", function(socket) {

    let indentifier = socket.handshake.query.pid;
    let shell = socket.handshake.query.shell;
    if(typeof shell == "string" && shell !== ""){
        lxdExecBody.command = [shell];
    }

    if(lxdConsoles[indentifier] == undefined) {
        let host = socket.handshake.query.hostId;
        let container = socket.handshake.query.container;

        let execOptions = createExecOptions(host, container);

        const wsoptions = {
            cert: execOptions.cert,
            key: execOptions.key,
            rejectUnauthorized: false
        }



        const lxdReq = https.request(execOptions, res => {
            res.on('data', d => {

                const output = JSON.parse(d);

                if(output.hasOwnProperty("error") && output.error !== ""){
                    socket.emit("data", "Container Offline");
                    return false;
                }

                const lxdWs = new WebSocket('wss://' +
                    execOptions.host + ':' + execOptions.port + output.operation +
                    '/websocket?secret=' + output.metadata.metadata.fds['0'],
                    wsoptions
                );

                lxdWs.on('error', error => console.log(error));

                lxdWs.on('message', data => {
                    try {
                        const buf = Buffer.from(data);
                        data = buf.toString();
                        socket.emit("data", data);
                    } catch (ex) {
                        // The WebSocket is not open, ignore
                    }
                });
                lxdConsoles.push(lxdWs);
            });
        });
        lxdReq.write(JSON.stringify(lxdExecBody));
        lxdReq.end();
    }

    //NOTE When user inputs from browser
    socket.on('data', function(msg) {
        if(lxdConsoles[indentifier] == undefined){
            return;
        }

        lxdConsoles[indentifier].send(msg, {
            binary: true
        }, () => {});
    });

    socket.on('close', function(indentifier) {
        setTimeout(() => {
            if(lxdConsoles[indentifier] == undefined){
                return
            }
             lxdConsoles[indentifier].send('exit  \r', { binary: true }, function(){
                lxdConsoles[indentifier].close();
                delete lxdConsoles[indentifier];
            });
         }, 100);
    });
});

app.post('/terminals', function(req, res) {
    // Create a indentifier for the console, this should allow multiple consolses
    // per user
    res.send(lxdConsoles.length.toString());
});

app.post('/deploymentProgress/:deploymentId', function(req, res) {
    let body = req.body;

    body.deploymentId = req.params.deploymentId;

    if(body.hasOwnProperty("hostname") !== true){
        // https://stackoverflow.com/questions/3050518/what-http-status-response-code-should-i-use-if-the-request-is-missing-a-required
        res.statusMessage = "Please provide host name in req body";
        res.status(422).end()
    }else{
        request.post({

            url: 'https://lxd.local/api/Deployments/UpdatePhoneHomeController/update',
            formData: {
                deploymentId: parseInt(body.deploymentId),
                hostname: body.hostname
            },
            rejectUnauthorized: false
        },
        function (error, response, body) {
            //TODO silent error failing
        }
        );

        operationSocket.emit("deploymentProgress", body);
    }
    // Send an empty response
    res.send()
});


createWebSockets();
