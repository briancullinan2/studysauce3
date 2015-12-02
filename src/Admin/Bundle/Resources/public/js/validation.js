$(document).ready(function () {

    var body = $('body');

    body.on('click', '#validation a[href="#settings"]', function (evt) {
        evt.preventDefault();
        $(this).parent().parent().toggleClass('open');
    });

    body.on('click', '#validation a[href="#run-test"]', function (evt) {
        evt.preventDefault();
        var validation = $('#validation');
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'text',
            data: {
                suite: $(this).attr('data-suite'),
                test: $(this).attr('data-test'),
                host: validation.find('.host-setting input').val().trim(),
                browser: validation.find('.browser-setting select').val().trim(),
                wait: validation.find('.wait-setting input').val().trim(),
                url: validation.find('.url-setting input').val().trim()
            },
            success: function (response) {
                // TODO: update labels with new test results times
                validation.trigger('testended');
            }
        });
    });

    body.on('click', '#validation a[href^="#results-"]', function (evt) {
        evt.preventDefault();
        var id = $(this).attr('href').substring(9) + '',
            validation = $('#validation'),
            test = id.substring('TestResults-PASS-'.length, id.length - 10),
            result = $('[data-resultId="' + id + '"]');
        if(result.length > 0) {
            validation.find('.result.open').removeClass('open');
            result.addClass('open');
            return;
        }
        $.ajax({
            url: window.callbackPaths['validation_result'],
            type: 'POST',
            dataType: 'json',
            data: {
                result: id
            },
            success: function (response) {
                // TODO: update labels with new test results times
                var newResult = $('<div class="result" data-resultId="{{resultId}}"><div class="results-inner"><pre>{{status}}</pre>{{steps}}</div></div>'
                        .replace('{{resultId}}', id)
                        .replace('{{status}}', response[test].result)
                        .replace('{{steps}}', response[test].steps)
                ).appendTo(validation.find('.pane-content'));
                validation.find('.result.open').removeClass('open');
                newResult.addClass('open');
            }
        });
    });

    body.on('click', '#validation h3', function () {
        $(this).toggleClass('selected');
    });

    body.on('click', '#validation code', function () {
        var text = this;
        var range, selection;
        if (document.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    });

    body.on('click', '#validation a[href="#run-all"]', function (evt) {
        evt.preventDefault();
        var validation = $('#validation'),
            suite = (/suite-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'text',
            data: {
                suite: suite,
                test: validation.find('tr:has(td:nth-child(3) input:checked)')
                    .map(function () { return (/test-id-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1]; }).toArray()
                    .join('|'),
                host: validation.find('.host-setting input').val().trim(),
                browser: validation.find('.browser-setting select').val().trim(),
                wait: validation.find('.wait-setting input').val().trim(),
                url: validation.find('.url-setting input').val().trim()
            },
            success: function (response) {
                var content = $(response),
                    first = (/test-id-(.*?)(\s|$)/ig).exec(content.filter('[class*="test-id-"]').first().attr('class'))[1];
                content.filter('[class*="test-id-"]').each(function () {
                    var test = (/test-id-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1];
                    validation.find('tr.test-id-' + test + ' td:nth-child(2)').html('');
                    $(this).appendTo(validation.find('tr.test-id-' + test + ' td:nth-child(2)'));
                });
                content.not(content.filter('[class*="test-id-"]')).prependTo(validation.find('tr.test-id-' + first + ' td:nth-child(2)'));
            }
        });
    });

    function highlight() {
        var validation = $('#validation'),
            re = /depends-on-(.*?)(\s|$)/ig,
            match;
        while(match = re.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).not('.dependency').addClass('dependency').each(highlight);
        }
        var ire = /includes-(.*?)(\s|$)/ig;
        while(match = ire.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).not('.dependency').addClass('included').each(highlight);
        }
    }

    body.on('mouseover', '#validation tbody tr', highlight);

    function removeHighlight() {
        var validation = $('#validation'),
            re = /depends-on-(.*?)(\s|$)/ig,
            match;
        while(match = re.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).filter('.dependency').removeClass('dependency').each(removeHighlight);
        }
        var ire = /includes-(.*?)(\s|$)/ig;
        while(match = ire.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).filter('.included').removeClass('included').each(removeHighlight);
        }
    }

    body.on('mouseout', '#validation tbody tr', removeHighlight);

    var graphConfig = {
        container: 'sigma-container',
        type: 'canvas',
        settings: {
            labelThreshold: 0,
            nodeHaloColor: 'white',
            edgeHaloColor: 'white',
            nodeHaloSize: 3,
            edgeHaloSize: 3,
            defaultLabelSize: 20,
            scalingMode: 'inside',
            minArrowSize: 10,
            zoomingRatio: 1,
            mouseZoomDuration: 0,
            mouseWheelEnabled: false,
            doubleClickZoomingRatio: 1,
            doubleClickEnabled: false,
            zoomMin:.5,
            zoomMax:.5,
            nodePowRatio: 2,
            edgePowRatio: 2,
            labelSizeRatio: 1,
            labelHoverShadow: false,
            labelSize: 'fixed',
            minNodeSize: 5,
            maxNodeSize: 20,
            minEdgeSize: 5,
            defaultLabelColor: 'rgba(0,0,0,.66)',
            defaultLabelHoverColor: 'rgba(0,0,0,.66)',
            defaultHoverLabelBGColor: 'rgba(0,0,0,0)',
            maxEdgeSize: 10,
            sideMargin: 1
        }
    };

    function createGraph(s) {
        // We first need to save the original colors of our
        // nodes and edges, like this:
        s.graph.nodes().forEach(function(n) {
            n.originalColor = n.color;
        });
        s.graph.edges().forEach(function(e) {
            e.originalColor = e.color;
        });

        //var listener = sigma.layouts.fruchtermanReingold.configure(s, {});
// Bind all events:
        //listener.bind('start stop interpolate', function(event) {
        //    console.log(event.type);
        //});

        //s.startForceAtlas2({autoStop: true, labelAlignment: 'inside', scale: 2, barnesHutOptimize: false, edgeWeightInfluence: 1, adjustSizes: true});
        //sigma.layouts.fruchtermanReingold.start(s);
        //setTimeout(function () {
        //    s.stopForceAtlas2();
        //}, 200);

        // Configure the ForceLink algorithm:
        /*var fa = sigma.layouts.configForceLink(s, {
         worker: true,
         autoStop: true,
         background: true,
         scaleRatio: 10,
         gravity: 3,
         easing: 'cubicInOut'
         });
         // Bind the events:
         fa.bind('start stop', function(e) {
         console.log(e.type);
         if (e.type == 'start') {
         }
         });
         // Start the ForceLink algorithm:
         sigma.layouts.startForceLink();

         */

        var config = {
            node: {
                //show: 'clickNode',
                //hide: 'hovers',
                cssClass: 'sigma-tooltip',
                position: 'right',
                template: '<div class="arrow"></div>' +
                '<div class="previous"><a href="#results-{{resultId}}">{{prev}}</a></div>' +
                '<div class="sigma-tooltip-header">{{label}} <a href="#run-test" data-suite="{{suite}}" data-test="{{id}}">Run</a></div>' +
                '<div class="results">{{results}}</div>',
                renderer: function(node, template) {
                    // The function context is s.graph
                    node.degree = this.degree(node.id);
                    // Returns an HTML string:
                    return template.replace('{{label}}', node.label)
                        .replace('{{suite}}', node.suite)
                        .replace('{{id}}', node.id)
                        .replace('{{prev}}', typeof node.results != 'undefined' && node.results != null ? node.results[0].created : '')
                        .replace('{{resultId}}', typeof node.results != 'undefined' && node.results != null ? node.results[0].resultId : '');
                    // Returns a DOM Element:
                    //var el = document.createElement('div');
                    //return el.innerHTML = Mustache.render(template, node);
                }
            }
        };
        var tooltips = sigma.plugins.tooltips(s, s.renderers[0], config);

        //var listener = sigma.layouts.dagre.start(s, {rankDir: 'TB'});
        // When a node is clicked, we check for each node
        // if it is a neighbor of the clicked one. If not,
        // we set its color as grey, and else, it takes its
        // original color.
        // We do the same for the edges, and we only keep
        // edges that have both extremities colored.
        s.bind('hovers', function(e) {
            if(e.data.current.nodes.length == 0 ) {
                s.graph.nodes().forEach(function(n) {
                    n.color = n.originalColor;
                });

                s.graph.edges().forEach(function(e) {
                    e.color = e.originalColor;
                });

                // Same as in the previous event:
                s.refresh();
                return;
            }

            var node = e.data.current.nodes[0],
                nodeId = node.id,
                toKeep = s.graph.neighbors(nodeId);
            window.lastNode = node;
            toKeep[nodeId] = e.data.current.nodes[0];

            tooltips.open(node, config.node, node[s.renderers[0].options.prefix + 'x'], node[s.renderers[0].options.prefix + 'y']);


            s.graph.nodes().forEach(function(n) {
                if (toKeep[n.id])
                    n.color = n.originalColor;
                else
                    n.color = 'rgba(0,0,0,0)';
            });

            s.graph.edges().forEach(function(e) {
                if (e.source == node.id || e.target == node.id) {
                    if (typeof toKeep[e.source].depends != 'undefined' && toKeep[e.source].depends.indexOf(e.target) > -1) {
                        e.color = 'rgba(255,85,85,.66)';
                    }
                    else if (typeof toKeep[e.source].includes != 'undefined' && toKeep[e.source].includes.indexOf(e.target) > -1) {
                        e.color = 'rgba(85,85,255,.66)';
                    }
                    else {
                        e.color = 'rgba(85,85,85,.66)';
                    }
                }
                else
                    e.color = 'rgba(0,0,0,0)';
            });

            // Since the data has been modified, we need to
            // call the refresh method to make the colors
            // update effective.
            s.refresh();
        });

        $(window).resize(function () {
            s.refresh();
            if(typeof window.lastNode != 'undefined') {
                setTimeout(function () {
                    tooltips.open(window.lastNode, config.node, window.lastNode[s.renderers[0].options.prefix + 'x'], window.lastNode[s.renderers[0].options.prefix + 'y']);
                }, 13);
            }
        });

        s.bind('clickNode', function (e) {

            var node = e.data.node;
            setTimeout(function () {
                tooltips.open(node, config.node, node[s.renderers[0].options.prefix + 'x'], node[s.renderers[0].options.prefix + 'y']);
            }, 13);

        });

        // When the stage is clicked, we just color each
        // node and edge with its original color.
        s.bind('clickStage', function(e) {
            s.graph.nodes().forEach(function(n) {
                n.color = n.originalColor;
            });

            s.graph.edges().forEach(function(e) {
                e.color = e.originalColor;
            });

            // Same as in the previous event:
            s.refresh();
        });

        body.one('testended', '#validation', function () {
            tooltips.close();
            sigma.plugins.killTooltips(s);
            s.kill();
            sigma.parsers.json(window.callbackPaths['validation_refresh'], graphConfig, createGraph);
        });

        /*
         function renderHalo() {
         s.renderers[0].halo({
         nodes: s.graph.nodes()
         });
         }

         renderHalo();

         s.renderers[0].bind('render', function(e) {
         renderHalo();
         });

         s.bind('clickStage', function(e) {
         renderHalo();
         });

         s.bind('hovers', function(e) {
         var adjacentNodes = [],
         adjacentEdges = [];

         if (!e.data.enter.nodes.length) return;

         // Get adjacent nodes:
         e.data.enter.nodes.forEach(function(node) {
         adjacentNodes = adjacentNodes.concat(s.graph.adjacentNodes(node.id));
         });

         // Add hovered nodes to the array and remove duplicates:
         adjacentNodes = arrayUnique(adjacentNodes.concat(e.data.enter.nodes));
         var dependsNodes = [], includesNodes = [];
         for(var i = 0; i < adjacentNodes.length; i++) {
         if(e.data.enter.nodes[0].depends.indexOf(adjacentNodes[i].id) > -1) {
         dependsNodes = dependsNodes.concat(adjacentNodes[i]);
         }
         else {
         includesNodes = includesNodes.concat(adjacentNodes[i]);
         }
         }

         // Get adjacent edges:
         e.data.enter.nodes.forEach(function(node) {
         adjacentEdges = adjacentEdges.concat(s.graph.adjacentEdges(node.id));
         });

         // Remove duplicates:
         adjacentEdges = arrayUnique(adjacentEdges);
         var dependsEdges = [], includesEdges = [];
         for(var j = 0; j < adjacentEdges.length; j++) {
         if(e.data.enter.nodes[0].depends.indexOf(adjacentEdges[j].source) > -1 ||
         e.data.enter.nodes[0].depends.indexOf(adjacentEdges[j].target) > -1) {
         dependsEdges = dependsEdges.concat(adjacentEdges[j]);
         }
         else {
         includesEdges = includesEdges.concat(adjacentEdges[j]);
         }
         }

         // Render halo:
         s.renderers[0].halo({
         nodes: adjacentNodes,
         edges: adjacentEdges
         });
         });
         */

    }

    body.on('show', '#validation', function () {
        if ($(this).is('.loaded')) {
            $(this).trigger('testended');
        }
        else {
            $(this).addClass('loaded');


            // Add a method to the graph model that returns an
            // object with every neighbors of a node inside:
            sigma.classes.graph.addMethod('neighbors', function(nodeId) {
                var k,
                    neighbors = {},
                    index = this.allNeighborsIndex[nodeId] || {};

                for (k in index) {
                    neighbors[k] = this.nodesIndex[k];
                }

                return neighbors;
            });

            sigma.renderers.def = sigma.renderers.canvas;

            sigma.parsers.json(
                window.callbackPaths['validation_refresh'], graphConfig, createGraph);
        }
    });

});