<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

class Tenant extends SpatieTenant
{
    /**
     * Get the subscription for this tenant.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Check if the tenant has a specific feature enabled based on their current plan.
     *
     * @param string $featureCode
     * @return bool
     */
    public function hasFeature(string $featureCode): bool
    {
        $subscription = $this->subscription()->with('plan.features')->first();

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->features->contains('code', $featureCode);
    }

    /**
     * Get the limit value for a specific feature, if any.
     * Returns null if unlimited or not set.
     *
     * @param string $featureCode
     * @return int|null
     */
    public function getFeatureLimit(string $featureCode): ?int
    {
        $subscription = $this->subscription()->with('plan.features')->first();

        if (!$subscription || !$subscription->plan) {
            return 0; // Or whatever default limit you prefer for no subscription
        }

        $feature = $subscription->plan->features->firstWhere('code', $featureCode);

        if ($feature && isset($feature->pivot->limit_value)) {
            return (int) $feature->pivot->limit_value;
        }

        return null; // Means unlimited
    }
}
