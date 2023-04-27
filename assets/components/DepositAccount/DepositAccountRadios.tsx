import React, {useCallback, useState} from 'react';
import {AttrProps} from '@components/Form/Field';
import {DepositAccount as DepositAccountEntity} from '@entities/DepositAccount';
import {DepositAccount} from '@components/DepositAccount/DepositAccount';
import {isCurrentMonth} from '@functions/date';
import {addFlash} from '@elements/AlertElement';
import {classNames} from '@functions/dom';
import {Button} from '@components/Button';

type Props = {
    values: DepositAccountEntity[];
} & AttrProps;

type State = {
    editing?: number;
};

export function DepositAccountRadios({values, ...props}: Props) {

    return (
        <>
            {values.map((depositAccount) => (
                <div className='deposit-account-radio' key={depositAccount.id}>
                    <label htmlFor={String(depositAccount.id)}>
                        <DepositAccount
                            key={depositAccount.id}
                            depositAccount={depositAccount}
                            isSelected={depositAccount.id === props.defaultValue}
                        />
                    </label>
                    <input
                        type="radio"
                        id={String(depositAccount.id)}
                        defaultChecked={depositAccount.id === props.defaultValue ? true : undefined}
                        name={props.name}
                        value={depositAccount.id}
                    />
                </div>
            ))}
            <Button className='text-center'>
                <a href="/deposit-accounts/new">Nouveau</a>
            </Button>
        </>
    )
}
