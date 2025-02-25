<div>
    <button wire:click="downloadPDF({{$row->id}})">
        <img class='h-6' src="{{asset('img/icons/pdf_cpe.svg')}}"/>
    </button>
</div>