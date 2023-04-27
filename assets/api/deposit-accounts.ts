import {jsonFetchOrFlash} from '@functions/api';
import {DepositAccountResource} from '@entities/DepositAccountResource';
import {DepositAccount} from '@entities/DepositAccount';

export async function findDepositAccounts(): Promise<DepositAccount[]> {
    const depositAccounts = await jsonFetchOrFlash<DepositAccount[]>('/api/deposit-accounts');
    return depositAccounts.map((depositAccount) => new DepositAccount(depositAccount));
}

export async function findDepositAccountResource(): Promise<DepositAccountResource> {
    const depositAccountResource = await jsonFetchOrFlash<DepositAccountResource>('/api/deposit-accounts/favorite-recap');
    return new DepositAccountResource(depositAccountResource);
}