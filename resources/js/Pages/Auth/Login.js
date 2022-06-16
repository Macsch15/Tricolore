import React, { useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Layouts/Layout';
import ValidationErrors from '@/Components/ValidationErrors';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: '',
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value);
    };

    const submit = (e) => {
        e.preventDefault();

        post(route('login'));
    };

    return (
        <Layout>
            <Head title="Log in" />

            <ValidationErrors errors={errors} />

            <form onSubmit={submit}>
                <div className="mb-3">
                    <label htmlFor="email" className="form-label">Email address</label>
                    <input
                        type="email"
                        onChange={onHandleChange}
                        className="form-control"
                        name="email"
                        value={data.email}
                        id="email" />
                </div>

                <div className="mb-3">
                    <label htmlFor="password" className="form-label">Password</label>
                    <input
                        type="password"
                        className="form-control"
                        name="password"
                        value={data.password}
                        onChange={onHandleChange}
                        id="password" />
                </div>

                <div className="mb-3 form-check">
                    <input
                        type="checkbox"
                        name="remember"
                        value={data.remember}
                        onChange={onHandleChange}
                        className="form-check-input"
                        id="remember" />
                    <label className="form-check-label" htmlFor="remember">Remember me</label>
                </div>

                {canResetPassword && (
                    <Link
                        href={route('password.request')}
                        className="float-end"
                    >
                        Forgot your password?
                    </Link>
                )}

                <button type="submit" className="btn btn-primary">Log in</button>
            </form>
        </Layout>
    );
}
