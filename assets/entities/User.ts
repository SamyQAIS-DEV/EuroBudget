interface UserInterface {
    id?: number;
    email?: string;
    lastname?: string;
    firstname?: string;
    avatarName?: string;
}

export class User implements UserInterface {
    public id: number;
    public email: string;
    public lastname: string;
    public firstname: string;
    public avatarName: string;

    constructor(options: UserInterface = {}) {
        Object.assign(this, options);
    }

    get fullName(): string {
        return this.firstname + ' ' + this.lastname;
    }

    get avatarFileName(): string {
        if (this.avatarName === null) {
            return '/images/default.png';
        }
        return '/uploads/' + this.avatarName;
    }
}