import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler, useEffect } from 'react';
import Swal from 'sweetalert2';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type LoginForm = {
    username: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
    success?: string;
    error?: string;
}

export default function Login({ status, canResetPassword, success, error }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        username: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        if (success) {
            Toast.fire({
                icon: 'success',
                title: success
            });
        }

        if (error) {
            Toast.fire({
                icon: 'error',
                title: error
            });
        }

        if (status) {
            Toast.fire({
                icon: 'success',
                title: status
            });
        }
    }, [success, error, status]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <AuthLayout title="ورود به حساب کاربری" description="نام کاربری و کلمه عبور خود را وارد نمایید">
            <Head title="Log in" />

            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="username">نام کاربری (شماره موبایل)</Label>
                        <Input
                            id="username"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="username"
                            value={data.username}
                            onChange={(e) => setData('username', e.target.value)}
                            placeholder="username"
                        />
                        <InputError message={errors.username} />
                    </div>

                    <div className="grid gap-2">
                        <div className="flex items-center">
                            <Label htmlFor="password">کلمه عبور</Label>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={2}
                            autoComplete="current-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Password"
                        />
                        <InputError message={errors.password} />
                    </div>


                    <div className="flex items-center space-x-3">
                        <Checkbox
                            id="remember"
                            name="remember"
                            checked={data.remember}
                            onClick={() => setData('remember', !data.remember)}
                            tabIndex={3}
                        />
                        <Label htmlFor="remember">Remember me</Label>
                    </div>

                    <div className="flex items-center space-x-3">
                        {canResetPassword && (
                            <TextLink href={route('password.request')} className="ml-auto text-sm" tabIndex={5}>
                                فراموش کردن کلمه عبور ؟
                            </TextLink>
                        )}
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing} style={{background:'#69499C'}}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                        ورود
                    </Button>
                </div>

                <div className="text-muted-foreground text-center text-sm">
                    حساب کاربری ندارید؟{' '}
                    <TextLink href={route('register')} tabIndex={5}>
                        ثبت نام
                    </TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}