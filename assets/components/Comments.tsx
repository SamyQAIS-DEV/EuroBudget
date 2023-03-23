import React, {useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {NetworkStatus} from '@components//NetworkStatus';
import {SearchInput} from '@components/SearchInput';
import {Copy} from '@components/Copy';
import {Animated} from '@components/Animation/Animated';

type CommentsProps = {
    userId: number;
};

export const Comments = ({userId}: CommentsProps) => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<any>('https://jsonplaceholder.typicode.com/users');
    const [showDiv, setDiv] = useState<boolean>(false);

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
        <div className="comment-area">
            Votre ID : {userId}
            <div className="comments__title">
                {isDone && data.toString()}
                <button className="btn-secondary" onClick={() => fetch()}>Reload</button>
            </div>
            <br/>
            <NetworkStatus/>
            <br/>
            <SearchInput/>
            <Copy text="Bonjour"/>
            <Copy text="Hello"/>
            <button className="btn btn-secondary" onClick={() => setDiv(prev => !prev)}>Toggle Animated div render</button>
            <Animated show={showDiv} animationName="slide">
                <div>JE SUIS LA DIV Animated</div>
            </Animated>
        </div>
    );
};