<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Admin\BaseCrudController;
use Modules\ModuleRegistry\Models\Module;
use Modules\Subscription\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Paket langganan (menu dinamis). CRUD generik dari tabel `subscription`.
 */
class SubscriptionController extends BaseCrudController
{
    protected string $model = Subscription::class;
    protected string $routeName = 'admin.subscriptions';
    protected string $menuKey = 'subscriptions';

    protected function title(): string
    {
        return __('subscription.title');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'code', 'type' => 'string', 'label' => __('common.code'), 'nullable' => false, 'unique' => true],
            ['name' => 'name', 'type' => 'string', 'label' => __('subscription.name'), 'nullable' => false],
            ['name' => 'description', 'type' => 'text', 'label' => __('common.description'), 'nullable' => true],
            ['name' => 'status', 'type' => 'select', 'label' => __('common.status'), 'nullable' => false, 'default' => 'active',
                'options' => ['active' => __('common.active'), 'inactive' => __('common.inactive')]],
            ['name' => 'price_amount', 'type' => 'decimal', 'label' => __('common.price'), 'nullable' => false, 'default' => 0],
            ['name' => 'currency', 'type' => 'string', 'label' => __('common.currency'), 'nullable' => false, 'default' => 'IDR'],
            ['name' => 'billing_interval', 'type' => 'select', 'label' => __('subscription.billing_interval'), 'nullable' => false, 'default' => 'monthly',
                'options' => [
                    'monthly' => __('subscription.interval_monthly'),
                    'yearly'  => __('subscription.interval_yearly'),
                    'weekly'  => __('subscription.interval_weekly'),
                    'daily'   => __('subscription.interval_daily'),
                ]],
            ['name' => 'trial_days', 'type' => 'integer', 'label' => __('subscription.trial_days'), 'nullable' => false, 'default' => 0, 'hide_in_table' => true],
            ['name' => 'seat_limit', 'type' => 'integer', 'label' => __('subscription.seat_limit'), 'nullable' => true, 'hide_in_table' => true],
            ['name' => 'sina_credit_per_patient_per_period', 'type' => 'integer', 'label' => __('subscription.sina_credit'), 'nullable' => false, 'default' => 0, 'hide_in_table' => true],
        ];
    }

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

        return view('subscription::subscriptions.form', [
            'record' => null,
            'modules' => $this->moduleOptions(),
            'routeName' => $this->routeName,
            'title' => $this->title(),
        ]);
    }

    public function edit($id)
    {
        $this->authorizeAbility('update');
        $record = Subscription::whereKey($id)->firstOrFail();

        return view('subscription::subscriptions.form', [
            'record' => $record,
            'modules' => $this->moduleOptions(),
            'routeName' => $this->routeName,
            'title' => $this->title(),
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
