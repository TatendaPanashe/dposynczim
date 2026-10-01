@php
    $seoTitle = $title ?? 'Data Protection Zimbabwe · DPOSync Zim';
    $seoDescription = $description ?? 'DPOSync Zim helps Zimbabwean data protection officers, system integrators, and organisations manage POTRAZ-ready privacy compliance workflows.';
    $seoCanonical = $canonical ?? url()->current();
    $seoImage = $image ?? asset('images/kodomo-hero-ops.png');
    $seoType = $type ?? 'website';
    $seoKeywords = $keywords ?? 'data protection Zimbabwe, DPO Zimbabwe, privacy compliance Zimbabwe, POTRAZ compliance, data protection officer, DP1 registration, DP2 appointment, ROPA, privacy policy generator';
    $seoJsonLd = $jsonLd ?? null;
@endphp

<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<meta name="robots" content="index, follow, max-image-preview:large">

<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="DPOSync Zim">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

@if ($seoJsonLd)
    <script type="application/ld+json">@json($seoJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
@endif
