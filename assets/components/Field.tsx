import React, {FormEvent, HTMLProps, useMemo, useRef} from 'react';

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
        autoComplete: 'off',
        type,
        ...(value === undefined ? {} : {value}),
        ...props,
    };

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