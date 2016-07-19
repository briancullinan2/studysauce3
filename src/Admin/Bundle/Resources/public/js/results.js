
if (typeof window == 'undefined') {
    window = {};
}
if (typeof window.views == 'undefined') {
    window.views = {};
}
if (typeof window.views.__vars == 'undefined') {
    window.views.__vars = {};
}
if (typeof window.views.__varStack == 'undefined') {
    window.views.__varStack = [];
}
if (typeof window.views.__parentStack == 'undefined') {
    window.views.__parentStack = [];
}
if (typeof window.views.__output == 'undefined') {
    window.views.__output = '';
}
if (typeof window.views.__outputStack == 'undefined') {
    window.views.__outputStack = [];
}
if (typeof window.views.__globalVars == 'undefined') {
    window.views.__globalVars = {};
}
if (typeof window.views.__globalVars.app == 'undefined') {
    window.views.__globalVars.app = {};
}
if (typeof window.views.__globalVars.view == 'undefined') {
    window.views.__globalVars.view = {};
}
if (typeof window.views.__globalVars.view['slots'] == 'undefined') {
    window.views.__globalVars.view['slots'] = {};
}
if (typeof window.views.__globalVars.view['slots'].output == 'undefined') {
    window.views.__globalVars.view['slots'].output = {};
}
window.views.slotStack = [];
window.views.__globalVars.app.getUser = function () {
    var welcome = $('.header');
    var user = $.extend({}, welcome.data('user'));
    var newUser = applyEntityObj(user);
    welcome.data('user', newUser);
    return newUser;
};
window.views.__globalVars.app.getRequest = function () {
    return {
        get: function (name) { return getQueryObject(window.location.href)[name];},
        cookies: Cookies
    }
};
if(typeof window.views.exists == 'undefined') {
    window.views.exists = function (name) {
        name = name.replace(/.*?:.*?:|\.html\.php/ig, '').replace(/[^a-z0-9]/ig, '_');
        return typeof window.views[name] != 'undefined';
    };
}
if (typeof window.views.render == 'undefined') {
    window.views.render = function (name, vars) {
        name = name.replace(/.*?:.*?:|\.html\.php/ig, '').replace(/[^a-z0-9]/ig, '_');
        if(typeof window.views[name] == 'undefined') {
            throw 'View not found';
        }
        if(typeof vars == 'undefined') {
            vars = {};
        }
        // save state
        var useThis;
        if(typeof vars.context != 'undefined') {
            useThis = vars.context;
        }
        else if (typeof this.this != 'undefined') {
            useThis = jQuery('<div />');
        }
        else {
            useThis = this;
        }
        window.views.__varStack.push(window.views.__vars);
        window.views.__vars = $.extend({this: useThis}, window.views.__globalVars);
        window.views.__vars.view = $.extend({this: useThis}, window.views.__vars.view); // don't override default or leave any traces, the rest are just top level function references
        window.views.__vars = $.extend(window.views.__vars, vars);
        window.views.__outputStack.push(window.views.__output);
        window.views.__output = '';

        // set up parent template to be executed at the end on top of this template
        var hasParent = false;
        window.views.__vars.view.extend = function (name, vars) {
            name = name.replace(/.*?:.*?:|\.html\.php/ig, '').replace(/[^a-z0-9]/ig, '_');
            if(typeof window.views[name] == 'undefined') {
                throw 'Parent view not found';
            }
            // add parent to stack above current view with vars for popping after the first render is complete
            hasParent = true;
            window.views.__varStack.push($.extend({this: useThis}, vars));
            window.views.__parentStack.push(name);
        };

        window.views[name].apply(useThis, [window.views.__vars]);
        var output = window.views.__output;

        // restore state
        window.views.__vars = window.views.__varStack.pop();
        window.views.__output = window.views.__outputStack.pop();

        if(hasParent) {
            // parentVars already from above
            var parent = window.views.__parentStack.pop();
            output += window.views.render.apply(useThis, [parent, window.views.__vars]);
            // pop back to incoming state
            window.views.__vars = window.views.__varStack.pop();
        }

        return output;
    };
}
window.views.__defaultEntities = {};
window.views.__defaultEntities['ss_group'] = {
    subgroups: $([]),
    users: $([]),
    groupPacks: $([]),
    invites: $([]),
    getId: function () {return this.id;},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getLogo: function () {return this.logo ? (typeof this.logo == 'string' ? applyEntityObj({table:'file', url: this.logo}) : applyEntityObj(this.logo)) : null;},
    getName: function () {return this.name;},
    getParent: function () {return this.parent ? applyEntityObj(this.parent) : null;},
    getSubgroups: function () {
        return $($(this.subgroups).toArray().map(function (c) {return applyEntityObj(c);}));
    },
    getInvites: function () {
        return $($(this.invites).toArray().map(function (c) {return applyEntityObj(c);}));
    },
    getUsers: function () {return $($(this.users).toArray().map(function (u) { return applyEntityObj(u);}));},
    getGroupPacks: function () {return $($(this.groupPacks).toArray().map(function (u) { return applyEntityObj(u);}));},
    getDeleted: function () {return this.deleted},
    getPacks: function () {return this.getGroupPacks();},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);}
};
window.views.__defaultEntities['invite'] = {
    group: null,
    invitee: null,
    getCode: function () {return this.code;},
    getFirst: function () {return this.first;},
    getLast: function () {return this.last;},
    getEmail: function () {return this.email;},
    getGroup: function () {return this.group ? applyEntityObj(this.group) : null;},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getInvitee: function () {return this.invitee ? applyEntityObj(this.invitee) : null;}
};
window.views.__defaultEntities['pack'] = {
    user: null,
    userPacks: $([]),
    groups: $([]),
    cards: $([]),
    properties: {},
    cardCount: 0,
    getCardCount: function () {return this.cardCount;},
    getDeleted: function () {return this.status == 'DELETED';},
    getProperty: function (name) {return this.properties.hasOwnProperty(name) ? this.properties[name] : null;},
    getStatus: function () {return this.status},
    getId: function () {return this.id;},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getLogo: function () {return this.logo;},
    getUsers: function () {
        var users = [];
        if(this.user) {
            users[users.length] = applyEntityObj(this.user);
        }
        for(var u = 0; u < this.userPacks.length; u++) {
            // TODO fix this relational caching, lookup needs to merge with single instance
            var up;
            if(!(up = applyEntityObj(this.userPacks[u])).getRemoved()) {
                users[users.length] = applyEntityObj(this.userPacks[u].user);
            }
        }
        return $(users);
    },
    getCards: function () {
        return $($(this.cards).toArray().map(function (c) {return applyEntityObj(c)}));
    },
    getTitle: function () {return this.title;},
    getUserPack: function (user) {
        for(var up = 0; up < this.userPacks.length; up++) {
            if(this.userPacks[up].user.id == user.id) {
                return applyEntityObj(this.userPacks[up]);
            }
        }
        for(var up2 = 0; up2 < user.userPacks.length; up2++) {
            if(user.userPacks[up2].pack.id == this.id) {
                return applyEntityObj(pack.userPacks[up2]);
            }
        }
    },
    getUserPacks: function () {return $($(this.userPacks).toArray().map(function (up) {return applyEntityObj(up);}));},
    getGroups: function () {return $($(this.groups).toArray().map(function (up) {return applyEntityObj(up);}));},
    getUser: function () {return this.user ? applyEntityObj(this.user) : null;},
    getUserById: function (id) {
        /** @var UserPack $up */
        if(this.getUser() != null && this.getUser().getId() == id) {
            return $this.getUser();
        }
        var up = this.getUserPacks().filter(function () {return this.getUser().getId() == id;}).first();
        if(up == null || up.length == 0) {
            return null;
        }
        return up[0].getUser();
    }
};
window.views.__defaultEntities['user_pack'] = {
    retention: {},
    getRemoved: function () {
        return this.removed
    },
    getUser: function () {
        return applyEntityObj(this.user);
    },
    getPack: function () {
        return applyEntityObj(this.pack);
    },
    getDownloaded: function () {
        return !(this.downloaded) ? null : new Date(this.downloaded);
    },
    getRetention: function () {
        return this.retention;
    },
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    setRetention: function (retention) {this.retention = retention;}
};
window.views.__defaultEntities['card'] = {
    answers: [],
    getDeleted: function () {return this.deleted},
    getId: function () {return this.id},
    getCorrect: function () {
        var card = this;
        var correct = this.getAnswers().filter(function (i, a) {
            return (a.getCorrect() || a.getValue() == card.correct || card.getResponseType() == 'tf'
                && ((a.getValue().match(/true|false/i) || [])[0] || '').toLowerCase() == card.correct)
                && !a.getDeleted();})[0];
        if(typeof correct == 'undefined' && typeof this.correct == 'string') {
            return applyEntityObj({table: 'answer', value: this.correct});
        }
        return correct;
    },
    getPack: function () {return this.pack == null ? null : applyEntityObj(this.pack)},
    getAnswers: function () {
        // look up answers
        var result = [];
        for(var s in this.answers) {
            if (this.answers.hasOwnProperty(s)) {
                if (this.answers[s] == '_clear') {
                    continue;
                }
                if (typeof this.answers[s] == 'string') {
                    result[result.length] = applyEntityObj({
                        table: 'answer',
                        value: this.answers[s],
                        content: this.answers[s],
                        correct: this.correct == this.answers[s]
                    });
                }
                else {
                    result[result.length] = applyEntityObj(this.answers[s])
                }
            }
        }
        return $(result);
    },
    getContent: function () {return (this.upload ? this.upload + "\n" : '') + this.content},
    getIndex: function () {return this.index},
    getResponseType: function () {return (this.responseType || '').split(/\s+/ig)[0]},
    getResponseContent: function () {return this.responseContent},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);}
};
window.views.__defaultEntities['answer'] = {
    getId: function () {return this.id;},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getCorrect: function () {return this.correct},
    getDeleted: function () {return this.deleted},
    getValue: function () {return this.value},
    getContent: function () {return typeof this.content == 'undefined' ? this.value : this.content;}
};
window.views.__defaultEntities['file'] = {
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getUrl: function () { return this.url },
    getId: function () { return this.id },
    getUser: function () {return this.user ? applyEntityObj(this.user) : null;}
};
window.views.__defaultEntities['ss_user'] = {
    userPacks: $([]),
    groups: $([]),
    invites: $([]),
    getFirst: function () {return this.first;},
    getLast: function () {return this.last;},
    getId: function () {return this.id;},
    getEmailCanonical: function () {return this.email.toLowerCase();},
    hasRole: function (role) { return this.roles.indexOf(role) > -1; },
    getEmail: function () { return this.email; },
    getUserPack: function (pack) {
        for(var up = 0; up < this.userPacks.length; up++) {
            if(this.userPacks[up].pack.id == pack.id) {
                return applyEntityObj(this.userPacks[up]);
            }
        }
        for(var up2 = 0; up2 < pack.userPacks.length; up2++) {
            if(pack.userPacks[up2].user.id == this.id) {
                return applyEntityObj(pack.userPacks[up2]);
            }
        }
    },
    getInvites: function () {return $($(this.invites).toArray().map(function (up) {return applyEntityObj(up);}));},
    getLastVisit: function () {return !(this.lastVisit) ? null : new Date(this.lastVisit);},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getUserPacks: function () {return $($(this.userPacks).toArray().map(function (up) {return applyEntityObj(up);}));},
    getGroups: function () {return $($(this.groups).toArray().map(function (up) {return applyEntityObj(up);}));}
};
window.views.__defaultEntities['coupon'] = {
    packs: $([]),
    getId: function () {return this.id;},
    getName: function () {return this.name;},
    getOptions: function () {return this.options;},
    getDescription: function () {return this.description;},
    getCreated: function () {return !(this.created) ? null : new Date(this.created);},
    getGroup: function () {return this.group ? applyEntityObj(this.group) : null;},
    getPacks: function () {return $($(this.packs).toArray().map(function (u) { return applyEntityObj(u);}));},
    getLogo: function () {
        if(this.getGroup() != null) {
            var logo = this.getGroup().getLogo();
            if(logo != null) {
                return logo;
            }
        }
        var packs = this.getPacks().toArray();
        for(var p in packs) {
            if(packs.hasOwnProperty(p)) {
                if(packs[p].getLogo() != null) {
                    return packs[p].getLogo();
                }
            }
        }
    }
};
window.views.__globalVars.view.exists = window.views.exists;
window.views.__globalVars.view.render = window.views.render;
window.views.__globalVars.view.router = Routing;
window.views.__globalVars.view.escape = _.escape;
window.views.__globalVars.view['assets'] = {};
window.views.__globalVars.view['assets'].getUrl = function (url) {return '/' + url;};
window.views.__globalVars.view['slots'].start = function (name) {
    window.views.__outputStack.push(window.views.__output);
    window.views.__output = '';
    window.views.slotStack.push(name);
};
window.views.__globalVars.view['slots'].stop = function () {
    window.views.__globalVars.view['slots'].output[window.views.slotStack.pop()] = window.views.__output;
    window.views.__output = window.views.__outputStack.pop();
};
window.views.__globalVars.view['slots'].get = function (name) {
    return window.views.__globalVars.view['slots'].output[name];
};
window.views.__globalVars.view['slots'].output = function (name) {
    return window.views.__output += window.views.__globalVars.view['slots'].output[name];
};
Date.prototype.format = function (format) {
    if (format == 'r') {
        return moment(this).formatPHP('ddd, DD MMM YYYY HH:mm:ss ZZ');
    }
    return moment(this).formatPHP(format);
};
Function.prototype.class = function () {return this.name};

