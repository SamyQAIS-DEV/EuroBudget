import React from 'react';
import ReactDOM from 'react-dom/client';
import {Switch} from '@components/Switch';

export class SwitchElement extends HTMLInputElement {
    connectedCallback() {
        const element = this as HTMLInputElement;
        const root = ReactDOM.createRoot(element.parentElement);
        const label = element.parentElement.innerText;
        const id = element.getAttribute('id');
        const name = element.getAttribute('name');
        const required = element.getAttribute('required');
        const className = element.getAttribute('class');
        const value = element.getAttribute('value');
        root.render(
            <Switch
                id={id}
                name={name}
                required={required}
                className={className}
                value={value}
                label={label}
            />
        );
    }
}