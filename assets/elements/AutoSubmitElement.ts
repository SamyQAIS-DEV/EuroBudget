import React from 'react';

export class AutoSubmitElement extends HTMLFormElement {
    connectedCallback() {
        const element = this as HTMLFormElement;
        Array.from(element.querySelectorAll('input, select'))
            .forEach((input) => {
                input.addEventListener('change', () => {
                    element.submit();
                });
            })
        ;
    }
}