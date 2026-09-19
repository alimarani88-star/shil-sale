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
            },
        });

        if (success) {
            Toast.fire({ icon: 'success', title: success });
        }
        if (error) {
            Toast.fire({ icon: 'error', title: error });
        }
        if (status) {
            Toast.fire({ icon: 'success', title: status });
        }
    }, [success, error, status]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <AuthLayout title="ورود به حساب کاربری" description="برای ادامه، شماره موبایل و کلمه عبور خود را وارد کنید">
            <Head title="ورود">
                <meta name="robots" content="noindex, follow" />
                <link rel="canonical" href="https://www.shil.ir/login" />
            </Head>

            <form className="flex flex-col gap-5" onSubmit={submit}>
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
                        placeholder="09120000000"
                    />
                    <InputError message={errors.username} />
                </div>

                <div className="grid gap-2">
                    <div className="flex items-center justify-between gap-3">
                        <Label htmlFor="password">کلمه عبور</Label>
                        {canResetPassword && (
                            <TextLink href={route('password.request')} className="auth-link text-xs" tabIndex={5}>
                                فراموش کرده‌اید؟
                            </TextLink>
                        )}
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabIndex={2}
                        autoComplete="current-password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        placeholder="کلمه عبور"
                    />
                    <InputError message={errors.password} />
                </div>

                <div className="flex items-center gap-2">
                    <Checkbox
                        id="remember"
                        name="remember"
                        checked={data.remember}
                        onClick={() => setData('remember', !data.remember)}
                        tabIndex={3}
                    />
                    <Label htmlFor="remember" className="cursor-pointer font-normal text-slate-600">
                        مرا به خاطر بسپار
                    </Label>
                </div>

                <Button type="submit" className="auth-submit mt-1 w-full" tabIndex={4} disabled={processing}>
                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                    ورود به حساب
                </Button>

                <p className="pt-1 text-center text-sm text-slate-500">
                    حساب کاربری ندارید؟{' '}
                    <TextLink href={route('register')} className="auth-link" tabIndex={6}>
                        ثبت‌نام
                    </TextLink>
                </p>
            </form>
        </AuthLayout>
    );
}
