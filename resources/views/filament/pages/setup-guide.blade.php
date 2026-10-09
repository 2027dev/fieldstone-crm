<x-filament-panels::page>
    @php
        $tasks = $this->getTasks();
        $completed = $this->getCompletedCount();
        $total = count($tasks);
    @endphp

    <x-filament::section>
        <h2 class="text-xl font-bold text-gray-950 dark:text-white">
            Hi, {{ auth()->user()->name }}! Let's get you set up
        </h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Connect Fieldstone CRM with your workflow. Follow these milestones to customize your workspace.
        </p>

        <div class="mt-4 max-w-xl">
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                <div
                    class="h-2 rounded-full bg-primary-500 transition-all"
                    style="width: {{ $total > 0 ? (($completed + 1) / ($total + 1)) * 100 : 0 }}%"
                ></div>
            </div>
            <p class="mt-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                {{ $completed + 1 }} of {{ $total + 1 }} suggested tasks completed
            </p>
        </div>
    </x-filament::section>

    <x-filament::section>
        <div class="flex items-center gap-3">
            <x-filament::icon icon="heroicon-o-check-circle" class="h-6 w-6 text-success-500" />
            <div>
                <p class="font-semibold text-gray-950 dark:text-white">Set up account</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">You've successfully joined Fieldstone CRM! Continue by customizing your account to your needs.</p>
            </div>
        </div>
    </x-filament::section>

    <div>
        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            Your to-do list &mdash; cover the basics ({{ $completed }} of {{ $total }} tasks)
        </p>

        <div class="flex flex-col gap-4">
            @foreach ($tasks as $task)
                <x-filament::section>
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <x-filament::icon
                                :icon="$task['done'] ? 'heroicon-s-check-circle' : 'heroicon-o-ellipsis-horizontal-circle'"
                                @class(['h-6 w-6 shrink-0', 'text-success-500' => $task['done'], 'text-gray-300 dark:text-gray-600' => ! $task['done']])
                            />
                            <div>
                                <p @class(['font-semibold text-gray-950 dark:text-white', 'line-through text-gray-400 dark:text-gray-500' => $task['done']])>
                                    {{ $task['title'] }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $task['description'] }}</p>
                                <x-filament::badge color="gray" class="mt-2">{{ $task['time'] }}</x-filament::badge>
                            </div>
                        </div>

                        <a href="{{ $task['url'] }}">
                            <x-filament::button :color="$task['done'] ? 'gray' : 'success'" size="sm">
                                {{ $task['done'] ? 'View' : $task['cta'] }}
                            </x-filament::button>
                        </a>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
