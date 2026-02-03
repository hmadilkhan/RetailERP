{{-- Sidebar Component 

 Whenever add new page in the backend please make sure to add the translation in sidebar.php lang file
 
--}}
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
                            <li class="treeview @yield('{{ $pages->navclass }}') ">
                                <a href="{{ url('/' . $pages->page_url) }}" class="bg-success">
                                    <i class='{{ $pages->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($pages->page_name))) }}</span>
                                </a>
                            </li>
                        @else
                            <li class="treeview @yield('{{ $pages->navclass }}') ">
                                <a class="bg-success">
                                    <i class='{{ $pages->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($pages->page_name))) }}</span><i
                                        class="icon-arrow-down"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach ($result as $childs)
                                        @if ($pages->id == $childs->parent_id)
                                            @if ($childs->icofont_arrow == 0)
                                                <li class="treeview @yield('{{ $childs->navclass }}')">
                                                    <a href="{{ url('/' . $childs->page_url) }}">
                                                        <i
                                                            class='{{ $childs->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($childs->page_name))) }}</span>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="treeview @yield('{{ $childs->navclass }}')">
                                                    <a class="bg-success">
                                                        <i
                                                            class='{{ $childs->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($childs->page_name))) }}</span><i
                                                            class="icon-arrow-down"></i>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        @foreach ($result as $grandchild)
                                                            @if ($childs->id == $grandchild->parent_id)
                                                                <li class="treeview @yield('{{ $grandchild->navclass }}')">
                                                                    <a href="{{ url('/' . $grandchild->page_url) }}">
                                                                        <i
                                                                            class='{{ $grandchild->icofont }}'></i><span>{{ __('sidebar.' . Str::snake(strtolower($grandchild->page_name))) }}</span>
                                                                    </a>

                                                                    @foreach ($result as $grandgrandchild)
                                                                        @if ($grandchild->id == $grandgrandchild->parent_id)
                                                                            <ul class="treeview-menu">
                                                                                <li class="treeview @yield('{{ $grandgrandchild->navclass }}')">
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
