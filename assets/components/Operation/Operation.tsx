import React from 'react';
import {classNames} from '@functions/dom';
import {Icon} from '@components/Icon';
import {OperationEntity} from '@components/Operation/OperationList';
import {Switch} from '@components/Switch';

export const Operation = ({operation}: { operation: OperationEntity }) => {
    const amountClassName = classNames('operation__amount amount', operation.type === '-' ? 'type__debit' : 'type__credit');

    const handleEdit = () => {
        console.log('handleEdit');
    };

    const handleDelete = () => {
        console.log('handleDelete');
    };

    const handlePastChanged = () => {
        console.log('handlePastChanged');
    };

    return (
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
                <Switch id={String(operation.id)} defaultChecked={operation.past} label="Opération passée" onChange={handlePastChanged}/>
            </div>
            {/*<div className="operation__date"><small>{formatDate(createdAt)}</small></div>*/}
            <div className="operation__date"><small>{operation.date}</small></div>
            <div className="operation__actions">
                <Icon name="edit" onClick={handleEdit}>Éditer l'opération</Icon>
                {/*<ConfirmModal show={showOperationModal} title="Édition d'une opération" closable={true} submit={true} formId="operation-form" onCancel={() => setShowOperationModal(false)}*/}
                {/*onConfirm={handleConfirm}>*/}
                {/*<OperationForm formId="operation-form" onSubmit={handleSubmit} onSuccess={handleSuccess} item={operation}/>*/}
                {/*</ConfirmModal>*/}
                <Icon name="trash" onClick={handleDelete}/>
            </div>
        </div>
    );
};