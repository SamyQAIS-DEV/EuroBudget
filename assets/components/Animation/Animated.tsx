import React, {CSSProperties, HTMLProps, PropsWithChildren, Ref, useEffect, useState} from 'react';

type AnimatedProps = {
    show: boolean;
    animationName: string;
    style?: CSSProperties;
    forwardedRef?: Ref<HTMLDivElement>
} & PropsWithChildren & HTMLProps<HTMLDivElement>;

export const Animated = ({
    show,
    animationName,
    style = {},
    forwardedRef = null,
    className,
    children,
    ...props
}: AnimatedProps) => {
    const [shouldRender, setRender] = useState<boolean>(show);
    style = {
        animation: `${show ? `${animationName}In` : `${animationName}Out`} .3s both`,
        ...style,
    };

    const onAnimationEnd = (e): void => {
        if (!show && e.animationName === `${animationName}Out`) setRender(false);
    };

    useEffect(() => {
        if (show) setRender(true);
    }, [show]);

    return (
        shouldRender && (
            <div
                style={style}
                onAnimationEnd={onAnimationEnd}
                ref={forwardedRef}
                {...props}
            >
                {children}
            </div>
        )
    );
};