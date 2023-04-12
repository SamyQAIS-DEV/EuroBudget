import React, {FormEvent, useState} from 'react';
import {Operation} from '@entities/Operation';
import {Form, FormButton, FormField, FormSubmitButton} from '@components/Form/Form';
import {formatDate} from '@functions/date';
import {Switch} from '@components/Switch';

type OperationFormProps = {
    operation?: Operation,
    onSubmit: (operation: Operation) => void
};

export const OperationForm = ({
    operation,
    onSubmit,
}: OperationFormProps) => {
    const [item, setItem] = useState<Operation>(operation ?? new Operation());

    const handleSubmit = async () => {
        await onSubmit(item);
    };

    const handleLabel = (e: FormEvent<HTMLInputElement>) => {
        const target = e.target as HTMLInputElement;
        setItem(i => ({...i, label: String(target.value)}));
    };

    const handleAmount = (e: FormEvent<HTMLInputElement>) => {
        const target = e.target as HTMLInputElement;
        setItem(i => ({...i, amount: Number(target.value)}));
    };

    const handleType = (e: FormEvent<HTMLInputElement>) => {
        const target = e.target as HTMLInputElement;
        setItem(i => ({...i, type: String(target.value)}));
    };

    const handlePast = (e: FormEvent<HTMLInputElement>) => {
        setItem(i => ({...i, past: !item.past}));
    };

    const handleDate = (e: FormEvent<HTMLInputElement>) => {
        const target = e.target as HTMLInputElement;
        if (target.value) {
            setItem(i => ({...i, date: new Date(target.value)}));
        }
    };

    return (
        <Form onSubmit={handleSubmit}>
            <div className='grid2'>
                <FormField
                    type="text"
                    name="label"
                    label="Libellé"
                    onChange={handleLabel}
                    required
                    defaultValue={item.label}
                />
                <FormField
                    type="number"
                    name="amount"
                    label="Montant"
                    onChange={handleAmount}
                    required
                    defaultValue={item.amount}
                />
                <FormField
                    type="text"
                    name="type"
                    label="Type"
                    onChange={handleType}
                    required
                    defaultValue={item.type}
                />
                <FormField
                    type="date"
                    name="date"
                    label="Date"
                    onChange={handleDate}
                    required
                    defaultValue={formatDate(item.date, '-', true)}
                />
                <Switch id='operation-past' checked={item.past} onChange={handlePast} label='Opération passée' />
            </div>
            <FormSubmitButton>Ajouter</FormSubmitButton>
        </Form>
    );
};