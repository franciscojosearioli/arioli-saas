@if(count($logs = \Auth::user()->AuditLogs()->where('read', false)->limit(10)->orderBy('created_at', 'ASC')->get()->reverse()) > 0)
    @foreach($logs as $log)
        <div class="dropdown-item">
            <span rel="noopener noreferrer">
                <strong>{{ \App\Models\User::where('id', $log->user_id)->value('name') }}</strong>:
                <strong>{{ $log->description }}</strong> <br>
                <strong>{{ $log->subject_type }}</strong> <br>
                {{ $log->created_at }}
            </span>
        </div>
    @endforeach
    <div class="dropdown-item" id="read-logs">
        <span rel="noopener noreferrer">
            {{ trans('global.read_logs') }}
        </span>
    </div>
@else
    <div class="text-center">
        {{ trans('global.no_logs') }}
    </div>
@endif