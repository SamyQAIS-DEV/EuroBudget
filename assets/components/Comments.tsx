import React, {useRef} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {NetworkStatus} from '@components//NetworkStatus';
import {SearchInput} from '@components/SearchInput';
import {Copy} from '@components/Copy';

// type CommentsType = {
//     target: number;
// } & PropsWithChildren;

type CommentsType = {
    userId: number;
};

export const Comments = ({userId}: CommentsType) => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<any>('https://jsonplaceholder.typicode.com/users');
    const element = useRef<HTMLDivElement>(null);

    // useEffect(() => {
    //     if (!isDone) {
    //         fetch();
    //     }
    // }, [isDone]);

    if (isLoading) {
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
                {isDone && data.toString()}
                <button className='btn-secondary' onClick={() => fetch()}>Reload</button>
            </div>
            <br/>
            <NetworkStatus />
            <br/>
            <SearchInput />
            <Copy text="Bonjour" />
            <Copy text="Hello" />
        </div>
    );
};