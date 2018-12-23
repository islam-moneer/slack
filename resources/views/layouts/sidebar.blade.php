<div class="col-md-2">
    <div class="panel panel-default panel-flush">
        <div class="card-header">Slack channels</div>
        <div class="card">
            <div class="panel-body">
                <div class="card-body">
                    <ul class="nav" role="tablist">
                            @if($channels->getChannels()->ok != true)
                                <li>Error in retrieve channels</li>
                                @foreach($channels->getChannels() as $channel)
                                    <li>@php print_r($channel)@endphp</li>
                                @endforeach

                            @else
                                @foreach($channels->getChannels()->channels as $channel)
                                    <a href="{{ route('channel', ['t' => $channel->id]) }}"><li>- {{$channel->name}}</li></a>
                                @endforeach
                            @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>