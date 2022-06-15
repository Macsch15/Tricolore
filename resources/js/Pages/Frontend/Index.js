import React from 'react';
import { Head } from '@inertiajs/inertia-react';
import Layout from '@/Layouts/Layout';

export default function Index(props) {
    return (
        <Layout auth={props.auth}>
            <Head title="Index" />
        </Layout>
    );
}
