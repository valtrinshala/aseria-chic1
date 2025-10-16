<button type="button" class="btn btn-light border-0 px-3 w-125px" data-kt-menu-trigger="{default: 'click', lg: 'click'}"
    data-kt-menu-placement="bottom-end">
    <svg xmlns="http://www.w3.org/2000/svg" height="21" viewBox="0 -960 960 960" width="21">
        <path
            d="m476-120 174.308-440h40.923l174.308 440h-43.231l-47.616-122H566.846l-47.615 122H476ZM160.769-235.384l-28.308-28.308 195.847-196.616q-34.231-35-68.116-86.153Q226.308-597.615 210-640h43.231q14.615 33.615 44.231 77.231 29.615 43.615 59.153 74.153 52.231-52.999 92.731-116.346 40.5-63.346 53.885-115.038H95.384v-40H340v-49.231h40V-760h244.616v40h-79.847q-17.923 62.769-61.077 133.769-43.153 71-98.769 126.385l101.385 104.154-15.385 41.231-114.308-117.308-195.846 196.385Zm419.539-42.77h180.923l-90.462-232.461-90.461 232.461Z" />
    </svg>
    @if (request()->query('language_update_id'))
        {{ __($languageInQuery?->name) }}
    @else
        {{ __($mainLanguage->name) }}
    @endif
    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6">
        <path id="Path_6" data-name="Path 6"
            d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
            transform="translate(-0.009 -0.033)" fill="#264653" />
    </svg>
</button>
<div class="menu menu-sub menu-sub-dropdown menu-column w-auto" data-kt-menu="true">
    <div class="card card-body w-auto">
        <div class="menu-item">
            @foreach (\App\Helpers\Helpers::languages() as $language)
                <div class="p-2">
                    <a
                        href="{{ route($url, [$keyUrl => $valueUrl, 'language_update_id' => $language->id]) }}">{{ $language->name }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
