/**
 * Copyright © 2019 Denistrator. No rights reserved.
 * bitch
 */

define([], function () {
    'use strict';

    var blockToCollapse = document.querySelector('.portfolio-list');
    var collapsedHeight = document.querySelector('.portfolio-collapse-pos').offsetTop +
        document.querySelector('.portfolio-collapse-pos').offsetHeight;

    blockToCollapse.style.height = collapsedHeight + 'px';

    var expander = document.querySelector('.portfolio-expander');
    expander.addEventListener('click', function () {
        blockToCollapse.style.height = blockToCollapse.scrollHeight + 'px';
        expander.classList.add('hide');
    });
});
