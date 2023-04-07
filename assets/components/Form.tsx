import React, {createContext, FormEvent, HTMLProps, useContext, useState} from 'react';
import {Field} from '@components/Field';
import {PremiumButtonElement} from '@elements/PremiumButtonElement';
import {Button, SubmitButton} from '@components/Button';
import {ApiError} from '@functions/api';
import {addFlash} from '@elements/AlertElement';
import {AlertEnum} from '@enums/AlertEnum';
import {HttpResponseCodeEnum} from '@enums/HttpEnum';

type ContextState = {
    emptyError: (name: string) => void;
} & State;

export const FormContext = createContext<ContextState>({
    isLoading: false,
    errors: {},
    emptyError: () => {},
});

type State = {
    isLoading: boolean;
    errors: object;
};

type FormProps = {
    onSubmit: () => void
} & HTMLProps<HTMLFormElement>;

export const Form = ({
    onSubmit,
    children,
    ...props
}: FormProps) => {
    const [{isLoading, errors}, setState] = useState<State>({
        isLoading: false,
        errors: {},
    });

    const emptyError = (name: string) => {
        if (!errors[name]) return null;
        const newErrors = {...errors};
        delete newErrors[name];
        setState(s => ({...s, errors: newErrors}));
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();
        setState({isLoading: true, errors: {}});
        try {
            await onSubmit();
        } catch (error) {
            if (error instanceof ApiError) {
                if (error.status === HttpResponseCodeEnum.HTTP_SERVER_ERROR) {
                    addFlash(error.name, AlertEnum.ERROR);
                } else {
                    setState(s => ({...s, errors: error.violations}));
                }
            } else {
                addFlash(error.detail, AlertEnum.ERROR);
            }
        }
        setState(s => ({...s, isLoading: false}));
    };

    return (
        <FormContext.Provider value={{isLoading, errors, emptyError}}>
            <form onSubmit={handleSubmit} {...props}>
                {children}
            </form>
        </FormContext.Provider>
    );
};

type FormFieldProps = {
    type?: string;
    name: string;
} & HTMLProps<HTMLInputElement>;

export const FormField = ({
    type = 'text',
    name,
    children,
    ...props
}: FormFieldProps) => {
    const {errors, emptyError, isLoading} = useContext(FormContext);
    const error = errors[name] || null;

    return (
        <Field type={type} name={name} error={error} onInput={() => emptyError(name)} {...props}>
            {children}
        </Field>
    );
};

export const FormButton = ({children, ...props}: HTMLProps<PremiumButtonElement>) => {
    const {isLoading} = useContext(FormContext);

    return (
        <Button isLoading={isLoading} {...props}>
            {children}
        </Button>
    );
};

export const FormSubmitButton = ({children, ...props}: HTMLProps<PremiumButtonElement>) => {
    const {isLoading} = useContext(FormContext);

    return (
        <SubmitButton isLoading={isLoading} {...props}>
            {children}
        </SubmitButton>
    );
};