/**
 * Copyright © 2019 Denistrator. No rights reserved.
 * bitch
 */

define([], function () {
    'use strict';

    var userTime = new Date();
    if (userTime.getHours() > 10 && userTime.getHours() < 16) {
        document.querySelector('.page').classList.toggle('light');
        document.querySelector('.reading-mode-toggle').classList.toggle('dark');
    }

    (function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)
});
