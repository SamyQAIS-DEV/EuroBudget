import React, {FormEvent, HTMLProps, useMemo, useRef, useState} from 'react';

type FieldProps = {
    type?: string,
    name: string,
    value?: any;
    label?: string,
    onInput?: (event: FormEvent) => void;
    error?: string[];
    component?: () => void;
    wrapperClassName?: string;
} & HTMLProps<HTMLInputElement>;

export const Field = ({
    type = 'text',
    name,
    value,
    label,
    onInput = () => {},
    error,
    component = null,
    wrapperClassName = '',
    className,
    ...props
}: FieldProps) => {
    const ref = useRef<HTMLDivElement>(null);

    if (error) {
        className += ' is-invalid';
    }

    const attr = {
        id: name,
        name,
        className,
        onInput: onInput,
        type,
        ...(value === undefined ? {} : {value}),
        ...props,
    };

    const FieldComponent = useMemo(() => {
        if (component) {
            return component;
        }
        switch (type) {
            // case 'textarea':
            //     return FieldTextarea
            // case 'editor':
            //     return FieldEditor
            // case 'checkbox':
            //     return FieldCheckbox
            default:
                return FieldInput;
        }
    }, [component, type]);

    return (
        <div className={`form-group ${wrapperClassName}`} ref={ref}>
            <input {...attr} />
            {label && <label title={label} htmlFor={name}/>}
            {error && <div className="invalid-feedback">{error}</div>}
        </div>
    );
};

const FieldInput = ({...props}: FieldProps) => {
    return <input {...props} />;
};