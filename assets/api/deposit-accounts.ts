import {jsonFetch} from '@functions/api';
import {DepositAccountResource} from '@entities/DepositAccountResource';
import {DepositAccount} from '@entities/DepositAccount';

export const findDepositAccounts = async (): Promise<DepositAccount[]> => {
    const depositAccounts = await jsonFetch<DepositAccount[]>('/api/deposit-accounts');
    return depositAccounts.map((depositAccount) => new DepositAccount(depositAccount));
}

export const findDepositAccountResource = async (): Promise<DepositAccountResource> => {
    const depositAccountResource = await jsonFetch<DepositAccountResource>('/api/deposit-accounts/favorite-recap');
    return new DepositAccountResource(depositAccountResource);
}