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
        const checked = element.getAttribute('checked');
        const disabled = element.getAttribute('disabled');
        root.render(
            <Switch
                id={id}
                name={name}
                required={required}
                className={className}
                label={label}
                defaultChecked={checked !== null && checked !== undefined && checked !== "false"}
                disabled={disabled !== null && disabled !== undefined && disabled !== "false"}
            />
        );
    }
}