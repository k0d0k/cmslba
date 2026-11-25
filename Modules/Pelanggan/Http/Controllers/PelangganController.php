<?php

namespace Modules\Pelanggan\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\Pelanggan\Mail\PelangganPasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Contoh modul CRUD Pelanggan (data multi cabang pada database mitra).
 * Pelanggan direlasikan ke akun login pada tabel users (database pusat).
 */
class PelangganController extends BaseCrudController
{
    protected string $model = \Modules\Pelanggan\Models\Pelanggan::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.pelanggan';
    protected string $menuKey = 'pelanggan';
    protected bool $hasCabang = true;

    protected function title(): string
    {
        return __('common.customer');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'picture', 'type' => 'image', 'label' => __('common.picture'), 'nullable' => true],
            ['name' => 'name', 'type' => 'string', 'label' => __('common.name'), 'nullable' => false],
            ['name' => 'email', 'type' => 'email', 'label' => __('common.email'), 'nullable' => true],
            ['name' => 'phone', 'type' => 'string', 'label' => __('common.phone'), 'nullable' => true],
            ['name' => 'user_id', 'type' => 'relation', 'label' => __('common.user_account'), 'nullable' => true, 'relation' => [
                'model' => \Modules\User\Models\User::class,
                'label' => 'name_with_role',
                'with'  => ['roles'],
                // Akun mitra hanya boleh memilih akun user milik mitranya sendiri;
                // user pusat (principal) tetap melihat seluruh akun.
                'query' => function ($query) {
                    $user = auth()->user();
                    if ($user && ! $user->isPrincipal() && $user->mitra_id) {
                        $query->where('mitra_id', $user->mitra_id);
                    }

                    return $query;
                },
            ]],
            ['name' => 'cabang_id', 'type' => 'relation', 'label' => __('common.branch'), 'nullable' => true, 'relation' => ['model' => \Modules\Cabang\Models\Cabang::class, 'label' => 'name']],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => false],
        ];
    }

    /**
     * Reset password akun pelanggan: buat password acak, simpan, lalu kirim ke email.
     */
    public function resetPassword($slug)
    {
        $this->authorizeAbility('update');

        $record = ($this->model)::where('slug', $slug)->firstOrFail();
        $user = $record->user;

        if (! $user) {
            return back()->with('error', __('messages.pelanggan_no_user'));
        }
        if (blank($user->email)) {
            return back()->with('error', __('messages.pelanggan_no_email'));
        }

        $password = Str::password(10);
        $user->forceFill(['password' => $password])->save();

        Mail::to($user->email)->send(new PelangganPasswordReset($user, $password));

        return back()->with('success', __('messages.pelanggan_password_reset', ['email' => $user->email]));
    }

    protected function rowActions($row): string
    {
        if (! $this->can('update') || ! $row->user_id) {
            return '';
        }

        return '<form method="POST" action="' . route($this->routeName . '.reset-password', $row->slug) . '" class="js-confirm" data-confirm="' . e(__('messages.confirm_reset_password')) . '">'
            . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
            . '<button type="submit" class="btn btn-sm btn-secondary">' . __('common.reset_password') . '</button>'
            . '</form>';
    }
}
