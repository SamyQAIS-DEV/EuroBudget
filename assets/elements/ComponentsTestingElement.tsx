import React from 'react';
import ReactDOM from 'react-dom/client';
import {ComponentsTesting} from '@components/ComponentsTesting';

export class ComponentsTestingElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const userId = element.getAttribute('user-id');
        root.render(
            <ComponentsTesting userId={parseInt(userId, 10)} />
        );
    }
}