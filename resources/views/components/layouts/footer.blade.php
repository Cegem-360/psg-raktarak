 <x-layouts.partials.ajanlat />
 <footer class="p-4 bg-white sm:p-12">
     <div class="max-w-screen-xl mx-auto">
         <div class="md:flex md:gap-8 md:justify-between">
             <div class="mb-6 md:mb-0">
                 <a href="{{ localized_route('home') }}" class="flex items-center">
                     <img src="{{ Vite::asset('resources/images/psg-raktarak-logo.webp') }}" class="h-8 mr-3 sm:h-16"
                         alt="PSG Raktárak logo" loading="lazy">
                 </a>
             </div>
             <div class="flex flex-wrap gap-8 lg:gap-20">
                 <div>
                     <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">{{ __('Contact') }}</h2>
                     <ul class="text-gray-600">
                         <li class="mb-4">
                             <h4 class="text-bold">Property Solution Group Kft.</h4>
                         </li>
                         <li class="mb-4">
                             <a href="tel:+36203813917 "
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600"> +36 20 381
                                 3917 </a>
                         </li>
                         <li class="mb-4">
                             <a href="mailto:info@psg-raktarak.hu"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">info@psg-raktarak.hu</a>
                         </li>
                         <li class="mb-4">
                             <a wire:navigate href="{{ localized_route('kapcsolat') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('online contact') }}</a>
                         </li>
                         <li>
                             <a wire:navigate href="{{ route('filament.admin.auth.login') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600"
                                 title="Coming soon">{{ __('login') }}</a>
                         </li>
                     </ul>
                 </div>
                 <div>
                     <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">{{ __('Menu') }}</h2>
                     <ul class="text-gray-600">
                         <li class="mb-4">
                             <a href="{{ localized_route('home') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('Homepage') }}</a>
                         </li>
                         <li class="mb-4">
                             <a href="{{ localized_route('kiado-raktarak') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('Warehouses for rent') }}</a>
                         </li>
                         <li class="mb-4">
                             <a href="{{ localized_route('elado-raktarak') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('Warehouses for Sale') }}</a>
                         </li>
                         @if (app()->getLocale() === 'hu')
                             <li class="mb-4">
                                 <a wire:navigate href="{{ localized_route('news.index') }}"
                                     class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('News') }}</a>
                             </li>
                         @endif
                     </ul>
                 </div>
                 <div>
                     <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">&nbsp;</h2>
                     <ul class="text-gray-600">
                         <li class="mb-4">
                             <a wire:navigate href="{{ localized_route('rolunk') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('About Us') }}</a>
                         </li>

                         </li>
                         <li>
                             <a wire:navigate href="{{ localized_route('kapcsolat') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('Contact') }}</a>
                         </li>
                     </ul>
                 </div>
                 <div>
                     <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">{{ __('Legal Statements') }}</h2>
                     <ul class="text-gray-600">
                         <li class="mb-4">
                             <a wire:navigate href="{{ localized_route('privacy-policy') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('Privacy Policy') }}</a>
                         </li>
                         <li>
                             <a wire:navigate href="{{ localized_route('impressum') }}"
                                 class="transition-colors duration-200 hover:underline hover:text-blue-600">{{ __('Imprint') }}</a>
                         </li>
                     </ul>
                 </div>
             </div>
         </div>
         <hr class="my-6 border-gray-200 sm:mx-auto lg:my-8" />
         <div class="sm:flex sm:items-center sm:justify-between">
             <span class="text-sm text-gray-500 sm:text-center">
                ©{{ date('Y')-1 }}-{{ date('Y') }}
                 <a wire:navigate href="/"
                     class="transition-colors duration-200 hover:underline hover:text-blue-600">
                     Property Solution Group
                    </a> - {{ __('All rights reserved') }}.
                </span>
             
             <div class="flex items-center gap-4 text-xs">
                 <div>Weboldalt készítette:</div>
                 <a href="https://cegem360.hu/" target="_blank">
                     <img src="{{ Vite::asset('resources/images/cegem360logo-black.webp') }}"
                         alt="Cégem 360 weboldal készítés" style="width:135px;border:0;">
                 </a>
             </div>
         </div>
     </div>
 </footer>
