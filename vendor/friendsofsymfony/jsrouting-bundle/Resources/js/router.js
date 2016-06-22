
var fos = {};

/**
 * @constructor
 * @param {fos.Router.Context=} opt_context
 * @param {Object.<string, fos.Router.Route>=} opt_routes
 */
fos.Router = function(opt_context, opt_routes) {
    this.context_ = opt_context || {base_url: '', prefix: '', host: '', scheme: ''};
    this.setRoutes(opt_routes || {});
};

fos.Router.getInstance = function() {
    if (fos.Router.instance_) {
        return fos.Router.instance_;
    }
    return fos.Router.instance_ = new fos.Router();
};

/**
 * @typedef {{
 *     tokens: (Array.<Array.<string>>),
 *     defaults: (Object.<string, string>),
 *     requirements: Object,
 *     hosttokens: (Array.<string>),
 *     regexpr: (string)
 * }}
 */
fos.Router.Route;

/**
 * @typedef {{
 *     base_url: (string)
 * }}
 */
fos.Router.Context;

fos.Router.setData = function(data) {
    var router = fos.Router.getInstance();
    router.setBaseUrl(/** @type {string} */ (data['base_url']));
    router.setRoutes(/** @type {Object.<string, fos.Router.Route>} */ (data['routes']));
    if ('prefix' in data) {
        router.setPrefix(/** @type {string} */ (data['prefix']));
    }
    router.setHost(/** @type {string} */ (data['host']));
    router.setScheme(/** @type {string} */ (data['scheme']));
};

/**
 * @param {Object.<string, fos.Router.Route>} routes
 */
fos.Router.prototype.setRoutes = function(routes) {
    this.routes_ = routes;
};

/**
 * @return {Object.<string, fos.Router.Route>} routes
 */
fos.Router.prototype.getRoutes = function() {
    return this.routes_;
};

/**
 * @param {string} baseUrl
 */
fos.Router.prototype.setBaseUrl = function(baseUrl) {
    this.context_.base_url = baseUrl;
};

/**
 * @return {string}
 */
fos.Router.prototype.getBaseUrl = function() {
    return this.context_.base_url;
};

/**
 * @param {string} prefix
 */
fos.Router.prototype.setPrefix = function(prefix) {
    this.context_.prefix = prefix;
};

/**
 * @param {string} scheme
 */
fos.Router.prototype.setScheme = function(scheme) {
    this.context_.scheme = scheme;
};

/**
 * @return {string}
 */
fos.Router.prototype.getScheme = function() {
    return this.context_.scheme;
};

/**
 * @param {string} host
 */
fos.Router.prototype.setHost = function(host) {
    this.context_.host = host;
};

/**
 * @return {string}
 */
fos.Router.prototype.getHost = function() {
    return this.context_.host;
};


/**
 * Builds query string params added to a URL.
 * Port of jQuery's $.param() function, so credit is due there.
 *
 * @param {string} prefix
 * @param {Array|Object|string} params
 * @param {Function} add
 */
fos.Router.prototype.buildQueryParams = function(prefix, params, add) {
    var self = this;
    var name;
    var rbracket = new RegExp(/\[\]$/);

    if (params instanceof Array) {
        params.forEach(function(val, i) {
            if (rbracket.test(prefix)) {
                add(prefix, val);
            } else {
                self.buildQueryParams(prefix + '[' + (typeof val === 'object' ? i : '') + ']', val, add);
            }
        });
    } else if (typeof params === 'object') {
        for (name in params) {
            this.buildQueryParams(prefix + '[' + name + ']', params[name], add);
        }
    } else {
        add(prefix, params);
    }
};

/**
 * Returns a raw route object.
 *
 * @param {string} name
 * @return {fos.Router.Route}
 */
fos.Router.prototype.getRoute = function(name) {
    var prefixedName = this.context_.prefix + name;
    if (!this.routes_.hasOwnProperty(prefixedName)) {
        // Check first for default route before failing
        if (!this.routes_.hasOwnProperty(name)) {
            throw new Error('The route "' + name + '" does not exist.');
        }
    } else {
        name = prefixedName;
    }

    return (this.routes_[name]);
};


/**
 * Generates the URL for a route.
 *
 * @param {string} name
 * @param {Object.<string, string>} opt_params
 * @param {boolean} absolute
 * @return {string}
 */
