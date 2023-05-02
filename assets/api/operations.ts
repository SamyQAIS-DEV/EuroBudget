import {jsonFetch, jsonFetchOrFlash} from '@functions/api';
import {Operation} from '@entities/Operation';
import {HttpRequestMethodEnum} from '@enums/HttpEnum';

export const findOperationsForCurrentMonth = async (): Promise<Operation[]> => {
    const operations = await jsonFetch<Operation[]>('/api/operations/current-month');
    return operations.map((operation) => new Operation(operation));
}

export const findOperationsForMonth = async (year: string, month: string): Promise<Operation[]> => {
    const operations = await jsonFetch<Operation[]>(`/api/operations/${year}/${month}`);
    return operations.map((operation) => new Operation(operation));
}

export const addOperation = async (operation: Operation): Promise<Operation> => {
    const newOperation = await jsonFetch<Operation>('/api/operations', operation, HttpRequestMethodEnum.POST);
    return new Operation(newOperation);
}

export const updateOperation = async (operation: Operation): Promise<Operation> => {
    const newOperation = await jsonFetch<Operation>(`/api/operations/${operation.id}`, operation, HttpRequestMethodEnum.PUT);
    return new Operation(newOperation);
}

export const updatePastOperation = async (operation: Operation): Promise<Operation> => {
    const newOperation = await jsonFetchOrFlash<Operation>(`/api/operations/${operation.id}`, operation, HttpRequestMethodEnum.PUT);
    return new Operation(newOperation);
}

export const deleteOperation = async (operation: Operation): Promise<void> => {
    return await jsonFetch<void>(`/api/operations/${operation.id}`, {}, HttpRequestMethodEnum.DELETE);
}