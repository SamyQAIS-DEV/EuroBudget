import React from 'react';
import ReactDOM from 'react-dom/client';
import {Search} from '@components/Search/Search';

export class SearchButtonElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        root.render(
            <Search />
        );
    }
}