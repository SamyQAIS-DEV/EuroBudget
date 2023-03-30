import React from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Loader} from '@components/Animation/Loader';
import {Button} from '@components/Button';
import {jsonFetch} from '@functions/api';

type DepositAccountResource = {
    id: number;
    title: string;
    amount: number;
    color: string;
    creatorId: number;
    finalAmount: number;
    waitingAmount: number;
    waitingOperationsNb: number;
};

export const DepositAccountRecap = () => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<DepositAccountResource>(true, () => jsonFetch('/api/deposit-accounts/favorite-recap')); // TODO Use api file

    if (isLoading) {
        return <Loader/>;
    }

    if (isError) {
        return (
            <p>
                Une erreur est survenue <Button onClick={() => fetch()}>Réessayez</Button>
            </p>
        );
    }

    return (
        <section id="deposit-account-recap">
            <h3 className='pill mb1'>{data.title}</h3>
            <div className="recap grid3" style={{backgroundColor: data.color}}>
                <div className="recap__item">
                    Montant actuel
                    <div className="recap__amount">{data.amount}<sup>€</sup></div>
                </div>
                <div className="recap__item">
                    {data.waitingOperationsNb} operation{data.waitingOperationsNb > 0 && 's'} en attente, pour
                    <div className="recap__amount">{data.waitingAmount}<sup>€</sup></div>
                </div>
                <div className="recap__item">
                    Montant final
                    <div className="recap__amount">{data.finalAmount}<sup>€</sup></div>
                </div>
            </div>
        </section>
    );
};