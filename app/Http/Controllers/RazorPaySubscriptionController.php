<?php

namespace App\Http\Controllers;

use App\Models\RazorPayPayments;
use App\Models\RazorPaySubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class RazorPaySubscriptionController extends Controller
{
    //
    public function index($userId, $planType)
    {
        $user = DB::connection('mysql2')->table('users')->where('id', $userId)->first();
        if ($user && ($planType === 'basic' or $planType === 'premium')) {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            if ($planType === 'basic') {
                $subscription = $api->subscription->create(array('plan_id' => 'plan_KkDpiehLt0Qrek', 'customer_notify' => 1, 'quantity' => 1, 'total_count' => 12, 'notes' => array('customer_id' => $userId)));
                return view('checkout', ['planType' => $planType, 'user' => $user, 'subscription_id' => $subscription['id'], 'planId' => 'plan_KkDpiehLt0Qrek']);
            } else {
                $subscription = $api->subscription->create(array('plan_id' => 'plan_KkDq1Ewnpv6V1H', 'customer_notify' => 1, 'quantity' => 1, 'total_count' => 12, 'notes' => array('customer_id' => $userId)));
                return view('checkout', ['planType' => $planType, 'user' => $user, 'subscription_id' => $subscription['id'], 'planId' => 'plan_KkDq1Ewnpv6V1H']);
            }
        } else {
            return redirect()->route('home');
        }
    }

    public function store($userId, $planType, $planId, Request $request)
    {
        $input = $request->all();

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        if (count($input) && !empty($input['razorpay_payment_id']) && !empty($input['razorpay_subscription_id']) && !empty('razorpay_signature')) {
            try {

                $api->utility->verifyPaymentSignature(array('razorpay_subscription_id' => $input['razorpay_subscription_id'], 'razorpay_payment_id' => $input['razorpay_payment_id'], 'razorpay_signature' => $input['razorpay_signature']));

                $invoice = $api->invoice->all(['subscription_id' => $input['razorpay_subscription_id']]);

//                dd($invoice);

                $allowed_data = $planType === 'basic' ? 10 : 15;

                User::where('id', $userId)->update(['plan_type' => $planType, 'allowed_data' => $allowed_data]);

                $RazorpaySubscriptionData = new RazorPaySubscription();
                $RazorpaySubscriptionData->user_id = $userId;
                $RazorpaySubscriptionData->razorpay_subscription_id = $input['razorpay_subscription_id'];
                $RazorpaySubscriptionData->razorpay_plan_id = $planId;
                $RazorpaySubscriptionData->razorpay_customer_id = $invoice['items']['0']['customer_id'];

                $RazorpaySubscriptionData->save();

                $start_date = $invoice['items']['0']['billing_start'];
                $end_date = $invoice['items']['0']['billing_end'];

                $start_date = date('d-m-Y h-i-s A', $start_date);
                $end_date = date('d-m-Y h-i-s A', $end_date);

                $RazorPayPaymentData = new RazorPayPayments();
                $RazorPayPaymentData->subscription_status = $invoice['items']['0']['status'];
                $RazorPayPaymentData->subscription_start_date = $start_date;
                $RazorPayPaymentData->subscription_end_date = $end_date;
                $RazorPayPaymentData->subscription_id = $invoice['items']['0']['subscription_id'] ?? $input['razorpay_subscription_id'];
                $RazorPayPaymentData->razorpay_invoice_url = $invoice['items']['0']['short_url'];
                $RazorPayPaymentData->razorpay_payment_id = $invoice['items']['0']['payment_id'] ?? $input['razorpay_payment_id'];
                $RazorPayPaymentData->razorpay_invoice_id = $invoice['items']['0']['id'];

                $RazorPayPaymentData->save();

                $user = DB::connection('mysql2')->table('users')->where('id', $userId)->first();

                            return redirect('http://' . $user->subdomain . '.rutviknabhoya.me' . '/new/app');
//                return redirect('http://' . $user->subdomain . '.saascrm.test' . '/new/app');

            } catch (Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back()->withErrors('subdomain', 'Payment failed for this subdomain');
            }
        }

    }
}
