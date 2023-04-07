import React from 'react';
import ReactDOM from 'react-dom/client';
import {Alert} from '@components/Alert';
import {AlertEnum} from '@enums/AlertEnum';

export class AlertElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const type = element.getAttribute('type');
        const duration = element.getAttribute('duration');
        const floating = element.getAttribute('floating');
        const handleClose = () => {
            element.remove();
        };
        root.render(
            <Alert
                type={type}
                duration={duration !== null ? parseInt(duration) : undefined}
                floating={floating !== null && floating !== undefined && floating !== 'false'}
                onClose={handleClose}
            >
                {element.innerHTML}
            </Alert>,
        );
    }
}

/**
 *
 * @param message
 * @param type
 * @param duration in ms
 */
export const addFlash = (message: string, type: AlertEnum = AlertEnum.SUCCESS, duration: number|null = 5000) => {
    const alert = document.createElement('alert-element');
    if (type !== AlertEnum.ERROR && duration) {
        alert.setAttribute('duration', String(duration));
    }
    alert.setAttribute('type', type);
    alert.setAttribute('floating', String(true));
    alert.innerText = message;
    document.querySelector('#floating-alerts').appendChild(alert);
};