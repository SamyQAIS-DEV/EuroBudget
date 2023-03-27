import React from 'react';
import ReactDOM from 'react-dom/client';
import {Modal} from '@components/Modal';

export class ModalElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const handleClose = () => {
            element.remove();
        };
        root.render(
            <Modal show={true} onClose={handleClose}>
                <div dangerouslySetInnerHTML={{__html: element.innerHTML}}></div>
            </Modal>,
        );
    }
}