fos.Router.prototype.generate = function(name, opt_params, absolute) {
    var route = (this.getRoute(name)),
        params = opt_params || {},
        unusedParams = $.extend({}, params),
        url = '',
        optional = true,
        host = '';

    route.tokens.forEach(function(token) {
        if ('text' === token[0]) {
            url = token[1] + url;
            optional = false;

            return;
        }

        if ('variable' === token[0]) {
            var hasDefault = route.defaults.hasOwnProperty(token[3]);
            if (false === optional || !hasDefault || (params.hasOwnProperty(token[3]) && params[token[3]] != route.defaults[token[3]])) {
                    var value;

                    if (params.hasOwnProperty(token[3])) {
                        value = params[token[3]];
                        delete unusedParams[token[3]];
                    } else if (hasDefault) {
                        value = route.defaults[token[3]];
                    } else if (optional) {
                        return;
                    } else {
                        throw new Error('The route "' + name + '" requires the parameter "' + token[3] + '".');
                    }

                    var empty = true === value || false === value || '' === value;

                    if (!empty || !optional) {
                        var encodedValue = encodeURIComponent(value).replace(/%2F/g, '/');

                        if ('null' === encodedValue && null === value) {
                            encodedValue = '';
                        }

                        url = token[1] + encodedValue + url;
                    }

                    optional = false;
                } else if (hasDefault) {
                    delete unusedParams[token[3]];
                }

                return;
        }

        throw new Error('The token type "' + token[0] + '" is not supported.');
    });

    if (url === '') {
        url = '/';
    }

    route.hosttokens.forEach(function (token) {
        var value;

        if ('text' === token[0]) {
            host = token[1] + host;

            return;
        }

        if ('variable' === token[0]) {
            if (params.hasOwnProperty(token[3])) {
                value = params[token[3]];
                delete unusedParams[token[3]];
            } else if (route.defaults.hasOwnProperty(token[3])) {
                value = route.defaults[token[3]];
            }

            host = token[1] + value + host;
        }
    });

    url = this.context_.base_url + url;
    if (route.requirements.hasOwnProperty("_scheme") && this.getScheme() != route.requirements["_scheme"]) {
        url = route.requirements["_scheme"] + "://" + (host || this.getHost()) + url;
    } else if (host && this.getHost() !== host) {
        url = this.getScheme() + "://" + host + url;
    } else if (absolute === true) {
        url = this.getScheme() + "://" + this.getHost() + url;
    }

    var hasUnused = false;
    for(var v in unusedParams) {
        if(unusedParams.hasOwnProperty(v)) {
            hasUnused = true;
            break;
        }
    }
    if (hasUnused) {
        var prefix;
        var queryParams = [];
        var add = function(key, value) {
            // if value is a function then call it and assign it's return value as value
            value = (typeof value === 'function') ? value() : value;

            // change null to empty string
            value = (value === null) ? '' : value;

            queryParams.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
        };

        for (prefix in unusedParams) {
            this.buildQueryParams(prefix, unusedParams[prefix], add);
        }

        url = url + '?' + queryParams.join('&').replace(/%20/g, '+');
    }

    return url;
};
/**
 * Generates the URL for a route.
 *
 * @param {string} url
 * @return {Array}
 */
fos.Router.prototype.match = function(url) {
    // TODO: Extract only uri from url
    // TODO: Extract GET params
    // TODO: Remove hash
    // TODO: Write tests

    var matchedRoutes = [];

    var routes     = this.getRoutes();
    var routeNames = [];
    for(var k in routes) {
        if(routes.hasOwnProperty(k)) {
            routeNames[routeNames.length] = k;
        }
    }

    for (var i in routeNames) {
        var routeName = routeNames[i];
        var route = routes[routeName];

        var regexpr = route['regexpr'];

        // Extract base regexpr
        var match = regexpr.match(/^#(.*?)#s?$/);

        if (null !== match) {

            // Ignore all groups by adding :? after (
            // Dont replace if bracket is backslashed
            var baseRegExpr = match[1].replace(/(((\\\\)+)|([^\\]))(\((\:\?)?)/, "$1(");
            var paramsMapper = [];

            // Look for (?P<param_name> groups
            while (match = baseRegExpr.match(/(.*?)\((?:\?\:)?\?P\<([^\>]+)\>(.*)/)) {

                baseRegExpr = match[1] + '(' + match[3];

                // Save param name
                paramsMapper.push(match[2]);
            }

            // Add backslashes and remove doubled plus char
            baseRegExpr = baseRegExpr.replace(/\//g, "\\/").replace(/\+\+/g, "+").replace(/\(\?:/g, '(');

            var newRegExpr = new RegExp(baseRegExpr);

            var queryStart = (url || '').indexOf('?');
            var urlMatch, params = {}, urlNoQuery = (url || '').substr(0, queryStart == -1 ? (url || '').length : queryStart);

            if (urlMatch = newRegExpr.exec(urlNoQuery)) {

                //var error = url + "\n" + baseRegExpr;

                // Recover params
                for (var i in paramsMapper) {
                    var paramName  = paramsMapper[i];
                    var paramValue = urlMatch[(i*2)+2];

                    params[paramName] = paramValue;
                    //error += '[' + paramName + ' = ' + paramValue + ']';
                }

                //if(routeName == 'cards') {
                //    throw new Error(error);
                //}

                matchedRoutes.push({
                    'name':   routeName,
                    'route':  route,
                    'params': params
                });

            }
        }
    }

    return matchedRoutes;
};

window['Routing'] = fos.Router.getInstance();
