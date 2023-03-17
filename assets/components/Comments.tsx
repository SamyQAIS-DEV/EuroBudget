import React, {PropsWithChildren, PropsWithRef, useRef} from 'react';

type CommentsType = {
    target: number;
} & PropsWithChildren;

export const Comments = ({ target }: CommentsType) => {
    const element = useRef<HTMLDivElement>(null);

    return (
        <div className='comment-area' ref={element}>
            <div className='comments__title'>
                Comments Title
            </div>
        </div>
    );
};