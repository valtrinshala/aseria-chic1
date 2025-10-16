@extends('layouts.blank-view')
@section('title', 'Results')
@section('content')
    <div class="row m-0 p-0 h-100">
        <div class="col m-0 p-0" style="background-color: #264653">
            <div class="mt-10 mb-10">
                <h1 class="text-white text-center" style="font-size: 50px">{{__('In-progress')}}</h1>
            </div>

            <div id="inProgressContainer"></div>
        </div>
        <div class="col m-0 p-0" style="background-color: #F6F8F8">
            <div class="mt-10 mb-10">
                <h1 class="text-center" style="font-size: 50px; color: #5D4BDF">{{__("Ready")}}</h1>
            </div>
            <div id="readyContainer"></div>
        </div>
    </div>
    <script>
        function getResults() {
            $.ajax({
                url: `/queue/ajax/{{ $queue->id }}/{{ $queue->location_id }}`,
                type: 'GET',
                success: function (response) {
                    updateResults(response.inProgress, response.ready);
                }
            });
        }

        function updateResults(inProgress, ready) {
            var inProgressHTML = '';
            $.each(inProgress, function(index, item) {
                if (index % 2 === 0) {
                    inProgressHTML += '<div class="text-start row w-100">';
                }
                inProgressHTML += '<div class="col mw-50"><h1 class="text-white text-center fs-3hx">#' + item['order_number'] +'</h1></div>';
                if ((index + 1) % 2 === 0 || index === inProgress.length - 1) {
                    inProgressHTML += '</div>';
                }
            });
            document.getElementById('inProgressContainer').innerHTML = inProgressHTML;
            var readyHTML = '';
            $.each(ready, function(index, item) {
                if (index % 2 === 0) {
                    readyHTML += '<div class="row w-100">';
                }
                readyHTML += '<div class="col mw-50"><h1 class="text-center fs-3hx" style="color: #5D4BDF">#' + item['order_number'] +'</h1></div>';
                if ((index + 1) % 2 === 0 || index === ready.length - 1) {
                    readyHTML += '</div>';
                }
            });
            document.getElementById('readyContainer').innerHTML = readyHTML;
        }
        getResults();
        setInterval(getResults, 1000);
    </script>
@endsection
