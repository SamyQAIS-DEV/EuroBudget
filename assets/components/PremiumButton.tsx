import React, {PropsWithChildren, useEffect, useRef, useState} from 'react';
import {useToggle} from '@hooks/useToggle';
import * as scriptjs from 'scriptjs';
import {jsonFetchOrFlash} from '@functions/api';
import {HttpRequestMethodEnum} from '@enums/HttpEnum';
import {Button} from '@components/Button';
import {classNames} from '@functions/dom';

declare const window: any;

type PremiumButtonProps = {
    planId: number;
    price: number;
    duration: number;
    paypalId: string;
} & PropsWithChildren;

export const PremiumButton = ({
    planId,
    price,
    duration,
    paypalId,
    children,
}: PremiumButtonProps) => {
    const [payment, togglePayment] = useToggle(false);
    const description = `Compte premium ${duration} mois`;

    if (payment === false) {
        return <Button onClick={togglePayment}>{children}</Button>;
    }

    return (
        <PaymentMethods planId={planId} onPaypalApproval={() => console.log('onPaypalApproval')} price={price} description={description} paypalId={paypalId}/>
    );
};

const PAYMENT_CARD = 'CARD';
const PAYMENT_PAYPAL = 'PAYPAL';

type PaymentMethodsProps = {
    planId: number;
    onPaypalApproval: any;
    description: string;
    price: number;
    paypalId: string;
} & PropsWithChildren;

const PaymentMethods = ({
    planId,
    onPaypalApproval,
    description,
    price,
    paypalId,
}: PaymentMethodsProps) => {
    const [method, setMethod] = useState(PAYMENT_PAYPAL);

    return (
        <div className="text-left">
            <div className="form-group mb2">
                <label>Méthode de paiement</label>
                <div className="btn-group">
                    <Button
                        onClick={() => setMethod(PAYMENT_CARD)}
                        className={classNames('btn-secondary btn-small', method === PAYMENT_CARD && 'active')}
                    >
                        Carte
                        {/*<img src="/images/payment-methods.png" width="76" className="mr1"/>*/}
                    </Button>
                    <Button
                        onClick={() => setMethod(PAYMENT_PAYPAL)}
                        className={classNames('btn-secondary btn-small', method === PAYMENT_PAYPAL && 'active')}
                    >
                        Paypal
                        {/*<img src="/images/paypal.svg" width="20" className="mr1"/>*/}
                    </Button>
                </div>
            </div>
            {method === PAYMENT_PAYPAL ? (
                <PaymentPaypal
                    planId={planId}
                    price={price}
                    description={description}
                    onApprove={onPaypalApproval}
                    paypalId={paypalId}
                />
            ) : (
                <PaymentCard/>
            )}
        </div>
    );
};

type PaymentPaypalProps = {
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
    paypalId,
}: PaymentPaypalProps) => {
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
            await jsonFetchOrFlash(`/api/premium/paypal/${orderId}`, {}, HttpRequestMethodEnum.POST);
            window.location.replace('/profil?payment_success=1');
        } catch (e) {
            console.error(e.name);
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
            <div style={{minHeight: 52, display: loading ? 'none' : null}} ref={container}/>
            {loading && (
                <Button className="btn-primary" loading={true}>
                    Chargement...
                </Button>
            )}
        </div>
    );
};

const PaymentCard = () => {
    return (
        <div>
            <p>Ce moyen de paiement n'est pas encore disponible.</p>
        </div>
    );
};
