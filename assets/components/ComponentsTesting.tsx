import React, {useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {NetworkStatus} from '@components//NetworkStatus';
import {SearchInput} from '@components/SearchInput';
import {Copy} from '@components/Copy';
import {Animated} from '@components/Animation/Animated';

type ComponentsTestingProps = {
    userId: number;
};

export const ComponentsTesting = ({userId}: ComponentsTestingProps) => {
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
        <div className="components-testing">
            <h2>Votre ID : {userId}</h2>
            <hr/>
            <h2>useJsonFetch</h2>
            {isDone && data.toString()}
            <button className="btn-secondary" onClick={() => fetch()}>Reload</button>
            <hr/>
            <h2>useNetworkStatus</h2>
            <NetworkStatus/>
            <hr/>
            <h2>useDebounce</h2>
            <SearchInput/>
            <hr/>
            <h2>useCopyToClipBoard</h2>
            <Copy text="Bonjour"/>
            <Copy text="Hello"/>
            <hr/>
            <h2>Animated Component</h2>
            <button className="btn btn-secondary" onClick={() => setDiv(prev => !prev)}>Toggle Animated div render</button>
            <Animated show={showDiv} animationName="slide">
                <div>JE SUIS LA DIV Animated</div>
            </Animated>
        </div>
    );
};