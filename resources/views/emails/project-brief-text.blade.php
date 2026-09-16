New project brief

Name: {{ $brief['name'] }}
Email: {{ $brief['email'] }}
Phone: {{ $brief['phone'] ?? 'Not provided' }}
Organization: {{ $brief['organization'] ?? 'Not provided' }}
Service: {{ str($brief['service'])->replace('-', ' ')->title() }}
Budget: {{ $brief['budget'] ?? 'Not provided' }}
Timeline: {{ $brief['timeline'] ?? 'Not provided' }}

What they want built:
{{ $brief['project_goal'] }}

@if (! empty($brief['features']))
Features and details:
{{ $brief['features'] }}
@endif

@if (! empty($brief['references']))
References:
{{ $brief['references'] }}
@endif

Attached images: {{ count($images) }}
