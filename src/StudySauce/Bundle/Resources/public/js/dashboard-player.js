
$(document).ready(function () {

    // Use a single player for all audio on the dashboard
    var body = $('body');

    // -------------- Player --------------- //
    //window.musicIndex = 0;
    if(typeof $.fn.jPlayer == 'function') {
        var jp = jQuery('#jquery_jplayer');
        //window.musicIndex = Math.floor(Math.random() * window.musicLinks.length);
        jp.jPlayer({
            swfPath: Routing.generate('_welcome') + 'bundles/studysauce/js',
            solution: 'html,flash',
            supplied: 'm4a,mp3,oga',
            preload: 'metadata',
            volume: 0.8,
            muted: false,
            cssSelectorAncestor: '.preview-play:visible',
            cssSelector: {
                play: '.play',
                pause: '.pause'
            },
            ready: function() {

            }
        });

        /*
         jp.bind($.jPlayer.event.ended, function () {
         if(window.musicIndex == -1) {
         window.musicIndex = Math.floor(Math.random() * window.musicLinks.length);
         return;
         }
         var index = ++window.musicIndex % window.musicLinks.length;
         jp.jPlayer("setMedia", {
         mp3: window.musicLinks[index],
         m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
         oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
         });
         $(this).jPlayer("play");
         });
         */
    }
    // -------------- END Player --------------- //



});