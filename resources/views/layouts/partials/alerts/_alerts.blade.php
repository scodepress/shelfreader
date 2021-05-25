{{-- 
'LOST-ASSUM','CHECKEDOUT','MISSING','LOST','LOST-CLAIM','Z-MISSING','WITHDRAWN','CANCELED','Z-REMOVED','INTRANSIT','DISCARD','PALCI','SHADOW' --}}


@if(session()->has('success'))

@component('layouts.partials.alerts._alerts_component', ['type' => 'green'])

{{ session('success') }}
{{-- Success sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

@endcomponent

@endif

@if(session()->has('error'))

@if(session('error') === 'CHECKEDOUT')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ str_limit($title,15) }} is CHECKED OUT. Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

	@if(session('error') === 'ONHOLD')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ str_limit($title,15) }} is ON HOLD. Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif


@if(session('error') === 'PALCI')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ str_limit($title,15) }} is a PALCI item. Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

@if(session('error') === 'EMPTY_RESPONSE')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>This Item returned no results. Please scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

@if(session('error') === 'LOST-ASSUM')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ str_limit($title,15) }} is ASSUMED LOST. If you would like to place it on the shelf, re-scan the barcode.
If not, scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif


	@if(session('error') === 'Empty Barcode')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>The barcode field was empty.</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

	@if(session('error') == 'Bad Barcode')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>Scanning error. The barcode scanned does not seem valid.</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif



@if(session('error') === 'SHADOW')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>No location information is available for this copy of {{ str_limit($title,15) }}. It may be 
	SHADOWED, WTHDRAWN, CANCELED or LOST
	&nbsp;<a style="color:white;" href="#">(more info)</a>&nbsp;
	Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

	@endif