@component('mail::message')
# New Contact Form Submission

A new message has been submitted via the contact form on the Einspot Solutions website.

**Sender Details:**
- **Name:** {{ $submissionData->name }}
- **Email:** {{ $submissionData->email }}
@if($submissionData->phone)
- **Phone:** {{ $submissionData->phone }}
@endif
@if($submissionData->company)
- **Company:** {{ $submissionData->company }}
@endif
@if($submissionData->service)
- **Service of Interest:** {{ $submissionData->service }}
@endif

**Message:**
{{ $submissionData->message }}

**Submitted At:** {{ $submissionData->created_at->format('Y-m-d H:i:s') }}

You can view this submission in the admin panel if contact submissions are stored and displayed there.
{{-- @component('mail::button', ['url' => route('admin.contact-submissions.show', $submissionData->id)]) --}}
{{-- View Submission (Admin Panel Link - TODO: Ensure this route exists and works) --}}
{{-- @endcomponent --}}

Thanks,<br>
{{ config('app.name') }} Website
@endcomponent
