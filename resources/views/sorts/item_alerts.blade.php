
@if($alert === 'CHECKEDOUT')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ \Illuminate\Support\Str::limit($title,15) }} is CHECKED OUT. Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

@if($alert === 'CALLNUMBERERROR')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>Callnumber "{{ \Illuminate\Support\Str::limit($title,15) }}" cannot be processed. It has been recorded for review. Please scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

	@if($alert === 'ONHOLD')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ \Illuminate\Support\Str::limit($title,15) }} is ON HOLD. Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif


@if($alert === 'PALCI')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ \Illuminate\Support\Str::limit($title,15) }} is a PALCI item. Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

@if($alert === 'EMPTY_RESPONSE')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>This Item returned no results. It may be shadowed.
	&nbsp;<a style="color:white;" href="#">(more info)</a>&nbsp; Please scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif


@if($alert === 'EMPTY_BARCODE')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>An Empty Barcode was Entered. Please scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

@if($alert === 'NON_NUMERIC')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>A Non Numeric Barcode was Entered. ({{$title}})
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif	

	@if($alert === 'LENGTH')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>Barcode is Non-Standard Length. ({{$title}})
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent
 
	@endif	


@if($alert === 'LOST-ASSUM')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>{{ \Illuminate\Support\Str::limit($title,15) }} is ASSUMED LOST. If you would like to place it on the shelf, re-scan the barcode.
If not, scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif


	@if($alert === 'Empty Barcode')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>The barcode field was empty.</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

	@if($alert == 'Bad Barcode')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>Scanning error. The barcode scanned does not seem valid.</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif



@if($alert === 'InventoryAlert')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span style="margin:3px;"> 
	{{$title}}
	Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif

@if($alert === 'SHADOW')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>No location information is available for this copy of {{ \Illuminate\Support\Str::limit($title,15) }}. It may be shadowed.
	&nbsp;<a style="color:white;" href="#">(more info)</a>&nbsp;
	Re-scan the barcode to place the item on the shelf, or scan the next item.
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif


	@if($alert === 'WrongBook')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>You re-scanned the wrong book. Please re-scan {{ \Illuminate\Support\Str::limit($title,15) }}. 
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif



@if($alert === 'NoMshelf')

@component('layouts.partials.alerts._alerts_component', ['type' => 'red'])

<span>You re-scanned a book but there are no errors. 
</span>
{{-- Error sound --}}
<audio autoplay>
	<source src="/assets/beep-02.wav" type="audio/wav">
	</audio>

	@endcomponent

	@endif
