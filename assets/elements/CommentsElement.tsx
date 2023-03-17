import React from 'react';
import ReactDOM from 'react-dom/client';
import {Comments} from '@components/Comments';

export class CommentsElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const userId = element.getAttribute('user-id');
        root.render(
            <Comments userId={parseInt(userId, 10)} />
        );
    }
}