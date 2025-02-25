<div x-show="$wire.showDeleteModal"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="relative inline-block transform overflow-hidden rounded-lg bg-white dark:bg-gray-900 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-white dark:bg-gray-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <!-- Modal content -->
                <div class="sm:flex sm:items-start">
                    <!-- Warning Icon -->
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">Delete Member</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                This action cannot be undone. This will permanently delete the member
                                <strong>{{ $memberToDelete?->name }}</strong> and all associated data.
                            </p>
                            <div class="mt-4">
                                <label for="confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Please type <strong>{{ $memberToDelete?->name }}</strong> to confirm.
                                </label>
                                <div class="mt-1">
                                    <flux:input
                                        wire:model.live="deleteConfirmation"
                                        type="text"
                                        id="confirmation"
                                        class="w-full"
                                        placeholder="Enter member name"
                                    />
                                    @error('deleteConfirmation')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <flux:button
                    wire:click="delete"
                    variant="danger"
                    class="w-full sm:ml-3 sm:w-auto"
                    :disabled="$deleteConfirmation !== ($memberToDelete?->name ?? '')"
                >
                    Delete Member
                </flux:button>
                <flux:button
                    wire:click="cancelDelete"
                    variant="ghost"
                    class="mt-3 w-full sm:mt-0 sm:w-auto"
                >
                    Cancel
                </flux:button>
            </div>
        </div>
    </div>
</div>
