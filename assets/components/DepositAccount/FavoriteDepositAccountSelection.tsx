import React, {useEffect, useState} from 'react';
import {Button} from '@components/Button';
import {Modal} from '@components/Modal/Modal';
import {Field} from '@components/Form/Field';
import {DepositAccountRadios} from '@components/DepositAccount/DepositAccountRadios';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {DepositAccount as DepositAccountEntity} from '@entities/DepositAccount';
import {findDepositAccounts} from '@api/deposit-accounts';
import {AutoSubmitForm} from '@components/Form/AutoSubmitForm';
import {Icon} from '@components/Icon';

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

    const handleOpen = () => {
        setShow(true);
    };

    const handleClose = () => {
        setShow(false);
    };

    if (isError) {
        return (
            <p>
                <Button onClick={() => fetch()}>Une erreur est survenue, réessayez</Button>
            </p>
        );
    }

    return (
        <>
            <Button isLoading={isLoading} onClick={handleOpen}><Icon name='briefcase'/></Button>
            <Modal title="Sélection du compte en banque" icon='briefcase' show={show} onClose={handleClose}>
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