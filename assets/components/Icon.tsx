import React, {HTMLProps} from 'react';
import {classNames} from '@functions/dom';

type IconProps = {
    name: string;
    size?: number;
} & HTMLProps<any>;

export const Icon = ({
    name,
    size,
    className,
    ...props
}: IconProps) => {
    const href = `/sprite.svg#${name}`;
    className = classNames(`icon icon-${name}`, className);

    const style = {
        width: size ? `${size}px` : undefined,
        height: size ? `${size}px` : undefined,
    };

    return (
        <svg className={className} style={style} {...props}>
            <use xlinkHref={href}/>
        </svg>
    );
};