export const cookie = (name: string, value?: string, expiresInDays: number = 7): string | null => {
    // On veut lire le cookie
    if (value === undefined) {
        const escape = (string): string => {
            return string.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1');
        }

        const match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));

        return match ? match[1] : null;
    }

    // On veut écrire le cookie
    if (value === null) {
        value = '';
        expiresInDays = -365;
    }
    if (expiresInDays) {
        const now = new Date();
        now.setTime(now.getTime() + (expiresInDays * 60 * 60 * 24 * 1000));
        value += '; expires=' + now.toUTCString();
    }
    value += '; path=/';
    document.cookie = name + '=' + value;
};