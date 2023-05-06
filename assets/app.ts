import './css/app.scss';
import './bootstrap';
import './elements/index';
import './prototypes/index';

import Turbolinks from 'turbolinks';

import './modules/scrollreveal';
import {registerColor} from './modules/color';

declare global {
    interface Window {
        eurobudget: {
            USER_ID?: number;
            FAVORITE_DEPOSIT_ACCOUNT_ID?: number;
            FAVORITE_DEPOSIT_ACCOUNT_COLOR?: string;
            IS_PREMIUM?: boolean;
        };
    }
}


document.addEventListener('turbolinks:load', () => {
    registerColor();

    setTimeout(() => {
        const $mainLoader = document.querySelector('#main-loader');
        if ($mainLoader) {
            $mainLoader.remove();
        }
    }, 300);
});

Turbolinks.start();