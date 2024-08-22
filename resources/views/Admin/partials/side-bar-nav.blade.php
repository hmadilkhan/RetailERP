     <aside class="main-sidebar hidden-print ">
         <section class="sidebar" id="sidebar-scroll">
            <!-- Sidebar Menu-->
            <ul class="sidebar-menu">
               <li class="nav-level"><span></span></li>
               <li class="treeview @yield('navdashboard')">
                  <a class="waves-effect waves-dark" href="{{ route('admin.home') }}">
                     <i class="icofont icofont-dashboard text-primary"></i><span> Dashboard</span>
                  </a>
               </li>

               <li class="treeview @yield('navcompany')">
                  <a href="{{ route('company.index') }}" class="waves-effect waves-dark">
                     <i class="icofont icofont-users text-primary"></i><span>Company</span>
                  </a>
               </li>

               <li class="treeview @yield('navbranches')">
                  <a href="{{ url('view-branch') }}" class="waves-effect waves-dark">
                     <i class="icofont icofont-architecture-alt text-primary"></i><span>Branch</span>
                  </a>
               </li>

               <li class="treeview @yield('navusers')">
                  <a href="{{ url('view-users') }}" class="waves-effect waves-dark">
                     <i class="icofont icofont-architecture-alt text-primary"></i><span>User</span>
                  </a>
               </li>               
               
             
            </ul>
         </section>
      </aside>
