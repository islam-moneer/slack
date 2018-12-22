<div class="col-md-3">
    <div class="panel panel-default panel-flush">
        <div class="card-header">Slack channels</div>
        <div class="card">
            <div class="panel-body">
                <div class="card-body">
                    <ul class="nav" role="tablist">
                        <ul role="presentation">
                            @if($channels->getChannels()->ok != true)
                                <li>Error in retrieve channels</li>
                                @foreach($channels->getChannels() as $channel)
                                    <li>@php print_r($channel)@endphp</li>
                                @endforeach

                            @else
                                @foreach($channels->getChannels()->channels as $channel)
                                    <li>{{$channel->name}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>