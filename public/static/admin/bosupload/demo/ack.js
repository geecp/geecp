var http = require('http');
var url = require('url');
var util = require('util');

var sdk = require('bce-sdk-js');

var kCredentials = {
    // ak: 'b92ea4a39f3645c8ae5f64ba5fc2a357',
    // sk: 'a4ce012968714958a21bb90dc180de17'
    ak: 'd0d4f217d2a4414ca63361a53dbe74a9',
    sk: 'f80754a0412b4e0c9d7a12acf1b417d9',

    // ak: '724c9abc6cd9403daece9d4d17c3e31b',
    // sk: 'a3ce5284f0d844c2b2deb52e4b785002'
};

function safeParse(text) {
    try {
        return JSON.parse(text);
    }
    catch (ex) {
        return null;
    }
}

function buildStsResponse(sts) {
    var stsClient = new sdk.STS({
        credentials: kCredentials,
        region: 'bj'
    });
    return stsClient.getSessionToken(60 * 60 * 24, safeParse(sts)).then(function (response) {
        var body = response.body;
        return {
            AccessKeyId: body.accessKeyId,
            SecretAccessKey: body.secretAccessKey,
            SessionToken: body.sessionToken,
            Expiration: body.expiration
        };
    });
}

function buildPolicyResponse(policy) {
    var auth = new sdk.Auth(kCredentials.ak, kCredentials.sk);
    policy = new Buffer(policy).toString('base64');
    signature = auth.hash(policy, kCredentials.sk);

    return sdk.Q.resolve({
        accessKey: kCredentials.ak,
        policy: policy,
        signature: signature
    });
}

function buildNormalResponse(query) {
    if (!(query.httpMethod && query.path && query.queries && query.headers)) {
        return sdk.Q.resolve({statusCode: 403});
    }

    if (query.httpMethod === 'DELETE') {
        // 只允许 PUT/POST/GET Method
        return sdk.Q.resolve({statusCode: 403});
    }

    var httpMethod = query.httpMethod;
    var path = query.path;
    var queries = safeParse(query.queries) || {};
    var headers = safeParse(query.headers) || {};

    var auth = new sdk.Auth(kCredentials.ak, kCredentials.sk);
    var xbceDate = new Date();
    var timestamp = xbceDate.getTime() / 1000;
    signature = auth.generateAuthorization(httpMethod, path, queries, headers, timestamp);

    return sdk.Q.resolve({
        statusCode: 200,
        signature: signature,
        xbceDate: xbceDate.toISOString().replace(/\.\d+Z$/, 'Z')
    });
}

http.createServer(function (req, res) {
    console.log(req.url);

    var query = url.parse(req.url, true).query;

    var promise = null;
    if (query.sts) {
        promise = buildStsResponse(query.sts);
    }
    else if (query.policy) {
        promise = buildPolicyResponse(query.policy);
    }
    else {
        promise = buildNormalResponse(query);
    }

    promise.then(function (payload) {
        res.writeHead(200, {
            'Content-Type': 'text/javascript; charset=utf-8',
            'Access-Control-Allow-Origin': '*'
        });

        if (query.callback) {
            res.end(util.format('%s(%s)', query.callback, JSON.stringify(payload)));
        }
        else {
            res.end(JSON.stringify(payload));
        }
    });
}).listen(1337);
console.log('Server running at http://0.0.0.0:1337/');
