<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Instagram, Twitter, Facebook } from 'lucide-vue-next';
</script>

<template>
    <Head title="BandPress - Increase Your Online Presence">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@700&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-white text-figma-black">
        <!-- Hero Section -->
        <div class="relative h-[571px] w-full overflow-hidden">
            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-black to-gray-800"></div>
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center opacity-50"
                 style="background-image: url('/bp_splash.png');"></div>


            <!-- Header Navigation -->
            <header class="absolute top-8 left-0 right-0 z-20 flex items-center justify-between px-8">
                <!-- Left Side: Logo + Brand -->
                <div class="flex items-center space-x-4">
                    <img src="/bp_logo.png" alt="BandPress Logo" class="w-16 h-16 object-contain" />
                    <span class="text-white text-2xl font-bold uppercase tracking-wider font-saira">
                        BandPress
                    </span>
                </div>

                <!-- Right Side: Navigation + Sign In -->
                <div class="flex items-center space-x-8">
                    <nav class="flex items-center space-x-8">
                        <Link href="/" class="text-white hover:text-gray-200 transition-colors text-lg font-bold uppercase tracking-wider font-saira">
                            Home
                        </Link>
                        <Link href="#about" class="text-white hover:text-gray-200 transition-colors text-lg font-bold uppercase tracking-wider font-saira">
                            About
                        </Link>
                        <Link href="#clients" class="text-white hover:text-gray-200 transition-colors text-lg font-bold uppercase tracking-wider font-saira">
                            Clients
                        </Link>
                        <Link href="#contact" class="text-white hover:text-gray-200 transition-colors text-lg font-bold uppercase tracking-wider font-saira">
                            Contact
                        </Link>
                    </nav>

                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="bg-figma-red text-white px-6 py-2 rounded hover:bg-figma-bright-red transition-colors font-bold uppercase tracking-wider font-saira"
                    >
                        Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="bg-figma-red text-white px-6 py-2 rounded hover:bg-figma-bright-red transition-colors font-bold uppercase tracking-wider font-saira"
                    >
                        Sign In
                    </Link>
                </div>
            </header>
        </div>

        <!-- Diagonal Gradient Transition -->
        <div class="relative h-32 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-800 via-gray-600 to-white transform -skew-y-3"></div>
        </div>

        <!-- Mid Section -->
        <div class="py-16 px-8 bg-white relative -mt-32">
            <div class="max-w-6xl mx-auto">
                <!-- Top Row: Image + Red Line with Centered Heading -->
                <div class="flex items-start mb-12">
                    <!-- Left: Image Section (Rectangle 3) -->
                    <div class="flex-shrink-0">
                        <div class="w-[550px] h-[415px] bg-gray-200 rounded-lg flex items-center justify-center opacity-80">
                            <img src="/bp_logo.png" alt="BandPress Logo" class="w-32 h-32 object-contain" />
                        </div>
                    </div>

                    <!-- Right: Red Line + Centered Heading -->
                    <div class="ml-8 flex items-center">
                        <!-- Red Line (Rectangle 4) - Full Image Height -->
                        <div class="w-2 h-[415px] bg-figma-red flex-shrink-0"></div>

                        <!-- Main Heading - Vertically Centered on Red Line -->
                        <h1 class="ml-4 text-4xl md:text-5xl lg:text-6xl font-bold text-figma-black leading-tight uppercase">
                            WE INCREASE YOUR ONLINE PRESENCE, TO HELP YOUR STAGE PRESENCE
                        </h1>
                    </div>
                </div>

                <!-- Bottom: Lorem Text -->
                <div class="max-w-2xl mx-auto text-center mb-8">
                    <p class="text-xl text-figma-dark-grey leading-relaxed">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. In lobortis, purus nec euismod tristique, ex purus fermentum magna, eu gravida nulla dolor non felis. Aenean diam risus, luctus tincidunt tortor vel, finibus sollicitudin lorem. Nullam magna mi, dictum sit amet fermentum eget, interdum ac neque.
                    </p>
                </div>

                <!-- Bottom: Sign UP Button -->
                <div class="text-center">
                    <Link
                        :href="route('register')"
                        class="bg-figma-red text-white px-6 py-2 rounded hover:bg-figma-bright-red transition-colors font-bold uppercase tracking-wider font-saira"
                    >
                        Sign UP
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-8 px-8 bg-figma-black">
            <div class="max-w-6xl mx-auto">
                <!-- Main Footer Row -->
                <div class="flex items-center justify-between mb-8">
                    <!-- Left Side: Logo + Brand -->
                    <div class="flex items-center space-x-4">
                        <img src="/bp_logo.png" alt="BandPress Logo" class="w-16 h-16 object-contain" />
                        <span class="text-white text-2xl font-bold uppercase tracking-wider font-saira">
                            BandPress
                        </span>
                    </div>

                    <!-- Right Side: Social Media Icons -->
                    <div class="flex items-center space-x-4">
                        <a href="#" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <Facebook class="w-5 h-5 text-figma-dark-grey" />
                        </a>
                        <a href="#" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <Twitter class="w-5 h-5 text-figma-dark-grey" />
                        </a>
                        <a href="#" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <Instagram class="w-5 h-5 text-figma-dark-grey" />
                        </a>
                    </div>
                </div>

                <!-- Centered Terms & Privacy Row - Below All Other Elements -->
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-8 text-figma-soft-grey">
                        <Link href="#" class="hover:text-white transition-colors text-lg font-bold uppercase tracking-wider font-saira">
                            Terms & Conditions
                        </Link>
                        <Link href="#" class="hover:text-white transition-colors text-lg font-bold uppercase tracking-wider font-saira">
                            Privacy Policy
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
