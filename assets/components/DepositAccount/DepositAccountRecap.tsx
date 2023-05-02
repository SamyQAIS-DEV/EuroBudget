import React, {useEffect} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Loader} from '@components/Animation/Loader';
import {Button} from '@components/Button';
import {Operation} from '@entities/Operation';
import {findDepositAccountResource} from '@api/deposit-accounts';
import {DepositAccountResource} from '@entities/DepositAccountResource';
import {LoaderWrapper} from '@components/LoaderWrapper';
import {formatCurrency} from '@functions/number';

type Props = {
    operationChanged: Operation;
};

export const DepositAccountRecap = ({
    operationChanged,
}: Props) => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<DepositAccountResource>(true, findDepositAccountResource);

    useEffect(() => {
        if (!operationChanged) {
            return;
        }
        fetch(() => findDepositAccountResource());
    }, [operationChanged]);

    if (isLoading && !isDone) {
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
        <section id='deposit-account-recap'>
            <div className="flex wrap">
                <h3 className="pill mb1">{data.title}</h3>
                <a className="btn-secondary" href="/transfers/new" data-turbolinks="false">Faire un virement</a>
            </div>
            <div className="recap grid3 mt1" style={{backgroundColor: data.color}}>
                <div className="recap__item">
                    Montant actuel
                    <div className="recap__amount">{formatCurrency(data.amount)}</div>
                </div>
                <div className="recap__item">
                    {data.waitingOperationsNb} operation{data.waitingOperationsNb > 0 && 's'} en attente, pour
                    <div className="recap__amount">{formatCurrency(data.waitingAmount)}</div>
                </div>
                <div className="recap__item">
                    Montant final
                    <div className="recap__amount">{formatCurrency(data.finalAmount)}</div>
                </div>
                {isLoading && (
                    <LoaderWrapper />
                )}
            </div>
        </section>
    );
};