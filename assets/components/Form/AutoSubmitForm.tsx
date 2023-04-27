import React, {HTMLProps} from 'react';

export const AutoSubmitForm = ({children}: HTMLProps<HTMLFormElement>) => {
    return (
        <form encType="multipart/form-data" method="post" action="/deposit-accounts/select-favorite" is="auto-submit">
            {children}
        </form>
    );
};