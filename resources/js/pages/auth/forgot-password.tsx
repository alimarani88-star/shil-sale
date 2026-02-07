// Components
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
        post(route('password.mobile'));
    };

    return (
        <AuthLayout title="فراموشی رمز عبور" description="برای دریافت کد بازیابی رمز عبور، موبایل خود را وارد کنید">
            <Head title="فراموشی رمز عبور" />

            <div className="space-y-6">
                <form onSubmit={submit}>
                    <div className="grid gap-2">
                        <Label htmlFor="mobile">شماره موبایل</Label>
                        <Input
                            id="mobile"
                            type="mobile"
                            name="mobile"
                            autoComplete="off"
                            value={data.mobile}
                            autoFocus
                            onChange={(e) => setData('mobile', e.target.value)}
                            placeholder="09123456789"
                        />
                        <InputError message={errors.mobile} />
                    </div>
                    <div className="my-6 flex items-center justify-start">
                        <Button className="w-full custom-primary" disabled={processing} style={{background:'#6a1b9a'}}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            ارسال کد بازیابی رمز عبور
                        </Button>
                    </div>
                </form>
                <div className="text-muted-foreground space-x-1 text-center text-sm">
                    <span>یا بازگشت به </span>
                    <TextLink href={route('login')}>ورود</TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}