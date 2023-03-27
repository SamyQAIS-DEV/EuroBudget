import React, {useEffect} from 'react';
import {Loader} from '@components/Animation/Loader';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Button} from '@components/Button';

type Operation = {
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
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<Operation[]>('/api/operations/current-month');

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
        <div id="operation-list">
            <h2>Operations List</h2>
            <div className="operations">
                {data.map((operation: Operation) => (
                    <div key={operation.id} className="year">
                        <p>{operation.label}</p>
                        <p>{operation.amount}</p>
                        <p>{operation.date}</p>
                        <p>{operation.type}</p>
                        <p>{operation.past}</p>
                    </div>
                ))}
            </div>
        </div>
    );
};