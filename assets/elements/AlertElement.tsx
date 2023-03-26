import React from 'react';
import ReactDOM from 'react-dom/client';
import {Alert} from '@components/Alert';

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
                floating={floating !== null && floating !== undefined && floating !== "false"}
                onClose={handleClose}
            >
                {element.innerHTML}
            </Alert>
        );
    }
}