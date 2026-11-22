<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Paket langganan (menu dinamis). CRUD generik dari tabel `subscription`.
 */
class SubscriptionController extends BaseCrudController
{
    protected string $model = Subscription::class;
    protected string $routeName = 'admin.subscriptions';
    protected string $title = 'Paket Langganan';
    protected string $menuKey = 'subscriptions';
    protected array $fields = [
        ['name' => 'code', 'type' => 'string', 'label' => 'Kode', 'nullable' => false, 'unique' => true],
        ['name' => 'name', 'type' => 'string', 'label' => 'Nama Paket', 'nullable' => false],
        ['name' => 'description', 'type' => 'text', 'label' => 'Deskripsi', 'nullable' => true],
        ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'nullable' => false, 'default' => 'active',
            'options' => ['active' => 'Aktif', 'inactive' => 'Nonaktif']],
        ['name' => 'price_amount', 'type' => 'decimal', 'label' => 'Harga', 'nullable' => false, 'default' => 0],
        ['name' => 'currency', 'type' => 'string', 'label' => 'Mata Uang', 'nullable' => false, 'default' => 'IDR'],
        ['name' => 'billing_interval', 'type' => 'select', 'label' => 'Interval Tagihan', 'nullable' => false, 'default' => 'monthly',
            'options' => ['monthly' => 'Bulanan', 'yearly' => 'Tahunan', 'weekly' => 'Mingguan', 'daily' => 'Harian']],
        ['name' => 'trial_days', 'type' => 'integer', 'label' => 'Trial (hari)', 'nullable' => false, 'default' => 0],
        ['name' => 'seat_limit', 'type' => 'integer', 'label' => 'Batas Seat', 'nullable' => true],
        ['name' => 'sina_credit_per_patient_per_period', 'type' => 'integer', 'label' => 'Kredit SINA per pasien per periode', 'nullable' => false, 'default' => 0],
    ];

    protected function moduleOptions(): array
    {
        return Module::query()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->all();
    }

    public function create()
    {
        $this->authorizeAbility('create');

        return view('admin.subscriptions.form', [
            'record' => null,
            'modules' => $this->moduleOptions(),
            'routeName' => $this->routeName,
            'title' => $this->title,
        ]);
    }

    public function edit($id)
    {
        $this->authorizeAbility('update');
        $record = Subscription::whereKey($id)->firstOrFail();

        return view('admin.subscriptions.form', [
            'record' => $record,
            'modules' => $this->moduleOptions(),
            'routeName' => $this->routeName,
            'title' => $this->title,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');
        $data = $this->validatedData($request);
        $data['module_keys'] = $request->input('module_keys', []);
        Subscription::create($data);

        return redirect()->route($this->routeName . '.index')->with('success', __('messages.created'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAbility('update');
        $subscription = Subscription::whereKey($id)->firstOrFail();
        $data = $this->validatedData($request, $subscription);
        $data['module_keys'] = $request->input('module_keys', []);
        $subscription->update($data);

        return redirect()->route($this->routeName . '.index')->with('success', __('messages.updated'));
    }

    protected function validatedData(Request $request, ?Subscription $subscription = null): array
    {
        $moduleKeys = array_keys($this->moduleOptions());
        $uniqueCode = Rule::unique('subscription', 'code');
        if ($subscription) {
            $uniqueCode->ignore($subscription->id, 'id');
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:255', $uniqueCode],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'price_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_interval' => ['required', Rule::in(['monthly', 'yearly', 'weekly', 'daily'])],
            'trial_days' => ['required', 'integer', 'min:0'],
            'seat_limit' => ['nullable', 'integer', 'min:1'],
            'sina_credit_per_patient_per_period' => ['required', 'integer', 'min:0'],
            'module_keys' => ['nullable', 'array'],
            'module_keys.*' => ['string', Rule::in($moduleKeys)],
        ]);
    }
}
