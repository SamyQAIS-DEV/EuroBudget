import React, {PropsWithChildren, Ref, useEffect, useRef, useState} from 'react';
import {useToggle} from '@hooks/useToggle';
import * as scriptjs from 'scriptjs';
import {jsonFetch} from '@functions/api';

declare const window: any;

type PremiumButtonType = {
    plan: number;
    price: number;
    duration: number;
    paypalId: string;
} & PropsWithChildren;

export const PremiumButton = ({
    plan,
    price,
    duration,
    paypalId,
    children,
}: PremiumButtonType) => {
    const [payment, togglePayment] = useToggle(false);
    const description = `Compte premium ${duration} mois`;

    if (payment === false) {
        return <button onClick={togglePayment}>{children}</button>;
    }

    return (
        <PaymentMethods plan={plan} onPaypalApproval={() => console.log('onPaypalApproval')} price={price} description={description} paypalId={paypalId}/>
    );
};

const PAYMENT_CARD = 'CARD';
const PAYMENT_PAYPAL = 'PAYPAL';

type PaymentMethodsType = {
    plan: number;
    onPaypalApproval: any;
    description: string;
    price: number;
    paypalId: string;
} & PropsWithChildren;

const PaymentMethods = ({
    plan,
    onPaypalApproval,
    description,
    price,
    paypalId
}: PaymentMethodsType) => {
    const [method, setMethod] = useState(PAYMENT_PAYPAL);

    return (
        <div className="text-left">
            <div className="form-group mb2">
                <label>Méthode de paiement</label>
                <div className="btn-group">
                    <button
                        onClick={() => setMethod(PAYMENT_CARD)}
                        // className={classNames('btn-secondary btn-small', method === PAYMENT_CARD && 'active')}
                    >
                        Carte
                        {/*<img src="/images/payment-methods.png" width="76" className="mr1"/>*/}
                    </button>
                    <button
                        onClick={() => setMethod(PAYMENT_PAYPAL)}
                        // className={classNames('btn-secondary btn-small', method === PAYMENT_PAYPAL && 'active')}
                    >
                        Paypal
                        {/*<img src="/images/paypal.svg" width="20" className="mr1"/>*/}
                    </button>
                </div>
            </div>
            {method === PAYMENT_PAYPAL ? (
                <PaymentPaypal
                    planId={plan}
                    price={price}
                    description={description}
                    onApprove={onPaypalApproval}
                    paypalId={paypalId}
                />
            ) : (
                <></>
                // <PaymentCard plan={plan}/>
            )}
        </div>
    );
};

type PaymentPaypalType = {
    planId: number;
    price: number;
    description: string;
    onApprove: () => void;
    paypalId: string;
};

const PaymentPaypal = ({
    planId,
    price,
    description,
    paypalId
}: PaymentPaypalType) => {
    const container = useRef<HTMLDivElement>(null);
    const approveRef = useRef<(orderId: string) => void>(null);
    const currency = 'EUR';
    const [country, setCountry] = useState(null);
    const [loading, toggleLoading] = useToggle(false);
    // const vat = country ? vatPrice(price, country) : null;
    const vat = 0.2;

    approveRef.current = async (orderId: string) => {
        toggleLoading();
        try {
            await jsonFetch(`/api/premium/paypal/${orderId}`, {method: 'POST'});
            // await redirect('?success=1');
        } catch (e) {
            console.error(e.name);
            // if (e instanceof ApiError) {
            //     flash(e.name, 'danger', null);
            // }
        }
        toggleLoading();
    };

    useEffect(() => {
        if (vat === null) {
            return;
        }
        toggleLoading();
        const priceWithoutTax = price - vat;
        scriptjs(
            `https://www.paypal.com/sdk/js?client-id=${paypalId}&disable-funding=card,credit&integration-date=2020-12-10&currency=${currency}`,
            () => {
                toggleLoading();
                container.current.innerHTML = '';
                window.paypal
                    .Buttons({
                        style: {
                            label: 'pay',
                        },
                        createOrder: (data, actions) => {
                            return actions.order.create({
                                purchase_units: [
                                    {
                                        description,
                                        custom_id: planId,
                                        items: [
                                            {
                                                name: description,
                                                quantity: '1',
                                                unit_amount: {
                                                    value: priceWithoutTax,
                                                    currency_code: currency,
                                                },
                                                tax: {
                                                    value: vat,
                                                    currency_code: currency,
                                                },
                                                category: 'DIGITAL_GOODS',
                                            },
                                        ],
                                        amount: {
                                            currency_code: currency,
                                            value: price,
                                            breakdown: {
                                                item_total: {
                                                    currency_code: currency,
                                                    value: priceWithoutTax,
                                                },
                                                tax_total: {
                                                    currency_code: currency,
                                                    value: vat,
                                                },
                                            },
                                        },
                                    },
                                ],
                            });
                        },
                        onApprove: data => {
                            approveRef.current(data.orderID);
                        },
                    })
                    .render(container.current);
                return () => {
                    container.current.innerHTML = '';
                };
            },
        );
    }, [description, planId, price, vat]);

    return (
        <div>
            {/*<Field*/}
            {/*    name='countryCode'*/}
            {/*    required*/}
            {/*    component={CountrySelect}*/}
            {/*    value={country}*/}
            {/*    onChange={e => setCountry(e.target.value)}*/}
            {/*>*/}
            {/*    Pays de résidence*/}
            {/*</Field>*/}
            {/*{country && <div style={{ minHeight: 52, display: loading ? 'none' : null }} ref={container} />}*/}
            <div style={{ minHeight: 52, display: loading ? 'none' : null }} ref={container} />
            {loading && (
                // <button className='btn-primary btn-block' loading>
                <button className='btn-primary btn-block'>
                    Chargement...
                </button>
            )}
        </div>
    );
};

/*function PaymentCard({plan, publicKey}) {
    const [subscription, toggleSubscription] = useToggle(true);
    const [loading, toggleLoading] = useToggle(false);
    const startPayment = async () => {
        toggleLoading();
        try {
            const Stripe = await importScript('https://js.stripe.com/v3/', 'Stripe');
            const stripe = new Stripe(publicKey);
            const {id} = await jsonFetch(`/api/premium/${plan}/stripe/checkout?subscription=${subscription ? '1' : '0'}`, {
                method: 'POST',
            });
            stripe.redirectToCheckout({sessionId: id});
        } catch (e) {
            flash(e instanceof ApiError ? e.name : e, 'error');
            toggleLoading();
        }
    };

    return (
        <Stack gap={2}>
            <Stack gap={1}>
                <Checkbox id={'subscription' + plan} name="subscription" checked={subscription} onChange={toggleSubscription}>
                    Renouveler automatiquement
                </Checkbox>
                {subscription && <p className="text-small text-muted">
                    Le renouvellement automatique est activé, vous serez prélevé automatiquement à la fin de chaque période.
                    Vous pourrez interrompre l'abonnement à tout moment depuis <a
                    href="/profil/edit" target="_blank">votre compte</a>.
                </p>}
            </Stack>
            <PrimaryButton size="block" onClick={startPayment} loading={loading}>
                {subscription ? 'S\'abonner via' : 'Payer via '}
                <img src="/images/stripe.svg" height="20" style={{marginLeft: '.4rem'}}/>
            </PrimaryButton>
        </Stack>
    );
};*/
