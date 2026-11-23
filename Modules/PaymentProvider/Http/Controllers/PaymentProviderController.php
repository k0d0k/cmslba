<?php

namespace Modules\PaymentProvider\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\PaymentProvider\Models\PaymentProvider;
use Illuminate\Http\Request;

/**
 * Metode Pembayaran (payment gateway) — Data Master.
 * Menyimpan konfigurasi gateway (Midtrans, Doku, iPaymu, PayPal) untuk
 * integrasi tagihan mitra. Kredensial dienkripsi pada level model.
 */
class PaymentProviderController extends BaseCrudController
{
    protected string $model = \Modules\PaymentProvider\Models\PaymentProvider::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.payment_providers';
    protected string $menuKey = 'payment_providers';

    protected function title(): string
    {
        return __('payment.title');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'provider', 'type' => 'select', 'label' => __('common.provider'), 'nullable' => false, 'unique' => true,
                'options' => PaymentProvider::providerOptions()],
            ['name' => 'label', 'type' => 'string', 'label' => __('common.label'), 'nullable' => false],
            ['name' => 'mode', 'type' => 'select', 'label' => __('common.mode'), 'nullable' => false, 'default' => 'sandbox',
                'options' => ['sandbox' => __('payment.mode_sandbox'), 'live' => __('payment.mode_live')]],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => __('common.active'), 'nullable' => true, 'default' => true],

            ['type' => 'section', 'title' => __('payment.credentials'),
                'text' => __('payment.credentials_text')],

            ['name' => 'server_key', 'type' => 'secret', 'label' => __('common.server_key'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'client_key', 'type' => 'secret', 'label' => __('common.client_key'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'api_key', 'type' => 'secret', 'label' => __('common.api_key'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'virtual_account', 'type' => 'secret', 'label' => __('common.virtual_account'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'client_id', 'type' => 'secret', 'label' => __('common.client_id'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'client_secret', 'type' => 'secret', 'label' => __('common.client_secret'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'webhook_id', 'type' => 'secret', 'label' => __('common.webhook_id'), 'nullable' => true, 'hide_in_table' => true],

            ['name' => 'currency', 'type' => 'select', 'label' => __('common.currency'), 'nullable' => true,
                'options' => ['IDR' => 'IDR', 'MYR' => 'MYR'],
                'hint' => __('payment.currency_hint')],
        ];
    }

    protected function rules(?int $ignoreId = null): array
    {
        $rules = parent::rules($ignoreId);

        // Kredensial hanya divalidasi sebagai string (bebas panjang).
        foreach (PaymentProvider::SECRET_COLUMNS as $col) {
            $rules[$col] = 'nullable|string';
        }

        return $rules;
    }

    protected function values(Request $request): array
    {
        $data = parent::values($request);

        // Kredensial kosong tidak diubah (pertahankan nilai tersimpan saat edit).
        foreach (PaymentProvider::SECRET_COLUMNS as $col) {
            if (array_key_exists($col, $data) && blank($data[$col])) {
                unset($data[$col]);
            }
        }

        return $data;
    }

    public function edit($slug)
    {
        $this->authorizeAbility('update');
        $record = PaymentProvider::where('slug', $slug)->firstOrFail();

        // Kunci penyedia tidak dapat diubah setelah gateway dibuat.
        $fields = $this->fields();
        foreach ($fields as &$f) {
            if (($f['name'] ?? null) === 'provider') {
                $f['disabled'] = true;
                $f['hint'] = __('payment.provider_locked');
            }
        }
        unset($f);

        return view($this->view . '.form', [
            'fields'    => $fields,
            'routeName' => $this->routeName,
            'title'     => $this->title(),
            'record'    => $record,
        ]);
    }

    public function update(Request $request, $slug)
    {
        $this->authorizeAbility('update');
        $record = PaymentProvider::where('slug', $slug)->firstOrFail();

        $request->validate($this->rules($record->id));

        $data = $this->values($request);
        unset($data['provider']); // kunci penyedia tidak dapat diubah.

        $record->update($data);

        return redirect()->route($this->routeName . '.index')
            ->with('success', __('messages.updated'));
    }
}
