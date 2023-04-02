import React, {HTMLProps, PropsWithChildren} from 'react';
import {classNames} from '@functions/dom';
import {ButtonEnum} from '@enums/ButtonEnum';
import {Loader} from '@components/Animation/Loader';

type ButtonProps = {
    type?: ButtonEnum;
    isLoading?: boolean;
} & PropsWithChildren<any> & HTMLProps<HTMLButtonElement>;

export const Button = ({
    type = ButtonEnum.PRIMARY,
    isLoading = false,
    className,
    children,
    ...props
}: ButtonProps) => {
    className = classNames(`btn-${type}`, className);

    return (
        <button className={className} disabled={isLoading} {...props}>
            {isLoading && <Loader className="icon"/>}
            {children}
        </button>
    );
};