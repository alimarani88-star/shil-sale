import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div dir="rtl" className="auth-page relative flex min-h-svh overflow-hidden bg-[#f3f4f6]">
            <link
                rel="stylesheet"
                href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap"
            />

            {/* Atmosphere */}
            <div
                aria-hidden
                className="pointer-events-none absolute inset-0 opacity-70"
                style={{
                    background:
                        'radial-gradient(ellipse 80% 50% at 100% -10%, rgba(105,73,156,0.18), transparent 55%), radial-gradient(ellipse 60% 40% at 0% 100%, rgba(30,41,59,0.08), transparent 50%)',
                }}
            />
            <div
                aria-hidden
                className="pointer-events-none absolute inset-0 opacity-[0.035]"
                style={{
                    backgroundImage:
                        'linear-gradient(#1e293b 1px, transparent 1px), linear-gradient(90deg, #1e293b 1px, transparent 1px)',
                    backgroundSize: '48px 48px',
                }}
            />

            <div className="relative z-10 mx-auto grid w-full max-w-6xl flex-1 items-stretch lg:grid-cols-2">
                {/* Brand panel */}
                <aside className="relative hidden flex-col justify-between overflow-hidden bg-[#1c1a22] px-10 py-12 text-white lg:flex">
                    <div
                        aria-hidden
                        className="absolute inset-0"
                        style={{
                            background:
                                'linear-gradient(160deg, #2a2435 0%, #1c1a22 45%, #15131a 100%)',
                        }}
                    />
                    <div
                        aria-hidden
                        className="absolute -left-20 top-1/4 h-72 w-72 rounded-full bg-[#69499C]/opacity-25 blur-3xl"
                    />
                    <div
                        aria-hidden
                        className="absolute -right-16 bottom-0 h-64 w-64 rounded-full bg-[#ac4fea]/opacity-15 blur-3xl"
                    />

                    <div className="relative z-10">
                        <Link href={route('home')} className="inline-flex items-center gap-3 transition hover:opacity-90">
                            <div className="auth-logo-box flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-white/10 ring-1 ring-white/15 backdrop-blur">
                                <AppLogoIcon />
                            </div>
                            <div className="text-right">
                                <div className="text-xl font-bold tracking-wide" style={{ fontFamily: 'Vazirmatn, sans-serif' }}>
                                    شیل ایران
                                </div>
                                <div className="text-xs text-white/60" style={{ fontFamily: 'Vazirmatn, sans-serif' }}>
                                    SHIL IRAN
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div className="relative z-10 max-w-md space-y-4" style={{ fontFamily: 'Vazirmatn, sans-serif' }}>
                        <p className="text-sm font-medium text-[#c4b5e0]">فروشگاه تجهیزات برق صنعتی</p>
                        <h2 className="text-3xl leading-relaxed font-bold">
                            خرید مطمئن،
                            <br />
                            پشتیبانی تخصصی
                        </h2>
                        <p className="text-sm leading-7 text-white/65">
                            به حساب کاربری خود وارد شوید و سفارش محصولات برق صنعتی را به‌سادگی ثبت و پیگیری کنید.
                        </p>
                    </div>

                    <div className="relative z-10 text-xs text-white/40" style={{ fontFamily: 'Vazirmatn, sans-serif' }}>
                        www.shil.ir
                    </div>
                </aside>

                {/* Form panel */}
                <main className="flex items-center justify-center px-4 py-10 sm:px-8">
                    <div className="auth-card w-full max-w-[420px] animate-[authFadeIn_0.45s_ease-out]">
                        <div className="mb-8 flex flex-col items-center gap-4 lg:items-start">
                            <Link href={route('home')} className="lg:hidden">
                                <div className="auth-logo-box flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
                                    <AppLogoIcon />
                                </div>
                            </Link>

                            <div className="w-full space-y-2 text-center lg:text-right" style={{ fontFamily: 'Vazirmatn, sans-serif' }}>
                                <h1 className="text-2xl font-bold text-slate-900">{title}</h1>
                                {description ? <p className="text-sm leading-6 text-slate-500">{description}</p> : null}
                            </div>
                        </div>

                        <div
                            className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-[0_20px_50px_-24px_rgba(15,23,42,0.35)] sm:p-8"
                            style={{ fontFamily: 'Vazirmatn, sans-serif' }}
                        >
                            {children}
                        </div>
                    </div>
                </main>
            </div>

            <style>{`
                @keyframes authFadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .auth-page input[data-slot="input"] {
                    height: 2.75rem;
                    border-radius: 0.75rem;
                    border-color: #e2e8f0;
                    background: #f8fafc;
                    color: #0f172a;
                    -webkit-text-fill-color: #0f172a;
                    caret-color: #0f172a;
                    padding-inline: 0.9rem;
                    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
                }
                .auth-page input[data-slot="input"]::placeholder {
                    color: #94a3b8;
                    -webkit-text-fill-color: #94a3b8;
                    opacity: 1;
                }
                .auth-page input[data-slot="input"]:focus-visible {
                    border-color: #69499C;
                    background: #fff;
                    color: #0f172a;
                    -webkit-text-fill-color: #0f172a;
                    box-shadow: 0 0 0 3px rgba(105, 73, 156, 0.18);
                }
                .auth-page label {
                    font-size: 0.875rem;
                    font-weight: 500;
                    color: #334155;
                }
                .auth-submit {
                    height: 2.85rem;
                    border-radius: 0.85rem;
                    background: #69499C !important;
                    color: #fff !important;
                    font-weight: 600;
                    transition: transform .15s ease, background .2s ease, box-shadow .2s ease;
                    box-shadow: 0 10px 24px -12px rgba(105, 73, 156, 0.8);
                }
                .auth-submit:hover:not(:disabled) {
                    background: #5a3d88 !important;
                    transform: translateY(-1px);
                }
                .auth-submit:active:not(:disabled) {
                    transform: translateY(0);
                }
                .auth-link {
                    color: #69499C;
                    font-weight: 600;
                    text-decoration: none;
                }
                .auth-link:hover {
                    color: #553a7d;
                    text-decoration: underline;
                }
                .auth-logo-box img {
                    width: 100% !important;
                    height: 100% !important;
                    max-width: none !important;
                    object-fit: contain;
                }
            `}</style>
        </div>
    );
}
