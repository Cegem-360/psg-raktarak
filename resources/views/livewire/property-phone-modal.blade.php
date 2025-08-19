<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal Header -->
                    <div class="px-8 py-5 bg-orange-500">
                        <h3 class="text-2xl font-semibold text-center text-white">Kapcsolatfelvétel</h3>
                    </div> <!-- Modal Body -->
                    <div class="px-6 py-6 bg-white">
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <a href="mailto:info@psg-raktarak.hu"
                                class="px-4 py-3 text-sm font-medium text-center text-white transition-colors bg-blue-600 hover:bg-blue-700">
                                INFO@PSG-RAKTARAK.HU
                            </a>

                            <a href="tel:+36203813917"
                                class="px-4 py-3 text-sm font-medium text-center text-white transition-colors bg-blue-600 hover:bg-blue-700">
                                +36 20 381 3917
                            </a>

                            <a href="#" wire:click="openContactModal"
                                class="px-4 py-3 text-sm font-medium text-center text-white transition-colors bg-blue-600 hover:bg-blue-700">
                                ONLINE KAPCSOLATFELVÉTEL
                            </a>
                        </div>

                        @if ($property && $property->contact_person)
                            <div class="pt-4 mb-6 text-center border-t border-gray-200">
                                <p class="text-sm text-gray-600">Kapcsolattartó</p>
                                <p class="font-medium text-gray-900">{{ $property->contact_person }}</p>
                            </div>
                        @endif

                        <div class="text-center">
                            <button wire:click="closeModal"
                                class="px-8 py-2 text-gray-700 transition-colors bg-white border border-gray-300 hover:bg-gray-50">
                                MÉGSE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
