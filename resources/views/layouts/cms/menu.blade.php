@php
$menus = auth()->data()['menu'];
$current_url = request()->getRequestUri();
@endphp

<ul class="sidebar-nav">
    @foreach($menus as $menu)

        @php

        $children_active = array_filter($menu->children, function($item) use ($current_url) {
            return str_starts_with($item->url, $current_url);
        });

        $collapsed = [
            'class'=> count($children_active) === 0 ? 'collapsed' : '',
            'aria'=> count($children_active) === 0 ? 'false' : 'true',
            'ul'=> count($children_active) === 0 ? 'collapse' : 'collapse show',
        ];
        @endphp

        <li  class="{{ !empty($menu->children) ? 'sidebar-header' : 'sidebar-item'}}" >
            @if(!empty($menu->children))
                <span class="{{$collapsed['class']}}" data-bs-toggle="collapse" href="#sub_{{$menu->id}}" role="button" aria-expanded="{{$collapsed['aria']}}" aria-controls="sub_{{$menu->id}}" >
                    {{ $menu->title }}
                </span>

                <ul id="sub_{{$menu->id}}" class="sidebar-nav {{$collapsed['ul']}}" >

                    @foreach($menu->children as $child)
                        <li class="sidebar-item {{ str_starts_with($child->url, $current_url) ? 'active' : '' }}"  >
                            <a href="{{$child->url}}" class="sidebar-link">
                                <span class="align-middle">{{ $child->title }}</span>
                            </a>
                        </li>
                    @endforeach

                </ul>
            @else
                <a href="{{$menu->url}}" class="sidebar-link">
                    <span class="align-middle">{{ $menu->title }}</span>
                </a>
            @endif

        </li>
    @endforeach
</ul>
