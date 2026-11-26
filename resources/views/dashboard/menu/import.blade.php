@extends('layouts.panel')
@section('title', 'Import menu')

@section('content')
<div class="page-head">
    <div>
        <a href="{{ route('dashboard.menu') }}" class="crumb"><x-icon name="left" class="sm"/>Menu</a>
        <h1>Import from a spreadsheet</h1>
        <p>Have your menu in Excel or Google Sheets? Bring it all in at once.</p>
    </div>
</div>

<div class="grid-2" style="align-items:start">
    <div class="card card-pad">
        <ol class="steps">
            <li class="step"><span class="dot">1</span><div class="grow"><h4>Download the template</h4><p class="small muted">It shows the columns and a few example rows.</p></div>
                <a href="{{ route('dashboard.import.template') }}" class="btn btn-sm"><x-icon name="download" class="sm"/>Template</a></li>
            <li class="step"><span class="dot">2</span><div class="grow"><h4>Fill it in</h4><p class="small muted">One row per item. Open it in Excel or Google Sheets, then save as <b>CSV</b>.</p></div></li>
            <li class="step"><span class="dot">3</span><div class="grow"><h4>Upload it here</h4><p class="small muted">New categories are created for you. Existing items stay as they are.</p></div></li>
        </ol>
        <form method="post" enctype="multipart/form-data" class="mt-4">
            @csrf
            <input class="input" type="file" name="file" accept=".csv,text/csv" required style="padding-top:10px">
            <button class="btn btn-primary btn-block mt-3"><x-icon name="upload"/>Import items</button>
        </form>
    </div>
    <div class="card card-pad">
        <div class="section-title">Columns</div>
        <table class="tbl mt-2">
            <tr><td><code>category</code></td><td class="muted">Required. E.g. Coffee</td></tr>
            <tr><td><code>name_en</code></td><td class="muted">Item name in English</td></tr>
            @if (multilang())<tr><td><code>name_am</code></td><td class="muted">Item name in Amharic</td></tr>@endif
            <tr><td><code>description_en</code></td><td class="muted">Optional</td></tr>
            <tr><td><code>price</code></td><td class="muted">Required. Numbers only</td></tr>
            <tr><td><code>tags</code></td><td class="muted">{{ implode(', ', array_keys(config('xmenu.tags'))) }}. Separate with <code>|</code></td></tr>
            <tr><td><code>sizes</code></td><td class="muted"><code>Small:60|Large:80</code></td></tr>
        </table>
    </div>
</div>
@endsection
