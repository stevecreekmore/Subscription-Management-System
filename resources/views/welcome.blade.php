<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Subscription Management</title>

        <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
<!-- Header -->
  <header class="absolute inset-x-0 top-0 z-50">
    <nav aria-label="Global" class="flex items-center justify-between p-6 lg:px-8">
      <div class="flex lg:flex-1">
      </div>
      <div class="flex lg:hidden">

      </div>

      <div class="hidden lg:flex lg:flex-1 lg:justify-end">
        <a href="{{ route('dashboard') }}" class="text-sm/6 font-semibold text-gray-900">Log in <span aria-hidden="true">&rarr;</span></a>
      </div>
    </nav>
    <el-dialog>
      <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
        <div tabindex="0" class="fixed inset-0 focus:outline-none">
          <el-dialog-panel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
            <div class="flex items-center justify-between">
              <a href="#" class="-m-1.5 p-1.5">
                <span class="sr-only">Your Company</span>
                <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="" class="h-8 w-auto" />
              </a>

            </div>
            <div class="mt-6 flow-root">
              <div class="-my-6 divide-y divide-gray-500/10">

                <div class="py-6">
                  <a href="{{ route('dashboard') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Log in</a>
                </div>
              </div>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>
    </el-dialog>
  </header>
             <main class="isolate">
    <!-- Hero section -->
    <div class="  relative pt-14">
      <div aria-hidden="true" class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
        <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"></div>
      </div>
      <div class="  py-10 sm:py-10 lg:pb-10">
        <div class="  mx-auto max-w-7xl px-6 lg:px-8">
          <div class="  mx-auto max-w-2xl text-center">
            <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Subscription Management Portal</h1>
            <p class="mt-8 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem cupidatat commodo. Elit sunt amet fugiat veniam occaecat.</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
              <a href="{{  route('dashboard') }}" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Get started</a>
            </div>
          </div>

        </div>
      </div>

    </div>



    <!-- Testimonial section -->
    <div class="mx-auto mt-10 max-w-7xl sm:mt-10 sm:px-6 lg:px-8">
      <div class="relative overflow-hidden bg-gray-900 px-6 py-20 shadow-xl sm:rounded-3xl sm:px-10 sm:py-24 md:px-12 lg:px-20">
        <img src="https://images.unsplash.com/photo-1601381718415-a05fb0a261f3?ixid=MXwxMjA3fDB8MHxwcm9maWxlLXBhZ2V8ODl8fHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1216&q=80" alt="" class="absolute inset-0 size-full object-cover brightness-150 saturate-0" />
        <div class="absolute inset-0 bg-gray-900/90 mix-blend-multiply"></div>
        <div aria-hidden="true" class="absolute -top-56 -left-80 transform-gpu blur-3xl">
          <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="aspect-1097/845 w-274.25 bg-linear-to-r from-[#ff4694] to-[#776fff] opacity-[0.45]"></div>
        </div>
        <div aria-hidden="true" class="hidden md:absolute md:bottom-16 md:left-200 md:block md:transform-gpu md:blur-3xl">
          <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="aspect-1097/845 w-274.25 bg-linear-to-r from-[#ff4694] to-[#776fff] opacity-25"></div>
        </div>
        <div class="relative mx-auto max-w-2xl lg:mx-0">
          <img src="https://tailwindcss.com/plus-assets/img/logos/workcation-logo-white.svg" alt="" class="h-12 w-auto" />
          <figure>
            <blockquote class="mt-6 text-lg font-semibold text-white sm:text-xl/8">
              <p>“Amet amet eget scelerisque tellus sit neque faucibus non eleifend. Integer eu praesent at a. Ornare arcu gravida natoque erat et cursus tortor consequat at. Vulputate gravida sociis enim nullam ultricies habitant malesuada lorem ac.”</p>
            </blockquote>
            <figcaption class="mt-6 text-base text-white">
              <div class="font-semibold">Judith Black</div>
              <div class="mt-1">CEO of Tuple</div>
            </figcaption>
          </figure>
        </div>
      </div>
    </div>


  </main>

  <!-- Footer -->
  <footer class="relative mx-auto mt-32 max-w-7xl px-6 lg:px-8">
    <div class="border-t border-gray-900/10 py-16 sm:py-24 lg:py-32">
      <div class="xl:grid xl:grid-cols-3 xl:gap-8">
        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Company name" class="h-9" />
        <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm/6 font-semibold text-gray-900">Solutions</h3>
              <ul role="list" class="mt-6 space-y-4">
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Hosting</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Data services</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Uptime monitoring</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Enterprise services</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Analytics</a>
                </li>
              </ul>
            </div>
            <div class="mt-10 md:mt-0">
              <h3 class="text-sm/6 font-semibold text-gray-900">Support</h3>
              <ul role="list" class="mt-6 space-y-4">
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Submit ticket</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Documentation</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Guides</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm/6 font-semibold text-gray-900">Company</h3>
              <ul role="list" class="mt-6 space-y-4">
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">About</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Blog</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Jobs</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Press</a>
                </li>
              </ul>
            </div>
            <div class="mt-10 md:mt-0">
              <h3 class="text-sm/6 font-semibold text-gray-900">Legal</h3>
              <ul role="list" class="mt-6 space-y-4">
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Terms of service</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">Privacy policy</a>
                </li>
                <li>
                  <a href="#" class="text-sm/6 text-gray-600 hover:text-gray-900">License</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</div>


        </div>
    </body>
</html>
