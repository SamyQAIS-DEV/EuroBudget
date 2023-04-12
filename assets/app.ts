import './css/app.scss';
import './bootstrap';
import './elements/index';
import './prototypes/index';

declare global {
    interface Window {
        eurobudget: {
            USER_ID?: number;
            FAVORITE_DEPOSIT_ACCOUNT_ID?: number;
        };
    }
}

import './modules/scrollreveal';

document.addEventListener('DOMContentLoaded', () => {

});

