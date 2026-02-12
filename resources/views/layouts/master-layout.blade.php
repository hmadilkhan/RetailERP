<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title')</title>
    @include('partials.html-libs')
    @yield('css_code')
    @yield('scriptcode_one')
    @livewireStyles
</head>

@yield('scriptcode_two')

<body class="sidebar-mini fixed">
    <div class="loader-bg">
        <div class="loader-bar">
        </div>
    </div>
    <div id="cover-spin"></div>
    <!--wrapper-->
    <div class="wrapper">

        <!-- Navbar header-->
        @include('partials.header')
        <!-- end Navbar header -->

        <!-- Side-Nav-->
        <x-sidebar />
        <!-- end Side-Nav -->
        <div class="content-wrapper">
            <!-- Container-fluid starts -->
            <!-- Main content starts -->
            <div class="container-fluid p-4" 
            {{-- @hasSection('dashboardInlineCSS')
            @else
                style="padding-top:3.9rem;"
                @endif --}}
            >

                <!-- start contect-->

                @yield('content')
                <!-- end contect-->


            </div>
            <!-- Main content ends -->
            <!-- Container-fluid ends -->
        </div>
    </div>
    <!-- Required Jqurey -->
    @include('partials.js-libs')
    @yield('scriptcode_three')
    @livewireScripts
    {{-- <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
    <script>
        var firebaseConfig = {
            apiKey: "AIzaSyCYYAyfXYoxYce9frA_6OQMP8B0O7Kg6NQ",
            authDomain: "push-notification-540cc.firebaseapp.com",
            projectId: "push-notification-540cc",
            storageBucket: "push-notification-540cc.appspot.com",
            messagingSenderId: "554454577405",
            appId: "1:554454577405:web:1b47175dee06fe3b1b4e94",
            measurementId: "G-969BF3W3TC"
        };
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        // initFirebaseMessagingRegistration(); 
        function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken()
                })
                .then(function(token) {
                    console.log(token);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '{{ route('save-token') }}',
                        type: 'POST',
                        data: {
                            token: token
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            console.log(response)
                            alert('Token saved successfully.');
                        },
                        error: function(err) {
                            console.log('User Chat Token Error' + err);
                        },
                    });

                }).catch(function(err) {
                    console.log('User Chat Token Error' + err);
                });
        }

        messaging.onMessage(function(payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(noteTitle, noteOptions);
        });
    </script> --}}
    <script type="text/javascript">
        // setInterval(function(){checkOrders();},300000);
        // setInterval(function(){dueDateOrders()},50000);
        function checkOrders() {
            $.ajax({
                url: "{{ url('/order-notify') }}",
                type: "GET",
                dataType: 'json',
                success: function(result) {
                    // console.log(result.order.length);
                    // console.log(result.onlineorders.length);
                    if (result.order.length == 1) {
                        var title = result.order[0].company_name + " (" + result.order[0].branch_name + ")";
                        var body = "Hey you received new " + result.order[0].payment_mode + " Sales Order # " +
                            result.order[0].id
                        notifyMe(title, body, "pos");
                    } else if (result.order.length > 1) {
                        var title = result.order[0].company_name + " (" + result.order[0].branch_name + ")";
                        var body = "Hey you received " + result.order.length + " new Sales Orders"
                        notifyMe(title, body, "pos");
                    }

                    if (result.onlineorders.length == 1) {
                        var title = result.onlineorders[0].company_name + " (" + result.onlineorders[0]
                            .branch_name + ")";
                        var body = "Hey you received new Online Order # " + result.onlineorders[0].id
                        notifyMe(title, body, "online");
                    } else if (result.onlineorders.length > 1) {
                        var title = result.onlineorders[0].company_name + " (" + result.onlineorders[0]
                            .branch_name + ")";
                        var body = "Hey you received " + result.onlineorders.length + " new Online Orders"
                        notifyMe(title, body, "online");
                    }


                    // $.each(result.order, function( index, value ) {
                    // console.log(value)
                    // if(value.order_mode_id != 4){
                    // var title = value.company_name + " (" + value.branch_name + ")";
                    // var body = "Hey you received new "+value.payment_mode+" Sales Order # " + value.id
                    // }
                    // notifyMe(title,body);
                    // });
                }
            });
        }

        function dueDateOrders() {
            $.ajax({
                url: "{{ url('/due-date-orders') }}",
                type: "GET",
                dataType: 'json',
                success: function(result) {
                    // console.log(result);
                    var totalAmount = result.orders[0].totalAmount;
                    var totalCustomers = result.orders[0].totalCustomers;
                    if (totalAmount != null && totalCustomers > 0) {
                        var title = "Due Date Notification"
                        var body = "Today you have to receive payment of " + totalAmount + " from " +
                            totalCustomers + " Customers.";
                        notifyMe(title, body, "customer");
                    }
                }
            });
        }
        // setInterval(function(){notifyMe()},30000);
        function notifyMe(title, body, mode) {
            // Let's check if the browser supports notifications
            if (!("Notification" in window)) {
                alert("This browser does not support desktop notification");
            }

            // Let's check if the user is okay to get some notification
            else if (Notification.permission === "granted") {
                // If it's okay let's create a notification
                var notification = new Notification(title, {
                    body: body,
                    icon: "https://sabsoft.com.pk/Retail/public/assets/images/desktop-notify-icon.png"
                });

                notification.onclick = (e) => {
                    if (mode == "pos") {
                        window.open("https://sabsoft.com.pk/Retail/orders-view");
                    } else if (mode == "online") {
                        window.open("https://sabsoft.com.pk/Retail/web-orders-view");
                    } else {
                        window.open("https://sabsoft.com.pk/Retail/customer-due-payment");
                    }

                };
            }

            // Otherwise, we need to ask the user for permission
            // Note, Chrome does not implement the permission static property
            // So we have to check for NOT 'denied' instead of 'default'
            else if (Notification.permission !== 'denied') {
                Notification.requestPermission(function(permission) {

                    // Whatever the user answers, we make sure we store the information
                    if (!('permission' in Notification)) {
                        Notification.permission = permission;
                    }

                    // If the user is okay, let's create a notification
                    if (permission === "granted") {
                        var notification = new Notification("Hi there!");
                    }
                });
            } else {
                console.log(`Permission is ${Notification.permission}`)
                // alert(`Permission is ${Notification.permission}`);
            }

            // At last, if the user already denied any notification, and you
            // want to be respectful there is no need to bother him any more.
        }
    </script>
</body>

</html>
