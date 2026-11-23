<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentProvider;
use Illuminate\Http\Request;

/**
 * Metode Pembayaran (payment gateway) — Data Master.
 * Menyimpan konfigurasi gateway (Midtrans, Doku, iPaymu, PayPal) untuk
 * integrasi tagihan mitra. Kredensial dienkripsi pada level model.
 */
class PaymentProviderController extends BaseCrudController
{
    protected string $model = PaymentProvider::class;
    protected string $view = 'admin.crud';
    protected string $routeName = 'admin.payment_providers';
    protected string $title = 'Metode Pembayaran';
    protected string $menuKey = 'payment_providers';

    protected function fields(): array
    {
        return [
            ['name' => 'provider', 'type' => 'select', 'label' => 'Penyedia', 'nullable' => false, 'unique' => true,
                'options' => PaymentProvider::providerOptions()],
            ['name' => 'label', 'type' => 'string', 'label' => 'Label', 'nullable' => false],
            ['name' => 'mode', 'type' => 'select', 'label' => 'Mode', 'nullable' => false, 'default' => 'sandbox',
                'options' => ['sandbox' => 'Sandbox', 'live' => 'Live']],
            ['name' => 'is_active', 'type' => 'boolean', 'label' => 'Aktif', 'nullable' => true, 'default' => true],

            ['type' => 'section', 'title' => 'Kredensial',
                'text' => 'Kredensial dienkripsi saat disimpan dan tidak pernah ditampilkan lagi. Biarkan rahasia kosong untuk mempertahankan nilai tersimpan.'],

            ['name' => 'server_key', 'type' => 'secret', 'label' => 'Server Key', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'client_key', 'type' => 'secret', 'label' => 'Client Key', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'api_key', 'type' => 'secret', 'label' => 'API Key', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'virtual_account', 'type' => 'secret', 'label' => 'Virtual Account', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'client_id', 'type' => 'secret', 'label' => 'Client ID', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'client_secret', 'type' => 'secret', 'label' => 'Client Secret', 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'webhook_id', 'type' => 'secret', 'label' => 'Webhook ID', 'nullable' => true, 'hide_in_table' => true],

            ['name' => 'currency', 'type' => 'select', 'label' => 'Mata Uang', 'nullable' => true,
                'options' => ['IDR' => 'IDR', 'MYR' => 'MYR'],
                'hint' => 'Biarkan kosong untuk mengizinkan mata uang bawaan platform.'],
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
                $f['hint'] = 'Kunci penyedia tidak dapat diubah setelah gateway dibuat.';
            }
        }
        unset($f);

        return view($this->view . '.form', [
            'fields'    => $fields,
            'routeName' => $this->routeName,
            'title'     => $this->title,
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
