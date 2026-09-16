<h1>New project brief</h1>

<p><strong>Name:</strong> {{ $brief['name'] }}</p>
<p><strong>Email:</strong> {{ $brief['email'] }}</p>
<p><strong>Phone:</strong> {{ $brief['phone'] ?? 'Not provided' }}</p>
<p><strong>Organization:</strong> {{ $brief['organization'] ?? 'Not provided' }}</p>
<p><strong>Service:</strong> {{ str($brief['service'])->replace('-', ' ')->title() }}</p>
<p><strong>Budget:</strong> {{ $brief['budget'] ?? 'Not provided' }}</p>
<p><strong>Timeline:</strong> {{ $brief['timeline'] ?? 'Not provided' }}</p>

<h2>What they want built</h2>
<p>{{ $brief['project_goal'] }}</p>

@if (! empty($brief['features']))
    <h2>Features and details</h2>
    <p>{{ $brief['features'] }}</p>
@endif

@if (! empty($brief['references']))
    <h2>References</h2>
    <p>{{ $brief['references'] }}</p>
@endif

@if (count($images) > 0)
    <p><strong>Attached images:</strong> {{ count($images) }}</p>
@endif
