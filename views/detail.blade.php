@php
    $level = $level ?? 1;
    $iteration = $iteration ?? 0;
    $maxLevel = $maxLevel ?? 2;
@endphp

@foreach($details as $header => $value)
    @if (is_numeric($header) && $value === 'divider')
        <hr/>
        @continue
    @endif

    @php
        $iteration += 1;
    @endphp


    <div class="row g-0 @if($iteration % 2 === 0)  bg-light @else bg-white @endif">
    @if(is_array($value))
            @if(is_numeric($header))
                <div class="col col-lg-12">
                    @if($level >= $maxLevel)
                        <code>@dump($value)</code>
                    @else
                        @include('change_logs::detail', ['details' => $value, 'level' => $level + 1, 'iteration' => $iteration + 1])
                    @endif
                </div>
            @else
                <div class="col col-lg-4">
                    <p class="mt-2 text-dark fw-light mb-2 fw-bold">{{ $header }}</p>
                </div>
                <div class="col col-lg-8">
                    @if($level >= $maxLevel)
                        <code>@dump($value)</code>
                    @else
                        @include('change_logs::detail', ['details' => $value, 'level' => $level + 1, 'iteration' => $iteration + 1])
                    @endif
                </div>
            @endif

    @elseif(is_numeric($header) && is_string($value))
        <div class="col col-lg-12">
            <p class="mt-2 fw-light mb-2 ">{{ $value }}</p>
        </div>
    @else
        <div class="col col-lg-4">
            <p class="mt-2 text-dark fw-light mb-2 fw-bold">{{ $header }}</p>
        </div>
        <div class="col col-lg-8">
            @if(is_null($value))
                <p class="mt-2 text-dark fw-light mb-2"><code>NULL</code></p>
            @elseif(is_bool($value))
                <p class="mt-2 text-dark fw-light mb-2"><b>{{$value ? 'Да' : 'Нет'}}</b></p>
            @else
                <p class="mt-2 text-dark fw-light mb-2"><b>{{$value}}</b></p>
            @endif
        </div>
    @endif
    </div>
@endforeach
