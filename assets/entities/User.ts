interface UserInterface {
    id?: number;
    email?: string;
    lastname?: string;
    firstname?: string;
}

export class User implements UserInterface {
    public id: number;
    public email: string;
    public lastname: string;
    public firstname: string;

    constructor(options: UserInterface = {}) {
        Object.assign(this, options);
    }

    get fullName(): string {
        return this.firstname + ' ' + this.lastname;
    }
}