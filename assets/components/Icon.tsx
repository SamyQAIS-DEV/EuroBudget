import React, {HTMLProps, PropsWithChildren} from 'react';
import {classNames} from '@functions/dom';

type IconProps = {
    name: string;
    size?: number;
} & PropsWithChildren & HTMLProps<any>;

export const Icon = ({
    name,
    size,
    className,
    ...props
}: IconProps) => {
    const href = `/sprite.svg#${name}`;
    className = classNames(`icon icon-${name}`, className);

    return (
        <svg className={className} width={size} height={size} {...props}>
            <use xlinkHref={href}/>
        </svg>
    );
};