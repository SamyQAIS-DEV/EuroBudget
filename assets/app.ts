import './css/app.scss';
import './bootstrap';
import './elements/index';
import './prototypes/index';

import Turbolinks from 'turbolinks';

import './modules/scrollreveal';

declare global {
    interface Window {
        eurobudget: {
            USER_ID?: number;
            FAVORITE_DEPOSIT_ACCOUNT_ID?: number;
            IS_PREMIUM?: boolean;
        };
    }
}

document.addEventListener('turbolinks:load', () => {
    setTimeout(() => {
        const $mainLoader = document.querySelector('#main-loader');
        if ($mainLoader) {
            $mainLoader.remove();
        }
    }, 300);
});

Turbolinks.start();