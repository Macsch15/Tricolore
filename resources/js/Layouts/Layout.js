import React from 'react';
import Header from '@/Components/Header';

export default function Layout({ auth, header, children }) {
    return (
        <>
            <Header auth={auth} />
            <main>{children}</main>
        </>
    );
}
