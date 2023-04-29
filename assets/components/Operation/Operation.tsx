import React, {useState} from 'react';
import {classNames} from '@functions/dom';
import {Icon} from '@components/Icon';
import {Switch} from '@components/Switch';
import {Modal} from '@components/Modal/Modal';
import {Button} from '@components/Button';
import {ButtonEnum} from '@enums/ButtonEnum';
import {Loader} from '@components/Animation/Loader';
import {Operation as OperationEntity} from '@entities/Operation';
import {OperationForm} from '@components/Operation/OperationForm';
import {formatDate} from '@functions/date';
import {formatCurrency} from '@functions/number';
import {playNotification} from '@functions/notification';
import {TypeEnum} from '@enums/TypeEnum';

type OperationProps = {
    operation: OperationEntity;
    labels: string[];
    editing: boolean;
    onEdit: (operation: OperationEntity) => void;
    onCloseEdition: () => void;
    onUpdate: (operation: OperationEntity) => void;
    onPastChanged: (operation: OperationEntity) => void;
    onDelete: (operation: OperationEntity) => void;
};

export const Operation = ({
    operation,
    labels,
    editing = false,
    onEdit,
    onCloseEdition,
    onUpdate,
    onPastChanged,
    onDelete,
}: OperationProps) => {
    const amountClassName = classNames('operation__amount', operation.type === TypeEnum.DEBIT ? 'type__debit' : 'type__credit');
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [isDeleting, setIsDeleting] = useState<boolean>(false);

    const handleEdit = () => {
        onEdit(operation);
    };

    const handleClose = () => {
        onCloseEdition();
    };

    const handleUpdate = async (newOperation: OperationEntity) => {
        setIsLoading(true);
        await onUpdate(newOperation);
        setIsLoading(false);
    };

    const handlePastChanged = async () => {
        playNotification();
        operation.past = !operation.past;
        setIsLoading(true);
        await onPastChanged(operation);
        setIsLoading(false);
    };

    const handleDelete = async () => {
        if (confirm('Voulez vous vraiment supprimer cette opération ?')) {
            setIsDeleting(true);
            await onDelete(operation);
        }
    };

    return (
        <>
            <div className="operation grid fit">
                <div className="operation__title">
                    <span>{operation.label}</span>
                    { operation.category && (
                        <div className="operation__category">
                            <span className="pill small" style={{ color: operation.category?.color }}>{ operation.category?.name }</span>
                        </div>
                    ) }
                </div>
                <div className={amountClassName}>{operation.type}{formatCurrency(operation.amount)}</div>
                <div className="operation__past">
                    <Switch id={String(operation.id)} checked={operation.past} label="Opération passée" onChange={handlePastChanged}/>
                </div>
                <div className="operation__date"><small>{formatDate(operation.date)}</small></div>
                <div className="operation__actions actions">
                    <Button isLoading={isLoading} onClick={handleEdit} title="Éditer l'opération">
                        <Icon name="edit"/>
                    </Button>
                    <Button isLoading={isDeleting} type={ButtonEnum.ERROR} onClick={handleDelete} title="Supprimer l'opération">
                        <Icon name="trash"/>
                    </Button>
                </div>
            </div>
            <Modal show={editing} onClose={handleClose}>
                Édition d'une opération
                <OperationForm operation={operation} labels={labels} onSubmit={handleUpdate}/>
            </Modal>
        </>
    );
};