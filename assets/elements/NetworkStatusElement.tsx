import React from 'react';
import ReactDOM from 'react-dom/client';
import {NetworkStatus} from '@components/NetworkStatus';

export class NetworkStatusElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        root.render(
            <NetworkStatus />
        );
    }
}