import {jsonFetch} from '@functions/api';
import {User} from '@entities/User';

export const searchUsers = async (search: string): Promise<User[]> => {
    const users = await jsonFetch<User[]>(`/api/users?q=${search}`);
    return users.map((user) => new User(user));
}