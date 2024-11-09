<x-mail::message>
# Welcome to {{ config('app.name') }}, {{ $customer->name }}!

We're excited to have you join our ecommerce platform. Here, you'll find a wide range of products and exclusive deals just for you.

## Getting Started
Click the button below to visit our site and start exploring all we have to offer.

<x-mail::button :url="route('profile')">
Explore Now
</x-mail::button>

We hope you have a fantastic experience with us. If you have any questions, feel free to reach out!

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
