<?php

namespace App\Models;

use App\Services\WalletService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING_PAYMENT = 0;
    public const STATUS_PAID = 1;
    public const STATUS_PAYMENT_CONFIRMED = 2;
    public const STATUS_INVOICED = 3;
    public const STATUS_SHIPPED = 4;
    public const STATUS_PAYMENT_EXPIRED = 5;
    public const STATUS_CANCELLED_TO_WALLET = 6;

    public const STATUS_TITLES = [
        self::STATUS_PENDING_PAYMENT => 'در انتظار پرداخت',
        self::STATUS_PAID => 'پرداخت شده',
        self::STATUS_PAYMENT_CONFIRMED => 'تأیید پرداخت',
        self::STATUS_INVOICED => 'صدور فاکتور',
        self::STATUS_SHIPPED => 'ارسال بار',
        self::STATUS_PAYMENT_EXPIRED => 'منقضی شده - عدم پرداخت',
        self::STATUS_CANCELLED_TO_WALLET => 'لغو شده - بازگشت به کیف پول',
    ];

    public const STATUS_TITLE_PAYMENT_FAILED_RETRY = 'پرداخت ناموفق - در انتظار پرداخت مجدد';

    public const PAYMENT_DEADLINE_MINUTES = 15;

    protected $table = 'orders';

    protected $fillable = [
        'code',
        'customer_id',
        'customer_name',
        'status',
        'status_title',
        'payment_deadline_at',
        'copan',
        'total_price',
        'wallet_used_amount',
        'send_price',
        'send_type',
        'send_time',
        'address_id',
        'invoice',
        'tax_percent',
        'tax_amount',
        'RK_invoice_number',
    ];

    protected $casts = [
        'payment_deadline_at' => 'datetime',
        'wallet_used_amount' => 'integer',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'process_id', 'id')
            ->where('table_name', 'orders');
    }

    public static function paymentDeadlineFromNow(): Carbon
    {
        return now()->addMinutes(self::PAYMENT_DEADLINE_MINUTES);
    }

    public static function titleForStatus(int $status): string
    {
        return self::STATUS_TITLES[$status] ?? 'نامشخص';
    }

    public static function shilappAssignableStatuses(): array
    {
        return [
            self::STATUS_PAID,
            self::STATUS_PAYMENT_CONFIRMED,
            self::STATUS_INVOICED,
            self::STATUS_SHIPPED,
        ];
    }

    public function resolvedStatusTitle(): string
    {
        if (is_string($this->status_title) && $this->status_title !== '') {
            return $this->status_title;
        }

        return self::titleForStatus((int) $this->status);
    }

    public function isPendingPayment(): bool
    {
        return (int) $this->status === self::STATUS_PENDING_PAYMENT;
    }

    public function isCancelledToWallet(): bool
    {
        return (int) $this->status === self::STATUS_CANCELLED_TO_WALLET;
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array((int) $this->status, [
            self::STATUS_PAID,
            self::STATUS_PAYMENT_CONFIRMED,
        ], true);
    }

    public function isInPaidLifecycle(): bool
    {
        return in_array((int) $this->status, [
            self::STATUS_PAID,
            self::STATUS_PAYMENT_CONFIRMED,
            self::STATUS_INVOICED,
            self::STATUS_SHIPPED,
        ], true);
    }

    public function isPaymentDeadlinePassed(): bool
    {
        if (!$this->payment_deadline_at) {
            return false;
        }

        return $this->payment_deadline_at->isPast();
    }

    /**
     * اگر در انتظار پرداخت باشد و مهلت گذشته باشد، منقضی می‌کند.
     */
    public function expireIfPaymentDeadlinePassed(): bool
    {
        if (!$this->isPendingPayment() || !$this->isPaymentDeadlinePassed()) {
            return false;
        }

        DB::transaction(function () {
            $locked = static::query()->where('id', $this->id)->lockForUpdate()->first();
            if (!$locked || !$locked->isPendingPayment() || !$locked->isPaymentDeadlinePassed()) {
                return;
            }

            $locked->update([
                'status' => self::STATUS_PAYMENT_EXPIRED,
                'status_title' => self::titleForStatus(self::STATUS_PAYMENT_EXPIRED),
            ]);

            app(WalletService::class)->releasePaymentHold($locked, false);
        });

        $this->refresh();

        return $this->status !== null && (int) $this->status === self::STATUS_PAYMENT_EXPIRED;
    }

    /**
     * همه سفارش‌های در انتظار پرداخت منقضی‌شدهٔ یک مشتری را منقضی می‌کند.
     */
    public static function expirePendingPastDeadlineForCustomer(int $customerId): void
    {
        $orders = static::query()
            ->where('customer_id', $customerId)
            ->where('status', self::STATUS_PENDING_PAYMENT)
            ->whereNotNull('payment_deadline_at')
            ->where('payment_deadline_at', '<', now())
            ->get();

        foreach ($orders as $order) {
            $order->expireIfPaymentDeadlinePassed();
        }
    }
}
