import React from 'react';
import ReactDOM from 'react-dom/client';
import {ThemeSwitcher} from '@components/ThemeSwitcher';

export class ThemeSwitcherElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        root.render(
            <ThemeSwitcher />
        );
    }
}