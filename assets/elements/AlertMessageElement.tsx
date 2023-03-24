import React from 'react';
import ReactDOM from 'react-dom/client';
import {AlertMessage} from '@components/AlertMessage';

export class AlertMessageElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const type = element.getAttribute('type');
        const duration = element.getAttribute('duration');
        console.log('coucou');
        root.render(
            <AlertMessage
                // type={type}
                // duration={ParseInt(duration)}
                // floating={ParseInt(duration)}
            />
        );
    }
}