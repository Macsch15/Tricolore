import React from 'react';
import Header from '@/Components/Header';

export default function Layout({ auth, children }) {
    return (
        <>
            <Header auth={auth} />

            <div className={'container'}>
                {children}
            </div>
        </>
    );
}
