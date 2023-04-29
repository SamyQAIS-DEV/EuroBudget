import React, {useCallback, useEffect, useState} from 'react';
import {Loader} from '@components/Animation/Loader';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Button} from '@components/Button';
import {Operation} from '@components/Operation/Operation';
import {Icon} from '@components/Icon';
import {Modal} from '@components/Modal/Modal';
import {Operation as OperationEntity} from '@entities/Operation';
import {addOperation, deleteOperation, findOperationsForCurrentMonth, findOperationsForMonth, updateOperation, updatePastOperation} from '@api/operations';
import {OperationForm} from '@components/Operation/OperationForm';
import {addFlash} from '@elements/AlertElement';
import {isCurrentMonth} from '@functions/date';
import {Switch} from '@components/Switch';

type OperationListProps = {
    year: string;
    month: string;
    labels: string[];
    onOperationChanged?: (operation: OperationEntity) => void;
};

type State = {
    creating: boolean;
    editing?: number;
};

export const OperationList = ({
    year,
    month,
    labels,
    onOperationChanged = () => {},
}: OperationListProps) => {
    const [{data, isLoading, isError, isDone}, fetch, setData] = useJsonFetch<OperationEntity[]>(true, findOperationsForCurrentMonth);
    const [filterEnabled, setFilterEnabled] = useState<boolean>(false);
    const [state, setState] = useState<State>({
        creating: false,
        editing: null,
    });

    useEffect(() => {
        if (!year || !month) {
            return;
        }
        fetch(() => findOperationsForMonth(year, month));
    }, [year, month]);

    const handleCreating = useCallback(() => {
        setState(s => ({...s, creating: true}));
    }, []);

    const handleEditing = useCallback((operation: OperationEntity) => {
        setState(s => ({...s, editing: s.editing === operation.id ? null : operation.id}));
    }, []);

    const handleCloseCreating = useCallback(() => {
        setState(s => ({...s, creating: false}));
    }, []);

    const handleCloseEditing = useCallback(() => {
        setState(s => ({...s, editing: null}));
    }, []);

    const sortData = (array: OperationEntity[]): void => {
        array.sort((a, b) => {
            if (b.date.getDate() === a.date.getDate()) {
                return a.label.toLowerCase() > b.label.toLowerCase() ? 1 : -1;
            }
            return b.date.getDate() - a.date.getDate();
        });
        setData(array);
    };

    const handleCreate = async (operation: OperationEntity) => {
        const newOperation = await addOperation(operation);
        setState(s => ({...s, creating: false}));
        if (isCurrentMonth(operation.date, year && month ? new Date(year + '/' + month) : new Date())) {
            let newData = [newOperation, ...data];
            sortData(newData);
        }
        addFlash('Opération créée');
        onOperationChanged(operation);
    };

    const handleUpdate = async (operation: OperationEntity) => {
        const newOperation = await updateOperation(operation);
        setState(s => ({...s, editing: null}));
        if (isCurrentMonth(operation.date, year && month ? new Date(year + '/' + month) : new Date())) {
            let newData = data.map(o => (o.id === operation.id ? newOperation : o));
            sortData(newData);
        } else {
            setData(data.filter(o => o !== operation));
        }
        addFlash('Opération modifiée');
        onOperationChanged(operation);
    };

    const handlePastChanged = async (operation: OperationEntity) => {
        try {
            const newOperation = await updatePastOperation(operation);
            setData(data.map(o => (o.id === operation.id ? newOperation : o)));
            onOperationChanged(operation);
        } catch (e) {
            operation.past = !operation.past;
            setData(data.map(o => (o.id === operation.id ? operation : o)));
        }
    };

    const handleDelete = async (operation: OperationEntity) => {
        await deleteOperation(operation);
        setData(data.filter(o => o !== operation));
        addFlash('Opération suprimée');
        onOperationChanged(operation);
    };

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
            <div className="flex">
                <Button className="mb1" onClick={handleCreating}>
                    <Icon name="edit" title="Créer une opération"/><span>Créer une opération</span>
                </Button>
                {data.length > 0 && (
                    <Switch id='operation-past-filter' checked={filterEnabled} label='Filtrer' onChange={() => setFilterEnabled(!filterEnabled)}/>
                )}
            </div>
            <Modal show={state.creating} onClose={handleCloseCreating}>
                Création d'une opération
                <OperationForm labels={labels} onSubmit={handleCreate}/>
            </Modal>
            <div className="operations list-group p0">
                {data.map((operation) => {
                    if (filterEnabled && operation.past) {
                        return null;
                    }
                    return (
                        <Operation
                            key={operation.id}
                            operation={operation}
                            labels={labels}
                            editing={state.editing === operation.id}
                            onEdit={handleEditing}
                            onCloseEdition={handleCloseEditing}
                            onUpdate={handleUpdate}
                            onPastChanged={handlePastChanged}
                            onDelete={handleDelete}
                        />
                    );
                })}
            </div>
        </section>
    );
};