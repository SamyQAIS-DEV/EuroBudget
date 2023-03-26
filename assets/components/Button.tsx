import React, {HTMLProps, PropsWithChildren} from 'react';
import {classNames} from '@functions/dom';
import {ButtonEnum} from '@enums/ButtonEnum';
import {Loader} from '@components/Animation/Loader';

type ButtonProps = {
    type?: ButtonEnum;
    loading?: boolean;
} & PropsWithChildren<any> & HTMLProps<HTMLButtonElement>;

export const Button = ({
    type = ButtonEnum.PRIMARY,
    loading = false,
    className,
    children,
    ...props
}: ButtonProps) => {
    className = classNames(`btn-${type}`, className);

    return (
        <button className={className} disabled={loading} {...props}>
            {loading && <Loader className="icon"/>}
            {children}
        </button>
    );
};