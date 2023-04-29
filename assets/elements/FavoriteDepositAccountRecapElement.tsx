import React from 'react';
import ReactDOM from 'react-dom/client';
import {FavoriteDepositAccountRecap} from '@components/DepositAccount/FavoriteDepositAccountRecap';

export class FavoriteDepositAccountRecapElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const labels: string[] = JSON.parse(this.getAttribute('labels'));
        root.render(
            <FavoriteDepositAccountRecap labels={labels} />
        );
    }
}