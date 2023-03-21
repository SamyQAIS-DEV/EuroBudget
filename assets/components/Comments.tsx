import React, {useEffect, useRef} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';

type CommentsProps = {
    userId: number;
};

export const Comments = ({ userId }: CommentsProps) => {
    const {data, isLoading, isError, isDone, fetch} = useJsonFetch('https://jsonplaceholder.typicode.com/users');
    const element = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!isDone) {
            fetch();
        }
    }, [isDone]);

    if (isLoading || !isDone) {
        return (
            <>Loading...</>
        );
    }

    if (isError) {
        return (
            <>An error occured.</>
        );
    }

    return (
        <div className='comment-area' ref={element}>
            Votre ID : {userId}
            <div className='comments__title'>
                {data.toString()}
                <button className='btn-secondary' onClick={() => fetch()}>Reload</button>
            </div>
        </div>
    );
};