import React from 'react';
import ReactDOM from 'react-dom/client';
import {PremiumButton} from '@components/PremiumButton';
import {DepositAccountRecap} from '@components/DepositAccountRecap';

export class DepositAccountRecapElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        root.render(
            <DepositAccountRecap />
        );
    }
}