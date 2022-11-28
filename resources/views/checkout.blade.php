<x-check-domain-layout>
    <div class="dark:bg-gray-800 py-10">
        <div class="flex-wrap justify-between mx-auto max-w-screen-xl">
            <div class="bg-gray-400 px-10 py-10 rounded-xl w-3/5">
                <div class="flex justify-between mx-auto">
                    <p class="text-white font-semibold">Plan:</p>
                    <p class="text-white">{{ $planType === 'basic' ? 'Basic' : 'Premium' }}
                        Plan</p>
                </div>
                <div class="flex justify-between mx-auto">
                    <p class="text-white font-semibold">Price:</p>
                    <p class="text-white">{{ $planType === 'basic' ? '2000' : '3500' }}
                        Rs</p>
                </div>
                <div class="flex justify-between mx-auto">
                    <p class="text-white font-semibold">Duration:</p>
                    <p class="text-white">1 Month</p>
                </div>
                <div class="flex justify-between mx-auto">
                    <p class="text-white font-semibold">Allowed Entries:</p>
                    <p class="text-white">{{ $planType === 'basic' ? '10' : '15' }}
                        Entries</p>
                </div>
                <form action="{{ route('checkout.store', ['userId' => $user->id, 'planType' => $planType, 'planId' => $planId]) }}" method="post">
                    @csrf
                    @php
                        $price = $planType === 'basic' ? 200000 : 350000;
                    @endphp
                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="{{ env('RAZORPAY_KEY') }}"
                            data-amount="{{ $price }}"
                            data-buttontext="Pay Now"
                            data-description="Saas CRM"
                            data-currency="INR"
                            data-image="https://flowbite.com/docs/images/logo.svg"
                            data-prefill.name="{{ $user->name }}"
                            data-prefill.email="{{ $user->email }}"
                            data-subscription_id="{{ $subscription_id }}"
                    >
                    </script>
                </form>
            </div>
        </div>
    </div>
</x-check-domain-layout>
