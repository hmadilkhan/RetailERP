{{-- 

Sidebar Component 

Whenever add new page in the backend please make sure to add the translation in sidebar.php lang file
 
--}}
@php
    $currentUrl = request()->path();
@endphp
<aside class="main-sidebar hidden-print bg-success">
    <section class="sidebar" id="sidebar-scroll">
        <!-- Sidebar Menu-->
        <ul class="sidebar-menu">
            @if ($result)
                @foreach ($result as $pages)
                    @if ($pages->page_mode == 'Label')
                        <li class="nav-level">-------- .{{ __('sidebar.' . Str::snake(strtolower($pages->page_name))) }}<span></span></li>
                    @elseif($pages->page_mode == 'Parent')
                        @if ($pages->icofont_arrow == 0)
                            <li class="treeview {{ $currentUrl == $pages->page_url ? 'active' : '' }}">
                                <a href="{{ url('/' . $pages->page_url) }}" class="bg-success">
                                    <i class='{{ $pages->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($pages->page_name))) }}</span>
                                </a>
                            </li>
                        @else
                            @php
                                $hasActiveChild = $result->where('parent_id', $pages->id)->contains(function($child) use ($currentUrl, $result) {
                                    if ($child->page_url == $currentUrl) return true;
                                    $hasActiveGrandchild = $result->where('parent_id', $child->id)->contains(function($grandchild) use ($currentUrl, $result) {
                                        if ($grandchild->page_url == $currentUrl) return true;
                                        return $result->where('parent_id', $grandchild->id)->contains('page_url', $currentUrl);
                                    });
                                    return $hasActiveGrandchild;
                                });
                            @endphp
                            <li class="treeview {{ $hasActiveChild ? 'active' : '' }}">
                                <a class="bg-success">
                                    <i class='{{ $pages->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($pages->page_name))) }}</span><i
                                        class="icon-arrow-down"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach ($result as $childs)
                                        @if ($pages->id == $childs->parent_id)
                                            @if ($childs->icofont_arrow == 0)
                                                <li class="treeview {{ $currentUrl == $childs->page_url ? 'active' : '' }}">
                                                    <a href="{{ url('/' . $childs->page_url) }}">
                                                        <i
                                                            class='{{ $childs->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($childs->page_name))) }}</span>
                                                    </a>
                                                </li>
                                            @else
                                                @php
                                                    $hasActiveGrandchild = $result->where('parent_id', $childs->id)->contains(function($grandchild) use ($currentUrl, $result) {
                                                        if ($grandchild->page_url == $currentUrl) return true;
                                                        return $result->where('parent_id', $grandchild->id)->contains('page_url', $currentUrl);
                                                    });
                                                @endphp
                                                <li class="treeview {{ $hasActiveGrandchild ? 'active' : '' }}">
                                                    <a class="bg-success">
                                                        <i
                                                            class='{{ $childs->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($childs->page_name))) }}</span><i
                                                            class="icon-arrow-down"></i>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        @foreach ($result as $grandchild)
                                                            @if ($childs->id == $grandchild->parent_id)
                                                                @php
                                                                    $hasActiveGreatGrandchild = $result->where('parent_id', $grandchild->id)->contains('page_url', $currentUrl);
                                                                @endphp
                                                                <li class="treeview {{ $currentUrl == $grandchild->page_url || $hasActiveGreatGrandchild ? 'active' : '' }}">
                                                                    <a href="{{ url('/' . $grandchild->page_url) }}">
                                                                        <i
                                                                            class='{{ $grandchild->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($grandchild->page_name))) }}</span>
                                                                    </a>

                                                                    @foreach ($result as $grandgrandchild)
                                                                        @if ($grandchild->id == $grandgrandchild->parent_id)
                                                                            <ul class="treeview-menu">
                                                                                <li class="treeview {{ $currentUrl == $grandgrandchild->page_url ? 'active' : '' }}">
                                                                                    <a
                                                                                        href="{{ url('/' . $grandgrandchild->page_url) }}">
                                                                                        <i
                                                                                            class='{{ $grandgrandchild->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($grandgrandchild->page_name))) }}</span>
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        @endif
                                                                    @endforeach

                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @else
                    @endif
                @endforeach
            @endif

        </ul>
    </section>
</aside>
