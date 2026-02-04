{{-- 

Sidebar Component 

Whenever add new page in the backend please make sure to add the translation in sidebar.php lang file
 
--}}
@php
    $currentUrl = request()->path();
@endphp
<style>
    .main-sidebar {
        box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    }
    
    .sidebar-menu {
        padding: 0.5rem 0;
    }
    
    .sidebar-menu li.nav-level {
        color: rgba(255,255,255,0.6);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem 0.5rem;
        margin-top: 0.5rem;
    }
    
    .sidebar-menu .treeview > a {
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        /* text-decoration: none; */
    }
    
    .sidebar-menu .treeview > a:hover {
        background-color: rgba(255,255,255,0.1) !important;
        border-left-color: rgba(255,255,255,0.5);
        text-decoration: none;
    }
    
    .sidebar-menu .treeview.active > a {
        background-color: rgba(255,255,255,0.15) !important;
        border-left-color: #fff;
        font-weight: 600;
    }
    
    .sidebar-menu .treeview > a i:first-child {
        margin-right: 0.75rem;
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }
    
    .sidebar-menu .treeview > a .icon-arrow-down {
        margin-left: auto;
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }
    
    .sidebar-menu .treeview.active > a .icon-arrow-down {
        transform: rotate(180deg);
    }
    
    .sidebar-menu .treeview-menu {
        background-color: #fff;
    }
    
    .sidebar-menu .treeview-menu li a {
        padding: 0.6rem 1.5rem 0.6rem 3.5rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        color: #333 !important;
        text-decoration: none;
    }
    
    .sidebar-menu .treeview-menu li a:hover {
        background-color: #28a745 !important;
        color: #fff !important;
        padding-left: 3.75rem;
        text-decoration: none;
    }
    
    .sidebar-menu .treeview-menu li.active > a {
        /* background-color: #28a745 !important; */
        /* color: #fff !important; */
        color: #000 !important;
        font-weight: 1000;
    }
    
    .sidebar-menu .treeview-menu li.active > a:hover {
        background-color: #218838 !important;
        color: #fff !important;
    }
    
    .sidebar-menu .treeview-menu .treeview-menu li a {
        padding-left: 4.5rem;
        font-size: 0.85rem;
        color: #333 !important;
    }
    
    .sidebar-menu .treeview-menu .treeview-menu li a:hover {
        padding-left: 4.75rem;
        background-color: #28a745 !important;
        color: #fff !important;
    }
    
    .sidebar-menu .treeview-menu .treeview-menu li.active > a {
        background-color: #28a745 !important;
        color: #fff !important;
    }
    
    .sidebar-menu .treeview-menu .treeview-menu .treeview-menu li a {
        padding-left: 5.5rem;
        color: #333 !important;
    }
    
    .sidebar-menu .treeview-menu .treeview-menu .treeview-menu li a:hover {
        background-color: #28a745 !important;
        color: #fff !important;
    }
    
    .sidebar-menu .treeview-menu .treeview-menu .treeview-menu li.active > a {
        background-color: #28a745 !important;
        color: #fff !important;
    }
</style>
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
