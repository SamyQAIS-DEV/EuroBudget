import React from 'react';
import ReactDOM from 'react-dom/client';
import {FavoriteDepositAccountSelection} from '@components/DepositAccount/FavoriteDepositAccountSelection';

export class DepositAccountSelectionElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        root.render(
            <FavoriteDepositAccountSelection />
        );
    }
}