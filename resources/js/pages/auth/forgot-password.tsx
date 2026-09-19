import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { FormEventHandler, useEffect } from 'react';
import Swal from 'sweetalert2';

interface ForgetProps {
    status?: string;
    success?: string;
    error?: string;
}

export default function ForgotPassword({ status, success, error }: ForgetProps) {
    const { data, setData, post, processing, errors } = useForm<Required<{ mobile: string }>>({
        mobile: '',
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
        post(route('password.mobile'));
    };

    return (
        <AuthLayout
            title="بازیابی رمز عبور"
            description="شماره موبایل خود را وارد کنید تا کد بازیابی برای شما ارسال شود"
        >
            <Head title="فراموشی رمز عبور" />

            <form className="flex flex-col gap-5" onSubmit={submit}>
                <div className="grid gap-2">
                    <Label htmlFor="mobile">شماره موبایل</Label>
                    <Input
                        id="mobile"
                        type="tel"
                        name="mobile"
                        autoComplete="off"
                        value={data.mobile}
                        autoFocus
                        onChange={(e) => setData('mobile', e.target.value)}
                        placeholder="09123456789"
                    />
                    <InputError message={errors.mobile} />
                </div>

                <Button type="submit" className="auth-submit mt-1 w-full" disabled={processing}>
                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                    ارسال کد بازیابی
                </Button>

                <p className="pt-1 text-center text-sm text-slate-500">
                    به یاد آوردید؟{' '}
                    <TextLink href={route('login')} className="auth-link">
                        بازگشت به ورود
                    </TextLink>
                </p>
            </form>
        </AuthLayout>
    );
}