function applyEntityObj(data) {
    var obj = $.extend({}, window.views.__defaultEntities[data['table']]);
    obj = $.extend(obj, data);
    return obj;
}
window.applyEntityObj = applyEntityObj;

$(document).ready(function () {

    var body = $('body'),
        orderBy = 'last DESC',
        searchTimeout = null,
        searchRequest = null;
    var lastSelected = null;
    var selectViable = false;

    function resetHeader() {
        var command = $('.results.collapsible:visible').first();
        if (command.length == 0) {
            return;
        }
        command.each(function () {
            var command = $(this);
            var selected = $('[class*="-row"]:visible.selected').filter(function () {
                return isElementInViewport($(this));
            });

            if (selected.length == 0) {
                if ($(this).is('[class*="-row"]:visible') && isElementInViewport($(this))) {
                    selected = $(this);
                }
                else {
                    selected = command.find('[class*="-row"]:visible').filter(function () {
                        return isElementInViewport($(this));
                    });
                }
            }

            if (selected.length == 0) {
                command.attr('class', command.attr('class').replace(/showing-(.*?)(\s+|$)/i, ''));
                command.addClass('empty');
            }
            else {
                command.removeClass('empty');
                var table = (/(.*)-row/i).exec(selected.attr('class'))[1];
                table = 'showing-' + table;
                if (!command.is('.' + table)) {
                    command.attr('class', command.attr('class').replace(/showing-(.*?)(\s+|$)/i, ''));
                    command.addClass(table);
                }
            }
        });
    }

    function getDataRequest() {
        var admin = $(this).closest('.results');
        var request = admin.data('request');
        var result = typeof request.requestKey == 'undefined' ? request : {requestKey: request.requestKey};
        var dataTables = result['tables'];
        var tables = {};
        if (admin.find('.class-names').length > 0) {
            admin.find('.class-names input:checked').each(function () {
                if (dataTables.hasOwnProperty($(this).val())) {
                    tables[$(this).val()] = dataTables[$(this).val()];
                }
            });
        }
        else {
            tables = dataTables;
        }
        result['order'] = orderBy;
        result['tables'] = tables;
        result['search'] = (admin.find('input[name="search"]').val() || '').trim();


        admin.find('input[name="page"]').each(function () {
            var table = $(this).parents('.paginate > .paginate').parent().attr('class').replace('paginate', '').trim();
            result['page-' + table] = $(this).val();
        });

        admin.find('header .input input, header .input select').each(function () {
            result[$(this).attr('name')] = $(this).val();
        });

        return result;
    }
    window.getDataRequest = getDataRequest;

    function addResultRow(table) {
        var results = $(this).closest('.results');
        // TODO: fix this creating a blank through the template system
        var request = results.data('request');
        var newRow = $(window.views.render('row', {entity: applyEntityObj({table: table}), tables: request.tables, table: table, request: request, tableId: table}));
        var last;
        if((last = results.find('.' + table + '-row, .' + table + '-row + .expandable:not([class*="-row"]), header.' + table).last()).length > 0) {
            newRow.removeClass('read-only').addClass('edit').insertAfter(last);
        }
        else if((last = results.find('.highlighted-link.' + table)).length > 0) {
            newRow.removeClass('read-only').addClass('edit').insertBefore(last);
        }
        else {
            newRow.removeClass('read-only').addClass('edit').appendTo(results);
        }
    }
    window.addResultRow = addResultRow;

    function getTab(readonly) {
        return $(this).closest('.panel-pane').find('.results');
    }
    window.getTab = getTab;

    // TODO: port to server in a shared code file, saves to database at the end
    function resultsSave(save) {
        var field = $(this);
        var tab = getTab.apply(field);
        var tabId = getTabId.apply(tab);
        var fieldTab = field.closest('.results').first();

        var saveButton = fieldTab.find('.highlighted-link a[href^="#save-"]').first();
        if (saveButton.is('.read-only > *, [disabled], .invalid, .invalid > *') || isLoading) {
            // select incorrect row handled by #goto-error
            return;
        }

        // get the parsed list of data
        for (var r = 0; r < tab.length; r++) {
            var shouldContinue = (function (subTab) {
                var hasSomethingToSave = false;
                var data = {};
                var subAction = subTab.closest('[action], [data-action]');
                var tables = subTab.data('request').tables;
                var name = subAction.attr('name');
                var saveUrl = subAction.data('action') || subAction.attr('action') || field.closest('[action]').attr('action') || field.closest('[data-action]').data('action');
                var subData = {};
                for (var table in tables) {
                    if (tables.hasOwnProperty(table)) {
                        // get list of possible fields in form
                        var tmpTables = {};
                        tmpTables[table] = tables[table];
                        var fields = getAllFieldNames(tmpTables);
                        var rows = subTab.find('.' + table + '-row.valid.changed:not(.template), .' + table + '-row.removed:not(.template)');
                        for (var i = 0; i < rows.length; i++) {
                            var row = $(rows[i]);
                            var rowId = getRowId.apply(row);
                            var newVal = {};
                            if (row.is('.removed') || row.is('.empty')) {
                                if (rowId == '' || rowId == null) {
                                    continue;
                                }
                                newVal = {id: rowId, remove: true};
                            }
                            else {
                                newVal = $.extend({id: rowId}, gatherFields.apply(row, [fields]));
                                if (row.is('[class*="new-id-"]')) {
                                    newVal['newId'] = (/new-id-([a-z0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
                                }
                            }
                            newVal = $.extend(true, newVal, getQueryObject(saveUrl));
                            if (typeof subData[table] == 'undefined') {
                                subData[table] = [];
                            }
                            if (subData[table].constructor !== Array) {
                                subData[table] = [subData[table]];
                            }
                            subData[table][subData[table].length] = newVal;
                            if (rows.length == 1 && subData[table].length == 1) {
                                subData[table] = subData[table][0];
                            }
                        }

                        if (typeof name != 'undefined' && typeof subData[table] != 'undefined') {
                            assignSubKey(data, name, subData[table]);
                        }
                        else if (typeof subData[table] != 'undefined') {
                            data[table] = subData[table];
                        }
                        rows.removeClass('changed');
                        if (typeof data[table] != 'undefined' || (typeof save[table] != 'undefined' && fieldTab[0] == subTab[0])) {
                            hasSomethingToSave = true;
                        }
                    }
                }

                if (!hasSomethingToSave) {
                    return true;
                }

                var request = getDataRequest.apply(subTab);
                data = $.extend(data, {requestKey: request.requestKey});

                // loading animation from CTA or activating field
                standardSave.apply(subTab, [data, function (data) {
                    loadContent.apply(subTab, [data, 'saved']);
                }]);

                return tabId != 0;

            })($(tab[r]));
            if(shouldContinue === false) {
                break;
            }
        }
    }
    window.resultsSave = resultsSave;

    function standardSave(save, callback) {
        var subTab = $(this);
        var subAction = subTab.closest('[action], [data-action]');
        var saveUrl = subAction.data('action') || subAction.attr('action') || subTab.closest('[action]').attr('action') || subTab.closest('[data-action]').data('action');
        var data = {};
        data = $.extend(true, data, save || {});
        data = $.extend(true, data, getQueryObject(saveUrl));
        saveUrl = saveUrl.replace(/\?.*/ig, '');

        var saveButton = subTab.find('.highlighted-link [href^="#save-"], .highlighted-link [value^="#save-"]').first();
        if (saveButton.is('.read-only > *, [disabled], .invalid, .invalid > *') || isLoading) {
            // select incorrect row handled by #goto-error
            return;
        }
        isLoading = true;
        loadingAnimation(saveButton);

        $.ajax({
            url: saveUrl,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (data) {
                saveButton.find('.squiggle').stop().remove();
                isLoading = false;
                if(typeof callback == 'function') {
                    callback(data);
                }
            },
            error: function () {
                isLoading = false;
                saveButton.find('.squiggle').stop().remove();
            }
        });
    }
    window.standardSave = standardSave;

    function getAllFieldNames(tables) {
        var fields = [];
        for (var table in tables) {
            if (tables.hasOwnProperty(table)) {
                // get list of possible fields in form
                for (var f in tables[table]) {
                    if (tables[table].hasOwnProperty(f)) {
                        if (typeof f == 'string' && isNaN(parseInt(f))) {
                            fields = $.merge(fields, [f]);
                        }

                        if (typeof tables[table][f] == 'string') {
                            fields = $.merge(fields, [tables[table][f]]);
                        }
                        else if (Array.isArray(tables[table][f])) {
                            fields = $.merge(fields, tables[table][f]);
                        }
                        else {
                            throw 'Not supported!';
                        }
                    }
                }
            }
        }
        return fields;
    }
    window.getAllFieldNames = getAllFieldNames;

    function getTabId() {
        return getRowId.apply($(this).closest('.panel-pane').find('[class*="-row"]:not(.template)').first());
    }
    window.getTabId = getTabId;

    function getRowId() {
        var row = $(this).closest('[class*="-row"]').first();
        var table = ((/(^|\s)([a-z0-9_-]*)-row(\s|$)/ig).exec(row.attr('class')) || [])[2];
        return ((new RegExp(table + '-id-([0-9]*)(\\s|$)', 'ig')).exec(row.attr('class')) || [])[1];
    }
    window.getRowId = getRowId;

    function loadContent(data, namespace) {
        var admin = $(this).closest('.results').first();

        // merge updates using template system, same as results.html.php and rows.html.php
        if (typeof data == 'object') {
            for(var t in data.results) {
                if(!data.results.hasOwnProperty(t)) {
                    continue;
                }
                var tableName = t.split('-')[0];
                if(t == 'allGroups') {
                    tableName = 'ss_group';
                }
                if(window.views.__defaultEntities.hasOwnProperty(tableName)) {
                    for(var o = 0; o < data.results[t].length; o++) {
                        data.results[t][o] = applyEntityObj(data.results[t][o]);
                    }
                }
                if(t == 'allGroups') {
                    // TODO: update group list in user data heading
                }
            }

            window.views.render.apply(admin, ['results', data]);
        }
        else {
            throw 'Not allowed';
        }

        resetHeader();
        var event = $.Event('resulted' + (typeof namespace == 'string' ? ('.' + namespace) : '.refresh'), {results: data});
        admin.trigger(event);
        centerize.apply(admin.find('.centerized'));
    }
    // make available to save functions that always lead back to index
    window.loadContent = loadContent;

    function loadResults() {
        if (searchRequest != null)
            searchRequest.abort();
        if (searchTimeout != null)
            clearTimeout(searchTimeout);
        $(this).filter('.results:visible').each(function () {
            var that = $(this);
            searchTimeout = setTimeout(function () {
                searchRequest = $.ajax({
                    url: Routing.generate('command_callback'),
                    type: 'GET',
                    dataType: 'json',
                    data: getDataRequest.apply(that),
                    success: function (data) {
                        loadContent.apply(that, [data, 'refresh']);
                    }
                });
            }, 100);
        });
    }
    window.loadResults = loadResults;

    document.onselectstart = function () {
        if (key.shift && selectViable) {
            return false;
        }
    };

    body.on('mousedown', '.results [class*="-row"], table.results > tbody > tr', function (evt) {
        // cancel select toggle if target of click is also interactable
        if (($(this).is('.selected') || $(evt.target).is('a'))
            && $(evt.target).is('select, input, a, textarea, button, label.checkbox, label.radio, label.checkbox *, label.radio *, button *, .selectize-control, .selectize-control *')) {
            return;
        }

        selectViable = false;
        var results = $(this).closest('.results');
        var type = (/(.*)-row/i).exec($(this).attr('class'))[1];
        var state = !$(this).find('input[name="selected"]').prop('checked');
        // clear selection unless shift is pressed
        var range = $(this);
        if (!key.shift) {
            results.parents('.panel-pane').find('.results').find('.selected').not($(this)).removeClass('selected').find('> *:last-child input[name="selected"]')
                .prop('checked', false);
        }
        else {
            // check if range is viable
            if (lastSelected != null && lastSelected.is('.' + type + '-row')) {
                if (lastSelected.index() < $(this).index()) {
                    range = $.merge(range, lastSelected.nextUntil($(this)));
                }
                else {
                    range = $.merge(range, $(this).nextUntil(lastSelected));
                }
                selectViable = true;
            }
        }
        if (state) {
            range.addClass('selected').find('> *:last-child input[name="selected"]')
                .prop('checked', state);
            range.trigger('selected');
        }
        else {
            range.removeClass('selected').find('> *:last-child input[name="selected"]')
                .prop('checked', state);
        }

        // if we just did a select, reset the last select so it takes two more clicks to do another range
        if (selectViable) {
            lastSelected = null;
        }
        else {
            lastSelected = $(this);
        }
    });

    body.on('click', '.tiles [class*="-row"]', function (evt) {
        if (!$(evt.target).is('a, a *, [class*="-row"] > [class*="List"], [class*="-row"] > [class*="List"] *')) {
            evt.preventDefault();
            var results = $(this).parents('.results');
            var row = $(this).closest('[class*="-row"]');
            row.find('a.pack-icon').trigger('click');
            if(row.is('.edit')) {
                row.removeClass('edit').addClass('read-only');
            }
        }
    });

    body.on('show', '.panel-pane', function () {
        if (!$(this).is('.results-loaded')) {
            $(this).addClass('results-loaded');
            $(this).find('.results header .search .checkbox').draggable();
            //$(this).find('.results').each(function () {
            //    var results = $(this).data('results');
            //var request = $(this).data('request');
            //    loadContent.apply(this, [results]);
            //});
        }
        resetHeader();
    });

    body.on('click', '.results .class-names .checkbox a', function (evt) {
        evt.preventDefault();
        var command = $('.results:visible');
        var heading = $('[name="' + this.hash.substr(1) + '"]');
        var topPlusPane = DASHBOARD_MARGINS.padding.top + command.find('.pane-top').outerHeight(true) - heading.outerHeight();
        heading.scrollintoview({
            padding: {
                top: topPlusPane,
                right: 0,
                bottom: $(window).height() - DASHBOARD_MARGINS.padding.top + command.find('.pane-top').height() - heading.outerHeight(),
                left: 0
            }
        });
        command.find('[class*="-row"].' + this.hash.substr(1) + '-row').first().trigger('mouseover');
    });

    // collapse section feature
    body.on('change', '.results .class-names .checkbox input', function () {
        var command = $('.results:visible');
        var table = $(this).val();
        var heading = command.find('> h2.' + table);
        if ($(this).is(':checked')) {
            heading.removeClass('collapsed').addClass('expanded');
        }
        else {
            heading.removeClass('expanded').addClass('collapsed');
        }
        if ($(this).is('[disabled]')) {
            heading.hide();
        }
        else {
            heading.show();
        }
        if (command.is('.showing-' + table) && (heading.is('.collapsed') || !heading.is(':visible')) || command.is('.empty')) {
            resetHeader();
        }
    });

    body.on('click', '.results a[href^="#add-"]', function (evt) {
        evt.preventDefault();
        addResultRow.apply(this, [$(this).attr('href').substring(5)]);
    });

    body.on('click', '[class*="-row"] a[href^="#remove-"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('[class*="-row"]');
        if ($(this).is('[href^="#remove-confirm-"]')) {
            row.removeClass('selected').addClass('removed');
        }
        else {
            row.addClass('remove-confirm');
        }
    });

    // inline edit
    body.on('click', '[class*="-row"] a[href^="#edit-"]', function (evt) {
        evt.preventDefault();
        var row = $(this).closest('[class*="-row"]');
        row.removeClass('read-only').addClass('edit');
    });

    // footer edit
    body.on('click', '.form-actions a[href^="#edit-"]', function (evt) {
        evt.preventDefault();
        var row = getTab.apply(this).find('[class*="-row"].read-only');
        row.removeClass('read-only').addClass('edit');
    });

    // inline cancel
    body.on('click', '[class*="-row"] a[href="#cancel-edit"]', function (evt) {
        evt.preventDefault();
        var row = $(this).closest('[class*="-row"]');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });

    // footer cancel
    body.on('click', '.form-actions a[href^="#cancel-edit"], .form-actions .cancel-edit', function (evt) {
        evt.preventDefault();
        var row = getTab.apply(this).find('[class*="-row"].edit');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });

    // footer save
    body.on('click', '.form-actions a[href^="#save-"], .form-actions [value^="save-"]', function (evt) {
        evt.preventDefault();
        var tab = getTab.apply(this);
        //if (autoSaveTimeout != null) {
        //    clearTimeout(autoSaveTimeout);
        //    autoSaveTimeout = null;
        //}
        tab.trigger('validate');
        var rows = tab.find('[class*="-row"].empty:not(.template)');
        rows.add(rows.next('.expandable')).removeClass('selected').addClass('removed');
        tab.find('[class*="-row"].edit').removeClass('edit remove-confirm').addClass('read-only');
        resultsSave.apply(tab, [{}]);
    });

    var validationTimeout = null;
    function standardChangeHandler (evt) {
        var that = $(evt.target);
        // do not autosave from selectize because the input underneath will change
        if (that.parents('.selectize-input').length > 0) {
            return;
        }

        if(evt.type == 'change' && that.is('[data-confirm],select:has(option[data-confirm])')) {
            var oldValue = that.data('oldValue');
            // make sure some other trigger doesn't reset it
            if(that.val() != oldValue) {
                that.trigger('change.confirm');
            }
            if(that.val() != oldValue) {
                that.parents('[class*="-row"]').addClass('changed');
            }
        }
        else {
            that.parents('[class*="-row"]').addClass('changed');
        }

        if (validationTimeout != null) {
            clearTimeout(validationTimeout);
        }
        validationTimeout = setTimeout(function () {
            that.trigger('validate');
        }, 100);
    }
    window.standardChangeHandler = standardChangeHandler;
    body.on('change keyup keydown', '.results [class*="-row"] input, .results [class*="-row"] select, .results [class*="-row"] textarea', standardChangeHandler);

    body.on('click', '.results a[href^="#switch-view-"]', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results').first();
        var request = results.data('request');
        request['view'] = $(this).attr('href').substr(13);
        results.data('request', request);
        loadResults.apply(results);
    });

    var isLoading = false;

    $(window).on('beforeunload', function (evt) {
        if ($('.panel-pane:visible').find('.results [class*="-row"].edit.changed:not(.template):not(.removed)').length > 0) {
            evt.preventDefault();
            return "You have unsaved changes!  Please don't go!";
        }
    });

    body.on('hide', '.panel-pane', function () {
        var row = $(this).find('.results [class*="-row"].edit');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });

    //body.on('mouseover click', '.results [class*="-row"]', resetHeader);

    body.on('showing', '.panel-pane', function () {
        $(this).find('.results:not(.loaded)').each(function () {
            var results = $(this);
            results.addClass('loaded');
            var request = results.data('request');
            var resultsObj = window.setupRequest['setup' + request.requestKey]();
            results.data("results", resultsObj);
            loadContent.apply(results, [{tables: request.tables, context: results, request: request, results: resultsObj}, "refresh"]);
        });
    });

    body.on('change', '.paginate input', function () {
        var results = $(this).parents('.results');
        var paginate = $(this).closest('.paginate');
        results.find('.class-names a[href="#' + (/showing-(.*?)(\s|$)/i).exec(results.attr('class'))[1].trim() + '"]').trigger('click');
    });

    body.on('click', '.results a[href^="#search-"]', function (evt) {
        var admin = $(this).parents('.results');
        evt.preventDefault();
        var search = this.hash.substring(8);
        if (search.indexOf(':') > -1) {
            admin.find('header input, header select').each(function () {
                var subSearch = (new RegExp($(this).attr('name') + ':(.*?)(\\s|$)', 'i')).exec(search);
                if (subSearch) {
                    search = search.replace(subSearch[0], '');
                    $(this).val(subSearch[1]).trigger('change');
                }
            });
        }
        else {

        }
        $(this).parents('.results').find('.search input[name="search"]').val(search).trigger('change');
    });

    body.on('click change', '.paginate a', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results'),
            paginate = $(this).closest('.paginate'),
            page = this.hash.match(/([0-9]*|last|prev|next|first)$/i)[0],
            current = parseInt(paginate.find('input[name="page"]').val()),
            last = parseInt(paginate.find('.page-total').text());
        if (page == 'first')
            page = 1;
        if (page == 'next')
            page = current + 1;
        if (page == 'prev')
            page = current - 1;
        if (page == 'last')
            page = last;
        if (page > last)
            page = last;
        if (page < 1)
            page = 1;
        paginate.find('input[name="page"]').val(page).trigger('change');
    });

    body.on('submit', '.results header form', function (evt) {
        evt.preventDefault();

        loadResults.apply($(this).parents('.results'));
    });

    body.on('change', '.results header .input > select, .results header .input > input', function () {
        var that = $(this);
        var admin = $('.results:visible');
        var paginate = that.closest('.paginate');

        if (that.val() == '_ascending' || that.val() == '_descending') {
            orderBy = that.attr('name') + (that.val() == '_ascending' ? ' ASC' : ' DESC');
            that.val(that.data('last') || that.find('option').first().attr('value'));
        }
        else if (that.val().trim() != '') {
            that.parent().removeClass('unfiltered').addClass('filtered');
            that.data('last', that.val());
        }
        else {
            that.parent().removeClass('filtered').addClass('unfiltered');
            that.data('last', that.val());
        }

        var disabled = [];
        admin.find('header .filtered').each(function () {
            var header = $(this).parents('header > *');
            admin.find('.class-names .checkbox input').each(function () {
                if (!header.is('.search') && !header.is('.paginate') && !header.is('.' + $(this).val())) {
                    disabled = $.merge($(disabled), $(this));
                }
            });
        });
        admin.find('.class-names .checkbox input').each(function () {
            if (!$(this).is('[disabled]')) {
                $(this).data('last', $(this).prop('checked'));
            }
            if ($(this).is(disabled)) {
                $(this).attr('disabled', 'disabled').prop('checked', false);
            }
            else {
                $(this).removeAttr('disabled').prop('checked', $(this).data('last'))
            }
            $(this).trigger('change');
        });

        loadResults.apply(admin);
    });

    function gotoError () {
        var invalid = $(this).closest('form,.results,.pane-content').find('.invalid:has(input, select, textarea)').first();
        invalid.scrollintoview(DASHBOARD_MARGINS).addClass('pulsate');
        invalid.find('input, select, textarea').focus().one('change', function () {
            $(this).parents('.pulsate').removeClass('pulsate');
        });
    }
    window.gotoError = gotoError;

    body.on('click', 'a[href="#goto-error"]', function (evt) {
        evt.preventDefault();
        gotoError.apply(this);
    });


});
