import React from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Loader} from '@components/Animation/Loader';
import {Button} from '@components/Button';

type DepositAccountRecap = {
    id: number;
    title: string;
    amount: number;
    color: string;
    creatorId: number;
    finalAmount: number;
    waitingAmount: number;
    waitingOperationsNb: number;
};

export const FavoriteDepositAccountRecap = () => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<DepositAccountRecap>('/api/deposit-accounts/favorite-recap');

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
        <div id="favorite-deposit-account-recap">
            <p>Il y a {data.waitingOperationsNb} operation{data.waitingOperationsNb > 0 && 's'} en attente</p>
            <h3>{data.title}</h3>
            <div className="card grid3" style={{backgroundColor: data.color}}>
                <div className="recap__amount">
                    amount : {data.amount}
                </div>
                <div className="recap__waiting_amount">
                    waitingAmount : {data.waitingAmount}
                </div>
                <div className="recap__final_amount">
                    finalAmount : {data.finalAmount}
                </div>
            </div>
        </div>
    );
};