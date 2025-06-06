<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'TrainEase' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!-- Other head elements -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="bg-gray-50 text-gray-800">
    <nav class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <img class="h-12"
                            src="https://gondwana-collection.com/hs-fs/hubfs/Gondwana-Collection-Logo-1.png?width=378&height=96&name=Gondwana-Collection-Logo-1.png"
                            alt="Your Company">
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                            <a href="/" class="rounded-md bg-yellow-900 px-3 py-2 text-sm font-medium text-white"
                                aria-current="page">Rate Checker</a>
                            <a href="/docs"
                                class="rounded-md px-3 py-2 text-sm font-medium text-yellow-900 hover:bg-yellow-700   hover:text-white">API Docs</a>

                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">


                        <div class="relative ml-3">
                            <div
                                x-data="{
                                    tooltipVisible: false,
                                    tooltipText: 'Hubert Mouton (Web Artisan)',
                                    tooltipArrow: true,
                                    tooltipPosition: 'bottom',
                                }"
                                x-init="$refs.content.addEventListener('mouseenter', () => { tooltipVisible = true; }); $refs.content.addEventListener('mouseleave', () => { tooltipVisible = false; });"
                                class="relative">

                                <div x-ref="tooltip" x-show="tooltipVisible" :class="{ 'top-0 left-1/2 -translate-x-1/2 -mt-0.5 -translate-y-full' : tooltipPosition == 'top', 'top-1/2 -translate-y-1/2 -ml-0.5 left-0 -translate-x-full' : tooltipPosition == 'left', 'bottom-0 left-1/2 -translate-x-1/2 -mb-0.5 translate-y-full' : tooltipPosition == 'bottom', 'top-1/2 -translate-y-1/2 -mr-0.5 right-0 translate-x-full' : tooltipPosition == 'right' }" class="absolute w-auto text-sm" x-cloak>
                                    <div x-show="tooltipVisible" x-transition class="relative px-2 py-1 text-white bg-black rounded bg-opacity-90">
                                        <p x-text="tooltipText" class="flex-shrink-0 block text-xs whitespace-nowrap"></p>
                                        <div x-ref="tooltipArrow" x-show="tooltipArrow" :class="{ 'bottom-0 -translate-x-1/2 left-1/2 w-2.5 translate-y-full' : tooltipPosition == 'top', 'right-0 -translate-y-1/2 top-1/2 h-2.5 -mt-px translate-x-full' : tooltipPosition == 'left', 'top-0 -translate-x-1/2 left-1/2 w-2.5 -translate-y-full' : tooltipPosition == 'bottom', 'left-0 -translate-y-1/2 top-1/2 h-2.5 -mt-px -translate-x-full' : tooltipPosition == 'right' }" class="absolute inline-flex items-center justify-center overflow-hidden">
                                            <div :class="{ 'origin-top-left -rotate-45' : tooltipPosition == 'top', 'origin-top-left rotate-45' : tooltipPosition == 'left', 'origin-bottom-left rotate-45' : tooltipPosition == 'bottom', 'origin-top-right -rotate-45' : tooltipPosition == 'right' }" class="w-1.5 h-1.5 transform bg-black bg-opacity-90"></div>
                                        </div>
                                    </div>
                                </div>

                                <div x-ref="content">
                                    <img class="size-8 rounded-full"
                                        src="https://media.licdn.com/dms/image/v2/D4D03AQHbt3pbKRsa-Q/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1723576848725?e=1754524800&v=beta&t=ruBuUg6xv6wtMuY9qJP1LCpQJUvggzIOdjklU0-xB_I"
                                        alt="">
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <img class="size-8 rounded-full"
                        src="https://media.licdn.com/dms/image/v2/D4D03AQHbt3pbKRsa-Q/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1723576848725?e=1754524800&v=beta&t=ruBuUg6xv6wtMuY9qJP1LCpQJUvggzIOdjklU0-xB_I"
                        alt="">
                </div>
            </div>
        </div>


    </nav>

    @yield('content')
</body>

</html>
