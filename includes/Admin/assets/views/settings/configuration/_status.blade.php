<h2>Integration Status</h2>
<hr class="divider">

<table class="form-table" role="presentation">
    @foreach($checks as $check)
        <tr>
            <th scope="row" style="white-space: nowrap;">{{ $check->title }}</th>
            <td>
                @if($check->status == \Simpler\Services\IntegrationService::STATUS_SUCCESS)
                    <span class="dashicons dashicons-yes" style="color: #00a32a"></span>
                @elseif($check->status == \Simpler\Services\IntegrationService::STATUS_FAIL)
                    <span class="dashicons dashicons-no" style="color: #d63638"></span>
                @else
                    <span class="dashicons dashicons-minus"></span>
                @endif
                @if($check->message)
                    {{ $check->message }}
                @endif
                @if($check->actionUrl && $check->actionLabel)
                    <a href="{{ $check->actionUrl }}">{{ $check->actionLabel }}</a>
                @endif
            </td>
        </tr>
    @endforeach
</table>
