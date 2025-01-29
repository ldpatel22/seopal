<?php

namespace App\Http\Controllers;

class SubscriptionController extends Controller
{
    /**
     * Renders the "Subscription" page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function subscriptionPage()
    {
        return view('subscription.index');
    }
}
