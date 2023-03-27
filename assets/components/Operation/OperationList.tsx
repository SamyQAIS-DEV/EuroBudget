import React, {useEffect} from 'react';
import {Loader} from '@components/Animation/Loader';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Button} from '@components/Button';
import {Operation} from '@components/Operation/Operation';

export type OperationEntity = {
    id: number;
    label: string;
    amount: number;
    date: string;
    type: string;
    past: boolean;
};

type OperationListProps = {
    year: string;
    month: string;
};

export const OperationList = ({year, month}: OperationListProps) => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<OperationEntity[]>(true, '/api/operations/current-month');

    useEffect(() => {
        if (!year || !month) {
            return;
        }
        fetch(`/api/operations/${year}/${month}`);
    }, [year, month]);

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
        <section id="operation-list">
            <p><strong>{data.length}</strong> opération{data.length > 0 && 's'} ce mois-ci</p>
            <div className="operations list-group p0">
                {data.map((operation) => (
                    <Operation key={operation.id} operation={operation}/>
                ))}
            </div>
        </section>
    );
};