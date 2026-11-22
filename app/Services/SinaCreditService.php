<?php

namespace App\Services;

use App\Models\AiWallet;
use App\Models\Mitra;

/**
 * Pengelolaan kredit SINA AI per mitra.
 *
 * Saldo awal = Kredit SINA per pasien per periode (dari paket langganan aktif)
 * dikalikan jumlah kursi pada langganan tersebut. Mitra dapat menambah kredit
 * (top-up) sebelum masa berlangganannya berakhir.
 */
class SinaCreditService
{
    /** Ambil/buat dompet kredit mitra, sekaligus memberikan saldo awal paket bila belum pernah. */
    public function walletFor(Mitra $mitra): AiWallet
    {
        $wallet = AiWallet::firstOrCreate(['mitra_id' => $mitra->id]);

        if (! $wallet->last_granted_at) {
            $this->grantInitialCredits($wallet, $mitra);
        }

        return $wallet->refresh();
    }

    /** Berikan saldo awal dari paket langganan aktif (jika ada). */
    protected function grantInitialCredits(AiWallet $wallet, Mitra $mitra): void
    {
        $subscription = $mitra->subscriptions()
            ->whereIn('status', ['active', 'trialing'])
            ->latest('created_at')
            ->first();

        $package = $subscription?->subscription;
        if (! $package) {
            return;
        }

        $perPatient = (int) ($package->sina_credit_per_patient_per_period ?? 0);
        $seats = max(1, (int) $subscription->seats);
        $credits = $perPatient * $seats;

        $wallet->update([
            'balance_credits'   => $wallet->balance_credits + $credits,
            'granted_credits'   => $wallet->granted_credits + $credits,
            'period_start'      => $subscription->current_period_start,
            'period_end'        => $subscription->current_period_end,
            'last_granted_at'   => now(),
        ]);
    }

    /** Tambah kredit (top-up) pada dompet mitra. */
    public function topUp(AiWallet $wallet, int $credits): AiWallet
    {
        $wallet->update([
            'balance_credits'    => $wallet->balance_credits + $credits,
            'topped_up_credits'  => $wallet->topped_up_credits + $credits,
            'last_topped_up_at'  => now(),
        ]);

        return $wallet->refresh();
    }
}
