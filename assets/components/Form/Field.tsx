import React, {FormEvent, HTMLProps, useMemo, useRef} from 'react';

type FieldProps = {
    type?: string;
    name: string;
    value?: any;
    label?: string;
    onInput?: (event: FormEvent) => void;
    error?: string[];
    component?: ({ ...props }: AttrProps) => JSX.Element | JSX.Element[];
    wrapperClassName?: string;
    values?: any;
} & HTMLProps<HTMLInputElement>;

export type AttrProps = {
    id: string|number;
    name: string;
    className?: string;
    onInput?: () => void;
    autoComplete?: string;
    type?: string;
    value?: any;
    values?: any;
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
    values,
    ...props
}: FieldProps) => {
    const ref = useRef<HTMLDivElement>(null);

    if (values && !component) {
        console.error('Field Component : values can\'t be passed if the component isn\'t');
    }

    if (error) {
        className += ' is-invalid';
    }

    const attr = {
        id: name,
        name,
        className,
        onInput: onInput,
        autoComplete: 'off',
        type,
        values,
        ...(value === undefined ? {} : {value}),
        ...props,
    } as AttrProps;

    const FieldComponent = useMemo(() => {
        if (component) {
            return component;
        }
        switch (type) {
            // case 'checkbox':
            //     return FieldCheckbox
            case 'number':
                return FieldNumber;
            default:
                return FieldText;
        }
    }, [component, type]);

    return (
        <div className={`form-group ${wrapperClassName}`} ref={ref}>
            {/* @ts-expect-error */}
            <FieldComponent {...attr} />
            {label && <label title={label} htmlFor={name}/>}
            {error && <div className="invalid-feedback">{error}</div>}
        </div>
    );
};

const FieldText = ({...props}: FieldProps) => {
    return <input {...props} />;
};

const FieldNumber = ({...props}: FieldProps) => {
    return <input type="number" inputMode="decimal" min="0" step="0.01" {...props} />;
};