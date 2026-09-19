import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type RegisterForm = {
    name: string;
    username: string;
    password: string;
    password_confirmation: string;
};

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<RegisterForm>>({
        name: '',
        username: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <AuthLayout title="ایجاد حساب کاربری" description="فقط چند دقیقه تا عضویت در فروشگاه شیل ایران">
            <Head title="ثبت نام">
                <meta name="robots" content="noindex, follow" />
                <link rel="canonical" href="https://www.shil.ir/register" />
            </Head>

            <form className="flex flex-col gap-5" onSubmit={submit}>
                <div className="grid gap-2">
                    <Label htmlFor="name">نام و نام خانوادگی</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autoFocus
                        tabIndex={1}
                        autoComplete="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        disabled={processing}
                        placeholder="نام و نام خانوادگی"
                    />
                    <InputError message={errors.name} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="username">شماره موبایل</Label>
                    <Input
                        id="username"
                        type="text"
                        required
                        tabIndex={2}
                        autoComplete="username"
                        value={data.username}
                        onChange={(e) => setData('username', e.target.value)}
                        disabled={processing}
                        placeholder="09120000000"
                    />
                    <InputError message={errors.username} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="password">کلمه عبور</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabIndex={3}
                        autoComplete="new-password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        disabled={processing}
                        placeholder="حداقل ۸ کاراکتر"
                    />
                    <InputError message={errors.password} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="password_confirmation">تکرار کلمه عبور</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        tabIndex={4}
                        autoComplete="new-password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        disabled={processing}
                        placeholder="تکرار کلمه عبور"
                    />
                    <InputError message={errors.password_confirmation} />
                </div>

                <Button type="submit" className="auth-submit mt-1 w-full" tabIndex={5} disabled={processing}>
                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                    ثبت‌نام و ادامه
                </Button>

                <p className="pt-1 text-center text-sm text-slate-500">
                    قبلاً ثبت‌نام کرده‌اید؟{' '}
                    <TextLink href={route('login')} className="auth-link" tabIndex={6}>
                        ورود
                    </TextLink>
                </p>
            </form>
        </AuthLayout>
    );
}
