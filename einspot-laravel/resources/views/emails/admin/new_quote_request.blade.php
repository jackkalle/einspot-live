@component('mail::message')
# New Quote Request Received

A new quote request has been submitted on the Einspot Solutions website.

**Details:**
- **Name:** {{ $requestData->name }}
- **Email:** {{ $requestData->email }}
@if($requestData->phone)
- **Phone:** {{ $requestData->phone }}
@endif
@if($requestData->company)
- **Company:** {{ $requestData->company }}
@endif
@if($requestData->service_of_interest)
- **Service of Interest:** {{ $requestData->service_of_interest }}
@endif
@if($requestData->product_service_name)
- **Product/Service (from prefill):** {{ $requestData->product_service_name }}
@endif
- **Project Description:**
{{ $requestData->project_description }}

@if($requestData->estimated_budget)
- **Estimated Budget:** {{ $requestData->estimated_budget }}
@endif
@if($requestData->timeline)
- **Timeline:** {{ $requestData->timeline }}
@endif

**Submitted At:** {{ $requestData->created_at->format('Y-m-d H:i:s') }}

You can view this quote request in the admin panel.
{{-- @component('mail::button', ['url' => route('admin.quote-requests.show', $requestData->id)]) --}}
{{-- View Quote Request (Admin Panel Link - TODO: Ensure this route exists and works) --}}
{{-- @endcomponent --}}

Thanks,<br>
{{ config('app.name') }} Website
@endcomponent
