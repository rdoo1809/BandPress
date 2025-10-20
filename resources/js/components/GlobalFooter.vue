<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="border-t bg-white py-12 dark:bg-gray-900 dark:border-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2">
                        <img src="/bp_logo.png" alt="BandPress Logo" class="h-8 w-8" />
                        <span class="text-xl font-bold text-figma-black dark:text-white">BandPress</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 max-w-md">
                        Empowering musicians to build their online presence effortlessly. Create stunning websites, manage events, and showcase your music with BandPress.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-figma-black dark:text-white uppercase tracking-wider">
                        Quick Links
                    </h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <Link href="/" class="text-sm text-gray-600 hover:text-figma-red dark:text-gray-300 dark:hover:text-figma-bright-red transition-colors">
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link href="/dashboard" class="text-sm text-gray-600 hover:text-figma-red dark:text-gray-300 dark:hover:text-figma-bright-red transition-colors">
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <Link href="/builder" class="text-sm text-gray-600 hover:text-figma-red dark:text-gray-300 dark:hover:text-figma-bright-red transition-colors">
                                Builder
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-sm font-semibold text-figma-black dark:text-white uppercase tracking-wider">
                        Legal
                    </h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <Link href="#" class="text-sm text-gray-600 hover:text-figma-red dark:text-gray-300 dark:hover:text-figma-bright-red transition-colors">
                                Privacy Policy
                            </Link>
                        </li>
                        <li>
                            <Link href="#" class="text-sm text-gray-600 hover:text-figma-red dark:text-gray-300 dark:hover:text-figma-bright-red transition-colors">
                                Terms & Conditions
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        © {{ new Date().getFullYear() }} BandPress. All rights reserved.
                    </p>
                    <div class="flex items-center space-x-6">
                        <a href="#" class="text-gray-400 hover:text-figma-red dark:hover:text-figma-bright-red transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-figma-red dark:hover:text-figma-bright-red transition-colors">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.017 0C8.396 0 7.909.016 6.696.071 5.483.126 4.622.309 3.863.548c-.79.242-1.46.567-2.13.897C.97 1.776.646 2.446.404 3.236c-.239.76-.422 1.621-.477 2.834C-.076 7.909-.092 8.396-.092 12.017s.016 4.108.071 5.321c.055 1.213.238 2.074.477 2.834.242.79.567 1.46.897 2.13.63.63 1.46.897 2.13 1.237.76.239 1.621.422 2.834.477 1.213.055 1.7.071 5.321.071s4.108-.016 5.321-.071c1.213-.055 2.074-.238 2.834-.477.79-.242 1.46-.567 2.13-.897.63-.63.897-1.46 1.237-2.13.239-.76.422-1.621.477-2.834.055-1.213.071-1.7.071-5.321s-.016-4.108-.071-5.321c-.055-1.213-.238-2.074-.477-2.834-.242-.79-.567-1.46-.897-2.13-.63-.63-1.46-.897-2.13-1.237C19.445.422 18.584.239 17.371.071 16.158.016 15.671 0 12.017 0zm0 2.25c3.539 0 3.958.013 5.358.069 1.325.054 2.054.285 2.527.475.523.21.923.49 1.334.901.41.411.691.811.901 1.334.19.473.421 1.202.475 2.527.056 1.4.069 1.819.069 5.358s-.013 3.958-.069 5.358c-.054 1.325-.285 2.054-.475 2.527-.21.523-.49.923-.901 1.334-.411.41-.811.691-1.334.901-.473.19-1.202.421-2.527.475-1.4.056-1.819.069-5.358.069s-3.958-.013-5.358-.069c-1.325-.054-2.054-.285-2.527-.475-.523-.21-.923-.49-1.334-.901-.41-.411-.691-.811-.901-1.334-.19-.473-.421-1.202-.475-2.527C2.059 8.975 2.046 8.556 2.046 5.017s.013-3.958.069-5.358c.054-1.325.285-2.054.475-2.527.21-.523.49-.923.901-1.334.411-.41.811-.691 1.334-.901.473-.19 1.202-.421 2.527-.475 1.4-.056 1.819-.069 5.358-.069zm0 16.875c-3.636 0-6.562-2.926-6.562-6.562S8.381 5.75 12.017 5.75s6.562 2.926 6.562 6.562-2.926 6.563-6.562 6.563zm0-11c-2.829 0-5.125 2.296-5.125 5.125s2.296 5.125 5.125 5.125 5.125-2.296 5.125-5.125S14.846 9.875 12.017 9.875zm6.562 1.875c0 .759-.616 1.375-1.375 1.375s-1.375-.616-1.375-1.375.616-1.375 1.375-1.375 1.375.616 1.375 1.375z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-figma-red dark:hover:text-figma-bright-red transition-colors">
                            <span class="sr-only">YouTube</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
