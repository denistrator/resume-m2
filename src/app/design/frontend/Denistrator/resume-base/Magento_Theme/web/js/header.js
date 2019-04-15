/**
 * Copyright © 2019 Denistrator. No rights reserved.
 * bitch
 */

define([], function () {
    'use strict';

    var readingModeToggle = document.querySelector('.reading-mode-toggle');

    readingModeToggle.addEventListener('click', function () {
        var body = document.querySelector('body');

        body.classList.toggle('light');

        if (body.classList.contains('light')) {
            readingModeToggle.classList.remove('dark');
            readingModeToggle.classList.add('light');
        } else {
            readingModeToggle.classList.remove('light');
            readingModeToggle.classList.add('dark');
        }
    }, false);

    var langToggle = document.querySelector('.lang-toggle');
    var langToggleText = document.querySelector('.lang-toggle-text');
    var html = document.querySelector('html');

    langToggle.addEventListener('click', function () {
        var currentLang = html.getAttribute('lang');
        var lang, locale;
        switch (currentLang) {
            case 'en':
                lang = 'ru';
                locale = 'Ru';
                break;
            default:
                lang = 'en';
                locale = 'En';
        }
        html.setAttribute('lang', lang);
        langToggleText.innerHTML = locale;
    }, false);
});
