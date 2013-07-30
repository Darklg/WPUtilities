/*
 * Plugin Name: Smooth Scroll
 * Version: 1.3
 * JavaScriptUtilities Smooth Scroll may be freely distributed under the MIT license.
 */

/* ----------------------------------------------------------
   Smooth Scroll
---------------------------------------------------------- */

/*
 * Dependencies : MooTools More With Fx.Scroll.
 */

/*
new dkSmoothScroll($(element), {
    duration: 500,
    transition: Fx.Transitions.Sine.easeOut
});
*/

var dkSmoothScroll = new Class({
    settings: {},
    defaultSettings: {
        duration: 500,
        offsetTop: 0,
        transition: Fx.Transitions.Sine.easeOut
    },
    initialize: function(el, settings) {
        var controlClass = 'moo_dksmoothscroll';
        if (!el || el.hasClass(controlClass)) {
            return;
        }
        el.addClass(controlClass);
        this.el = el;
        this.getSettings(settings);
        this.setEvents();
    },
    getSettings: function(settings) {
        if (typeof settings != 'object') {
            settings = {};
        }
        this.settings = Object.merge({}, this.defaultSettings, settings);
    },
    setEvents: function() {
        var settings = this.settings;
        this.el.addEvent('click', function(e) {
            var href = $(this).get('href'),
                target = $$(href);
            if (target[0]) {
                e.preventDefault();
                var toptarget = target[0].getTop() + settings.offsetTop,
                    myFX = new Fx.Scroll(window, {
                    duration: settings.duration,
                    transition: settings.transition
                });
                myFX.start(0, toptarget);
            }
        });
    }
});