import React from 'react';
import ReactDOM from 'react-dom/client';
import {PremiumButton} from '@components/PremiumButton';

export class PremiumButtonElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const plan = element.getAttribute('plan');
        const price = element.getAttribute('price');
        const duration = element.getAttribute('duration');
        const paypalId = element.getAttribute('paypalId');
        root.render(
            <PremiumButton
                plan={parseInt(plan, 10)}
                price={parseInt(price, 10)}
                duration={parseInt(duration, 10)}
                paypalId={paypalId}
            >{element.innerHTML}</PremiumButton>
        );
    }
}