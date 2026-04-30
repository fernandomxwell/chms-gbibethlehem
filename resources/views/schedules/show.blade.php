@extends('layouts.app')

@section('content')
    <h1>@lang('schedules.show')</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="text-center align-middle table-light">
                <tr>
                    <th class="text-nowrap">@lang('date')</th>
                    @foreach ($serviceTypes as $serviceType)
                        <th class="text-nowrap">{{ $serviceType->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($scheduleData as $row)
                    <tr>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                        @foreach ($serviceTypes as $serviceType)
                            <td class="text-nowrap">{!! $row[$serviceType->name] !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
@endsection
