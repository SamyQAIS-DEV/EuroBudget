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

type OperationProps = {
    operation: OperationEntity;
    editing: boolean;
    onEdit: (operation: OperationEntity) => void;
    onCloseEdition: () => void;
    onUpdate: (operation: OperationEntity) => void;
    onPastChanged: (operation: OperationEntity) => void;
    onDelete: (operation: OperationEntity) => void;
};

export const Operation = ({
    operation,
    editing = false,
    onEdit,
    onCloseEdition,
    onUpdate,
    onPastChanged,
    onDelete,
}: OperationProps) => {
    const amountClassName = classNames('operation__amount amount', operation.type === '-' ? 'type__debit' : 'type__credit');
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
                    {/*{ category && (*/}
                    {/*    <div className="operation__category">*/}
                    {/*        <span className="pill small" style={{ color: category?.color }}>{ category?.name }</span>*/}
                    {/*    </div>*/}
                    {/*) }*/}
                </div>
                <div className={amountClassName}>{operation.type}{operation.amount}</div>
                {/*<div className={amountClassName}>{operation.type}{currencyFormatter.format(operation.amount)}</div>*/}
                <div className="operation__past">
                    <Switch id={String(operation.id)} checked={operation.past} label="Opération passée" onChange={handlePastChanged}/>
                </div>
                {/*<div className="operation__date"><small>{formatDate(createdAt)}</small></div>*/}
                <div className="operation__date"><small>{formatDate(operation.date)}</small></div>
                <div className="operation__actions actions">
                    <Button onClick={handleEdit} title="Éditer l'opération">
                        {isLoading ? <Loader/> : <Icon name="edit"/>}
                    </Button>
                    <Button type={ButtonEnum.ERROR} onClick={handleDelete} title="Supprimer l'opération">
                        {isDeleting ? <Loader/> : <Icon name="trash"/>}
                    </Button>
                </div>
            </div>
            <Modal show={editing} onClose={handleClose}>
                Édition d'une opération
                <OperationForm operation={operation} onSubmit={handleUpdate} />
            </Modal>
        </>
    );
};