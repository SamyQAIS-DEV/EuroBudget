import React from 'react';
import ReactDOM from 'react-dom/client';
import {PremiumButton} from '@components/PremiumButton';

export class PremiumButtonElement extends HTMLElement {
    connectedCallback() {
        const element = this as HTMLElement;
        const root = ReactDOM.createRoot(element);
        const planId = element.getAttribute('plan-id');
        const price = element.getAttribute('price');
        const duration = element.getAttribute('duration');
        const paypalId = element.getAttribute('paypal-id');
        root.render(
            <PremiumButton
                planId={parseInt(planId, 10)}
                price={parseInt(price, 10)}
                duration={parseInt(duration, 10)}
                paypalId={paypalId}
            >{element.innerHTML}</PremiumButton>
        );
    }
}