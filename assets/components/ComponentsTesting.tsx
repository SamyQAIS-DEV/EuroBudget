import React, {useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {NetworkStatus} from '@components//NetworkStatus';
import {SearchInput} from '@components/SearchInput';
import {Copy} from '@components/Copy';
import {Animated} from '@components/Animation/Animated';
import {ConfirmModal} from '@components/ConfirmModal';
import {AlertModal} from '@components/AlertModal';
import {Modal} from '@components/Modal';

type ComponentsTestingProps = {
    userId: number;
};

export const ComponentsTesting = ({userId}: ComponentsTestingProps) => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<any>('https://jsonplaceholder.typicode.com/users');
    const [showDiv, setDiv] = useState<boolean>(false);
    const [showModal, setShowModal] = useState<boolean>(false);
    const [showAlertModal, setShowAlertModal] = useState<boolean>(false);
    const [showConfirmModal, setShowConfirmModal] = useState<boolean>(false);

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
            <hr/>
            <h2>Modal</h2>
            <button className="btn btn-secondary" onClick={() => setShowModal(!showModal)}>Simulate Modal</button>
            <Modal show={showModal} onClose={() => setShowModal(false)}>
                <img src="/images/auth_background.png" alt="Auth Background"/>
                <h2>Inscription</h2>
                <form action="">
                    <div className="form-group">
                        <input type="email" id="login_form_email" name="login_form[email]" required={true} className="form-control" autoComplete="off" />
                        <label htmlFor="login_form_email" title="Adresse mail" className="required"></label>
                    </div>
                    <button type="submit" className='btn btn-primary'>Submit</button>
                </form>
            </Modal>
            <hr/>
            <h2>Alert Modal</h2>
            <button className="btn btn-secondary" onClick={() => setShowAlertModal(!showAlertModal)}>Simulate Alert Modal</button>
            <AlertModal show={showAlertModal} onClose={() => setShowAlertModal(false)}>
                <p>Alert Modal</p>
            </AlertModal>
            <hr/>
            <h2>Confirm Modal</h2>
            <button className="btn btn-secondary" onClick={() => setShowConfirmModal(!showConfirmModal)}>Simulate Confirm Modal</button>
            <ConfirmModal show={showConfirmModal} onCancel={() => setShowConfirmModal(false)} onConfirm={() => setShowConfirmModal(false)}>
                <p>Confirm Modal</p>
            </ConfirmModal>
        </div>
    );
};