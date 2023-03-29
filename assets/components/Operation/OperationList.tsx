import React, {useCallback, useEffect, useState} from 'react';
import {Loader} from '@components/Animation/Loader';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Button} from '@components/Button';
import {Operation} from '@components/Operation/Operation';
import {Icon} from '@components/Icon';
import {Modal} from '@components/Modal/Modal';
import {Operation as OperationEntity} from '@entities/Operation';
import {addOperation, deleteOperation, findOperationsForCurrentMonth, findOperationsForMonth, updateOperation, updatePastOperation} from '@api/operations';

type OperationListProps = {
    year: string;
    month: string;
    onOperationChanged?: (operation: OperationEntity) => void;
};

type State = {
    creating: boolean;
    editing?: number;
};

export const OperationList = ({
    year,
    month,
    onOperationChanged = () => {},
}: OperationListProps) => {
    const [{data, isLoading, isError, isDone}, fetch, setData] = useJsonFetch<OperationEntity[]>(true, findOperationsForCurrentMonth);
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

    const handleCreate = async (operation: OperationEntity) => {
        const newOperation = await addOperation(operation);
        setState(s => ({...s, creating: false}));
        setData([newOperation, ...data]); // TODO Checker la date de l'opération : ne pas l'ajouter si le mois est différent et bien la placer dans le tableau si elle doit se placer entre 2 autres opérations
        onOperationChanged(operation);
    };

    const handleUpdate = async (operation: OperationEntity) => {
        const newOperation = await updateOperation(operation);
        setState(s => ({...s, editing: null}));
        setData(data.map(o => (o === operation ? newOperation : o))); // TODO Checker la date de l'opération : ne pas l'ajouter si le mois est différent et bien la placer dans le tableau si elle doit se placer entre 2 autres opérations
        onOperationChanged(operation);
    };

    const handlePastChanged = async (operation: OperationEntity) => {
        try {
            const newOperation = await updatePastOperation(operation);
            setData(data.map(o => (o === operation ? newOperation : o)));
            onOperationChanged(operation);
        } catch (e) {
            operation.past = !operation.past;
            setData(data.map(o => (o === operation ? operation : o)));
        }
    };

    const handleDelete = async (operation: OperationEntity) => {
        await deleteOperation(operation);
        setData(data.filter(o => o !== operation));
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
            <Button className="mb1" onClick={handleCreate}>
                <Icon name="edit" title="Créer une opération"/><span>Créer une opération</span>
            </Button>
            <Modal show={state.creating} onClose={handleCloseCreating}>
                Création d'une opération
                Form
                {/*<OperationForm formId="operation-form" onSubmit={handleSubmit} onSuccess={handleSuccess} operation={operation}/>*/}
            </Modal>
            <div className="operations list-group p0">
                {data.map((operation) => (
                    <Operation
                        key={operation.id}
                        operation={operation}
                        editing={state.editing === operation.id}
                        onEdit={handleEditing}
                        onCloseEdition={handleCloseEditing}
                        onUpdate={handleUpdate}
                        onPastChanged={handlePastChanged}
                        onDelete={handleDelete}
                    />
                ))}
            </div>
        </section>
    );
};