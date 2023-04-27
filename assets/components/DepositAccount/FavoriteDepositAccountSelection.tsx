import React, {useEffect, useState} from 'react';
import {Button} from '@components/Button';
import {Modal} from '@components/Modal/Modal';
import {Field} from '@components/Form/Field';
import {DepositAccountRadios} from '@components/DepositAccount/DepositAccountRadios';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {DepositAccount as DepositAccountEntity} from '@entities/DepositAccount';
import {findDepositAccounts} from '@api/deposit-accounts';
import {AutoSubmitForm} from '@components/Form/AutoSubmitForm';

let cachedDepositAccounts: DepositAccountEntity[] | null = null;

export const FavoriteDepositAccountSelection = () => {
    const [show, setShow] = useState<boolean>(false);
    const [{data, isLoading, isError, isDone}, fetch, setData] = useJsonFetch<DepositAccountEntity[]>(false, findDepositAccounts);

    useEffect(() => {
        if (!show) {
            return;
        }
        const fetchData = async () => {
            cachedDepositAccounts = await fetch();
        };
        if (cachedDepositAccounts) {
            setData(cachedDepositAccounts);
            return;
        }
        fetchData();
    }, [show]);

    if (!show || isLoading) {
        return <Button isLoading={isLoading} onClick={() => setShow(true)}>Compte</Button>;
    }

    if (isError) {
        return (
            <p>
                <Button onClick={() => fetch()}>Une erreur est survenue, réessayez</Button>
            </p>
        );
    }

    return (
        <>
            <Button onClick={() => setShow(true)}>Compte</Button>
            <Modal show={show} onClose={() => setShow(false)}>
                Sélection du compte en banque
                <div className="modal__body">
                    {data && (
                        <AutoSubmitForm>
                            <Field
                                wrapperClassName="grid2 fit"
                                name="favoriteDepositAccount"
                                component={DepositAccountRadios}
                                values={data}
                                defaultValue={window.eurobudget.FAVORITE_DEPOSIT_ACCOUNT_ID}
                            />
                        </AutoSubmitForm>
                    )}
                    <a className="btn-primary mt2" href="/deposit-accounts/new" data-turbolinks="false">Nouveau</a>
                </div>
            </Modal>
        </>
    );
};