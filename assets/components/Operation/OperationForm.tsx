import React, {FormEvent, useEffect, useState} from 'react';
import {Operation} from '@entities/Operation';
import {Form, FormField, FormSubmitButton} from '@components/Form/Form';
import {formatDate} from '@functions/date';
import {Switch} from '@components/Switch';
import {isPremium} from '@functions/auth';
import {Category} from '@entities/Category';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {findCategories} from '@api/categories';
import {Button} from '@components/Button';
import {Loader} from '@components/Animation/Loader';

let cachedCategories: Category[] | null = null;

type OperationFormProps = {
    operation?: Operation,
    onSubmit: (operation: Operation) => void
};

export const OperationForm = ({
    operation,
    onSubmit,
}: OperationFormProps) => {
    const [item, setItem] = useState<Operation>(operation ?? new Operation());
    const [{data, isLoading, isError, isDone}, fetch, setData] = useJsonFetch<Category[]>(false, findCategories);

    useEffect(() => {
        const fetchData = async () => {
            cachedCategories = await fetch();
        };
        if (cachedCategories) {
            setData(cachedCategories);
            return;
        }
        fetchData();
    }, []);

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

    const handleCategory = (e: FormEvent<HTMLSelectElement>) => {
        const target = e.target as HTMLSelectElement;
        const category = data.find((c) => target.value === String(c.id)) ?? null;
        setItem(i => ({...i, category: category}));
    };

    const handleDate = (e: FormEvent<HTMLInputElement>) => {
        const target = e.target as HTMLInputElement;
        if (target.value) {
            setItem(i => ({...i, date: new Date(target.value)}));
        }
    };

    const handlePast = (e: FormEvent<HTMLInputElement>) => {
        setItem(i => ({...i, past: !item.past}));
    };

    if (isLoading) {
        return <Loader className="ma mt3"/>;
    }

    if (isError) {
        return (
            <p>
                <Button onClick={() => fetch()}>Une erreur est survenue, réessayez</Button>
            </p>
        );
    }

    return (
        <Form onSubmit={handleSubmit}>
            <div className="grid2">
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
                {/* TODO: FormField */}
                {isPremium() && (
                    <select name="category" onChange={handleCategory} value={item.category?.id}>
                        <option value="">Placeholder</option>
                        {data && data.map((category) => (
                            <option key={category.id} value={category.id}>{category.name}</option>
                        ))}
                    </select>
                    // <FormField
                    //     type="select"
                    //     name="category"
                    //     label="Catégorie"
                    //     onChange={handleCategory}
                    //     defaultValue={item.category}
                    // />
                )}
                <FormField
                    type="date"
                    name="date"
                    label="Date"
                    onChange={handleDate}
                    required
                    defaultValue={formatDate(item.date, '-', true)}
                />
                <Switch id="operation-past" checked={item.past} onChange={handlePast} label="Opération passée"/>
            </div>
            <FormSubmitButton>Ajouter</FormSubmitButton>
        </Form>
    );
};